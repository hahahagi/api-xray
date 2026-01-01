<!-- src/components/Dashboard/MachineControl.vue -->
<template>
  <div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-transparent border-0 fw-bold">
      Machine Control
    </div>
    <div
      class="card-body text-center d-flex flex-column justify-content-center"
    >
      <!-- Status Indicator -->
      <div class="mb-4">
        <div
          class="status-indicator-large mx-auto mb-3 shadow-sm"
          :class="machineStore.machineStatus === 'ON' ? 'on' : 'off'"
        >
          <i
            class="bi"
            :class="
              machineStore.machineStatus === 'ON'
                ? 'bi-lightning-charge-fill'
                : 'bi-power'
            "
          ></i>
        </div>
        <h3 class="mb-1 display-6 fw-bold">
          {{ machineStore.machineStatus }}
        </h3>
        <p class="text-muted small text-uppercase">Current Status</p>
      </div>

      <!-- Control Button -->
      <button
        class="btn w-100 py-3 fw-bold shadow-sm mt-auto"
        :class="
          machineStore.machineStatus === 'ON' ? 'btn-danger' : 'btn-success'
        "
        @click="
          toggleMachine(machineStore.machineStatus === 'ON' ? 'OFF' : 'ON')
        "
        :disabled="machineStore.isLoading"
      >
        <span
          v-if="machineStore.isLoading"
          class="spinner-border spinner-border-sm me-2"
        ></span>
        {{
          machineStore.machineStatus === "ON" ? "STOP MACHINE" : "START MACHINE"
        }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useMachineStore } from "../../stores/machineStore";

const machineStore = useMachineStore();

const toggleMachine = (status) => {
  machineStore.toggleMachine(status);
};
</script>

<style scoped>
.status-indicator-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  color: white;
  transition: all 0.3s ease;
}

.status-indicator-large.on {
  background: var(--status-on);
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
}

.status-indicator-large.off {
  background: var(--status-off);
  box-shadow: 0 0 20px rgba(239, 68, 68, 0.4);
}
</style>
