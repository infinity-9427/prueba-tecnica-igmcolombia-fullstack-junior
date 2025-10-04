import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { useUserStore } from '@/stores/userStore'
import { AuthService } from '@/services/authService'
import type { LoginCredentials, RegisterData } from '@/stores/userStore'

export function useAuth() {
  const userStore = useUserStore()
  const router = useRouter()
  const toast = useToast()

  // Essential auth state
  const isAuthenticated = computed(() => userStore.isAuthenticated)
  const currentUser = computed(() => userStore.currentUser)
  const userId = computed(() => userStore.userId)
  const userRole = computed(() => userStore.userRole)
  const canManageAll = computed(() => userStore.canManageAll)
  const loading = computed(() => userStore.authLoading)
  const error = computed(() => userStore.authError)

  /**
   * Show success toast
   */
  const showSuccessToast = (summary: string, detail?: string) => {
    toast.add({
      severity: 'success',
      summary,
      detail,
      life: 1500,
      group: 'br',
      styleClass: 'custom-success-toast'
    })
  }

  /**
   * Show error toast
   */
  const showErrorToast = (summary: string, detail?: string) => {
    toast.add({
      severity: 'error',
      summary,
      detail,
      life: 1500,
      group: 'br',
      styleClass: 'custom-error-toast'
    })
  }

  /**
   * Show warning toast
   */
  const showWarningToast = (summary: string, detail?: string) => {
    toast.add({
      severity: 'warn',
      summary,
      detail,
      life: 1500,
      group: 'br',
      styleClass: 'custom-warning-toast'
    })
  }

  /**
   * Login user
   */
  const login = async (credentials: LoginCredentials): Promise<boolean> => {
    try {
      userStore.setAuthLoading(true)
      userStore.setAuthError(null)

      const response = await AuthService.login(credentials)
      
      console.log('Login response:', response) // Debug log
      
      userStore.setAuth(response.user, response.token)
      
      showSuccessToast(
        'Welcome back!', 
        `Successfully signed in as ${response.user.name}`
      )
      
      // Redirect to dashboard
      await router.push('/app/invoices')
      
      return true
    } catch (error: any) {
      console.error('Login error:', error) // Debug log
      
      const errorMessage = error.response?.data?.error || error.response?.data?.message || 'Login failed'
      userStore.setAuthError(errorMessage)
      
      showErrorToast('Login Failed', errorMessage)
      
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
      
      console.log('Registration response:', response) // Debug log
      
      userStore.setAuth(response.user, response.token)
      
      showSuccessToast(
        'Account Created!', 
        `Welcome ${response.user.name}! Your account has been created successfully.`
      )
      
      // Redirect to dashboard
      await router.push('/app/invoices')
      
      return true
    } catch (error: any) {
      console.error('Registration error:', error) // Debug log
      
      const errorMessage = error.response?.data?.error || error.response?.data?.message || 'Registration failed'
      userStore.setAuthError(errorMessage)
      
      showErrorToast('Registration Failed', errorMessage)
      
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
      
      showSuccessToast('Goodbye!', 'You have been successfully signed out.')
      
    } catch (error) {
      console.warn('Logout API failed:', error)
      showErrorToast('Logout Warning', 'Signed out locally, but server request failed.')
    } finally {
      userStore.clearAuth()
      await router.push('/')
    }
  }

  /**
   * Initialize auth from stored token
   */
  const initializeAuth = async (): Promise<boolean> => {
    // First check if we have stored auth data
    const storedToken = localStorage.getItem('auth_token')
    const storedUser = localStorage.getItem('auth_user')
    
    if (!storedToken || !storedUser) {
      return false
    }

    try {
      userStore.setAuthLoading(true)
      
      // Try to validate the token with the server
      const response = await AuthService.me()
      userStore.setAuth(response.user, storedToken)
      
      return true
    } catch (error) {
      // Token is invalid, clear stored data
      userStore.clearAuth()
      return false
    } finally {
      userStore.setAuthLoading(false)
    }
  }

  /**
   * Check authentication status and redirect if needed
   */
  const requireAuth = () => {
    if (!isAuthenticated.value) {
      showWarningToast('Authentication Required', 'Please sign in to access this page.')
      router.push('/')
      return false
    }
    return true
  }

  return {
    // Essential state only
    isAuthenticated,
    currentUser,
    userId,
    userRole,
    canManageAll,
    loading,
    error,
    
    // Essential actions only
    login,
    register,
    logout,
    initializeAuth,
    requireAuth,
    
    // Toast helpers
    showSuccessToast,
    showErrorToast,
    showWarningToast
  }
}