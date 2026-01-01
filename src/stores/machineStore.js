// src/stores/machineStore.js
import { defineStore } from "pinia";
import apiClient from "../api";

export const useMachineStore = defineStore("machine", {
  state: () => ({
    machineStatus: "OFF", // Default OFF, akan diupdate dari database
    isLoading: false,
    error: null,
    // Default planned shift length (hours) used to compute availability when API doesn't provide planned time
    shiftHours: 8,
    dashboardData: {
      total_ban_terinspeksi: 0,
      ban_ok: 0,
      ban_ng: 0,
      jam_operasi: 0,
      jam_terencana: 0,
      target_produksi: 0,
      aktual_produksi: 0,
      lastUpdated: null,
    },
    oeeData: {
      availability: 0,
      performance: 0,
      quality: 0,
      oee: 0,
    },
  }),

  getters: {
    // Status warna sesuai kondisi
    statusColor: (state) => {
      return state.machineStatus === "ON" ? "success" : "danger";
    },

    // Data untuk Pie Chart (OK vs NG)
    pieChartData: (state) => {
      const total = state.dashboardData.ban_ok + state.dashboardData.ban_ng;
      if (total === 0) {
        return {
          labels: ["OK", "NG"],
          datasets: [
            {
              data: [0, 0],
              backgroundColor: ["#41b883", "#e74c3c"],
            },
          ],
        };
      }

      return {
        labels: ["OK", "NG"],
        datasets: [
          {
            data: [state.dashboardData.ban_ok, state.dashboardData.ban_ng],
            backgroundColor: ["#41b883", "#e74c3c"],
          },
        ],
      };
    },
  },

  actions: {
    // Toggle machine status (POST ke database)
    async toggleMachine(status) {
      this.isLoading = true;
      this.error = null;
      try {
        const response = await apiClient.get("", {
          params: {
            mode: "input_status",
            status: status,
          },
        });

        if (response.data.status === "success") {
          // Update status dari response database
          this.machineStatus = response.data.saved_status;
          console.log("✅ Machine status updated in DB:", response.data);

          // Refresh data setelah update
          await this.fetchDashboardData();
        } else {
          throw new Error(response.data.message || "API returned error");
        }
      } catch (err) {
        console.error("❌ Error toggling machine:", err);
        this.error = `Failed to update machine status: ${err.message}`;
      } finally {
        this.isLoading = false;
      }
    },

    // Fetch data dari database (GET dari API)
    async fetchDashboardData(isBackground = false) {
      if (!isBackground) this.isLoading = true;
      try {
        const response = await apiClient.get("", {
          params: { mode: "get_dashboard_data" },
        });

        console.log("📊 API Response:", response.data);
        const api = response.data;
        if (api) {
          // Primary API format (Numbered Keys from api-hosting.php)
          if (api["1_status_monitoring"] !== undefined) {
            this.machineStatus = api["1_status_monitoring"];

            // Parse runtime minutes from string like "123.4 Menit" or numeric
            let runtimeMinutes = 0;
            if (api["2_waktu_operasi"] !== undefined) {
              const raw = String(api["2_waktu_operasi"]);
              const m = raw.match(/[\d\.]+/);
              runtimeMinutes = m ? parseFloat(m[0]) : 0;
            }

            // Parse downtime minutes
            let downtimeMinutes = 0;
            if (api["3_waktu_downtime"] !== undefined) {
              const raw = String(api["3_waktu_downtime"]);
              const m = raw.match(/[\d\.]+/);
              downtimeMinutes = m ? parseFloat(m[0]) : 0;
            }

            const runtimeHours = runtimeMinutes / 60;

            const totalBanCheck =
              parseInt(api["5_total_ban_check"] ?? 0, 10) || 0;
            const banOk = parseInt(api["6_quality_ok"] ?? 0, 10) || 0;
            const banNg = parseInt(api["6_quality_ng"] ?? 0, 10) || 0;

            // If API doesn't provide planned time, use store's shiftHours
            const plannedHours = this.shiftHours || 8;

            // Calculate target production based on planned hours and ideal speed (e.g., 10 tires/min)
            const idealSpeedPerMin = 10;
            const targetProduksi = plannedHours * 60 * idealSpeedPerMin;

            this.dashboardData = {
              total_ban_terinspeksi: totalBanCheck,
              ban_ok: banOk,
              ban_ng: banNg,
              jam_operasi: parseFloat(runtimeHours.toFixed(2)),
              downtime_minutes: downtimeMinutes, // Added this line
              jam_terencana: plannedHours,
              target_produksi: targetProduksi,
              aktual_produksi: totalBanCheck,
              lastUpdated: new Date().toLocaleTimeString(),
            };

            this.calculateOEE();
          }
          // Alternative/Clean API format (if needed in future)
          else if (api.machine_status !== undefined) {
            this.machineStatus = api.machine_status;

            const runtimeMinutes = parseFloat(api.runtime_minutes || 0);
            const runtimeHours = runtimeMinutes / 60;

            const totalBanCheck = parseInt(api.total_checked || 0, 10);
            const banOk = parseInt(api.total_ok || 0, 10);
            const banNg = parseInt(api.total_ng || 0, 10);

            const plannedHours = this.shiftHours || 8;
            const idealSpeedPerMin = 10;
            const targetProduksi = plannedHours * 60 * idealSpeedPerMin;

            this.dashboardData = {
              total_ban_terinspeksi: totalBanCheck,
              ban_ok: banOk,
              ban_ng: banNg,
              jam_operasi: parseFloat(runtimeHours.toFixed(2)),
              jam_terencana: plannedHours,
              target_produksi: targetProduksi,
              aktual_produksi: totalBanCheck,
              lastUpdated: new Date().toLocaleTimeString(),
            };

            this.calculateOEE();
          } else {
            // Fallback: older API shape or direct field names
            this.dashboardData = {
              total_ban_terinspeksi: api.total_ban_terinspeksi || 0,
              ban_ok: api.ban_ok || 0,
              ban_ng: api.ban_ng || 0,
              jam_operasi: api.jam_operasi || 0,
              jam_terencana: api.jam_terencana || this.shiftHours || 8,
              target_produksi: api.target_produksi || 0,
              aktual_produksi: api.aktual_produksi || 0,
              lastUpdated: new Date().toLocaleTimeString(),
            };

            if (api.machineStatus) this.machineStatus = api.machineStatus;
            this.calculateOEE();
          }
        }
      } catch (err) {
        console.error("❌ Error fetching dashboard data:", err);
        this.error = err.message;
      } finally {
        if (!isBackground) this.isLoading = false;
      }
    },

    // Hitung OEE berdasarkan rumus
    calculateOEE() {
      const data = this.dashboardData;

      // 1. Availability = (Actual Operating Time / Planned Production Time) × 100
      let availability = 0;
      if (data.jam_terencana > 0) {
        availability = (data.jam_operasi / data.jam_terencana) * 100;
      }

      // 2. Performance = (Actual Production Rate / Ideal Production Rate) × 100
      let performance = 0;
      if (data.target_produksi > 0) {
        performance = (data.aktual_produksi / data.target_produksi) * 100;
      }

      // 3. Quality = (Good Products / Total Products) × 100
      let quality = 0;
      const totalProducts = data.ban_ok + data.ban_ng;
      if (totalProducts > 0) {
        quality = (data.ban_ok / totalProducts) * 100;
      }

      // 4. OEE = Availability × Performance × Quality / 10000
      const oee = (availability * performance * quality) / 10000;

      this.oeeData = {
        availability: Math.min(
          100,
          Math.max(0, parseFloat(availability.toFixed(2)))
        ),
        performance: Math.min(
          100,
          Math.max(0, parseFloat(performance.toFixed(2)))
        ),
        quality: Math.min(100, Math.max(0, parseFloat(quality.toFixed(2)))),
        oee: Math.min(100, Math.max(0, parseFloat(oee.toFixed(2)))),
      };

      console.log("🧮 OEE Calculated:", this.oeeData);
    },
  },
});
