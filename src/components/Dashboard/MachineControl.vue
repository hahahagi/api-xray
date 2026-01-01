<!-- src/components/Dashboard/MachineControl.vue -->
<template>
  <div class="machine-control-simple">
    <!-- Card 1: Status & Control -->
    <div class="status-control-card card shadow-sm mb-3">
      <div class="card-body">
        <!-- Status Display -->
        <div class="status-section text-center mb-4">
          <div class="status-indicator-wrapper mb-3">
            <div 
              class="status-indicator mx-auto"
              :class="statusClass"
            ></div>
          </div>
          <div>
            <h4 class="status-text" :class="statusTextClass">
              {{ machineStore.machineStatus }}
            </h4>
            <p class="text-muted mb-0 small">
              Last updated: {{ machineStore.dashboardData.lastUpdated || 'Never' }}
            </p>
          </div>
        </div>

        <!-- Control Button -->
        <div class="control-section text-center">
          <button 
            v-if="machineStore.machineStatus === 'OFF'"
            class="btn-start btn-lg w-100"
            @click="toggleMachine('ON')"
            :disabled="machineStore.isLoading"
          >
            <span v-if="machineStore.isLoading" class="spinner"></span>
            <span class="btn-text">START MACHINE</span>
            <i class="bi bi-play-fill ms-2"></i>
          </button>
          
          <button 
            v-else
            class="btn-stop btn-lg w-100"
            @click="toggleMachine('OFF')"
            :disabled="machineStore.isLoading"
          >
            <span v-if="machineStore.isLoading" class="spinner"></span>
            <span class="btn-text">STOP MACHINE</span>
            <i class="bi bi-stop-fill ms-2"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Card 2: Quick Stats -->
    <div class="stats-card card shadow-sm">
      <div class="card-body">
        <h6 class="card-title mb-3">
          <i class="bi bi-graph-up me-2"></i>
          Production Summary
        </h6>
        
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-icon">
              <i class="bi bi-box-seam"></i>
            </div>
            <div>
              <div class="stat-value">{{ formatNumber(machineStore.dashboardData.total_ban_terinspeksi) }}</div>
              <div class="stat-label">Total Inspected</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon success">
              <i class="bi bi-check-circle"></i>
            </div>
            <div>
              <div class="stat-value success">{{ formatNumber(machineStore.dashboardData.ban_ok) }}</div>
              <div class="stat-label">OK Tires</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon danger">
              <i class="bi bi-x-circle"></i>
            </div>
            <div>
              <div class="stat-value danger">{{ formatNumber(machineStore.dashboardData.ban_ng) }}</div>
              <div class="stat-label">NG Tires</div>
            </div>
          </div>
          
          <div class="stat-item">
            <div class="stat-icon primary">
              <i class="bi bi-percent"></i>
            </div>
            <div>
              <div class="stat-value primary">{{ machineStore.oeeData.quality.toFixed(1) }}%</div>
              <div class="stat-label">Quality Rate</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="machineStore.error" class="error-message mt-3">
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ machineStore.error }}
        <button type="button" class="btn-close" @click="machineStore.error = null"></button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import { useMachineStore } from '../../stores/machineStore'

const machineStore = useMachineStore()

const statusClass = computed(() => {
  return machineStore.machineStatus === 'ON' ? 'status-on' : 'status-off'
})

const statusTextClass = computed(() => {
  return machineStore.machineStatus === 'ON' ? 'text-success' : 'text-danger'
})

const toggleMachine = async (status) => {
  await machineStore.toggleMachine(status)
}

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num)
}

let pollingInterval

onMounted(() => {
  machineStore.fetchDashboardData(false)
  
  pollingInterval = setInterval(() => {
    machineStore.fetchDashboardData(true)
  }, 10000)
})

onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval)
})
</script>

<style scoped>
.machine-control-simple {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Status & Control Card */
.status-control-card {
  border: none;
  border-radius: 12px;
  background: white;
}

.status-indicator-wrapper {
  position: relative;
  height: 60px;
}

.status-indicator {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  position: relative;
  transition: all 0.3s ease;
}

.status-indicator::after {
  content: '';
  position: absolute;
  top: -8px;
  left: -8px;
  right: -8px;
  bottom: -8px;
  border-radius: 50%;
  opacity: 0.3;
  animation: pulse-ring 2s infinite;
}

.status-on {
  background: linear-gradient(135deg, #10b981, #059669);
  box-shadow: 0 0 30px rgba(16, 185, 129, 0.3);
}

.status-on::after {
  background: #10b981;
}

.status-off {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  box-shadow: 0 0 30px rgba(239, 68, 68, 0.3);
}

.status-off::after {
  background: #ef4444;
}

.status-text {
  font-weight: 600;
  letter-spacing: 1px;
  margin-bottom: 0.25rem;
}

/* Control Buttons */
.btn-start, .btn-stop {
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  letter-spacing: 0.5px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.btn-start {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.btn-stop {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
}

.btn-start:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
}

.btn-stop:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
}

.btn-start:disabled, .btn-stop:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.75rem;
}

/* Stats Card */
.stats-card {
  border: none;
  border-radius: 12px;
  background: white;
}

.card-title {
  font-weight: 600;
  color: #374151;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.stat-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  background: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  color: #6b7280;
}

.stat-icon.success {
  background: #d1fae5;
  color: #10b981;
}

.stat-icon.danger {
  background: #fee2e2;
  color: #ef4444;
}

.stat-icon.primary {
  background: #dbeafe;
  color: #3b82f6;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  font-family: 'Roboto Mono', monospace;
}

.stat-value.success {
  color: #10b981;
}

.stat-value.danger {
  color: #ef4444;
}

.stat-value.primary {
  color: #3b82f6;
}

.stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 0.125rem;
}

/* Error Message */
.error-message .alert {
  border-radius: 8px;
  border: none;
  background: #fef2f2;
  color: #991b1b;
  border-left: 4px solid #ef4444;
}

/* Animations */
@keyframes pulse-ring {
  0% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  80%, 100% {
    transform: scale(1.2);
    opacity: 0;
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .status-indicator {
    width: 50px;
    height: 50px;
  }
}
</style>