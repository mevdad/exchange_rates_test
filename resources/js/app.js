import './bootstrap';
import { createApp } from 'vue';
import VueApexCharts from "vue3-apexcharts";
import App from './components/App.vue';

createApp(App).use(VueApexCharts).mount('#app');