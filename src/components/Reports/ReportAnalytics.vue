<template>
  <div class="report-analytics">
    <div class="row g-4">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary">
              <i class="bi bi-graph-up-arrow me-2"></i>Production Trend
            </h6>
          </div>
          <div class="card-body">
            <div style="position: relative; height: 300px; width: 100%">
              <canvas ref="trendChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary">
              <i class="bi bi-pie-chart me-2"></i>Quality Distribution
            </h6>
          </div>
          <div class="card-body">
            <div style="position: relative; height: 300px; width: 100%">
              <canvas ref="qualityChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary">
              <i class="bi bi-speedometer2 me-2"></i>Performance Metrics
            </h6>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-md-3">
                <div
                  class="metric-card text-center p-4 bg-light rounded border h-100"
                >
                  <div class="metric-value h2 text-primary fw-bold mb-1">
                    {{ avgQuality }}%
                  </div>
                  <div
                    class="metric-label text-muted small text-uppercase fw-bold"
                  >
                    Avg Quality Rate
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div
                  class="metric-card text-center p-4 bg-light rounded border h-100"
                >
                  <div class="metric-value h2 text-success fw-bold mb-1">
                    {{ avgProduction }}
                  </div>
                  <div
                    class="metric-label text-muted small text-uppercase fw-bold"
                  >
                    Avg Daily Production
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div
                  class="metric-card text-center p-4 bg-light rounded border h-100"
                >
                  <div class="metric-value h2 text-info fw-bold mb-1">
                    {{ bestDay }}
                  </div>
                  <div
                    class="metric-label text-muted small text-uppercase fw-bold"
                  >
                    Best Day (Units)
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-md-3">
                <div
                  class="metric-card text-center p-4 bg-light rounded border h-100"
                >
                  <div class="metric-value h2 text-warning fw-bold mb-1">
                    {{ oeeScore }}%
                  </div>
                  <div
                    class="metric-label text-muted small text-uppercase fw-bold"
                  >
                    Estimated OEE
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const trendChart = ref(null);
const qualityChart = ref(null);

const avgQuality = ref(95.2);
const avgProduction = ref(1250);
const bestDay = ref(1820);
const oeeScore = ref(88.5);

let trendChartInstance = null;
let qualityChartInstance = null;

onMounted(() => {
  initCharts();
});

const initCharts = () => {
  // Trend Chart
  if (trendChart.value) {
    const ctx = trendChart.value.getContext("2d");

    trendChartInstance = new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [
          {
            label: "Production",
            data: [1200, 1450, 1350, 1650, 1820, 950, 1100],
            borderColor: "#3b82f6",
            backgroundColor: "rgba(59, 130, 246, 0.1)",
            borderWidth: 2,
            fill: true,
            tension: 0.4,
          },
          {
            label: "Target",
            data: [1400, 1400, 1400, 1400, 1400, 1400, 1400],
            borderColor: "#ef4444",
            borderWidth: 1,
            borderDash: [5, 5],
            fill: false,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "top",
          },
        },
      },
    });
  }

  // Quality Chart
  if (qualityChart.value) {
    const ctx = qualityChart.value.getContext("2d");

    qualityChartInstance = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: ["OK (95.2%)", "NG (4.8%)"],
        datasets: [
          {
            data: [95.2, 4.8],
            backgroundColor: ["#10b981", "#ef4444"],
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
        },
      },
    });
  }
};
</script>

<style scoped>
.metric-card {
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
  transition: transform 0.2s;
}

.metric-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.metric-label {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

canvas {
  width: 100% !important;
  height: 250px !important;
}
</style>
