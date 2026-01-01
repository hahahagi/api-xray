<!-- components/Dashboard/ProductionRateChart.vue -->
<template>
  <div class="production-rate-chart">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const chartCanvas = ref(null)
let chart = null

onMounted(() => {
  if (chartCanvas.value) {
    initChart()
  }
})

onUnmounted(() => {
  if (chart) {
    chart.destroy()
  }
})

function initChart() {
  const ctx = chartCanvas.value.getContext('2d')
  
  // Sample production data for last 8 hours
  const hours = ['8h', '7h', '6h', '5h', '4h', '3h', '2h', '1h', 'Now']
  const productionData = [45, 52, 48, 55, 58, 62, 65, 70, 68]
  
  // Create gradient
  const gradient = ctx.createLinearGradient(0, 0, 0, 300)
  gradient.addColorStop(0, 'rgba(33, 150, 243, 0.3)')
  gradient.addColorStop(1, 'rgba(33, 150, 243, 0.05)')
  
  chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: hours,
      datasets: [{
        label: 'Tires/Hour',
        data: productionData,
        borderColor: '#2196f3',
        backgroundColor: gradient,
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#2196f3',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(30, 41, 59, 0.95)',
          titleColor: '#f1f5f9',
          bodyColor: '#f1f5f9',
          borderColor: '#475569',
          borderWidth: 1,
          callbacks: {
            label: (context) => `Production: ${context.raw} tires/hour`
          }
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          min: 40,
          max: 80,
          grid: {
            color: 'rgba(226, 232, 240, 0.5)'
          },
          ticks: {
            color: '#64748b',
            font: {
              family: "'Roboto Mono', monospace"
            }
          },
          title: {
            display: true,
            text: 'Tires/Hour',
            color: '#475569',
            font: {
              weight: '600'
            }
          }
        },
        x: {
          grid: {
            color: 'rgba(226, 232, 240, 0.5)'
          },
          ticks: {
            color: '#64748b',
            font: {
              weight: '600'
            }
          },
          title: {
            display: true,
            text: 'Last 8 Hours',
            color: '#475569',
            font: {
              weight: '600'
            }
          }
        }
      }
    }
  })
}
</script>

<style scoped>
.production-rate-chart {
  position: relative;
  height: 200px;
  width: 100%;
}
</style>