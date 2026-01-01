<!-- src/views/HomeView.vue -->
<template>
  <div class="home-view">
    <div class="container-fluid p-4">
      <!-- Header -->
      <div class="row mb-4 align-items-center">
        <div class="col">
          <h1 class="mb-1">X-Ray Inspection Monitor</h1>
          <p class="text-muted">Real-time Production Dashboard</p>
        </div>
        <div class="col-auto text-end">
          <div class="h4 mb-0 fw-bold text-primary">{{ currentTime }}</div>
          <div class="text-muted small">{{ currentDate }}</div>
        </div>
      </div>

      <!-- Main Grid Layout -->
      <div class="row g-4">
        <!-- Top Row -->
        <div class="col-xl-3 col-lg-4 col-md-6">
          <MachineControl />
        </div>
        <div class="col-xl-6 col-lg-4 col-md-12">
          <DashboardVisuals />
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
          <QualitySummary />
        </div>

        <!-- Bottom Row -->
        <div class="col-xl-3 col-lg-4 col-md-6">
          <OperationTime />
        </div>
        <div class="col-xl-6 col-lg-4 col-md-12">
          <ProductionOutput />
        </div>
        <div class="col-xl-3 col-lg-4 col-md-6">
          <SystemLog />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useMachineStore } from "../stores/machineStore";
import MachineControl from "../components/Dashboard/MachineControl.vue";
import DashboardVisuals from "../components/Dashboard/DashboardVisuals.vue";
import OperationTime from "../components/Dashboard/OperationTime.vue";
import ProductionOutput from "../components/Dashboard/ProductionOutput.vue";
import QualitySummary from "../components/Dashboard/QualitySummary.vue";
import SystemLog from "../components/Dashboard/SystemLog.vue";

const machineStore = useMachineStore();
const currentTime = ref("");
const currentDate = ref("");

function updateDateTime() {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString("en-US", { hour12: false });
  currentDate.value = now.toLocaleDateString("en-US", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  });
}

let timeInterval;
let dataInterval;

onMounted(() => {
  updateDateTime();
  timeInterval = setInterval(updateDateTime, 1000);
  machineStore.fetchDashboardData();

  // Poll data every 2 seconds
  dataInterval = setInterval(() => {
    machineStore.fetchDashboardData(true);
  }, 2000);
});

onUnmounted(() => {
  clearInterval(timeInterval);
  clearInterval(dataInterval);
});
</script>

<style scoped>
/* Scoped styles if needed, mostly using global style.css */
</style>

<style scoped>
.dashboard-clean {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  display: flex;
  flex-direction: column;
}

/* Header */
.dashboard-header {
  background: white;
  padding: 1rem 0;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1.5rem;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.header-left h1 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.time-display {
  text-align: right;
}

.time {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  font-family: "Roboto Mono", monospace;
}

.date {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Main Content */
.dashboard-main {
  flex: 1;
  padding: 1.5rem 0;
}

.sidebar {
  position: sticky;
  top: 1rem;
}

.main-content {
  background: transparent;
}

/* Footer */
.dashboard-footer {
  background: white;
  padding: 0.75rem 0;
  border-top: 1px solid #e5e7eb;
  box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1.5rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.footer-left,
.footer-center,
.footer-right {
  flex: 1;
}

.footer-center {
  text-align: center;
}

.footer-right {
  text-align: right;
}

.connection-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.status-dot.connected {
  background: #10b981;
  animation: pulse 2s infinite;
}

.refresh-info {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
}

.version {
  font-family: "Roboto Mono", monospace;
  font-weight: 600;
  color: #374151;
}

/* Responsive */
@media (max-width: 992px) {
  .sidebar {
    position: static;
    margin-bottom: 1.5rem;
  }

  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .time-display {
    text-align: center;
  }

  .footer-content {
    flex-direction: column;
    gap: 0.5rem;
    text-align: center;
  }

  .footer-left,
  .footer-center,
  .footer-right {
    width: 100%;
    text-align: center;
  }
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
</style>
