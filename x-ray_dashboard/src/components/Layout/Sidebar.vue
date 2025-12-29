<template>
  <aside class="sidebar-industrial">
    <div class="sidebar-header">
      <div class="company-logo">
        <div class="logo-icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
            <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="var(--industrial-accent)"/>
            <path d="M2 17L12 22L22 17" stroke="var(--industrial-accent)" stroke-width="2"/>
            <path d="M2 12L12 17L22 12" stroke="var(--industrial-accent)" stroke-width="2"/>
          </svg>
        </div>
        <div class="company-info">
          <div class="company-name">INDUSTRIAL</div>
          <div class="company-sector">MANUFACTURING</div>
        </div>
      </div>
    </div>

    <div class="sidebar-divider"></div>

    <nav class="sidebar-nav">
      <router-link to="/" class="nav-item" active-class="active">
        <div class="nav-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="currentColor" stroke-width="2"/>
            <polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <span class="nav-text">DASHBOARD</span>
        <div class="nav-indicator"></div>
      </router-link>

      <router-link to="/report" class="nav-item" active-class="active">
        <div class="nav-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
            <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2"/>
            <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
            <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
            <polyline points="10 9 9 9 8 9" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <span class="nav-text">REPORTS</span>
        <div class="nav-indicator"></div>
      </router-link>

      <router-link to="/analytics" class="nav-item" active-class="active">
        <div class="nav-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="2"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96" stroke="currentColor" stroke-width="2"/>
            <line x1="12" y1="22.08" x2="12" y2="12" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <span class="nav-text">ANALYTICS</span>
        <div class="nav-indicator"></div>
      </router-link>
    </nav>

    <div class="sidebar-divider"></div>

    <!-- <div class="sidebar-footer">
      <div class="production-stats">
        <div class="stat-item">
          <div class="stat-label">SHIFT</div>
          <div class="stat-value">A</div>
        </div>
        <div class="stat-item">
          <div class="stat-label">LINE</div>
          <div class="stat-value">01</div>
        </div>
        <div class="stat-item">
          <div class="stat-label">PLANT</div>
          <div class="stat-value">4</div>
        </div>
      </div>
      <div class="connection-status">
        <div class="connection-dot" :class="connectionClass"></div>
        <span class="connection-text">LIVE CONNECTION</span>
      </div>
    </div> -->
  </aside>
</template>

<script setup>
import { computed } from 'vue'

const connectionClass = computed(() => {
  return 'connected' // You can connect this to real connection status
})
</script>

<style scoped>
.sidebar-industrial {
  width: 260px;
  background: linear-gradient(180deg, var(--industrial-dark) 0%, #1a202c 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  height: 100vh;
  position: sticky;
  top: 0;
}

.sidebar-header {
  padding: 2rem 1.5rem;
}

.company-logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo-icon {
  padding: 0.5rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: var(--radius-sm);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.company-info {
  display: flex;
  flex-direction: column;
}

.company-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  letter-spacing: 1px;
}

.company-sector {
  font-size: 0.75rem;
  color: var(--industrial-accent);
  font-weight: 600;
  letter-spacing: 0.5px;
}

.sidebar-divider {
  height: 1px;
  background: linear-gradient(90deg, 
    transparent 0%, 
    rgba(255, 255, 255, 0.1) 50%, 
    transparent 100%);
  margin: 0 1.5rem;
}

.sidebar-nav {
  flex: 1;
  padding: 2rem 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 1.5rem;
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: all var(--transition-normal);
  position: relative;
}

.nav-item:hover {
  color: white;
  background: rgba(255, 255, 255, 0.05);
}

.nav-item.active {
  color: white;
  background: rgba(33, 150, 243, 0.1);
  border-left: 3px solid var(--industrial-accent);
}

.nav-item.active .nav-indicator {
  opacity: 1;
  transform: scale(1);
}

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-text {
  font-size: 0.875rem;
  font-weight: 500;
  letter-spacing: 0.5px;
  flex: 1;
}

.nav-indicator {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--industrial-accent);
  opacity: 0;
  transform: scale(0);
  transition: all var(--transition-normal);
}

.sidebar-footer {
  padding: 1.5rem;
}

.production-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.5rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: var(--radius-sm);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-label {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.6);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: white;
  font-family: 'Roboto Mono', monospace;
}

.connection-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background: rgba(76, 175, 80, 0.1);
  border-radius: var(--radius-sm);
  border: 1px solid rgba(76, 175, 80, 0.2);
}

.connection-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--industrial-success);
  animation: pulse 2s infinite;
}

.connection-text {
  font-size: 0.75rem;
  color: var(--industrial-success);
  font-weight: 600;
  letter-spacing: 0.5px;
}
</style>