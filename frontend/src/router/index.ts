import { createRouter, createWebHistory } from 'vue-router'
import AuthForm from '@/components/auth/AuthForm.vue'
import { AuthFormType } from '@/types/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: AuthForm,
      props: { formType: AuthFormType.LOGIN }
    },
    {
      path: '/register',
      name: 'register', 
      component: AuthForm,
      props: { formType: AuthFormType.REGISTER }
    }
  ]
})

export default router
