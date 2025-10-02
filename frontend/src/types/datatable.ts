export interface TableColumn {
  field: string
  header: string
  sortable?: boolean
  type?: 'text' | 'badge' | 'currency' | 'date' | 'actions' | 'boolean'
  style?: string
  class?: string
  slot?: string
  badgeClasses?: Record<string, string>
  format?: (value: any) => string
}

export interface TableFilters {
  global?: string
  [key: string]: any
}

export interface PageEvent {
  first: number
  rows: number
  page: number
  pageCount: number
}

export interface SortEvent {
  sortField: string
  sortOrder: number
}

export interface FilterEvent {
  filters: Record<string, any>
}

export interface DataTableProps {
  // Data
  data: any[]
  columns: TableColumn[]
  totalRecords?: number
  
  // UI
  title?: string
  subtitle?: string
  searchPlaceholder?: string
  createRoute?: string
  createButtonText?: string
  enableColumnFilters?: boolean
  
  // Table behavior
  lazy?: boolean
  loading?: boolean
  showPagination?: boolean
  showFilters?: boolean
  rowsPerPage?: number
  stripedRows?: boolean
  showGridlines?: boolean
  responsiveLayout?: string
  breakpoint?: string
  scrollable?: boolean
  scrollHeight?: string
  sortMode?: string
  removableSort?: boolean
  filterDisplay?: string
  globalFilterFields?: string[]
  
  // Actions
  showViewAction?: boolean
  showEditAction?: boolean
  showDeleteAction?: boolean
  baseRoute?: string
}

// Predefined column configurations for different entities
export const InvoiceColumns: TableColumn[] = [
  {
    field: 'number',
    header: 'Invoice #',
    sortable: true
  },
  {
    field: 'client',
    header: 'Client',
    sortable: true
  },
  {
    field: 'issueDate',
    header: 'Issue Date',
    type: 'date',
    sortable: true
  },
  {
    field: 'dueDate',
    header: 'Due Date',
    type: 'date',
    sortable: true
  },
  {
    field: 'amount',
    header: 'Amount',
    type: 'currency',
    sortable: true
  },
  {
    field: 'status',
    header: 'Status',
    type: 'badge',
    sortable: true,
    badgeClasses: {
      paid: 'success',
      pending: 'warning',
      overdue: 'danger',
      draft: 'secondary'
    }
  },
  {
    field: 'actions',
    header: 'Actions',
    type: 'actions',
    sortable: false,
    style: 'width: 150px'
  }
]

export const ClientColumns: TableColumn[] = [
  {
    field: 'name',
    header: 'Name',
    sortable: true
  },
  {
    field: 'document',
    header: 'Document',
    sortable: true
  },
  {
    field: 'email',
    header: 'Email',
    sortable: true
  },
  {
    field: 'phone',
    header: 'Phone',
    sortable: false
  },
  {
    field: 'company',
    header: 'Company',
    sortable: true
  },
  {
    field: 'status',
    header: 'Status',
    type: 'badge',
    sortable: true,
    badgeClasses: {
      active: 'success',
      inactive: 'secondary'
    }
  },
  {
    field: 'actions',
    header: 'Actions',
    type: 'actions',
    sortable: false,
    style: 'width: 150px'
  }
]

export const UserColumns: TableColumn[] = [
  {
    field: 'name',
    header: 'Name',
    sortable: true
  },
  {
    field: 'email',
    header: 'Email',
    sortable: true
  },
  {
    field: 'department',
    header: 'Department',
    sortable: true
  },
  {
    field: 'role',
    header: 'Role',
    type: 'badge',
    sortable: true,
    badgeClasses: {
      admin: 'danger',
      manager: 'info',
      user: 'secondary'
    }
  },
  {
    field: 'status',
    header: 'Status',
    type: 'badge',
    sortable: true,
    badgeClasses: {
      active: 'success',
      inactive: 'secondary',
      pending: 'warning'
    }
  },
  {
    field: 'lastLogin',
    header: 'Last Login',
    type: 'date',
    sortable: true,
    format: (value) => value || 'Never'
  },
  {
    field: 'actions',
    header: 'Actions',
    type: 'actions',
    sortable: false,
    style: 'width: 150px'
  }
]