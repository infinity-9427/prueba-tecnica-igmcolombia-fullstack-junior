import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { AuthService } from '@/services/authService'
import type { LoginCredentials, RegisterData } from '@/stores/userStore'

export function useAuth() {
  const userStore = useUserStore()
  const router = useRouter()

  // Essential auth state
  const isAuthenticated = computed(() => userStore.isAuthenticated)
  const userId = computed(() => userStore.userId)
  const userRole = computed(() => userStore.userRole)
  const canManageAll = computed(() => userStore.canManageAll)
  const loading = computed(() => userStore.authLoading)
  const error = computed(() => userStore.authError)

  /**
   * Login user
   */
  const login = async (credentials: LoginCredentials): Promise<boolean> => {
    try {
      userStore.setAuthLoading(true)
      userStore.setAuthError(null)

      const response = await AuthService.login(credentials)
      userStore.setAuth(response.user, response.token)
      
      return true
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 'Login failed'
      userStore.setAuthError(errorMessage)
      return false
    } finally {
      userStore.setAuthLoading(false)
    }
  }

  /**
   * Register user
   */
  const register = async (userData: RegisterData): Promise<boolean> => {
    try {
      userStore.setAuthLoading(true)
      userStore.setAuthError(null)

      const response = await AuthService.register(userData)
      userStore.setAuth(response.user, response.token)
      
      return true
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 'Registration failed'
      userStore.setAuthError(errorMessage)
      return false
    } finally {
      userStore.setAuthLoading(false)
    }
  }

  /**
   * Logout user
   */
  const logout = async (): Promise<void> => {
    try {
      if (isAuthenticated.value) {
        await AuthService.logout()
      }
    } catch (error) {
      console.warn('Logout API failed:', error)
    } finally {
      userStore.clearAuth()
      await router.push('/')
    }
  }

  /**
   * Initialize auth from stored token
   */
  const initializeAuth = async (): Promise<boolean> => {
    const token = localStorage.getItem('auth_token')
    if (!token) return false

    try {
      userStore.setAuthLoading(true)
      const response = await AuthService.me()
      userStore.setAuth(response.user, token)
      return true
    } catch (error) {
      userStore.clearAuth()
      return false
    } finally {
      userStore.setAuthLoading(false)
    }
  }

  return {
    // Essential state only
    isAuthenticated,
    userId,
    userRole,
    canManageAll,
    loading,
    error,
    
    // Essential actions only
    login,
    register,
    logout,
    initializeAuth
  }
}