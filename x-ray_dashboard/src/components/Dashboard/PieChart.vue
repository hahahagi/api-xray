<!-- components/Dashboard/PieChart.vue -->
<template>
  <div class="pie-chart-container">
    <canvas ref="pieChartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  chartData: {
    type: Object,
    default: () => ({ labels: [], datasets: [] })
  }
});

const pieChartCanvas = ref(null);
let pieChart = null;

onMounted(() => {
  if (pieChartCanvas.value) {
    initPieChart();
  }
});

onUnmounted(() => {
  if (pieChart) {
    pieChart.destroy();
  }
});

watch(() => props.chartData, () => {
  if (pieChart) {
    pieChart.data = props.chartData;
    pieChart.update();
  }
}, { deep: true });

function initPieChart() {
  const ctx = pieChartCanvas.value.getContext('2d');
  
  pieChart = new Chart(ctx, {
    type: 'pie',
    data: props.chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
}
</script>

<style scoped>
.pie-chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}
</style>