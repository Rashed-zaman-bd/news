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
      },
      {
        path: "login",
        name: "login",
        component: () => import("@/views/LoginView.vue")
      },
      {
        path: "register",
        name: "register",
        component: () => import("@/views/RegisterView.vue")
      },
      {
        path: "profile",
        name: "profile",
        component: () => import("@/components/ProfileUpdate.vue")
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router
