import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '@/layouts/DefaultLatout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import api from '@/services/api'
import OpinionsView from '@/views/admin/OpinionsView.vue'




const ALL_ADMIN_ROLES = ['admin', 'editor', 'author']

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      { path: '', name: 'home', component: () => import('@/views/HomeView.vue') },

     {
        path: "/category/:slug",
        name: "category-detail",
        component: () => import("@/views/category/[slug].vue"),
        meta: { requiresAuth: false },
      },

      {
        path: "/category/latest",
        name: "category-latest",
        component: () => import("@/views/category/latest.vue"),
        meta: { requiresAuth: false },
      },

      {
          path: '/article/:slug',
          name: 'article.show',
          component: () => import('@/views/article/[slug].vue'),
      },

      {
        path: '/video/:slug',
        name: 'video.show', 
        component: () => import('@/views/video/[slug].vue')
      },

      {
        path: '/video/item',
        name: 'video.item', 
        component: () => import('@/views/video/Item.vue')
      },

      {
        path: '/opinion',
        name: 'opinion', 
        component: () => import('@/components/Opinion.vue')
      },

      
    ],
  },
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
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/components/ProfileUpdate.vue'),
    meta: { requiresAuth: true },
  },
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
    // Base gate: any admin-panel role can enter the layout/dashboard
    meta: { requiresAuth: true, requiresAdmin: true, roles: ALL_ADMIN_ROLES },
    children: [
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/DashboardView.vue'),
        meta: { roles: ALL_ADMIN_ROLES },
      },
      {
        path: 'article',
        name: 'admin.article',
        component: () => import('@/views/admin/ArticleView.vue'),
        meta: { roles: ['admin', 'editor'] }, // matches your backend articles routes
      },
      {
        path: 'article/create',
        name: 'admin.article.create',
        component: () => import('@/views/admin/ArticleCreateView.vue'),
        meta: { roles: ALL_ADMIN_ROLES },
      },
      {
        path: 'article/pending',
        name: 'admin.article.pending',
        component: () => import('@/views/admin/ArticalePendingView.vue'),
        meta: { roles: ALL_ADMIN_ROLES },
      },
      {
        path: 'category',
        name: 'admin.category',
        component: () => import('@/views/admin/CategoryView.vue'),
        meta: { roles: ['admin', 'editor'] }, // matches your backend admin-only categories routes
      },
      {
        path: 'advertisement',
        name: 'admin.advertisement',
        component: () => import('@/views/admin/ads/Advertisement.vue')
        
      },
      {
        path: 'user',
        name: 'admin.user',
        component: () => import('@/views/admin/UserView.vue'),
        meta: { roles: ['admin'] }, // your backend uses role:admin,editor,author for users though — see note below
      },
      {
        path: 'logo',
        name: 'admin.logo',
        component: () => import('@/views/admin/LogoViews.vue'),
        meta: { roles: ['admin'] }, 
      },
      {
        path: 'video',
        name: 'admin.video',
        component: () => import('@/views/admin/VideoView.vue'),
        meta: {roles: ['admin', 'editor']},
      },
      {
          path: 'categorypageads',
          name: 'admin.ads.categorypageads',
          component: () => import('@/views/admin/ads/CategoryPageAds.vue'),
          meta: {
              roles: ['admin', 'editor']
          },
      },
      {
          path: 'frontpageads',
          name: 'admin.ads.frontpageads',
          component: () => import('@/views/admin/ads/FrontPageAds.vue'),
          meta: {
              roles: ['admin', 'editor']
          },
      },
      {
          path: 'opinions',
          name: 'admin.opinions',
          component: () => import('@/views/admin/OpinionsView.vue'),
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

let adminVerified = false
let cachedRole: string | null = null

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
    // Merge roles meta from all matched route segments (deepest wins if defined)
    const allowedRoles = to.matched
      .slice()
      .reverse()
      .find((r) => r.meta.roles)?.meta.roles as string[] | undefined ?? ALL_ADMIN_ROLES

    // Fast path: already verified this session, trust cached role
    if (adminVerified && cachedRole) {
      if (allowedRoles.includes(cachedRole)) return next()
      return next({ name: 'home' })
    }

    try {
      const response = await api.get('/me')
      const user = response.data.user

      localStorage.setItem('user', JSON.stringify(user))
      cachedRole = user.role
      adminVerified = true

      if (!allowedRoles.includes(user.role)) {
        return next({ name: 'home' })
      }
    } catch (error: any) {
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