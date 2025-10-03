import api from './api'

export interface InvoiceItem {
  id?: number
  description: string
  quantity: number
  unit_price: number
  total_price: number
}

export interface Invoice {
  id: number
  invoice_number: string
  client_id: number
  user_id: number
  issue_date: string
  due_date: string
  subtotal: number
  tax_amount: number
  total_amount: number
  status: 'draft' | 'sent' | 'paid' | 'overdue' | 'cancelled'
  notes?: string
  attachment_url?: string
  created_at: string
  updated_at: string
  client?: {
    id: number
    name: string
    email: string
  }
  user?: {
    id: number
    name: string
  }
  items?: InvoiceItem[]
}

export interface CreateInvoiceData {
  client_id: number
  issue_date: string
  due_date: string
  subtotal: number
  tax_amount: number
  notes?: string
  items: Omit<InvoiceItem, 'id'>[]
  attachment?: File
}

export interface UpdateInvoiceData extends Partial<CreateInvoiceData> {
  id: number
}

export interface InvoiceFilters {
  search?: string
  status?: string
  client_id?: number
  user_id?: number
  date_from?: string
  date_to?: string
  min_amount?: number
  max_amount?: number
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  per_page: number
  total: number
  last_page: number
  from: number
  to: number
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
}

export class InvoiceService {
  /**
   * Get paginated list of invoices with filters
   */
  static async getInvoices(
    page: number = 1,
    perPage: number = 15,
    filters: InvoiceFilters = {}
  ): Promise<PaginatedResponse<Invoice>> {
    const params = {
      page,
      per_page: perPage,
      ...filters
    }
    
    const response = await api.get<PaginatedResponse<Invoice>>('/invoices', { params })
    return response.data
  }

  /**
   * Get single invoice by ID
   */
  static async getInvoice(id: number): Promise<Invoice> {
    const response = await api.get<Invoice>(`/invoices/${id}`)
    return response.data
  }

  /**
   * Create new invoice
   */
  static async createInvoice(data: CreateInvoiceData): Promise<Invoice> {
    const formData = new FormData()
    
    // Add regular fields
    Object.keys(data).forEach(key => {
      if (key === 'items') {
        formData.append('items', JSON.stringify(data.items))
      } else if (key === 'attachment' && data.attachment) {
        formData.append('attachment', data.attachment)
      } else if (data[key as keyof CreateInvoiceData] !== undefined) {
        formData.append(key, String(data[key as keyof CreateInvoiceData]))
      }
    })

    const response = await api.post<Invoice>('/invoices', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  }

  /**
   * Update existing invoice
   */
  static async updateInvoice(id: number, data: Partial<CreateInvoiceData>): Promise<Invoice> {
    const formData = new FormData()
    
    // Add regular fields
    Object.keys(data).forEach(key => {
      if (key === 'items') {
        formData.append('items', JSON.stringify(data.items))
      } else if (key === 'attachment' && data.attachment) {
        formData.append('attachment', data.attachment)
      } else if (data[key as keyof CreateInvoiceData] !== undefined) {
        formData.append(key, String(data[key as keyof CreateInvoiceData]))
      }
    })

    const response = await api.put<Invoice>(`/invoices/${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    return response.data
  }

  /**
   * Delete invoice
   */
  static async deleteInvoice(id: number): Promise<void> {
    await api.delete(`/invoices/${id}`)
  }

  /**
   * Update invoice status
   */
  static async updateStatus(id: number, status: Invoice['status']): Promise<Invoice> {
    const response = await api.patch<Invoice>(`/invoices/${id}/status`, { status })
    return response.data
  }

  /**
   * Get recent invoices
   */
  static async getRecentInvoices(limit: number = 5): Promise<Invoice[]> {
    const response = await api.get<Invoice[]>('/invoices-recent', {
      params: { limit }
    })
    return response.data
  }

  /**
   * Download invoice attachment
   */
  static getAttachmentUrl(filename: string): string {
    return `${api.defaults.baseURL}/files/download/invoice-attachment/${filename}`
  }
}