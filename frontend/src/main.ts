import { createApp,   } from 'vue'
import App from './App.vue'
import router from "@/router";
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from '@/store';
import Vuex from "vuex";

const app = createApp(App);
app.config.globalProperties.$store = store;
app.config.globalProperties.$http = axios;

app.use(router);
app.use(store);
app.use(VueAxios, axios, Vuex);
app.mount('#app');
