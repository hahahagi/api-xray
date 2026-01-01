<!-- src/components/Dashboard/DashboardVisuals.vue -->
<template>
  <div class="card border-0 shadow-sm h-100">
    <div
      class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center"
    >
      <span class="fw-bold">Overall Equipment Effectiveness</span>
      <span class="badge bg-primary rounded-pill">Live</span>
    </div>
    <div class="card-body">
      <div class="text-center mb-4">
        <OEEGauge :value="machineStore.oeeData.oee" :size="220" />
        <div class="mt-3">
          <h2 class="mb-0 display-6 fw-bold">
            {{ machineStore.oeeData.oee }}%
          </h2>
          <p class="text-muted">OEE Score</p>
        </div>
      </div>

      <!-- OEE Breakdown -->
      <div class="row g-3">
        <div class="col-4 text-center">
          <div class="h5 mb-1 fw-bold">
            {{ machineStore.oeeData.availability }}%
          </div>
          <div class="text-muted small text-uppercase">Availability</div>
          <div class="progress mt-2" style="height: 6px">
            <div
              class="progress-bar bg-dark"
              :style="{ width: machineStore.oeeData.availability + '%' }"
            ></div>
          </div>
        </div>
        <div class="col-4 text-center">
          <div class="h5 mb-1 text-primary fw-bold">
            {{ machineStore.oeeData.performance }}%
          </div>
          <div class="text-muted small text-uppercase">Performance</div>
          <div class="progress mt-2" style="height: 6px">
            <div
              class="progress-bar bg-primary"
              :style="{ width: machineStore.oeeData.performance + '%' }"
            ></div>
          </div>
        </div>
        <div class="col-4 text-center">
          <div class="h5 mb-1 text-success fw-bold">
            {{ machineStore.oeeData.quality }}%
          </div>
          <div class="text-muted small text-uppercase">Quality</div>
          <div class="progress mt-2" style="height: 6px">
            <div
              class="progress-bar bg-success"
              :style="{ width: machineStore.oeeData.quality + '%' }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useMachineStore } from "../../stores/machineStore";
import OEEGauge from "./OEEGauge.vue";

const machineStore = useMachineStore();
</script>
