<!-- components/Dashboard/LineChart.vue -->
<template>
  <div class="line-chart-container">
    <canvas ref="lineChartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const lineChartCanvas = ref(null);
let lineChart = null;

onMounted(() => {
  if (lineChartCanvas.value) {
    initLineChart();
  }
});

onUnmounted(() => {
  if (lineChart) {
    lineChart.destroy();
  }
});

function initLineChart() {
  const ctx = lineChartCanvas.value.getContext('2d');
  
  // Generate sample data for demo
  const hours = Array.from({length: 6}, (_, i) => `${i + 1} hour ago`);
  hours.reverse();
  
  const oeeData = hours.map(() => Math.floor(Math.random() * 20) + 70);
  
  lineChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: hours,
      datasets: [{
        label: 'OEE %',
        data: oeeData,
        borderColor: '#41b883',
        backgroundColor: 'rgba(65, 184, 131, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: false,
          min: 60,
          max: 100,
          title: {
            display: true,
            text: 'OEE %'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Time'
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
}
</script>

<style scoped>
.line-chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}
</style>