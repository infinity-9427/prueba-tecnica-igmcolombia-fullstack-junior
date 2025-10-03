import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface User {
  id: number
  name: string
  email: string
  roles?: Array<{
    name: string
  }>
}

export interface AuthResponse {
  message: string
  user: User
  token: string
  token_type: string
}

export interface LoginCredentials {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export const useUserStore = defineStore('user', () => {
  // Essential state only
  const currentUser = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const authLoading = ref(false)
  const authError = ref<string | null>(null)

  // Essential getters
  const isAuthenticated = computed(() => !!token.value && !!currentUser.value)
  const userId = computed(() => currentUser.value?.id || null)
  const userRole = computed(() => currentUser.value?.roles?.[0]?.name || 'user')
  const canManageAll = computed(() => ['admin', 'manager'].includes(userRole.value))

  // Essential actions
  const setAuth = (user: User, authToken: string) => {
    currentUser.value = user
    token.value = authToken
    localStorage.setItem('auth_token', authToken)
    authError.value = null
  }

  const clearAuth = () => {
    currentUser.value = null
    token.value = null
    localStorage.removeItem('auth_token')
    authError.value = null
  }

  const setAuthLoading = (loading: boolean) => {
    authLoading.value = loading
  }

  const setAuthError = (error: string | null) => {
    authError.value = error
  }

  return {
    // State (minimal)
    currentUser: computed(() => currentUser.value),
    isAuthenticated,
    userId,
    userRole,
    canManageAll,
    authLoading: computed(() => authLoading.value),
    authError: computed(() => authError.value),
    
    // Actions (essential only)
    setAuth,
    clearAuth,
    setAuthLoading,
    setAuthError
  }
})