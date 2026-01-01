import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import ReportView from "../views/ReportView.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
    meta: { title: "Dashboard" }
  },
  {
    path: "/report",
    name: "report",
    component: ReportView,
    meta: { title: "Reports" }
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Set document title based on route
router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title || 'X-Ray Monitor'} | Industrial System`;
  next();
});

export default router;