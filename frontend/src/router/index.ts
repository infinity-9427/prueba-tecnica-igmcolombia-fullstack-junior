import { createRouter, createWebHistory } from 'vue-router'
import AuthLayout from '@/layouts/AuthLayout.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import AuthForm from '@/components/auth/AuthForm.vue'
import { AuthFormType } from '@/types/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth routes with AuthLayout (no navbar)
    {
      path: '/',
      component: AuthLayout,
      children: [
        {
          path: '',
          name: 'login',
          component: AuthForm,
          props: { formType: AuthFormType.LOGIN }
        },
        {
          path: 'register',
          name: 'register',
          component: AuthForm,
          props: { formType: AuthFormType.REGISTER }
        }
      ]
    },
    // App routes with AppLayout (with navbar)
    {
      path: '/app',
      component: AppLayout,
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
        // User routes
        {
          path: 'users',
          children: [
            {
              path: '',
              name: 'user-list',
              component: () => import('@/views/users/UserListView.vue')
            },
            {
              path: 'create',
              name: 'user-create',
              component: () => import('@/views/users/UserCreateView.vue')
            },
            {
              path: ':id',
              name: 'user-detail',
              component: () => import('@/views/users/UserDetailView.vue')
            },
            {
              path: ':id/edit',
              name: 'user-edit',
              component: () => import('@/views/users/UserEditView.vue')
            }
          ]
        }
      ]
    }
  ]
})

export default router
