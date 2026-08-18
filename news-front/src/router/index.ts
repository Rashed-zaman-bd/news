import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLatout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import api from '@/services/api'

const routes = [
  // =========================
  // DEFAULT LAYOUT
  // =========================
  {
    path: '/',
    component: DefaultLayout,
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/views/HomeView.vue'),
      },
    ],
  },

  // =========================
  // AUTH PAGES
  // =========================
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { requiresAuth: false },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
    meta: { requiresAuth: false },
  },


  // =========================
  // PROFILE
  // =========================
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/components/ProfileUpdate.vue'),
    meta: { requiresAuth: true },
  },

  // =========================
  // SOCIAL AUTH CALLBACK
  // =========================
  {
    path: '/auth/callback',
    name: 'auth.callback',
    component: () => import('@/views/auth/AuthCallbackView.vue'),
    meta: { requiresAuth: false },
  },

  // =========================
  // ADMIN
  // =========================
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true },
    children: [
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/DashboardView.vue'),
      },

      {
        path: 'category',
        name: 'admin.category',
        component: () => import('@/views/admin/CategoryView.vue'),
      },

      {
        path: 'article',
        name: 'admin.article',
        component: () => import('@/views/admin/ArticleView.vue')
      },

      {
        path: 'user',
        name: 'admin.user',
        component: () => import('@/views/admin/UserView.vue'),
      },

      {
        path: 'logo',
        name: 'admin.logo',
        component: () => import('@/views/admin/LogoViews.vue'),
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Only verify with the backend once per browser session unless the cache is missing/stale.
// Keeps admin nav snappy while still confirming role server-side on first entry.
let adminVerified = false

router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some((route) => route.meta.requiresAuth)
  const requiresAdmin = to.matched.some((route) => route.meta.requiresAdmin)

  if (!requiresAuth && !requiresAdmin) {
    return next()
  }

  const token = localStorage.getItem('apiToken')

  if (!token) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  if (requiresAdmin) {
    // Fast path: already verified this session, trust cached role
    if (adminVerified) {
      const cached = localStorage.getItem('user')
      const user = cached ? JSON.parse(cached) : null
      if (user?.role === 'admin') return next()
    }

    try {
      const response = await api.get('/me')
      const user = response.data.user

      localStorage.setItem('user', JSON.stringify(user))

      if (user.role !== 'admin') {
        return next({ name: 'home' })
      }

      adminVerified = true
    } catch (error: any) {
      // Only nuke the session on an actual auth failure, not a network hiccup
      if (error?.response?.status === 401) {
        localStorage.removeItem('apiToken')
        localStorage.removeItem('user')
        return next({ name: 'login', query: { redirect: to.fullPath } })
      }

      console.error('Failed to verify admin access:', error)
      return next({ name: 'home' })
    }
  }

  return next()
})

export default router