<!-- src/components/Dashboard/DashboardVisuals.vue -->
<template>
  <div class="dashboard-minimal">
    <!-- Section 1: OEE Overview -->
    <div class="section-oee mb-4">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">Overall Equipment Effectiveness</h6>
        </div>
        <div class="card-body">
          <!-- OEE Score -->
          <div class="oee-score-section text-center mb-4">
            <div
              style="
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 1.25rem;
                flex-wrap: wrap;
              "
            >
              <OEEGauge :value="machineStore.oeeData.oee" :size="180" />
              <div>
                <div class="oee-value">{{ machineStore.oeeData.oee }}%</div>
                <div class="oee-label">Overall OEE Score</div>
                <div class="mt-2"><StatusIndicator /></div>
              </div>
            </div>
          </div>

          <!-- OEE Components -->
          <div class="oee-components">
            <div class="oee-component">
              <div class="component-header">
                <span class="component-name">Availability</span>
                <span class="component-value"
                  >{{ machineStore.oeeData.availability }}%</span
                >
              </div>
              <div class="progress">
                <div
                  class="progress-bar"
                  :style="{ width: machineStore.oeeData.availability + '%' }"
                ></div>
              </div>
              <div class="component-formula">
                ({{ machineStore.dashboardData.jam_operasi }}h /
                {{ machineStore.dashboardData.jam_terencana }}h) × 100
              </div>
            </div>

            <div class="oee-component">
              <div class="component-header">
                <span class="component-name">Performance</span>
                <span class="component-value"
                  >{{ machineStore.oeeData.performance }}%</span
                >
              </div>
              <div class="progress">
                <div
                  class="progress-bar bg-info"
                  :style="{ width: machineStore.oeeData.performance + '%' }"
                ></div>
              </div>
              <div class="component-formula">
                ({{ machineStore.dashboardData.aktual_produksi }} /
                {{ machineStore.dashboardData.target_produksi }}) × 100
              </div>
            </div>

            <div class="oee-component">
              <div class="component-header">
                <span class="component-name">Quality</span>
                <span class="component-value"
                  >{{ machineStore.oeeData.quality }}%</span
                >
              </div>
              <div class="progress">
                <div
                  class="progress-bar bg-success"
                  :style="{ width: machineStore.oeeData.quality + '%' }"
                ></div>
              </div>
              <div class="component-formula">
                ({{ machineStore.dashboardData.ban_ok }} / {{ totalProducts }})
                × 100
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section 2: Charts -->
    <div class="row g-3">
      <!-- Quality Chart -->
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">
            <h6 class="mb-0">Quality Distribution</h6>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <PieChart :chartData="machineStore.pieChartData" />
            </div>
            <div class="quality-breakdown mt-3">
              <div class="breakdown-item">
                <div class="breakdown-label">
                  <span class="color-dot bg-success"></span>
                  OK Tires
                </div>
                <div class="breakdown-value">
                  {{ machineStore.dashboardData.ban_ok }} ({{ okPercentage }}%)
                </div>
              </div>
              <div class="breakdown-item">
                <div class="breakdown-label">
                  <span class="color-dot bg-danger"></span>
                  NG Tires
                </div>
                <div class="breakdown-value">
                  {{ machineStore.dashboardData.ban_ng }} ({{ ngPercentage }}%)
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Production Chart -->
      <div class="col-md-6">
        <div class="card production-card h-100">
          <div class="card-header">
            <h6 class="mb-0">Production Trend</h6>
          </div>
          <div class="card-body">
            <div class="chart-container production-chart">
              <canvas ref="lineChart"></canvas>
            </div>
            <div class="production-summary mt-3">
              <div class="summary-item">
                <div class="summary-label">Current Rate</div>
                <div class="summary-value">65 tires/hour</div>
              </div>
              <div class="summary-item">
                <div class="summary-label">Target</div>
                <div class="summary-value">200 tires/shift</div>
              </div>
              <div class="summary-item">
                <div class="summary-label">Achieved</div>
                <div class="summary-value">
                  {{ machineStore.dashboardData.aktual_produksi }} tires
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Auto-refresh -->
    <div class="refresh-status mt-4">
      <div class="alert alert-light border">
        <div class="d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center">
            <div class="refresh-indicator me-3">
              <div class="refresh-dot"></div>
            </div>
            <div>
              <div class="refresh-text">Auto-refresh enabled</div>
              <small class="text-muted">Updates every 10 seconds</small>
            </div>
          </div>
          <div class="text-end">
            <div class="last-update">
              Last: {{ machineStore.dashboardData.lastUpdated || "Never" }}
            </div>
            <small class="text-muted">Connected to database</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useMachineStore } from "../../stores/machineStore";
import PieChart from "./PieChart.vue";
import OEEGauge from "./OEEGauge.vue";
import StatusIndicator from "./StatusIndicator.vue";

const machineStore = useMachineStore();
const lineChart = ref(null);

let chartInstances = [];

const totalProducts = computed(() => {
  return machineStore.dashboardData.ban_ok + machineStore.dashboardData.ban_ng;
});

const okPercentage = computed(() => {
  if (totalProducts.value === 0) return 0;
  return (
    (machineStore.dashboardData.ban_ok / totalProducts.value) *
    100
  ).toFixed(1);
});

const ngPercentage = computed(() => {
  if (totalProducts.value === 0) return 0;
  return (
    (machineStore.dashboardData.ban_ng / totalProducts.value) *
    100
  ).toFixed(1);
});

let pollInterval = null;

