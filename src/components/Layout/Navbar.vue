<template>
  <nav class="navbar-industrial">
    <div class="container-fluid">
      <div class="navbar-brand-wrapper">
        <div class="brand-icon">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
            <path
              d="M12 2L2 7L12 12L22 7L12 2Z"
              fill="var(--industrial-accent)"
            />
            <path
              d="M2 17L12 22L22 17"
              stroke="var(--industrial-accent)"
              stroke-width="2"
            />
            <path
              d="M2 12L12 17L22 12"
              stroke="var(--industrial-accent)"
              stroke-width="2"
            />
          </svg>
        </div>
        <div class="brand-text">
          <h1 class="brand-title">TIRE X-RAY MONITORING</h1>
          <div class="brand-subtitle">INDUSTRIAL INSPECTION SYSTEM</div>
        </div>
      </div>

      <div class="navbar-controls">
        <button
          class="hamburger-btn"
          @click="toggleSidebar"
          aria-label="Toggle sidebar"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path
              d="M3 12h18M3 6h18M3 18h18"
              stroke="white"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </button>
        <div class="system-status">
          <div class="status-indicator" :class="statusClass"></div>
          <span class="status-text">System: {{ currentStatus }}</span>
        </div>
        <div class="datetime">
          <span class="time">{{ currentTime }}</span>
          <span class="date">{{ currentDate }}</span>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useMachineStore } from "../../stores/machineStore";
import { useUIStore } from "../../stores/uiStore";

const uiStore = useUIStore();

function toggleSidebar() {
  uiStore.toggleSidebar();
}

const machineStore = useMachineStore();
const currentTime = ref("");
const currentDate = ref("");

const currentStatus = computed(() => {
  return machineStore.machineStatus;
});

const statusClass = computed(() => {
  return machineStore.machineStatus === "ON" ? "status-on" : "status-off";
});

function updateDateTime() {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString("en-US", {
    hour12: false,
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
  currentDate.value = now.toLocaleDateString("en-US", {
    weekday: "short",
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}

let interval;
onMounted(() => {
  updateDateTime();
  interval = setInterval(updateDateTime, 1000);
});

onUnmounted(() => {
  clearInterval(interval);
});
</script>

<style scoped>
.navbar-industrial {
  background: linear-gradient(135deg, var(--industrial-dark) 0%, #1e293b 100%);
  border-bottom: 3px solid var(--industrial-accent);
  padding: 0.75rem 1.5rem;
  box-shadow: var(--shadow-lg);
}

.navbar-brand-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.brand-icon {
  padding: 8px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: var(--radius-sm);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.brand-text {
  display: flex;
  flex-direction: column;
}

.brand-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  margin: 0;
  letter-spacing: 1px;
}

.brand-subtitle {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.6);
  letter-spacing: 0.5px;
}

.navbar-controls {
  display: flex;
  align-items: center;
  gap: 2rem;
}

/* Hamburger for small screens */
.hamburger-btn {
  display: none;
  background: transparent;
  border: none;
  padding: 0.25rem;
  margin-right: 0.5rem;
  cursor: pointer;
  z-index: 2100;
}

@media (max-width: 992px) {
  .hamburger-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
}

.system-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: var(--radius-sm);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.status-indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  animation: pulse 2s infinite;
}

.status-on {
  background: var(--industrial-success);
  box-shadow: 0 0 10px var(--industrial-success);
}

.status-off {
  background: var(--industrial-danger);
  box-shadow: 0 0 10px var(--industrial-danger);
}

.status-text {
  color: white;
  font-weight: 500;
  font-size: 0.9rem;
}

.datetime {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.time {
  font-family: "Roboto Mono", monospace;
  font-size: 1.25rem;
  font-weight: 600;
  color: white;
  letter-spacing: 1px;
}

.date {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.6);
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

@media (max-width: 768px) {
  .navbar-industrial {
    padding: 0.5rem 1rem;
  }

  .brand-title {
    font-size: 1rem;
  }

  .navbar-controls {
    gap: 1rem;
  }
}
</style>
