import {createRouter, createWebHashHistory, RouteRecordRaw} from 'vue-router'
import Home from "@/pages/home/home.vue";

const routeData: RouteRecordRaw[] = [
    { path: '/', name: 'Home', component: Home },
]


const router = createRouter({
    history: createWebHashHistory(),
    routes: routeData
})

export default router;