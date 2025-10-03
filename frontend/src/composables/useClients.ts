import { ref, computed, reactive } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { 
  ClientService, 
  type Client, 
  type CreateClientData, 
  type ClientFilters 
} from '@/services/clientService'
import type { PaginatedResponse } from '@/services/invoiceService'

export function useClients() {
  const userStore = useUserStore()

  // State
  const clients = ref<Client[]>([])
  const currentClient = ref<Client | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  
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
  const filters = reactive<ClientFilters>({
    search: '',
    name: '',
    email: '',
    tax_id: '',
    phone: ''
  })

  // Computed
  const hasClients = computed(() => clients.value.length > 0)
  const isFirstPage = computed(() => pagination.currentPage === 1)
  const isLastPage = computed(() => pagination.currentPage === pagination.lastPage)
  const totalPages = computed(() => pagination.lastPage)

  // Security: Filter clients based on user role
  const getSecureFilters = (): ClientFilters => {
    const secureFilters = { ...filters }
    
    // If user can't manage all, filter by user ID
    if (!userStore.canManageAll && userStore.userId) {
      secureFilters.user_id = userStore.userId
    }
    
    return secureFilters
  }

  /**
   * Fetch clients with pagination and filters
   */
  const fetchClients = async (page: number = 1, resetData: boolean = true): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      if (resetData) {
        clients.value = []
      }

      const response = await ClientService.getClients(
        page,
        pagination.perPage,
        getSecureFilters()
      )

      if (resetData) {
        clients.value = response.data
      } else {
        clients.value.push(...response.data)
      }

      // Update pagination
      pagination.currentPage = response.current_page
      pagination.total = response.total
      pagination.lastPage = response.last_page
      pagination.from = response.from
      pagination.to = response.to

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch clients'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch single client
   */
  const fetchClient = async (id: number): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      const client = await ClientService.getClient(id)
      
      // Security: Check if user can access this client
      if (!userStore.canManageAll && client.user_id !== userStore.userId) {
        error.value = 'Access denied'
        return false
      }

      currentClient.value = client
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch client'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Create new client
   */
  const createClient = async (data: CreateClientData): Promise<Client | null> => {
    try {
      loading.value = true
      error.value = null

      const client = await ClientService.createClient(data)
      
      // Add to local state
      clients.value.unshift(client)
      pagination.total += 1

      return client
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create client'
      return null
    } finally {
      loading.value = false
    }
  }

  /**
   * Update existing client
   */
  const updateClient = async (id: number, data: Partial<CreateClientData>): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      const updatedClient = await ClientService.updateClient(id, data)
      
      // Update local state
      const index = clients.value.findIndex(client => client.id === id)
      if (index !== -1) {
        clients.value[index] = updatedClient
      }
      
      // Update current client if it's the same
      if (currentClient.value?.id === id) {
        currentClient.value = updatedClient
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update client'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Delete client
   */
  const deleteClient = async (id: number): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      await ClientService.deleteClient(id)
      
      // Remove from local state
      clients.value = clients.value.filter(client => client.id !== id)
      pagination.total -= 1
      
      // Clear current client if it's the deleted one
      if (currentClient.value?.id === id) {
        currentClient.value = null
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete client'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Search clients (for dropdowns/autocomplete)
   */
  const searchClients = async (query: string): Promise<Client[]> => {
    try {
      const results = await ClientService.searchClients(query)
      return results
    } catch (err: any) {
      console.error('Search failed:', err)
      return []
    }
  }

  /**
   * Get all clients (for dropdowns)
   */
  const getAllClients = async (): Promise<Client[]> => {
    try {
      const allClients = await ClientService.getAllClients()
      return allClients
    } catch (err: any) {
      console.error('Failed to fetch all clients:', err)
      return []
    }
  }

  /**
   * Load more clients (for infinite scroll)
   */
  const loadMore = async (): Promise<boolean> => {
    if (isLastPage.value || loading.value) return false
    return await fetchClients(pagination.currentPage + 1, false)
  }

  /**
   * Go to specific page
   */
  const goToPage = async (page: number): Promise<boolean> => {
    if (page < 1 || page > pagination.lastPage || loading.value) return false
    return await fetchClients(page)
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
  const applyFilters = async (newFilters: Partial<ClientFilters>): Promise<boolean> => {
    Object.assign(filters, newFilters)
    return await fetchClients(1)
  }

  /**
   * Clear filters
   */
  const clearFilters = async (): Promise<boolean> => {
    Object.keys(filters).forEach(key => {
      filters[key as keyof ClientFilters] = ''
    })
    return await fetchClients(1)
  }

  /**
   * Refresh current page
   */
  const refresh = async (): Promise<boolean> => {
    return await fetchClients(pagination.currentPage)
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
    clients.value = []
    currentClient.value = null
    pagination.currentPage = 1
    pagination.total = 0
    pagination.lastPage = 1
    clearError()
  }

  return {
    // State
    clients: computed(() => clients.value),
    currentClient: computed(() => currentClient.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    filters: computed(() => filters),
    pagination: computed(() => pagination),
    
    // Computed
    hasClients,
    isFirstPage,
    isLastPage,
    totalPages,
    
    // Actions
    fetchClients,
    fetchClient,
    createClient,
    updateClient,
    deleteClient,
    searchClients,
    getAllClients,
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