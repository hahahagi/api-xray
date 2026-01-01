<template>
  <div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-transparent border-0 fw-bold">
      Production Output
    </div>
    <div class="card-body">
      <div class="row text-center">
        <div class="col-6 border-end">
          <div class="text-muted small mb-1 text-uppercase">TARGET</div>
          <div class="display-6 fw-bold mb-0">
            {{ machineStore.dashboardData.target_produksi }}
          </div>
        </div>
        <div class="col-6">
          <div class="text-muted small mb-1 text-uppercase">ACTUAL</div>
          <div class="display-6 fw-bold mb-0 text-primary">
            {{ machineStore.dashboardData.aktual_produksi }}
          </div>
        </div>
      </div>

      <div class="mt-4">
        <div class="d-flex justify-content-between small text-muted mb-1">
          <span class="text-uppercase">Progress</span>
          <span class="fw-bold"
            >{{
              (
                (machineStore.dashboardData.aktual_produksi /
                  (machineStore.dashboardData.target_produksi || 1)) *
                100
              ).toFixed(1)
            }}%</span
          >
        </div>
        <div class="progress" style="height: 10px">
          <div
            class="progress-bar progress-bar-striped progress-bar-animated"
            :style="{
              width:
                Math.min(
                  100,
                  (machineStore.dashboardData.aktual_produksi /
                    (machineStore.dashboardData.target_produksi || 1)) *
                    100
                ) + '%',
            }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useMachineStore } from "../../stores/machineStore";
const machineStore = useMachineStore();
</script>
