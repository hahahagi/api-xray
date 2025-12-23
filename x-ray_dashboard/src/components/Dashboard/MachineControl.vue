<template>
  <div class="card">
    <div class="card-header">Machine Control</div>
    <div class="card-body text-center">
      <h5 class="card-title">
        Status:
        <span :class="statusClass">{{ machineStore.machineStatus }}</span>
      </h5>
      <p class="card-text">Control the X-Ray machine operation.</p>

      <div class="d-grid gap-2 col-6 mx-auto">
        <button
          v-if="machineStore.machineStatus === 'OFF'"
          class="btn btn-success"
          @click="toggleMachine('ON')"
          :disabled="machineStore.isLoading"
        >
          <span
            v-if="machineStore.isLoading"
            class="spinner-border spinner-border-sm"
            role="status"
            aria-hidden="true"
          ></span>
          START MACHINE
        </button>

        <button
          v-else
          class="btn btn-danger"
          @click="toggleMachine('OFF')"
          :disabled="machineStore.isLoading"
        >
          <span
            v-if="machineStore.isLoading"
            class="spinner-border spinner-border-sm"
            role="status"
            aria-hidden="true"
          ></span>
          STOP MACHINE
        </button>
      </div>

      <div
        v-if="machineStore.error"
        class="alert alert-danger mt-3"
        role="alert"
      >
        {{ machineStore.error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from "vue";
import { useMachineStore } from "../../stores/machineStore";

const machineStore = useMachineStore();

const statusClass = computed(() => {
  return machineStore.machineStatus === "ON"
    ? "text-success fw-bold"
    : "text-danger fw-bold";
});

const toggleMachine = (status) => {
  machineStore.toggleMachine(status);
};

let pollingInterval;

onMounted(() => {
  // Fetch status immediately on load (show loading)
  machineStore.fetchMachineStatus(false);

  // Poll every 5 seconds to keep status updated (background, no loading spinner)
  pollingInterval = setInterval(() => {
    machineStore.fetchMachineStatus(true);
  }, 5000);
});

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
});
</script>

<style scoped></style>
