import { defineStore } from "pinia";

export const useUIStore = defineStore("ui", {
  state: () => ({
    sidebarVisible: true,
  }),
  actions: {
    toggleSidebar() {
      this.sidebarVisible = !this.sidebarVisible;
      console.log("[uiStore] toggleSidebar ->", this.sidebarVisible);
    },
    setSidebar(visible) {
      this.sidebarVisible = !!visible;
    },
  },
});
