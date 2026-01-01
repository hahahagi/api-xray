import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";

// Import Bootstrap CSS & JS
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import "./style.css";

// Kita siapkan router (nanti kita isi)
import router from "./router";

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount("#app");
