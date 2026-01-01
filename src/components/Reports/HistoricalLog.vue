<template>
  <div class="historical-log card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Historical Machine Log</h5>
      <div class="date-filter">
        <input 
          type="date" 
          v-model="selectedDate" 
          @change="fetchLogData"
          class="form-control form-control-sm"
        >
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-sm">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Timestamp</th>
              <th>Status</th>
              <th>Duration</th>
              <th>Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(log, index) in processedLogs" :key="log.id">
              <td>{{ index + 1 }}</td>
              <td>{{ formatDateTime(log.timestamp) }}</td>
              <td>
                <span :class="`badge ${log.status === 'ON' ? 'bg-success' : 'bg-danger'}`">
                  {{ log.status }}
                </span>
              </td>
              <td>{{ log.duration || 'N/A' }}</td>
              <td>
                <span :class="`badge ${log.type === 'Runtime' ? 'bg-info' : 'bg-warning'}`">
                  {{ log.type }}
                </span>
              </td>
              <td>
                <button 
                  @click="showLogDetails(log)" 
                  class="btn btn-sm btn-outline-primary"
                >
                  <i class="bi bi-eye"></i>
                </button>
              </td>
            </tr>
            <tr v-if="processedLogs.length === 0">
              <td colspan="6" class="text-center text-muted py-3">
                No log data available
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Summary Statistics -->
      <div class="summary-stats mt-4">
        <div class="row">
          <div class="col-md-4">
            <div class="stat-card bg-light p-3 rounded">
              <div class="stat-label text-muted">Total Runtime</div>
              <div class="stat-value h4">{{ totalRuntime }}</div>
              <div class="stat-unit">Minutes</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stat-card bg-light p-3 rounded">
              <div class="stat-label text-muted">Total Downtime</div>
              <div class="stat-value h4">{{ totalDowntime }}</div>
              <div class="stat-unit">Minutes</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stat-card bg-light p-3 rounded">
              <div class="stat-label text-muted">Machine Cycles</div>
              <div class="stat-value h4">{{ cycleCount }}</div>
              <div class="stat-unit">Times</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../api'

const selectedDate = ref(new Date().toISOString().split('T')[0])
const rawLogs = ref([])
const processedLogs = ref([])

// Summary statistics
const totalRuntime = ref(0)
const totalDowntime = ref(0)
const cycleCount = ref(0)

const fetchLogData = async () => {
  try {
    const response = await apiClient.get('', {
      params: {
        mode: 'get_historical_log',
        date: selectedDate.value
      }
    })
    
    rawLogs.value = response.data.logs || []
    processLogs()
  } catch (error) {
    console.error('Error fetching log data:', error)
  }
}

const processLogs = () => {
  processedLogs.value = []
  totalRuntime.value = 0
  totalDowntime.value = 0
  cycleCount.value = 0
  
  for (let i = 0; i < rawLogs.value.length - 1; i++) {
    const current = rawLogs.value[i]
    const next = rawLogs.value[i + 1]
    
    const startTime = new Date(current.timestamp)
    const endTime = new Date(next.timestamp)
    const duration = Math.round((endTime - startTime) / (1000 * 60)) // in minutes
    
    processedLogs.value.push({
      id: current.id,
      timestamp: current.timestamp,
      status: current.status,
      duration: duration,
      type: current.status === 'ON' ? 'Runtime' : 'Downtime'
    })
    
    if (current.status === 'ON') {
      totalRuntime.value += duration
    } else {
      totalDowntime.value += duration
    }
    
    if (current.status === 'ON' && next.status === 'OFF') {
      cycleCount.value++
    }
  }
}

const formatDateTime = (datetime) => {
  const date = new Date(datetime)
  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const showLogDetails = (log) => {
  alert(`Log Details:\n\nTimestamp: ${formatDateTime(log.timestamp)}\nStatus: ${log.status}\nDuration: ${log.duration} minutes\nType: ${log.type}`)
}

onMounted(() => {
  fetchLogData()
})
</script>

<style scoped>
.stat-card {
  border: 1px solid #dee2e6;
}

.stat-label {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  font-weight: 700;
  color: #2c3e50;
  margin: 0.5rem 0;
}

.stat-unit {
  font-size: 0.8rem;
  color: #6c757d;
}

.table th {
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.table td {
  vertical-align: middle;
}

.badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}

.date-filter {
  width: 180px;
}
</style>