<!-- components/Dashboard/PieChart.vue -->
<template>
  <div class="pie-chart-container">
    <canvas ref="pieChartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

const props = defineProps({
  chartData: {
    type: Object,
    default: () => ({ labels: [], datasets: [] }),
  },
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

watch(
  () => props.chartData,
  () => {
    if (pieChart) {
      pieChart.data = props.chartData;
      pieChart.update();
    }
  },
  { deep: true }
);

function initPieChart() {
  const ctx = pieChartCanvas.value.getContext("2d");

  pieChart = new Chart(ctx, {
    type: "pie",
    data: props.chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              const label = context.label || "";
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            },
          },
        },
      },
    },
  });
}
</script>

<style scoped>
.pie-chart-container {
  position: relative;
  width: 100%;
  /* Fill parent's height when embedded; set sensible max to avoid overflow */
  height: 100%;
  max-height: 320px;
}

/* Ensure the canvas fills the container so Chart.js sizing works as expected */
.pie-chart-container canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}
</style>
