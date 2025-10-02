<template>
  <div>
    <!-- Header Section -->
    <div class="mb-6 px-4 lg:px-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            Invoice List
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            Manage and view all invoices with filtering and pagination
          </p>
        </div>
        <div>
          <Button
            @click="$router.push('/app/invoices/create')"
            icon="pi pi-plus"
            label="Create Invoice"
          />
        </div>
      </div>
    </div>

    <!-- DataTable -->
    <DataTable
      :data="invoices"
      :columns="columns"
      search-placeholder="Invoice number, client name..."
      base-route="/app/invoices"
      :global-filter-fields="['number', 'client', 'status']"
      @page="onPageChange"
      @sort="onSort"
      @filter="onFilter"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import DataTable from '@/components/common/DataTable.vue'
import Button from 'primevue/button'
import { InvoiceColumns } from '@/types/datatable'
import type { PageEvent, SortEvent, FilterEvent } from '@/types/datatable'

// Columns configuration
const columns = InvoiceColumns


// Sample data with comprehensive invoice structure
const invoices = ref([
  {
    id: 1,
    number: 'INV-2024-001',
    client: 'John Smith',
    clientDetails: {
      firstName: 'John',
      lastName: 'Smith',
      documentType: 'passport',
      documentNumber: 'P123456789',
      email: 'john.smith@email.com',
      phone: '+1-555-0123'
    },
    description: 'Web development services for corporate website',
    notes: 'Payment due within 30 days. Late fees may apply.',
    issueDate: '2024-01-15',
    dueDate: '2024-02-15',
    amount: 1250.00,
    status: 'paid',
    items: [
      { name: 'Frontend Development', quantity: 40, tax: 19, unitPrice: 25.00 },
      { name: 'Backend Integration', quantity: 20, tax: 19, unitPrice: 35.00 }
    ],
    attachments: [
      { name: 'invoice_001.pdf', type: 'pdf', url: '/attachments/invoice_001.pdf' }
    ]
  },
  {
    id: 2,
    number: 'INV-2024-002',
    client: 'Sarah Johnson',
    clientDetails: {
      firstName: 'Sarah',
      lastName: 'Johnson',
      documentType: 'driver_license',
      documentNumber: 'DL987654321',
      email: 'sarah.johnson@techcorp.com',
      phone: '+1-555-0124'
    },
    description: 'Mobile app development and testing',
    notes: 'Includes 3 months of free maintenance.',
    issueDate: '2024-01-14',
    dueDate: '2024-02-14',
    amount: 2850.00,
    status: 'pending',
    items: [
      { name: 'Mobile App Development', quantity: 80, tax: 19, unitPrice: 30.00 },
      { name: 'Quality Assurance Testing', quantity: 15, tax: 19, unitPrice: 45.00 }
    ],
    attachments: [
      { name: 'invoice_002.pdf', type: 'pdf', url: '/attachments/invoice_002.pdf' }
    ]
  },
  {
    id: 3,
    number: 'INV-2024-003',
    client: 'Michael Brown',
    clientDetails: {
      firstName: 'Michael',
      lastName: 'Brown',
      documentType: 'national_id',
      documentNumber: 'ID456789123',
      email: 'michael.brown@startup.io',
      phone: '+1-555-0125'
    },
    description: 'E-commerce platform development',
    notes: 'Payment overdue. Please contact accounting department.',
    issueDate: '2024-01-10',
    dueDate: '2024-02-10',
    amount: 4200.00,
    status: 'overdue',
    items: [
      { name: 'E-commerce Platform', quantity: 120, tax: 19, unitPrice: 32.00 },
      { name: 'Payment Gateway Integration', quantity: 8, tax: 19, unitPrice: 75.00 }
    ],
    attachments: [
      { name: 'invoice_003.pdf', type: 'pdf', url: '/attachments/invoice_003.pdf' },
      { name: 'contract_003.jpg', type: 'image', url: '/attachments/contract_003.jpg' }
    ]
  }
])

// Event handlers
const onPageChange = (event: PageEvent) => {
  // TODO: Implement API call for pagination
}

const onSort = (event: SortEvent) => {
  // TODO: Implement API call for sorting
}

const onFilter = (event: FilterEvent) => {
  // TODO: Implement API call for filtering
}
</script>