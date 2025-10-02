<template>
  <div class="datatable-container">
    <DataTable 
      v-model:filters="filters" 
      :value="data" 
      paginator 
      :rows="rowsPerPage || 10" 
      dataKey="id"
      :loading="loading"
      :globalFilterFields="globalFilterFields"
      :rowsPerPageOptions="[5, 10, 20, 50]"
      paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
      currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
      responsiveLayout="scroll"
    >
      <template #header>
        <div class="flex justify-end items-center gap-2">
          <div class="flex items-center gap-2">
            <IconField>
              <InputIcon>
                <i class="pi pi-search text-sm" />
              </InputIcon>
              <InputText 
                v-model="filters['global'].value" 
                :placeholder="getPlaceholder" 
                class="w-full text-sm"
                size="small"
              />
            </IconField>
            <!-- Clear button - disable/enable based on input -->
            <Button 
              type="button" 
              label="Clear"
              icon="pi pi-times" 
              size="small"
              :disabled="!filters['global'].value || filters['global'].value.length === 0"
              :class="[
                'ml-2 transition-all duration-200',
                (!filters['global'].value || filters['global'].value.length === 0) 
                  ? 'bg-gray-400 text-gray-200 border-gray-400 opacity-60' 
                  : 'bg-gray-600 text-white border-gray-600 hover:bg-gray-800 hover:border-gray-800 hover:shadow-md'
              ]"
              :style="(!filters['global'].value || filters['global'].value.length === 0) ? 'cursor: not-allowed !important; pointer-events: auto !important;' : 'cursor: pointer;'"
              @click="clearFilter()" 
            />
          </div>
        </div>
      </template>
      
      <template #empty> 
        No records found. 
      </template>
      
      <template #loading> 
        Loading data. Please wait. 
      </template>

      <!-- Dynamic columns -->
      <Column
        v-for="col in columns"
        :key="col.field"
        :field="col.field"
        :header="col.header"
        :sortable="col.sortable !== false"
        :style="col.style || 'min-width: 12rem'"
      >
        <template #body="slotProps">
          <template v-if="col.type === 'badge'">
            <Tag 
              :value="formatValue(slotProps.data[col.field], col)"
              :severity="getBadgeSeverity(slotProps.data[col.field], col.badgeClasses)"
            />
          </template>
          <template v-else-if="col.type === 'currency'">
            {{ formatCurrency(slotProps.data[col.field]) }}
          </template>
          <template v-else-if="col.type === 'date'">
            {{ formatDate(slotProps.data[col.field]) }}
          </template>
          <template v-else-if="col.type === 'boolean'">
            <i class="pi" :class="{ 'pi-check-circle text-green-500': slotProps.data[col.field], 'pi-times-circle text-red-400': !slotProps.data[col.field] }"></i>
          </template>
          <template v-else-if="col.type === 'actions'">
            <div class="action-buttons">
              <Button
                v-if="showViewAction !== false"
                @click="$router.push(getActionRoute('view', slotProps.data))"
                icon="pi pi-eye"
                variant="text"
                size="small"
                severity="info"
                title="View Details"
              />
              <Button
                v-if="showEditAction !== false"
                @click="$router.push(getActionRoute('edit', slotProps.data))"
                icon="pi pi-pencil"
                variant="text"
                size="small"
                severity="warn"
                title="Edit Record"
              />
              <Button
                v-if="showDeleteAction"
                @click="onDelete(slotProps.data)"
                icon="pi pi-trash"
                variant="text"
                size="small"
                severity="danger"
                title="Delete Record"
              />
            </div>
          </template>
          <!-- Custom column content via slots -->
          <template v-else-if="col.slot">
            <slot :name="col.slot" :data="slotProps.data" :value="slotProps.data[col.field]" />
          </template>
          <!-- Default column content -->
          <template v-else>
            {{ formatValue(slotProps.data[col.field], col) }}
          </template>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import Tag from 'primevue/tag'
import { FilterMatchMode } from '@primevue/core/api'

// Types
interface TableColumn {
  field: string
  header: string
  sortable?: boolean
  type?: 'text' | 'badge' | 'currency' | 'date' | 'actions' | 'boolean'
  style?: string
  slot?: string
  badgeClasses?: Record<string, string>
  format?: (value: any) => string
}


