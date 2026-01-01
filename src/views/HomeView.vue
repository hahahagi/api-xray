<!-- src/views/HomeView.vue -->
<template>
  <div class="dashboard-clean">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="container-fluid">
        <div class="header-content">
          <div class="header-left">
            <div class="logo">
              <i class="bi bi-cpu"></i>
            </div>
            <div>
              <h1>X-Ray Monitor</h1>
              <p class="subtitle">Tire Inspection System</p>
            </div>
          </div>
          <div class="header-right">
            <div class="time-display">
              <div class="time">{{ currentTime }}</div>
              <div class="date">{{ currentDate }}</div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="dashboard-main">
      <div class="container-fluid">
        <div class="row">
          <!-- Sidebar: Machine Control -->
          <div class="col-lg-4">
            <div class="sidebar">
              <MachineControl />
            </div>
          </div>
          
          <!-- Main: Dashboard -->
          <div class="col-lg-8">
            <div class="main-content">
              <DashboardVisuals />
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="dashboard-footer">
      <div class="container-fluid">
        <div class="footer-content">
          <div class="footer-left">
            <span class="connection-status">
              <span class="status-dot connected"></span>
              Connected to X-Ray Machine
            </span>
          </div>
          <div class="footer-center">
            <span class="refresh-info">
              <i class="bi bi-arrow-clockwise"></i>
              Auto-refresh: 10s
            </span>
          </div>
          <div class="footer-right">
            <span class="version">v2.0</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import MachineControl from '../components/Dashboard/MachineControl.vue'
import DashboardVisuals from '../components/Dashboard/DashboardVisuals.vue'

const currentTime = ref('')
const currentDate = ref('')

function updateDateTime() {
  const now = new Date()
  
  currentTime.value = now.toLocaleTimeString('en-US', {
    hour12: false,
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
  
  currentDate.value = now.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

let timeInterval

onMounted(() => {
  updateDateTime()
  timeInterval = setInterval(updateDateTime, 1000)
})

onUnmounted(() => {
  clearInterval(timeInterval)
})
</script>

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
  font-family: 'Roboto Mono', monospace;
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
  font-family: 'Roboto Mono', monospace;
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
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
</style>