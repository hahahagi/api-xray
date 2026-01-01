<template>
  <div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-transparent border-0 fw-bold">System Log</div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted">Last Update</span>
        <span class="fw-bold font-monospace">{{
          machineStore.dashboardData.lastUpdated || "-"
        }}</span>
      </div>
      <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted">Last Log Time</span>
        <span class="fw-bold font-monospace">{{
          machineStore.dashboardData.last_log_time || "-"
        }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useMachineStore } from "../../stores/machineStore";
const machineStore = useMachineStore();
</script>
