import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

// https://vite.dev/config/
export default defineConfig({
  base: "./",
  plugins: [vue()],
  server: {
    proxy: {
      "/api-proxy": {
        target: "http://localhost/x-ray/api.php",
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api-proxy/, ""),
      },
    },
  },
  build: {
    chunkSizeWarningLimit: 1600,
    rollupOptions: {
      output: {
        manualChunks(id) {
          if (id.includes("node_modules")) {
            if (id.includes("jspdf") || id.includes("jspdf-autotable")) {
              return "pdf-libs";
            }
            if (id.includes("xlsx")) {
              return "excel-libs";
            }
            if (id.includes("chart.js")) {
              return "chart-libs";
            }
            return "vendor";
          }
        },
      },
    },
  },
});
