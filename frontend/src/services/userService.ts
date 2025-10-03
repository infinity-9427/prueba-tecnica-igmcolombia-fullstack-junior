import api from './api'
import type { PaginatedResponse } from './invoiceService'

export interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string
  created_at: string
  updated_at: string
  roles?: Array<{
    id: number
    name: string
    guard_name: string
  }>
  invoices_count?: number
  clients_count?: number
}

export interface CreateUserData {
  name: string
  email: string
  password: string
  password_confirmation: string
  role?: string
}

export interface UpdateUserData {
  name?: string
  email?: string
  password?: string
  password_confirmation?: string
}

export interface UserFilters {
  search?: string
  email?: string
  name?: string
  role?: string
  verified?: boolean
}

export interface RoleUpdateData {
  role: string
}

export class UserService {
  /**
   * Get paginated list of users with filters
   */
  static async getUsers(
    page: number = 1,
    perPage: number = 15,
    filters: UserFilters = {}
  ): Promise<PaginatedResponse<User>> {
    const params = {
      page,
      per_page: perPage,
      ...filters
    }
    
    const response = await api.get<PaginatedResponse<User>>('/users', { params })
    return response.data
  }

  /**
   * Get single user by ID
   */
  static async getUser(id: number): Promise<User> {
    const response = await api.get<User>(`/users/${id}`)
    return response.data
  }

  /**
   * Create new user
   */
  static async createUser(data: CreateUserData): Promise<User> {
    const response = await api.post<User>('/users', data)
    return response.data
  }

  /**
   * Update existing user
   */
  static async updateUser(id: number, data: UpdateUserData): Promise<User> {
    const response = await api.put<User>(`/users/${id}`, data)
    return response.data
  }

  /**
   * Delete user
   */
  static async deleteUser(id: number): Promise<void> {
    await api.delete(`/users/${id}`)
  }

  /**
   * Update user role
   */
  static async updateUserRole(id: number, data: RoleUpdateData): Promise<User> {
    const response = await api.patch<User>(`/users/${id}/role`, data)
    return response.data
  }

  /**
   * Get all users for dropdown/select (no pagination)
   */
  static async getAllUsers(): Promise<User[]> {
    const response = await api.get<User[]>('/users/all')
    return response.data
  }

  /**
   * Search users by name or email
   */
  static async searchUsers(query: string): Promise<User[]> {
    const response = await api.get<User[]>('/users/search', {
      params: { query }
    })
    return response.data
  }

  /**
   * Get available roles
   */
  static async getRoles(): Promise<Array<{ name: string; display_name: string }>> {
    const response = await api.get<Array<{ name: string; display_name: string }>>('/roles')
    return response.data
  }
}