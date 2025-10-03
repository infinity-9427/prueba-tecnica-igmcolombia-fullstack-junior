import { ref, computed, reactive } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { 
  UserService, 
  type User, 
  type CreateUserData, 
  type UpdateUserData,
  type UserFilters,
  type RoleUpdateData 
} from '@/services/userService'
import type { PaginatedResponse } from '@/services/invoiceService'

export function useUsers() {
  const userStore = useUserStore()

  // State
  const users = ref<User[]>([])
  const currentUser = ref<User | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const roles = ref<Array<{ name: string; display_name: string }>>([])
  
  // Pagination state
  const pagination = reactive({
    currentPage: 1,
    perPage: 15,
    total: 0,
    lastPage: 1,
    from: 0,
    to: 0
  })

  // Filters state
  const filters = reactive<UserFilters>({
    search: '',
    email: '',
    name: '',
    role: '',
    verified: undefined
  })

  // Computed
  const hasUsers = computed(() => users.value.length > 0)
  const isFirstPage = computed(() => pagination.currentPage === 1)
  const isLastPage = computed(() => pagination.currentPage === pagination.lastPage)
  const totalPages = computed(() => pagination.lastPage)
  const canManageUsers = computed(() => userStore.canManageAll)

  // Security check for user management
  const checkUserManagementAccess = (): boolean => {
    if (!canManageUsers.value) {
      error.value = 'Access denied. Insufficient permissions to manage users.'
      return false
    }
    return true
  }

  /**
   * Fetch users with pagination and filters
   */
  const fetchUsers = async (page: number = 1, resetData: boolean = true): Promise<boolean> => {
    if (!checkUserManagementAccess()) return false

    try {
      loading.value = true
      error.value = null

      if (resetData) {
        users.value = []
      }

      const response = await UserService.getUsers(
        page,
        pagination.perPage,
        filters
      )

      if (resetData) {
        users.value = response.data
      } else {
        users.value.push(...response.data)
      }

      // Update pagination
      pagination.currentPage = response.current_page
      pagination.total = response.total
      pagination.lastPage = response.last_page
      pagination.from = response.from
      pagination.to = response.to

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch users'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch single user
   */
  const fetchUser = async (id: number): Promise<boolean> => {
    if (!checkUserManagementAccess()) return false

    try {
      loading.value = true
      error.value = null

      const user = await UserService.getUser(id)
      currentUser.value = user
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Create new user
   */
  const createUser = async (data: CreateUserData): Promise<User | null> => {
    if (!checkUserManagementAccess()) return null

    try {
      loading.value = true
      error.value = null

      const user = await UserService.createUser(data)
      
      // Add to local state
      users.value.unshift(user)
      pagination.total += 1

      return user
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create user'
      return null
    } finally {
      loading.value = false
    }
  }

  /**
   * Update existing user
   */
  const updateUser = async (id: number, data: UpdateUserData): Promise<boolean> => {
    if (!checkUserManagementAccess()) return false

    try {
      loading.value = true
      error.value = null

      const updatedUser = await UserService.updateUser(id, data)
      
      // Update local state
      const index = users.value.findIndex(user => user.id === id)
      if (index !== -1) {
        users.value[index] = updatedUser
      }
      
      // Update current user if it's the same
      if (currentUser.value?.id === id) {
        currentUser.value = updatedUser
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update user'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Delete user
   */
  const deleteUser = async (id: number): Promise<boolean> => {
    if (!checkUserManagementAccess()) return false

    // Prevent deleting current user
    if (id === userStore.userId) {
      error.value = 'Cannot delete your own account'
      return false
    }

    try {
      loading.value = true
      error.value = null

      await UserService.deleteUser(id)
      
      // Remove from local state
      users.value = users.value.filter(user => user.id !== id)
      pagination.total -= 1
      
      // Clear current user if it's the deleted one
      if (currentUser.value?.id === id) {
        currentUser.value = null
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete user'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Update user role
   */
  const updateUserRole = async (id: number, roleData: RoleUpdateData): Promise<boolean> => {
    if (!checkUserManagementAccess()) return false

    // Prevent changing own role
    if (id === userStore.userId) {
      error.value = 'Cannot change your own role'
      return false
    }

    try {
      loading.value = true
      error.value = null

      const updatedUser = await UserService.updateUserRole(id, roleData)
      
      // Update local state
      const index = users.value.findIndex(user => user.id === id)
      if (index !== -1) {
        users.value[index] = updatedUser
      }
      
      // Update current user if it's the same
      if (currentUser.value?.id === id) {
        currentUser.value = updatedUser
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update user role'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch available roles
   */
  const fetchRoles = async (): Promise<boolean> => {
    try {
      const roleList = await UserService.getRoles()
      roles.value = roleList
      return true
    } catch (err: any) {
      console.error('Failed to fetch roles:', err)
      return false
    }
  }

  /**
   * Search users (for dropdowns/autocomplete)
   */
  const searchUsers = async (query: string): Promise<User[]> => {
    if (!canManageUsers.value) return []

    try {
      const results = await UserService.searchUsers(query)
      return results
    } catch (err: any) {
      console.error('Search failed:', err)
      return []
    }
  }

  /**
   * Get all users (for dropdowns)
   */
  const getAllUsers = async (): Promise<User[]> => {
    if (!canManageUsers.value) return []

    try {
      const allUsers = await UserService.getAllUsers()
      return allUsers
    } catch (err: any) {
      console.error('Failed to fetch all users:', err)
      return []
    }
  }

  /**
   * Load more users (for infinite scroll)
   */
  const loadMore = async (): Promise<boolean> => {
    if (isLastPage.value || loading.value) return false
    return await fetchUsers(pagination.currentPage + 1, false)
  }

  /**
   * Go to specific page
   */
  const goToPage = async (page: number): Promise<boolean> => {
    if (page < 1 || page > pagination.lastPage || loading.value) return false
    return await fetchUsers(page)
  }

  /**
   * Go to next page
   */
  const nextPage = async (): Promise<boolean> => {
    return await goToPage(pagination.currentPage + 1)
  }

  /**
   * Go to previous page
   */
  const previousPage = async (): Promise<boolean> => {
    return await goToPage(pagination.currentPage - 1)
  }

  /**
   * Apply filters and fetch
   */
  const applyFilters = async (newFilters: Partial<UserFilters>): Promise<boolean> => {
    Object.assign(filters, newFilters)
    return await fetchUsers(1)
  }

  /**
   * Clear filters
   */
  const clearFilters = async (): Promise<boolean> => {
    Object.keys(filters).forEach(key => {
      if (key !== 'verified') {
        filters[key as keyof UserFilters] = ''
      } else {
        filters.verified = undefined
      }
    })
    return await fetchUsers(1)
  }

  /**
   * Refresh current page
   */
  const refresh = async (): Promise<boolean> => {
    return await fetchUsers(pagination.currentPage)
  }

  /**
   * Clear error
   */
  const clearError = (): void => {
    error.value = null
  }

  /**
   * Reset state
   */
  const reset = (): void => {
    users.value = []
    currentUser.value = null
    pagination.currentPage = 1
    pagination.total = 0
    pagination.lastPage = 1
    clearError()
  }

  return {
    // State
    users: computed(() => users.value),
    currentUser: computed(() => currentUser.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    filters: computed(() => filters),
    pagination: computed(() => pagination),
    roles: computed(() => roles.value),
    
    // Computed
    hasUsers,
    isFirstPage,
    isLastPage,
    totalPages,
    canManageUsers,
    
    // Actions
    fetchUsers,
    fetchUser,
    createUser,
    updateUser,
    deleteUser,
    updateUserRole,
    fetchRoles,
    searchUsers,
    getAllUsers,
    loadMore,
    goToPage,
    nextPage,
    previousPage,
    applyFilters,
    clearFilters,
    refresh,
    clearError,
    reset
  }
}