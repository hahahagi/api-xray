<!-- components/Dashboard/OEEGauge.vue -->
<template>
  <div class="industrial-gauge">
    <div class="gauge-container" :style="{ width: size + 'px', height: size + 'px' }">
      <svg :width="size" :height="size" viewBox="0 0 200 200" class="gauge-svg">
        <!-- Background circle -->
        <circle 
          cx="100" 
          cy="100" 
          :r="radius" 
          fill="none" 
          stroke="#e2e8f0" 
          stroke-width="12"
        />
        
        <!-- Colored segments -->
        <path 
          v-for="segment in segments"
          :key="segment.color"
          :d="segment.path"
          fill="none"
          :stroke="segment.color"
          stroke-width="12"
          stroke-linecap="round"
        />
        
        <!-- Value arc -->
        <path 
          :d="valueArcPath" 
          fill="none" 
          stroke="#2196f3"
          stroke-width="14"
          stroke-linecap="round"
          class="value-arc"
        />
        
        <!-- Center text -->
        <text x="100" y="100" text-anchor="middle" dy="0" class="gauge-value">
          {{ displayValue }}%
        </text>
        <text x="100" y="125" text-anchor="middle" class="gauge-label">
          OEE
        </text>
        
        <!-- Threshold markers -->
        <circle 
          v-for="marker in markers"
          :key="marker.value"
          :cx="marker.x"
          :cy="marker.y"
          r="2"
          fill="#64748b"
        />
      </svg>
    </div>
    
    <!-- Legend -->
    <div class="gauge-legend">
      <div class="legend-item" v-for="segment in segments" :key="segment.color">
        <div class="legend-color" :style="{ backgroundColor: segment.color }"></div>
        <span class="legend-label">{{ segment.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  value: {
    type: Number,
    default: 0
  },
  size: {
    type: Number,
    default: 200
  }
})

const displayValue = computed(() => {
  return Math.min(100, Math.max(0, props.value)).toFixed(1)
})

const radius = 80
const startAngle = -Math.PI * 0.8
const endAngle = Math.PI * 0.8

const segments = computed(() => {
  const segmentRanges = [
    { min: 0, max: 50, color: '#f44336', label: 'Poor' },
    { min: 50, max: 75, color: '#ff9800', label: 'Fair' },
    { min: 75, max: 90, color: '#4caf50', label: 'Good' },
    { min: 90, max: 100, color: '#00e676', label: 'Excellent' }
  ]
  
  return segmentRanges.map(range => {
    const start = startAngle + (endAngle - startAngle) * (range.min / 100)
    const end = startAngle + (endAngle - startAngle) * (range.max / 100)
    return {
      ...range,
      path: describeArc(100, 100, radius, start, end)
    }
  })
})

const valueArcPath = computed(() => {
  const normalizedValue = Math.min(100, Math.max(0, props.value)) / 100
  const valueEndAngle = startAngle + (endAngle - startAngle) * normalizedValue
  return describeArc(100, 100, radius, startAngle, valueEndAngle)
})

const markers = computed(() => {
  const markerValues = [0, 25, 50, 75, 100]
  return markerValues.map(value => {
    const angle = startAngle + (endAngle - startAngle) * (value / 100)
    return {
      value,
      x: 100 + (radius + 10) * Math.cos(angle),
      y: 100 + (radius + 10) * Math.sin(angle)
    }
  })
})

function polarToCartesian(centerX, centerY, radius, angleInRadians) {
  return {
    x: centerX + (radius * Math.cos(angleInRadians)),
    y: centerY + (radius * Math.sin(angleInRadians))
  }
}

function describeArc(x, y, radius, startAngle, endAngle) {
  const start = polarToCartesian(x, y, radius, endAngle)
  const end = polarToCartesian(x, y, radius, startAngle)
  const largeArcFlag = endAngle - startAngle <= Math.PI ? "0" : "1"
  
  return [
    "M", start.x, start.y,
    "A", radius, radius, 0, largeArcFlag, 0, end.x, end.y
  ].join(" ")
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
  font-family: 'Roboto Mono', monospace;
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