import { defineStore } from "pinia";
import apiClient from "../api";

export const useMachineStore = defineStore("machine", {
  state: () => ({
    machineStatus: "OFF", // 'ON' or 'OFF'
    isLoading: false,
    error: null,
  }),
  actions: {
    async toggleMachine(status) {
      this.isLoading = true;
      this.error = null;
      try {
        // Menggunakan mode=input_status sesuai api.php
        // Ini akan menyimpan status ke tb_machine_log
        const response = await apiClient.get("", {
          params: {
            mode: "input_status",
            status: status,
          },
        });

        if (response.data.status === "success") {
          this.machineStatus = response.data.saved_status;
          console.log("Machine status updated:", response.data);
        } else {
          throw new Error("API returned error");
        }
      } catch (err) {
        console.error("Error toggling machine:", err);
        this.error = `Failed to update machine status: ${err.message}`;
      } finally {
        this.isLoading = false;
      }
    },
    async fetchMachineStatus(isBackground = false) {
      if (!isBackground) this.isLoading = true;
      try {
        // Menggunakan mode=get_dashboard_data sesuai api.php
        const response = await apiClient.get("", {
          params: { mode: "get_dashboard_data" },
        });

        // Mapping dari field JSON "1_status_monitoring"
        if (response.data && response.data["1_status_monitoring"]) {
          this.machineStatus = response.data["1_status_monitoring"];
        }
      } catch (err) {
        console.error("Error fetching status:", err);
      } finally {
        if (!isBackground) this.isLoading = false;
      }
    },
  },
});
