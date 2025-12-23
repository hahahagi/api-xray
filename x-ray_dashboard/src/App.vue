<script setup>
import Sidebar from "./components/Layout/Sidebar.vue";
import Navbar from "./components/Layout/Navbar.vue";
</script>

<template>
  <div class="d-flex">
    <Sidebar />
    <div
      class="flex-grow-1 d-flex flex-column"
      style="height: 100vh; overflow-y: auto"
    >
      <Navbar />
      <div class="p-3">
        <router-view />
      </div>
    </div>
  </div>
</template>

<style>
/* Global styles can go here */
</style>