onMounted(() => {
  // initial fetch
  machineStore.fetchDashboardData();

  // small delay to initialize charts after data is loaded
  setTimeout(initCharts, 200);

  // Poll every 8 seconds (background fetch)
  pollInterval = setInterval(() => {
    machineStore.fetchDashboardData(true);
    // update line chart if mounted
    if (chartInstances[0]) {
      try {
        chartInstances[0].update();
      } catch (e) {}
    }
  }, 8000);

  // ensure charts resize when window changes
  window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
  chartInstances.forEach((chart) => {
    if (chart) chart.destroy();
  });
  if (pollInterval) clearInterval(pollInterval);
  window.removeEventListener("resize", handleResize);
});

function handleResize() {
  chartInstances.forEach((chart) => {
    try {
      if (chart && typeof chart.resize === "function") chart.resize();
    } catch (e) {
      // ignore
    }
  });
}

function initCharts() {
  // Line Chart
  if (lineChart.value) {
    const ctx = lineChart.value.getContext("2d");

    const lineChartInstance = new window.Chart(ctx, {
      type: "line",
      data: {
        labels: ["8:00", "9:00", "10:00", "11:00", "12:00", "13:00", "14:00"],
        datasets: [
          {
            data: [45, 52, 48, 55, 58, 62, 65],
            borderColor: "#3b82f6",
            backgroundColor: "rgba(59, 130, 246, 0.05)",
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: "#3b82f6",
            pointBorderColor: "#ffffff",
            pointBorderWidth: 2,
            pointRadius: 4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: false,
            min: 40,
            grid: {
              color: "rgba(0,0,0,0.03)",
            },
            ticks: {
              color: "#6b7280",
            },
          },
          x: {
            grid: {
              color: "rgba(0,0,0,0.03)",
            },
            ticks: {
              color: "#6b7280",
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
        },
      },
    });

    chartInstances.push(lineChartInstance);
  }
}
</script>

<style scoped>
.dashboard-minimal {
  height: 100%;
}

/* Card Styles */
.card {
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 0.875rem 1.25rem;
  font-weight: 600;
  color: #374151;
}

.card-body {
  padding: 1.25rem;
}

/* OEE Score */
.oee-score-section {
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-radius: 8px;
}

.oee-value {
  font-size: 3.5rem;
  font-weight: 800;
  color: #3b82f6;
  line-height: 1;
  margin-bottom: 0.5rem;
}

.oee-label {
  color: #6b7280;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* OEE Components */
.oee-components {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.oee-component {
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.component-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.component-name {
  font-weight: 600;
  color: #374151;
}

.component-value {
  font-weight: 700;
  font-family: "Roboto Mono", monospace;
  color: #111827;
}

.progress {
  height: 6px;
  background: #e5e7eb;
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progress-bar {
  background: #3b82f6;
  border-radius: 3px;
  transition: width 0.6s ease;
}

.component-formula {
  font-size: 0.75rem;
  color: #6b7280;
  font-family: "Courier New", monospace;
  background: #f1f5f9;
  padding: 0.375rem 0.5rem;
  border-radius: 4px;
}

/* Charts */
.chart-container {
  position: relative;
  /* Use a consistent, reasonable chart height so the chart doesn't expand unexpectedly on mobile */
  min-height: 160px;
  height: 220px;
  max-height: 320px;
}

/* Ensure canvas fills the container so Chart.js can size responsively */
.chart-container canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}

/* Tighter height for very small screens to avoid long empty areas */
@media (max-width: 480px) {
  .chart-container {
    height: 180px;
    min-height: 140px;
  }
}

/* Production Trend specific - reduce visual footprint and keep summary close */
.production-card .card-body {
  display: flex;
  flex-direction: column;
}
.production-card .production-chart {
  /* smaller, tighter chart for Production Trend */
  height: 140px;
  min-height: 120px;
  max-height: 200px;
}
.production-card .production-summary {
  margin-top: 0.75rem;
}

@media (max-width: 480px) {
  .production-card .production-chart {
    height: 120px;
    min-height: 100px;
  }
}

/* Quality Breakdown */
.quality-breakdown {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.breakdown-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.breakdown-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.color-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

.breakdown-value {
  font-weight: 600;
  font-family: "Roboto Mono", monospace;
  color: #111827;
}

/* Production Summary */
.production-summary {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
}

.summary-item {
  text-align: center;
  padding: 0.75rem;
  background: #f9fafb;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.summary-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.25rem;
}

.summary-value {
  font-weight: 600;
  color: #374151;
  font-family: "Roboto Mono", monospace;
}

/* Refresh Status */
.refresh-status .alert {
  border-radius: 8px;
  background: white;
  border-color: #e5e7eb;
}

.refresh-indicator {
  position: relative;
  width: 40px;
  height: 40px;
}

.refresh-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #10b981;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  animation: pulse 2s infinite;
}

.refresh-dot::after {
  content: "";
  position: absolute;
  top: -4px;
  left: -4px;
  right: -4px;
  bottom: -4px;
  border-radius: 50%;
  background: #10b981;
  opacity: 0.2;
  animation: pulse-ring 2s infinite;
}

.refresh-text {
  font-weight: 600;
  color: #374151;
}

.last-update {
  font-weight: 600;
  color: #374151;
  font-family: "Roboto Mono", monospace;
}

/* Animations */
@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

@keyframes pulse-ring {
  0% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  80%,
  100% {
    transform: scale(1.4);
    opacity: 0;
  }
}

/* Responsive */
@media (max-width: 768px) {
  .oee-value {
    font-size: 2.5rem;
  }

  .oee-components {
    gap: 1rem;
  }

  .production-summary {
    grid-template-columns: 1fr;
  }

  .refresh-status .alert > div {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }

  .refresh-indicator {
    margin: 0 auto;
  }
}
</style>
