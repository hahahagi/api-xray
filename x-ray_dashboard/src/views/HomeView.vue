<template>
  <div class="container-fluid p-4">
    <div class="row">
      <div class="col-md-12 mb-4">
        <h1>Dashboard</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <MachineControl />
      </div>
      <div class="col-md-8">
        <!-- Danar's components will go here -->
        <div class="card">
          <div class="card-body">
            Placeholder for Visuals & Realtime (Danar)
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import MachineControl from "../components/Dashboard/MachineControl.vue";
</script>
