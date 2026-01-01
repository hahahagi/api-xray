<template>
  <div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-transparent border-0 fw-bold">
      Operation Time
    </div>
    <div class="card-body">
      <div class="mb-4">
        <div class="d-flex justify-content-between align-items-end mb-1">
          <span class="text-muted small text-uppercase">Runtime</span>
          <span class="h4 mb-0 text-success fw-bold"
            >{{ machineStore.dashboardData.jam_operasi }}h</span
          >
        </div>
        <div class="progress" style="height: 8px">
          <div class="progress-bar bg-success" style="width: 70%"></div>
        </div>
      </div>

      <div>
        <div class="d-flex justify-content-between align-items-end mb-1">
          <span class="text-muted small text-uppercase">Downtime</span>
          <span class="h4 mb-0 text-danger fw-bold"
            >{{
              (machineStore.dashboardData.downtime_minutes || 0).toFixed(1)
            }}m</span
          >
        </div>
        <div class="progress" style="height: 8px">
          <div class="progress-bar bg-danger" style="width: 30%"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useMachineStore } from "../../stores/machineStore";
const machineStore = useMachineStore();
</script>
