import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface User {
  id: number
  name: string
  email: string
  roles?: Array<{
    name: string
  }>
  role?: string // Direct role field
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
  role: string
}

// Secure token storage utility
class TokenStorage {
  private static readonly TOKEN_KEY = 'auth_token'
  private static readonly USER_KEY = 'auth_user'

  static setToken(token: string): void {
    try {
      localStorage.setItem(this.TOKEN_KEY, token)
    } catch (error) {
      console.warn('Failed to store auth token:', error)
    }
  }

  static getToken(): string | null {
    try {
      return localStorage.getItem(this.TOKEN_KEY)
    } catch (error) {
      console.warn('Failed to retrieve auth token:', error)
      return null
    }
  }

  static setUser(user: User): void {
    try {
      localStorage.setItem(this.USER_KEY, JSON.stringify(user))
    } catch (error) {
      console.warn('Failed to store user data:', error)
    }
  }

  static getUser(): User | null {
    try {
      const userData = localStorage.getItem(this.USER_KEY)
      return userData ? JSON.parse(userData) : null
    } catch (error) {
      console.warn('Failed to retrieve user data:', error)
      return null
    }
  }

  static clear(): void {
    try {
      localStorage.removeItem(this.TOKEN_KEY)
      localStorage.removeItem(this.USER_KEY)
    } catch (error) {
      console.warn('Failed to clear auth data:', error)
    }
  }
}

export const useUserStore = defineStore('user', () => {
  // Initialize state from storage
  const currentUser = ref<User | null>(TokenStorage.getUser())
  const token = ref<string | null>(TokenStorage.getToken())
  const authLoading = ref(false)
  const authError = ref<string | null>(null)

  // Essential getters
  const isAuthenticated = computed(() => !!token.value && !!currentUser.value)
  const userId = computed(() => currentUser.value?.id || null)
  const userRole = computed(() => {
    if (!currentUser.value) return 'user'
    // Handle both role formats: direct role field or roles array
    return currentUser.value.role || currentUser.value.roles?.[0]?.name || 'user'
  })
  const canManageAll = computed(() => ['admin', 'manager'].includes(userRole.value))

  // Essential actions
  const setAuth = (user: User, authToken: string) => {
    currentUser.value = user
    token.value = authToken
    TokenStorage.setToken(authToken)
    TokenStorage.setUser(user)
    authError.value = null
  }

  const clearAuth = () => {
    currentUser.value = null
    token.value = null
    TokenStorage.clear()
    authError.value = null
  }

  const setAuthLoading = (loading: boolean) => {
    authLoading.value = loading
  }

  const setAuthError = (error: string | null) => {
    authError.value = error
  }

  // Initialize auth on store creation
  const initializeAuth = () => {
    const storedToken = TokenStorage.getToken()
    const storedUser = TokenStorage.getUser()
    
    if (storedToken && storedUser) {
      token.value = storedToken
      currentUser.value = storedUser
    }
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
    setAuthError,
    initializeAuth
  }
})