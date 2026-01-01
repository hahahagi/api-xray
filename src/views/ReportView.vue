<template>
  <div class="report-view">
    <div class="container-fluid p-4">
      <div class="row mb-4">
        <div class="col-12">
          <h1 class="mb-1">Reports & Analytics</h1>
          <p class="text-muted">
            Monitor production performance and historical data
          </p>
        </div>
      </div>

      <ReportDashboard />
    </div>
  </div>
</template>

<script setup>
import ReportDashboard from "../components/Reports/ReportDashboard.vue";
</script>

<style scoped>
.report-view {
  min-height: 100vh;
}
</style>
