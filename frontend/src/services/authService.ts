import api from './api'
import type { 
  AuthResponse, 
  User, 
  LoginCredentials, 
  RegisterData 
} from '@/stores/userStore'

// Backend API response format
interface BackendAuthResponse {
  success: boolean
  message: string
  data: {
    user: User
    token: string
    token_type: string
    expires_in?: string | null
  }
}

export class AuthService {
  /**
   * Login user with credentials
   */
  static async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post<BackendAuthResponse>('/auth/login', credentials)
    
    // Transform backend response to frontend format
    return {
      message: response.data.message,
      user: response.data.data.user,
      token: response.data.data.token,
      token_type: response.data.data.token_type
    }
  }

  /**
   * Register new user
   */
  static async register(userData: RegisterData): Promise<AuthResponse> {
    const response = await api.post<BackendAuthResponse>('/auth/register', userData)
    
    // Transform backend response to frontend format
    return {
      message: response.data.message,
      user: response.data.data.user,
      token: response.data.data.token,
      token_type: response.data.data.token_type
    }
  }

  /**
   * Logout current user
   */
  static async logout(): Promise<{ message: string }> {
    const response = await api.post<{ success: boolean; message: string }>('/auth/logout')
    return { message: response.data.message }
  }

  /**
   * Get current authenticated user
   */
  static async me(): Promise<{ user: User }> {
    const response = await api.get<{ success: boolean; message: string; data: { user: User } }>('/auth/me')
    return { user: response.data.data.user }
  }

  /**
   * Refresh authentication token
   */
  static async refreshToken(): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/refresh')
    return response.data
  }

  /**
   * Check if current token is valid
   */
  static async validateToken(): Promise<boolean> {
    try {
      await this.me()
      return true
    } catch (error) {
      return false
    }
  }

  /**
   * Get user's permissions (if implemented on backend)
   */
  static async getUserPermissions(): Promise<string[]> {
    try {
      const response = await api.get<{ permissions: string[] }>('/auth/permissions')
      return response.data.permissions
    } catch (error) {
      console.warn('Permissions endpoint not available:', error)
      return []
    }
  }
}