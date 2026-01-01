<template>
  <div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-transparent border-0 fw-bold">
      Quality Summary
    </div>
    <div class="card-body text-center">
      <div class="row mb-4">
        <div class="col-6">
          <div class="metric-value text-success display-6 fw-bold">
            {{ machineStore.dashboardData.ban_ok }}
          </div>
          <div class="metric-label text-muted small text-uppercase">
            OK Tires
          </div>
        </div>
        <div class="col-6">
          <div class="metric-value text-danger display-6 fw-bold">
            {{ machineStore.dashboardData.ban_ng }}
          </div>
          <div class="metric-label text-muted small text-uppercase">
            NG Tires
          </div>
        </div>
      </div>
      <PieChart :chartData="machineStore.pieChartData" />
    </div>
  </div>
</template>

<script setup>
import { useMachineStore } from "../../stores/machineStore";
import PieChart from "./PieChart.vue";

const machineStore = useMachineStore();
</script>
