import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLatout.vue' 

const routes = [
  // Routes WITH DefaultLayout (Navbar/Footer will show here)
  {
    path: "/",
    component: DefaultLayout,
    children: [
      {
        path: "",
        name: "home",
        component: () => import("@/views/HomeView.vue")
      }
    ]
  },

  // Routes WITHOUT DefaultLayout (Standalone pages)
  {
    path: "/login",
    name: "login",
    component: () => import("@/views/LoginView.vue")
  },
  {
    path: "/register",
    name: "register",
    component: () => import("@/views/RegisterView.vue")
  },
  {
    path: "/profile",
    name: "profile",
    component: () => import("@/components/ProfileUpdate.vue")
  },
  {
    path: "/forgot-password",
    name: "forgot-password",
    component: () => import("@/views/ForgotPassword.vue")
  },
  {
    path: "/reset-password",
    name: "reset-password",
    component: () => import("@/views/ResetPassword.vue")
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router