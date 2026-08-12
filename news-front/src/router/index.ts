import { createRouter, createWebHistory } from 'vue-router'
import DefaultLatout from '@/layouts/DefaultLatout.vue'

const routes = [
  {
    path: "/",
    component: DefaultLatout,
    children: [
      {
        path: "",
        name: "home",
        component: () => import("@/views/HomeView.vue")
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router
