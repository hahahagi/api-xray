<template>
  <div class="production-report card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Production Report</h5>
      <div class="d-flex gap-2">
        <div class="input-group input-group-sm" style="width: 250px;">
          <span class="input-group-text"><i class="bi bi-calendar"></i></span>
          <input 
            type="date" 
            v-model="dateRange.start" 
            class="form-control"
            @change="fetchProductionData"
          >
          <span class="input-group-text">to</span>
          <input 
            type="date" 
            v-model="dateRange.end" 
            class="form-control"
            @change="fetchProductionData"
          >
        </div>
        <button @click="exportToExcel" class="btn btn-sm btn-success">
          <i class="bi bi-file-excel"></i> Excel
        </button>
        <button @click="exportToPDF" class="btn btn-sm btn-danger">
          <i class="bi bi-file-pdf"></i> PDF
        </button>
      </div>
    </div>
    <div class="card-body">
      <!-- Summary Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="summary-card bg-primary text-white p-3 rounded">
            <div class="d-flex align-items-center">
              <div class="icon me-3">
                <i class="bi bi-box-seam fs-4"></i>
              </div>
              <div>
                <div class="summary-label">Total Inspected</div>
                <div class="summary-value h3">{{ summary.totalInspected }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-card bg-success text-white p-3 rounded">
            <div class="d-flex align-items-center">
              <div class="icon me-3">
                <i class="bi bi-check-circle fs-4"></i>
              </div>
              <div>
                <div class="summary-label">OK Tires</div>
                <div class="summary-value h3">{{ summary.totalOK }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-card bg-danger text-white p-3 rounded">
            <div class="d-flex align-items-center">
              <div class="icon me-3">
                <i class="bi bi-x-circle fs-4"></i>
              </div>
              <div>
                <div class="summary-label">NG Tires</div>
                <div class="summary-value h3">{{ summary.totalNG }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-card bg-info text-white p-3 rounded">
            <div class="d-flex align-items-center">
              <div class="icon me-3">
                <i class="bi bi-percent fs-4"></i>
              </div>
              <div>
                <div class="summary-label">Quality Rate</div>
                <div class="summary-value h3">{{ summary.qualityRate }}%</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>Date</th>
              <th>Total Inspected</th>
              <th>OK Tires</th>
              <th>NG Tires</th>
              <th>Quality Rate</th>
              <th>Target</th>
              <th>Achievement</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in productionData" :key="report.date">
              <td>{{ formatDate(report.date) }}</td>
              <td class="text-center">
                <span class="badge bg-primary">{{ report.total_ban_check }}</span>
              </td>
              <td class="text-center">
                <span class="badge bg-success">{{ report.jumlah_ok }}</span>
              </td>
              <td class="text-center">
                <span class="badge bg-danger">{{ report.jumlah_ng }}</span>
              </td>
              <td>
                <div class="progress" style="height: 20px;">
                  <div 
                    class="progress-bar" 
                    :class="getQualityClass(report.quality_rate)"
                    :style="{ width: report.quality_rate + '%' }"
                  >
                    {{ report.quality_rate.toFixed(1) }}%
                  </div>
                </div>
              </td>
              <td>{{ report.target || 'N/A' }}</td>
              <td>
                <span :class="`badge ${getAchievementClass(report.achievement)}`">
                  {{ report.achievement }}%
                </span>
              </td>
              <td>
                <i :class="`bi ${getStatusIcon(report.status)} text-${getStatusColor(report.status)}`"></i>
                {{ report.status }}
              </td>
            </tr>
            <tr v-if="productionData.length === 0">
              <td colspan="8" class="text-center text-muted py-4">
                No production data available for selected date range
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../api'
import * as XLSX from 'xlsx'
import jsPDF from 'jspdf'
import 'jspdf-autotable'

// Date range (default: last 7 days)
const dateRange = ref({
  start: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  end: new Date().toISOString().split('T')[0]
})

const productionData = ref([])
const summary = ref({
  totalInspected: 0,
  totalOK: 0,
  totalNG: 0,
  qualityRate: 0
})

const fetchProductionData = async () => {
  try {
    const response = await apiClient.get('', {
      params: {
        mode: 'get_production_report',
        start_date: dateRange.value.start,
        end_date: dateRange.value.end
      }
    })
    
    productionData.value = response.data.reports || []
    calculateSummary()
  } catch (error) {
    console.error('Error fetching production data:', error)
  }
}

const calculateSummary = () => {
  summary.value = {
    totalInspected: productionData.value.reduce((sum, item) => sum + (item.total_ban_check || 0), 0),
    totalOK: productionData.value.reduce((sum, item) => sum + (item.jumlah_ok || 0), 0),
    totalNG: productionData.value.reduce((sum, item) => sum + (item.jumlah_ng || 0), 0),
    qualityRate: 0
  }
  
  const totalTires = summary.value.totalOK + summary.value.totalNG
  if (totalTires > 0) {
    summary.value.qualityRate = ((summary.value.totalOK / totalTires) * 100).toFixed(1)
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getQualityClass = (rate) => {
  if (rate >= 95) return 'bg-success'
  if (rate >= 90) return 'bg-info'
  if (rate >= 85) return 'bg-warning'
  return 'bg-danger'
}

const getAchievementClass = (rate) => {
  if (rate >= 100) return 'bg-success'
  if (rate >= 90) return 'bg-info'
  if (rate >= 80) return 'bg-warning'
  return 'bg-danger'
}

const getStatusIcon = (status) => {
  return status === 'Completed' ? 'bi-check-circle' : 'bi-clock'
}

const getStatusColor = (status) => {
  return status === 'Completed' ? 'success' : 'warning'
}

const exportToExcel = () => {
  const ws = XLSX.utils.json_to_sheet(productionData.value.map(item => ({
    Date: formatDate(item.date),
    'Total Inspected': item.total_ban_check,
    'OK Tires': item.jumlah_ok,
    'NG Tires': item.jumlah_ng,
    'Quality Rate': `${item.quality_rate}%`,
    Target: item.target,
    Achievement: `${item.achievement}%`,
    Status: item.status
  })))
  
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Production Report')
  XLSX.writeFile(wb, `production_report_${dateRange.value.start}_to_${dateRange.value.end}.xlsx`)
}

const exportToPDF = () => {
  const doc = new jsPDF()
  
  // Title
  doc.setFontSize(16)
  doc.text('Production Report', 105, 15, { align: 'center' })
  
  // Date range
  doc.setFontSize(10)
  doc.text(`Date Range: ${dateRange.value.start} to ${dateRange.value.end}`, 105, 25, { align: 'center' })
  
  // Table data
  const tableData = productionData.value.map(item => [
    formatDate(item.date),
    item.total_ban_check,
    item.jumlah_ok,
    item.jumlah_ng,
    `${item.quality_rate}%`,
    item.target,
    `${item.achievement}%`,
    item.status
  ])
  
  doc.autoTable({
    startY: 30,
    head: [['Date', 'Total', 'OK', 'NG', 'Quality', 'Target', 'Achievement', 'Status']],
    body: tableData,
    theme: 'grid',
    headStyles: { fillColor: [41, 128, 185] }
  })
  
  doc.save(`production_report_${dateRange.value.start}_to_${dateRange.value.end}.pdf`)
}

onMounted(() => {
  fetchProductionData()
})
</script>

<style scoped>
.summary-card {
  transition: transform 0.2s;
}

.summary-card:hover {
  transform: translateY(-5px);
}

.summary-label {
  font-size: 0.85rem;
  opacity: 0.9;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.summary-value {
  font-weight: 700;
  margin: 0.5rem 0;
}

.table th {
  font-weight: 600;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.progress {
  background-color: #e9ecef;
}

.progress-bar {
  font-size: 0.8rem;
  font-weight: 600;
}
</style>