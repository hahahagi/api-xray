<!-- components/Dashboard/OEEGauge.vue -->
<template>
  <div class="industrial-gauge">
    <div
      class="gauge-container"
      :style="{ width: size + 'px', height: size + 'px' }"
    >
      <svg :width="size" :height="size" viewBox="0 0 200 200" class="gauge-svg">
        <!-- Background Segments (Solid Colors) -->
        <path
          v-for="segment in segments"
          :key="'bg-' + segment.color"
          :d="segment.path"
          fill="none"
          :stroke="segment.color"
          stroke-width="12"
          stroke-linecap="butt"
        />

        <!-- Inactive Overlay (Dimming) -->
        <path
          v-if="inactiveOverlayPath"
          :d="inactiveOverlayPath"
          fill="none"
          stroke="#ffffff"
          stroke-width="12"
          stroke-linecap="butt"
          stroke-opacity="0.1"
        />

        <!-- Center text -->
        <text x="100" y="100" text-anchor="middle" dy="0" class="gauge-value">
          {{ displayValue }}%
        </text>
        <text x="100" y="125" text-anchor="middle" class="gauge-label">
          OEE
        </text>
      </svg>
    </div>

    <!-- Legend -->
    <div class="gauge-legend">
      <div class="legend-item" v-for="segment in segments" :key="segment.color">
        <div
          class="legend-color"
          :style="{ backgroundColor: segment.color }"
        ></div>
        <span class="legend-label">{{ segment.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  value: {
    type: Number,
    default: 0,
  },
  size: {
    type: Number,
    default: 200,
  },
});

const displayValue = computed(() => {
  return Math.min(100, Math.max(0, props.value)).toFixed(1);
});

const radius = 80;
const startAngle = -Math.PI / 2;
const endAngle = 1.5 * Math.PI;

const segments = computed(() => {
  const segmentRanges = [
    { min: 0, max: 50, color: "var(--status-off)", label: "Poor" },
    { min: 50, max: 75, color: "var(--status-warning)", label: "Fair" },
    { min: 75, max: 90, color: "var(--status-on)", label: "Good" },
    { min: 90, max: 100, color: "#0d6efd", label: "Excellent" },
  ];

  return segmentRanges.map((range) => {
    const start = startAngle + (endAngle - startAngle) * (range.min / 100);
    const end = startAngle + (endAngle - startAngle) * (range.max / 100);
    return {
      ...range,
      path: describeArc(100, 100, radius, start, end),
    };
  });
});

const inactiveOverlayPath = computed(() => {
  const val = Math.min(100, Math.max(0, props.value));
  if (val >= 100) return null;

  const start = startAngle + (endAngle - startAngle) * (val / 100);
  const end = endAngle;

  return describeArc(100, 100, radius, start, end);
});

function polarToCartesian(centerX, centerY, radius, angleInRadians) {
  return {
    x: centerX + radius * Math.cos(angleInRadians),
    y: centerY + radius * Math.sin(angleInRadians),
  };
}

function describeArc(x, y, radius, startAngle, endAngle) {
  const start = polarToCartesian(x, y, radius, endAngle);
  const end = polarToCartesian(x, y, radius, startAngle);
  const largeArcFlag = endAngle - startAngle <= Math.PI ? "0" : "1";

  return [
    "M",
    start.x,
    start.y,
    "A",
    radius,
    radius,
    0,
    largeArcFlag,
    0,
    end.x,
    end.y,
  ].join(" ");
}
</script>

<style scoped>
.industrial-gauge {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.gauge-container {
  position: relative;
}

.gauge-svg {
  filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}

.value-arc {
  filter: drop-shadow(0 0 8px rgba(33, 150, 243, 0.4));
  animation: arc-appear 1s ease-out;
}

.gauge-value {
  font-size: 2rem;
  font-weight: 700;
  fill: #1e293b;
  font-family: "Roboto Mono", monospace;
}

.gauge-label {
  font-size: 0.875rem;
  fill: #64748b;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

.gauge-legend {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-label {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
}

@keyframes arc-appear {
  from {
    stroke-dasharray: 0 1000;
  }
}
</style>
