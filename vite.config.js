import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/assets/css/nucleo-icons.css",
                "resources/assets/css/nucleo-svg.css",
                "resources/assets/css/perfect-scrollbar.css",
                "resources/assets/css/soft-ui-dashboard-tailwind.css",
                "resources/assets/css/soft-ui-dashboard-tailwind.min.css",
                "resources/assets/css/tooltips.css",
                "resources/assets/js/chart-1.js",
                "resources/assets/js/chart-2.js",
                "resources/assets/js/dropdown.js",
                "resources/assets/js/fixed-plugin.js",
                "resources/assets/js/navbar-collapse.js",
                "resources/assets/js/navbar-sticky.js",
                "resources/assets/js/nav-pills.js",
                "resources/assets/js/perfect-scrollbar.js",
                "resources/assets/js/sidenav-burger.js",
                "resources/assets/js/tooltips.js",
                "resources/assets/js/soft-ui-dashboard-tailwind.js",
                "resources/assets/js/soft-ui-dashboard-tailwind.min.js",
                "resources/assets/js/plugins/Chart.extension.js",
                "resources/assets/js/plugins/chartjs.min.js",
                "resources/assets/js/plugins/perfect-scrollbar.min.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
