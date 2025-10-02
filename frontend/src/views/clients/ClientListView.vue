<template>
  <div>
    <!-- Header Section -->
    <div class="mb-6 px-4 lg:px-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            Client List
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            Manage and view all clients with filtering and pagination
          </p>
        </div>
        <div>
          <Button
            @click="$router.push('/app/clients/create')"
            icon="pi pi-plus"
            label="Add Client"
          />
        </div>
      </div>
    </div>

    <!-- DataTable -->
    <DataTable
      :data="clients"
      :columns="columns"
      search-placeholder="Client name, email, phone, document..."
      base-route="/app/clients"
      :global-filter-fields="['name', 'email', 'phone', 'document']"
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
import { ClientColumns } from '@/types/datatable'
import type { PageEvent, SortEvent, FilterEvent } from '@/types/datatable'

// Columns configuration
const columns = ClientColumns


// Sample data with comprehensive client structure
const clients = ref([
  { 
    id: 1, 
    firstName: 'John',
    lastName: 'Smith',
    name: 'John Smith',
    documentType: 'passport',
    document: 'P123456789',
    email: 'john.smith@email.com', 
    phone: '+1-555-0123',
    company: 'Smith Consulting LLC',
    address: '123 Business Ave',
    city: 'New York',
    state: 'NY',
    zipCode: '10001',
    status: 'active', 
    invoiceCount: 5,
    totalRevenue: 12500.00,
    lastInvoiceDate: '2024-01-15',
    notes: 'Preferred client, always pays on time'
  },
  { 
    id: 2, 
    firstName: 'Sarah',
    lastName: 'Johnson',
    name: 'Sarah Johnson',
    documentType: 'driver_license',
    document: 'DL987654321',
    email: 'sarah.johnson@techcorp.com', 
    phone: '+1-555-0124',
    company: 'TechCorp Solutions',
    address: '456 Innovation Blvd',
    city: 'San Francisco',
    state: 'CA',
    zipCode: '94102',
    status: 'active', 
    invoiceCount: 3,
    totalRevenue: 8500.00,
    lastInvoiceDate: '2024-01-14',
    notes: 'Corporate client, requires formal invoicing'
  },
  { 
    id: 3, 
    firstName: 'Michael',
    lastName: 'Brown',
    name: 'Michael Brown',
    documentType: 'national_id',
    document: 'ID456789123',
    email: 'michael.brown@startup.io', 
    phone: '+1-555-0125',
    company: 'StartupXYZ Inc',
    address: '789 Entrepreneur St',
    city: 'Austin',
    state: 'TX',
    zipCode: '73301',
    status: 'inactive', 
    invoiceCount: 2,
    totalRevenue: 4200.00,
    lastInvoiceDate: '2024-01-10',
    notes: 'Payment issues, currently on hold'
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