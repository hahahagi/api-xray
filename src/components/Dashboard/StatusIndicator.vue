<template>
  <div
    class="status-indicator d-flex align-items-center"
    title="Machine Status"
  >
    <div :class="['status-lamp', statusClass]"></div>
    <div class="status-text">
      <div class="status-label">Machine</div>
      <div class="status-value">{{ machineStatus }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useMachineStore } from "../../stores/machineStore";

const store = useMachineStore();

const machineStatus = computed(() => store.machineStatus || "OFF");

const statusClass = computed(() => {
  return machineStatus.value === "ON" ? "on" : "off";
});
</script>

<style scoped>
.status-indicator {
  gap: 0.75rem;
}

.status-lamp {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  box-shadow: 0 0 6px rgba(0, 0, 0, 0.12);
  transition: all 0.25s ease;
}

.status-lamp.on {
  background: linear-gradient(180deg, #34d399, #059669);
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.45);
}

.status-lamp.off {
  background: linear-gradient(180deg, #f87171, #dc2626);
  box-shadow: 0 0 10px rgba(220, 38, 38, 0.25);
}

.status-text .status-label {
  font-size: 0.7rem;
  color: #6b7280;
}

.status-text .status-value {
  font-weight: 700;
  font-family: "Roboto Mono", monospace;
  color: #111827;
}
</style>
