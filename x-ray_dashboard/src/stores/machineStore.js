// src/stores/machineStore.js
import { defineStore } from "pinia";
import apiClient from "../api";

export const useMachineStore = defineStore("machine", {
  state: () => ({
    machineStatus: "OFF", // Default OFF, akan diupdate dari database
    isLoading: false,
    error: null,
    dashboardData: {
      total_ban_terinspeksi: 0,
      ban_ok: 0,
      ban_ng: 0,
      jam_operasi: 0,
      jam_terencana: 0,
      target_produksi: 0,
      aktual_produksi: 0,
      lastUpdated: null
    },
    oeeData: {
      availability: 0,
      performance: 0,
      quality: 0,
      oee: 0
    }
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
          labels: ['OK', 'NG'],
          datasets: [{
            data: [0, 0],
            backgroundColor: ['#41b883', '#e74c3c']
          }]
        };
      }
      
      return {
        labels: ['OK', 'NG'],
        datasets: [{
          data: [state.dashboardData.ban_ok, state.dashboardData.ban_ng],
          backgroundColor: ['#41b883', '#e74c3c']
        }]
      };
    }
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

        if (response.data) {
          // Update machine status dari database
          if (response.data["1_status_monitoring"]) {
            this.machineStatus = response.data["1_status_monitoring"];
          }

          // Simpan semua data dashboard
          this.dashboardData = {
            total_ban_terinspeksi: response.data.total_ban_terinspeksi || 0,
            ban_ok: response.data.ban_ok || 0,
            ban_ng: response.data.ban_ng || 0,
            jam_operasi: response.data.jam_operasi || 0,
            jam_terencana: response.data.jam_terencana || 0,
            target_produksi: response.data.target_produksi || 0,
            aktual_produksi: response.data.aktual_produksi || 0,
            lastUpdated: new Date().toLocaleTimeString()
          };

          // Hitung OEE dari data mentah
          this.calculateOEE();
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
        availability: Math.min(100, Math.max(0, parseFloat(availability.toFixed(2)))),
        performance: Math.min(100, Math.max(0, parseFloat(performance.toFixed(2)))),
        quality: Math.min(100, Math.max(0, parseFloat(quality.toFixed(2)))),
        oee: Math.min(100, Math.max(0, parseFloat(oee.toFixed(2))))
      };

      console.log("🧮 OEE Calculated:", this.oeeData);
    }
  }
});