<template>
  <div>
    <!-- Header Section -->
    <div class="mb-6 px-4 lg:px-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            User Management
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            Manage system users with filtering and pagination
          </p>
        </div>
        <div>
          <Button
            @click="$router.push('/app/users/create')"
            icon="pi pi-plus"
            label="Add User"
          />
        </div>
      </div>
    </div>

    <!-- DataTable -->
    <DataTable
      :data="users"
      :columns="columns"
      search-placeholder="Name, email, role..."
      base-route="/app/users"
      :global-filter-fields="['name', 'email', 'role']"
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
import { UserColumns } from '@/types/datatable'
import type { PageEvent, SortEvent, FilterEvent } from '@/types/datatable'

// Use original columns without modification
const columns = UserColumns


// Simple test data
const users = ref([
  { 
    id: 1, 
    name: 'John Smith',
    email: 'john.smith@invoiceapp.com', 
    department: 'Administration',
    role: 'admin', 
    status: 'active', 
    lastLogin: '2024-01-15'
  },
  { 
    id: 2, 
    name: 'Sarah Johnson',
    email: 'sarah.johnson@invoiceapp.com', 
    department: 'Finance',
    role: 'manager', 
    status: 'active', 
    lastLogin: '2024-01-14'
  },
  { 
    id: 3, 
    name: 'Michael Brown',
    email: 'michael.brown@invoiceapp.com', 
    department: 'Sales',
    role: 'user', 
    status: 'inactive', 
    lastLogin: '2024-01-10'
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