// Props
const props = defineProps<{
  // Data
  data: any[]
  columns: TableColumn[]
  
  // UI
  searchPlaceholder?: string
  
  // Table behavior
  loading?: boolean
  rowsPerPage?: number
  globalFilterFields?: string[]
  
  // Actions
  showViewAction?: boolean
  showEditAction?: boolean
  showDeleteAction?: boolean
  baseRoute?: string
}>()

// Emits
const emit = defineEmits<{
  delete: [item: any]
}>()

// Simple global filter only
const filters = ref({
  global: { value: null as string | null, matchMode: FilterMatchMode.CONTAINS }
})

// Helper functions
const formatValue = (value: any, column: TableColumn) => {
  if (column.format) {
    return column.format(value)
  }
  return value || ''
}

const formatCurrency = (value: number | string) => {
  const num = typeof value === 'string' ? parseFloat(value.replace(/,/g, '')) : value
  return num.toLocaleString('en-US', { style: 'currency', currency: 'USD' })
}

const formatDate = (value: string | Date) => {
  if (!value) return ''
  const date = value instanceof Date ? value : new Date(value)
  return date.toLocaleDateString('en-US', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const getBadgeSeverity = (value: string, badgeClasses?: Record<string, string>): 'secondary' | 'success' | 'info' | 'warn' | 'danger' | 'contrast' | undefined => {
  if (!badgeClasses) return undefined
  const severity = badgeClasses[value]
  if (['secondary', 'success', 'info', 'warn', 'danger', 'contrast'].includes(severity)) {
    return severity as 'secondary' | 'success' | 'info' | 'warn' | 'danger' | 'contrast'
  }
  return undefined
}

const getActionRoute = (action: 'view' | 'edit', item: any) => {
  if (!props.baseRoute) return '#'
  
  const route = props.baseRoute
  if (action === 'view') {
    return `${route}/${item.id}`
  }
  return `${route}/${item.id}/edit`
}

const clearFilter = () => {
  filters.value = {
    global: { value: null as string | null, matchMode: FilterMatchMode.CONTAINS }
  }
}

const onDelete = (item: any) => {
  emit('delete', item)
}

// Reactive screen width for responsive placeholder
const screenWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)

// Update screen width on resize
if (typeof window !== 'undefined') {
  const updateScreenWidth = () => {
    screenWidth.value = window.innerWidth
  }
  window.addEventListener('resize', updateScreenWidth)
  onMounted(() => {
    updateScreenWidth()
  })
}

// Generate dynamic responsive placeholder based on route or entity type
const getPlaceholder = computed(() => {
  if (props.searchPlaceholder) {
    return props.searchPlaceholder
  }
  
  // Try to detect entity type from route or data
  const currentRoute = typeof window !== 'undefined' ? window.location?.pathname : ''
  const isMobile = screenWidth.value < 640
  
  if (currentRoute && currentRoute.includes('/users')) {
    // Responsive placeholder for users
    return isMobile ? 'Search users...' : 'Search by name, email, role...'
  } else if (currentRoute && currentRoute.includes('/clients')) {
    // Responsive placeholder for clients
    return isMobile ? 'Search clients...' : 'Search by name, email, company, document...'
  } else if (currentRoute && currentRoute.includes('/invoices')) {
    // Responsive placeholder for invoices
    return isMobile ? 'Search invoices...' : 'Search by number, client, status...'
  }
  
  // Fallback to generic search
  return isMobile ? 'Search...' : 'Search records...'
})


// Trim whitespace from search input
watch(() => filters.value.global.value, (newValue) => {
  if (newValue && typeof newValue === 'string') {
    const trimmedValue = (newValue as string).trim()
    if (trimmedValue !== newValue) {
      // Use trimmed value or null if empty
      filters.value.global.value = trimmedValue.length > 0 ? trimmedValue : null
    }
  }
})

</script>

<style scoped>
.datatable-container {
  width: calc(100vw - 2rem);
  max-width: calc(100vw - 2rem);
  margin-left: calc(-50vw + 50%);
  margin-right: 1rem;
  margin-left: calc(-50vw + 50% + 1rem);
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

/* Professional table styling */
:deep(.p-datatable) {
  width: 100%;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

:deep(.p-datatable-wrapper) {
  width: 100%;
  overflow-x: auto;
}

/* Professional header styling */
:deep(.p-datatable .p-datatable-header) {
  background: #f9fafb;
  border: none;
  border-bottom: 1px solid #e5e7eb;
  padding: 1.5rem 2rem;
  border-radius: 8px 8px 0 0;
}

:deep(.p-datatable .p-datatable-thead > tr > th) {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 600;
  color: #374151;
  padding: 1rem;
  white-space: nowrap;
}

:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  white-space: nowrap;
}

:deep(.p-datatable .p-datatable-tbody > tr:hover) {
  background: #f9fafb;
}

/* Professional pagination styling */
:deep(.p-paginator) {
  border: none;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
  padding: 1.5rem 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  border-radius: 0 0 8px 8px;
}

:deep(.p-paginator .p-paginator-current) {
  background: transparent;
  border: 0;
  color: #6b7280;
  font-weight: 500;
  order: -1;
  width: 100%;
  text-align: center;
  margin-bottom: 0.5rem;
}

/* Pagination buttons styling */
:deep(.p-paginator .p-paginator-page) {
  background: white;
  border: 1px solid #d1d5db;
  color: #374151;
  padding: 0.5rem 0.75rem;
  margin: 0 0.125rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

:deep(.p-paginator .p-paginator-page:hover) {
  background: #f3f4f6;
  border-color: #9ca3af;
}

:deep(.p-paginator .p-paginator-page.p-highlight) {
  background: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

:deep(.p-paginator .p-paginator-first),
:deep(.p-paginator .p-paginator-prev),
:deep(.p-paginator .p-paginator-next),
:deep(.p-paginator .p-paginator-last) {
  background: white;
  border: 1px solid #d1d5db;
  color: #6b7280;
  padding: 0.5rem 0.75rem;
  margin: 0 0.125rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

:deep(.p-paginator .p-paginator-first:hover),
:deep(.p-paginator .p-paginator-prev:hover),
:deep(.p-paginator .p-paginator-next:hover),
:deep(.p-paginator .p-paginator-last:hover) {
  background: #f3f4f6;
  border-color: #9ca3af;
  color: #374151;
}

:deep(.p-paginator .p-paginator-first:disabled),
:deep(.p-paginator .p-paginator-prev:disabled),
:deep(.p-paginator .p-paginator-next:disabled),
:deep(.p-paginator .p-paginator-last:disabled) {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Rows per page dropdown */
:deep(.p-paginator .p-dropdown) {
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
}

:deep(.p-paginator .p-dropdown:hover) {
  border-color: #9ca3af;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .datatable-container {
    width: calc(100vw - 1rem);
    margin-left: calc(-50vw + 50% + 0.5rem);
    border-radius: 6px;
  }
  
  :deep(.p-datatable .p-datatable-header) {
    padding: 1rem;
  }
  
  :deep(.p-paginator) {
    padding: 1rem;
    flex-direction: column;
    gap: 1rem;
    justify-content: center;
  }
  
  :deep(.p-paginator .p-paginator-current) {
    order: 0;
    margin-bottom: 1rem;
  }
  
  .table-header {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  :deep(.p-datatable .p-datatable-thead > tr > th),
  :deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 0.75rem 0.5rem;
    font-size: 0.875rem;
  }
}

@media (max-width: 640px) {
  .datatable-container {
    width: calc(100vw - 0.5rem);
    margin-left: calc(-50vw + 50% + 0.25rem);
    border-radius: 4px;
  }
  
  :deep(.p-datatable .p-datatable-thead > tr > th),
  :deep(.p-datatable .p-datatable-tbody > tr > td) {
    padding: 0.5rem 0.25rem;
    font-size: 0.8rem;
  }
}

/* Large screens - more generous margins */
@media (min-width: 1280px) {
  .datatable-container {
    width: calc(100vw - 4rem);
    margin-left: calc(-50vw + 50% + 2rem);
  }
}

@media (min-width: 1536px) {
  .datatable-container {
    width: calc(100vw - 6rem);
    margin-left: calc(-50vw + 50% + 3rem);
  }
}
</style>