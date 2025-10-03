import { ref, computed, reactive } from 'vue'
import { useUserStore } from '@/stores/userStore'
import { 
  InvoiceService, 
  type Invoice, 
  type CreateInvoiceData, 
  type InvoiceFilters,
  type PaginatedResponse 
} from '@/services/invoiceService'

export function useInvoices() {
  const userStore = useUserStore()

  // State
  const invoices = ref<Invoice[]>([])
  const currentInvoice = ref<Invoice | null>(null)
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
  const filters = reactive<InvoiceFilters>({
    search: '',
    status: '',
    client_id: undefined,
    date_from: '',
    date_to: '',
    min_amount: undefined,
    max_amount: undefined
  })

  // Computed
  const hasInvoices = computed(() => invoices.value.length > 0)
  const isFirstPage = computed(() => pagination.currentPage === 1)
  const isLastPage = computed(() => pagination.currentPage === pagination.lastPage)
  const totalPages = computed(() => pagination.lastPage)

  // Security: Filter invoices based on user role
  const getSecureFilters = (): InvoiceFilters => {
    const secureFilters = { ...filters }
    
    // If user can't manage all, filter by user ID
    if (!userStore.canManageAll && userStore.userId) {
      secureFilters.user_id = userStore.userId
    }
    
    return secureFilters
  }

  /**
   * Fetch invoices with pagination and filters
   */
  const fetchInvoices = async (page: number = 1, resetData: boolean = true): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      if (resetData) {
        invoices.value = []
      }

      const response = await InvoiceService.getInvoices(
        page,
        pagination.perPage,
        getSecureFilters()
      )

      if (resetData) {
        invoices.value = response.data
      } else {
        invoices.value.push(...response.data)
      }

      // Update pagination
      pagination.currentPage = response.current_page
      pagination.total = response.total
      pagination.lastPage = response.last_page
      pagination.from = response.from
      pagination.to = response.to

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch invoices'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Fetch single invoice
   */
  const fetchInvoice = async (id: number): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      const invoice = await InvoiceService.getInvoice(id)
      
      // Security: Check if user can access this invoice
      if (!userStore.canManageAll && invoice.user_id !== userStore.userId) {
        error.value = 'Access denied'
        return false
      }

      currentInvoice.value = invoice
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch invoice'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Create new invoice
   */
  const createInvoice = async (data: CreateInvoiceData): Promise<Invoice | null> => {
    try {
      loading.value = true
      error.value = null

      const invoice = await InvoiceService.createInvoice(data)
      
      // Add to local state
      invoices.value.unshift(invoice)
      pagination.total += 1

      return invoice
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create invoice'
      return null
    } finally {
      loading.value = false
    }
  }

  /**
   * Update existing invoice
   */
  const updateInvoice = async (id: number, data: Partial<CreateInvoiceData>): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      const updatedInvoice = await InvoiceService.updateInvoice(id, data)
      
      // Update local state
      const index = invoices.value.findIndex(inv => inv.id === id)
      if (index !== -1) {
        invoices.value[index] = updatedInvoice
      }
      
      // Update current invoice if it's the same
      if (currentInvoice.value?.id === id) {
        currentInvoice.value = updatedInvoice
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update invoice'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Delete invoice
   */
  const deleteInvoice = async (id: number): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      await InvoiceService.deleteInvoice(id)
      
      // Remove from local state
      invoices.value = invoices.value.filter(inv => inv.id !== id)
      pagination.total -= 1
      
      // Clear current invoice if it's the deleted one
      if (currentInvoice.value?.id === id) {
        currentInvoice.value = null
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete invoice'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Update invoice status
   */
  const updateInvoiceStatus = async (id: number, status: Invoice['status']): Promise<boolean> => {
    try {
      loading.value = true
      error.value = null

      const updatedInvoice = await InvoiceService.updateStatus(id, status)
      
      // Update local state
      const index = invoices.value.findIndex(inv => inv.id === id)
      if (index !== -1) {
        invoices.value[index] = updatedInvoice
      }
      
      // Update current invoice if it's the same
      if (currentInvoice.value?.id === id) {
        currentInvoice.value = updatedInvoice
      }

      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update invoice status'
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Load more invoices (for infinite scroll)
   */
  const loadMore = async (): Promise<boolean> => {
    if (isLastPage.value || loading.value) return false
    return await fetchInvoices(pagination.currentPage + 1, false)
  }

  /**
   * Go to specific page
   */
  const goToPage = async (page: number): Promise<boolean> => {
    if (page < 1 || page > pagination.lastPage || loading.value) return false
    return await fetchInvoices(page)
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
  const applyFilters = async (newFilters: Partial<InvoiceFilters>): Promise<boolean> => {
    Object.assign(filters, newFilters)
    return await fetchInvoices(1)
  }

  /**
   * Clear filters
   */
  const clearFilters = async (): Promise<boolean> => {
    Object.keys(filters).forEach(key => {
      filters[key as keyof InvoiceFilters] = ''
    })
    return await fetchInvoices(1)
  }

  /**
   * Refresh current page
   */
  const refresh = async (): Promise<boolean> => {
    return await fetchInvoices(pagination.currentPage)
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
    invoices.value = []
    currentInvoice.value = null
    pagination.currentPage = 1
    pagination.total = 0
    pagination.lastPage = 1
    clearError()
  }

  return {
    // State
    invoices: computed(() => invoices.value),
    currentInvoice: computed(() => currentInvoice.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    filters: computed(() => filters),
    pagination: computed(() => pagination),
    
    // Computed
    hasInvoices,
    isFirstPage,
    isLastPage,
    totalPages,
    
    // Actions
    fetchInvoices,
    fetchInvoice,
    createInvoice,
    updateInvoice,
    deleteInvoice,
    updateInvoiceStatus,
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