import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";

// Import Bootstrap and related styles
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle";

// Import Normalize.css for consistent styling across browsers
import "normalize.css";

// Import FontAwesome
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { far } from "@fortawesome/free-regular-svg-icons";
import { fas } from "@fortawesome/free-solid-svg-icons";

// Add icons to the library
library.add(fas, far);

// Create and mount the Vue app
createApp(App)
  .component("FIcons", FontAwesomeIcon)
  .use(store)
  .use(router)
  .mount("#app");
