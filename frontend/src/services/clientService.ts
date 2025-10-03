import api from './api'
import type { PaginatedResponse } from './invoiceService'

export interface Client {
  id: number
  name: string
  email: string
  phone?: string
  address?: string
  tax_id?: string
  user_id: number
  created_at: string
  updated_at: string
  user?: {
    id: number
    name: string
  }
  invoices_count?: number
  total_invoiced?: number
}

export interface CreateClientData {
  name: string
  email: string
  phone?: string
  address?: string
  tax_id?: string
}

export interface UpdateClientData extends Partial<CreateClientData> {
  id: number
}

export interface ClientFilters {
  search?: string
  name?: string
  email?: string
  tax_id?: string
  phone?: string
  user_id?: number
}

export class ClientService {
  /**
   * Get paginated list of clients with filters
   */
  static async getClients(
    page: number = 1,
    perPage: number = 15,
    filters: ClientFilters = {}
  ): Promise<PaginatedResponse<Client>> {
    const params = {
      page,
      per_page: perPage,
      ...filters
    }
    
    const response = await api.get<PaginatedResponse<Client>>('/clients', { params })
    return response.data
  }

  /**
   * Get single client by ID
   */
  static async getClient(id: number): Promise<Client> {
    const response = await api.get<Client>(`/clients/${id}`)
    return response.data
  }

  /**
   * Create new client
   */
  static async createClient(data: CreateClientData): Promise<Client> {
    const response = await api.post<Client>('/clients', data)
    return response.data
  }

  /**
   * Update existing client
   */
  static async updateClient(id: number, data: Partial<CreateClientData>): Promise<Client> {
    const response = await api.put<Client>(`/clients/${id}`, data)
    return response.data
  }

  /**
   * Delete client
   */
  static async deleteClient(id: number): Promise<void> {
    await api.delete(`/clients/${id}`)
  }

  /**
   * Get all clients for dropdown/select (no pagination)
   */
  static async getAllClients(): Promise<Client[]> {
    const response = await api.get<Client[]>('/clients/all')
    return response.data
  }

  /**
   * Search clients by name or email
   */
  static async searchClients(query: string): Promise<Client[]> {
    const response = await api.get<Client[]>('/clients/search', {
      params: { query }
    })
    return response.data
  }
}