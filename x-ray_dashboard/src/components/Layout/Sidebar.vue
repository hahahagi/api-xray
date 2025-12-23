<template>
  <div
    class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark"
    style="width: 280px; height: 100vh"
  >
    <a
      href="/"
      class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none"
    >
      <span class="fs-4">Menu</span>
    </a>
    <hr />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <router-link to="/" class="nav-link text-white" active-class="active">
          Dashboard
        </router-link>
      </li>
      <li>
        <router-link
          to="/report"
          class="nav-link text-white"
          active-class="active"
        >
          Report
        </router-link>
      </li>
    </ul>
    <hr />
  </div>
</template>

<script setup></script>

<style scoped>
.nav-link.active {
  background-color: #0d6efd;
}
</style>
