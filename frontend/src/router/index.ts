import { createRouter, createWebHistory } from 'vue-router'
import AuthLayout from '@/layouts/AuthLayout.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import AuthForm from '@/components/auth/AuthForm.vue'
import { AuthFormType } from '@/types/auth'
import { useUserStore } from '@/stores/userStore'
import '@/types/router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth routes with AuthLayout (no navbar)
    {
      path: '/',
      component: AuthLayout,
      meta: { requiresGuest: true },
      children: [
        {
          path: '',
          name: 'login',
          component: AuthForm,
          props: { formType: AuthFormType.LOGIN },
          meta: { requiresGuest: true }
        },
        {
          path: 'register',
          name: 'register',
          component: AuthForm,
          props: { formType: AuthFormType.REGISTER },
          meta: { requiresGuest: true }
        }
      ]
    },
    // App routes with AppLayout (with navbar)
    {
      path: '/app',
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        // Redirect /app to /app/invoices
        {
          path: '',
          redirect: '/app/invoices'
        },
        // Invoice routes
        {
          path: 'invoices',
          children: [
            {
              path: '',
              name: 'invoice-list',
              component: () => import('@/views/invoices/InvoiceListView.vue')
            },
            {
              path: 'create',
              name: 'invoice-create',
              component: () => import('@/views/invoices/InvoiceCreateView.vue')
            },
            {
              path: ':id',
              name: 'invoice-detail',
              component: () => import('@/views/invoices/InvoiceDetailView.vue')
            },
            {
              path: ':id/edit',
              name: 'invoice-edit',
              component: () => import('@/views/invoices/InvoiceEditView.vue')
            }
          ]
        },
        // Client routes
        {
          path: 'clients',
          children: [
            {
              path: '',
              name: 'client-list',
              component: () => import('@/views/clients/ClientListView.vue')
            },
            {
              path: 'create',
              name: 'client-create',
              component: () => import('@/views/clients/ClientCreateView.vue')
            },
            {
              path: ':id',
              name: 'client-detail',
              component: () => import('@/views/clients/ClientDetailView.vue')
            },
            {
              path: ':id/edit',
              name: 'client-edit',
              component: () => import('@/views/clients/ClientEditView.vue')
            }
          ]
        },
        // User routes (Admin only)
        {
          path: 'users',
          meta: { requiresAdmin: true },
          children: [
            {
              path: '',
              name: 'user-list',
              component: () => import('@/views/users/UserListView.vue'),
              meta: { requiresAdmin: true }
            },
            {
              path: 'create',
              name: 'user-create',
              component: () => import('@/views/users/UserCreateView.vue'),
              meta: { requiresAdmin: true }
            },
            {
              path: ':id',
              name: 'user-detail',
              component: () => import('@/views/users/UserDetailView.vue'),
              meta: { requiresAdmin: true }
            },
            {
              path: ':id/edit',
              name: 'user-edit',
              component: () => import('@/views/users/UserEditView.vue'),
              meta: { requiresAdmin: true }
            }
          ]
        }
      ]
    },
    // Catch-all route for 404 pages
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      redirect: () => {
        // If user is authenticated, redirect to invoices
        // If not, redirect to login
        const userStore = useUserStore()
        return userStore.isAuthenticated ? '/app/invoices' : '/'
      }
    }
  ]
})

/**
 * Global Navigation Guards
 * 
 * This implements Vue Router navigation guards to protect routes based on authentication state:
 * 
 * 1. requiresAuth: Routes that need the user to be logged in (/app/*)
 * 2. requiresGuest: Routes that need the user to be logged out (/, /register)
 * 3. requiresAdmin: Routes that need admin role (/app/users/*)
 * 
 * Flow:
 * - Unauthenticated users trying to access /app/* → Redirected to login (/)
 * - Authenticated users trying to access /, /register → Redirected to dashboard (/app/invoices)
 * - Non-admin users trying to access /app/users/* → Redirected to dashboard (/app/invoices)
 * - Unknown routes → Redirected based on auth state (login or dashboard)
 */
router.beforeEach(async (to, _from, next) => {
  const userStore = useUserStore()
  
  // Check if user is authenticated
  const isAuthenticated = userStore.isAuthenticated
  
  // Routes that require authentication
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  
  // Routes that require guest (not authenticated)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)
  
  // Routes that require admin role
  const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
  
  // If route requires authentication and user is not authenticated
  if (requiresAuth && !isAuthenticated) {
    console.log('🔒 Access denied: Authentication required')
    next({ name: 'login' })
    return
  }
  
  // If route requires guest (login/register) and user is authenticated
  if (requiresGuest && isAuthenticated) {
    console.log('🔓 Redirecting authenticated user to dashboard')
    next({ path: '/app/invoices' })
    return
  }
  
  // If route requires admin role and user is not admin
  if (requiresAdmin && isAuthenticated && !userStore.canManageAll) {
    console.log('🚫 Access denied: Admin role required')
    next({ path: '/app/invoices' })
    return
  }
  
  // Allow navigation
  next()
})

// After each navigation
router.afterEach((to, from) => {
  // Log navigation for debugging
  console.log(`🚀 Navigated from ${from.path} to ${to.path}`)
})

export default router
