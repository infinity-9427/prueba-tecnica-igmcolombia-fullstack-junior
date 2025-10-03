import api from './api'
import type { 
  AuthResponse, 
  User, 
  LoginCredentials, 
  RegisterData 
} from '@/stores/userStore'

export class AuthService {
  /**
   * Login user with credentials
   */
  static async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/login', credentials)
    return response.data
  }

  /**
   * Register new user
   */
  static async register(userData: RegisterData): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/register', userData)
    return response.data
  }

  /**
   * Logout current user
   */
  static async logout(): Promise<{ message: string }> {
    const response = await api.post<{ message: string }>('/auth/logout')
    return response.data
  }

  /**
   * Get current authenticated user
   */
  static async me(): Promise<{ user: User }> {
    const response = await api.get<{ user: User }>('/auth/me')
    return response.data
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