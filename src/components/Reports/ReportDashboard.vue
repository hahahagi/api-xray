<template>
  <div class="report-dashboard">
    <!-- Summary Row -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="summary-today card border-primary">
          <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Today's Production</h6>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="card-title">{{ summary.today.total }}</h3>
                <small class="text-muted">Total Inspected</small>
              </div>
              <div class="text-end">
                <div class="badge bg-primary">{{ summary.today.quality_rate }}% Quality</div>
                <div class="mt-1">
                  <span class="text-success">{{ summary.today.ok }} OK</span> / 
                  <span class="text-danger">{{ summary.today.ng }} NG</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="summary-yesterday card">
          <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Yesterday</h6>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="card-title">{{ summary.yesterday.total }}</h3>
                <small class="text-muted">Total Inspected</small>
              </div>
              <div class="text-end">
                <span class="text-success">{{ summary.yesterday.ok }} OK</span> / 
                <span class="text-danger">{{ summary.yesterday.ng }} NG</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="summary-month card border-info">
          <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">This Month</h6>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="card-title">{{ summary.month.total }}</h3>
                <small class="text-muted">Total Inspected</small>
              </div>
              <div class="text-end">
                <span class="text-success">{{ summary.month.ok }} OK</span> / 
                <span class="text-danger">{{ summary.month.ng }} NG</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-3">
        <div class="quick-actions card border-success">
          <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Quick Actions</h6>
            <div class="d-grid gap-2">
              <button @click="exportAllData" class="btn btn-sm btn-outline-success">
                <i class="bi bi-download"></i> Export All Data
              </button>
              <button @click="generateDailyReport" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-text"></i> Daily Report
              </button>
              <button @click="showDateRangePicker = true" class="btn btn-sm btn-outline-info">
                <i class="bi bi-calendar-range"></i> Custom Range
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Date Range Picker Modal -->
    <div v-if="showDateRangePicker" class="modal-backdrop fade show"></div>
    <div v-if="showDateRangePicker" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Select Date Range</h5>
            <button @click="showDateRangePicker = false" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Start Date</label>
              <input type="date" v-model="customDateRange.start" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">End Date</label>
              <input type="date" v-model="customDateRange.end" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button @click="showDateRangePicker = false" class="btn btn-secondary">Cancel</button>
            <button @click="applyCustomRange" class="btn btn-primary">Apply</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
          <li class="nav-item">
            <button 
              :class="`nav-link ${activeTab === 'production' ? 'active' : ''}`"
              @click="activeTab = 'production'"
            >
              <i class="bi bi-graph-up"></i> Production Report
            </button>
          </li>
          <li class="nav-item">
            <button 
              :class="`nav-link ${activeTab === 'historical' ? 'active' : ''}`"
              @click="activeTab = 'historical'"
            >
              <i class="bi bi-clock-history"></i> Historical Log
            </button>
          </li>
          <li class="nav-item">
            <button 
              :class="`nav-link ${activeTab === 'analytics' ? 'active' : ''}`"
              @click="activeTab = 'analytics'"
            >
              <i class="bi bi-bar-chart"></i> Analytics
            </button>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <ProductionReport v-if="activeTab === 'production'" />
        <HistoricalLog v-if="activeTab === 'historical'" />
        <ReportAnalytics v-if="activeTab === 'analytics'" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductionReport from './ProductionReport.vue'
import HistoricalLog from './HistoricalLog.vue'
import ReportAnalytics from './ReportAnalytics.vue'
import apiClient from '../../api'

const activeTab = ref('production')
const showDateRangePicker = ref(false)

const summary = ref({
  today: { total: 0, ok: 0, ng: 0, quality_rate: 0 },
  yesterday: { total: 0, ok: 0, ng: 0 },
  month: { total: 0, ok: 0, ng: 0 }
})

const customDateRange = ref({
  start: new Date().toISOString().split('T')[0],
  end: new Date().toISOString().split('T')[0]
})

const fetchSummary = async () => {
  try {
    const response = await apiClient.get('', {
      params: { mode: 'get_dashboard_summary' }
    })
    
    if (response.data.status === 'success') {
      summary.value = response.data
    }
  } catch (error) {
    console.error('Error fetching summary:', error)
  }
}

const exportAllData = async () => {
  try {
    // Export all data from different endpoints
    const [logRes, prodRes] = await Promise.all([
      apiClient.get('', { params: { mode: 'get_historical_log', date: new Date().toISOString().split('T')[0] } }),
      apiClient.get('', { params: { 
        mode: 'get_production_report', 
        start_date: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
        end_date: new Date().toISOString().split('T')[0]
      } })
    ])
    
    // Combine data and export
    const allData = {
      logs: logRes.data.logs || [],
      production: prodRes.data.reports || []
    }
    
    // Export as JSON file
    const dataStr = JSON.stringify(allData, null, 2)
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr)
    const exportFileDefaultName = `xray_data_export_${new Date().toISOString().split('T')[0]}.json`
    
    const linkElement = document.createElement('a')
    linkElement.setAttribute('href', dataUri)
    linkElement.setAttribute('download', exportFileDefaultName)
    linkElement.click()
    
  } catch (error) {
    console.error('Error exporting data:', error)
    alert('Failed to export data')
  }
}

const generateDailyReport = () => {
  alert('Daily report generation feature will be implemented')
}

const applyCustomRange = () => {
  showDateRangePicker.value = false
  // Trigger data refresh with custom range
  // This would be handled by child components through props/events
}

onMounted(() => {
  fetchSummary()
})
</script>

<style scoped>
.summary-today, .summary-month, .summary-yesterday, .quick-actions {
  transition: transform 0.2s, box-shadow 0.2s;
}

.summary-today:hover, .summary-month:hover, .summary-yesterday:hover, .quick-actions:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.summary-today {
  border-left: 4px solid #0d6efd;
}

.summary-month {
  border-left: 4px solid #0dcaf0;
}

.quick-actions {
  border-left: 4px solid #198754;
}

.modal-backdrop {
  z-index: 1040;
}

.modal {
  z-index: 1050;
}

.nav-tabs .nav-link {
  font-weight: 500;
}

.nav-tabs .nav-link.active {
  border-bottom: 3px solid #0d6efd;
  font-weight: 600;
}
</style>