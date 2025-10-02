<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ client?.name }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Client details and invoice history
          </p>
        </div>
        <div class="flex space-x-3">
          <router-link
            :to="`/app/clients/${client?.id}/edit`"
            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
          >
            <Icon icon="mdi:pencil" class="w-4 h-4 mr-2" />
            Edit Client
          </router-link>
          <router-link
            to="/app/clients"
            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
          >
            <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
            Back to Clients
          </router-link>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Client Information -->
      <div class="lg:col-span-2">
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Client Information</h3>
          
          <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <dt class="text-sm font-medium text-gray-500">Name</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client?.name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Email</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client?.email }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Phone</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client?.phone || 'Not provided' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Company</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client?.company || 'Not provided' }}</dd>
            </div>
            <div class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Address</dt>
              <dd class="mt-1 text-sm text-gray-900">
                {{ client?.address || 'Not provided' }}
                <span v-if="client?.city || client?.state || client?.zip">
                  <br>{{ client?.city }}{{ client?.city && client?.state ? ', ' : '' }}{{ client?.state }} {{ client?.zip }}
                </span>
              </dd>
            </div>
            <div class="sm:col-span-2" v-if="client?.notes">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ client?.notes }}</dd>
            </div>
          </dl>
        </div>

        <!-- Invoice History -->
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Invoice History</h3>
            <router-link
              :to="`/app/invoices/create?client=${client?.id}`"
              class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500"
            >
              <Icon icon="mdi:plus" class="w-4 h-4 mr-1" />
              Create Invoice
            </router-link>
          </div>
          
          <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Invoice #
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="invoice in invoices" :key="invoice.id">
                  <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ invoice.number }}
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ invoice.date }}
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${{ invoice.amount }}
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(invoice.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                      {{ invoice.status }}
                    </span>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <router-link
                      :to="`/app/invoices/${invoice.id}`"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Quick Stats -->
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
          <dl class="space-y-3">
            <div>
              <dt class="text-sm font-medium text-gray-500">Total Invoices</dt>
              <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ invoices.length }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
              <dd class="mt-1 text-2xl font-semibold text-gray-900">${{ totalAmount }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Outstanding</dt>
              <dd class="mt-1 text-2xl font-semibold text-red-600">${{ outstandingAmount }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Status</dt>
              <dd class="mt-1">
                <span :class="getStatusBadgeClass(client?.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  {{ client?.status }}
                </span>
              </dd>
            </div>
          </dl>
        </div>

        <!-- Actions -->
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
          <div class="space-y-3">
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              <Icon icon="mdi:email" class="w-4 h-4 mr-2" />
              Send Email
            </button>
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              <Icon icon="mdi:file-export" class="w-4 h-4 mr-2" />
              Export Data
            </button>
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
              <Icon icon="mdi:account-off" class="w-4 h-4 mr-2" />
              Deactivate Client
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'

const route = useRoute()
const clientId = route.params.id

// Sample client data with comprehensive structure
const client = ref({
  id: 1,
  firstName: 'John',
  lastName: 'Smith',
  name: 'John Smith',
  documentType: 'passport',
  documentNumber: 'P123456789',
  email: 'john.smith@email.com',
  phone: '+1-555-0123',
  company: 'Smith Consulting LLC',
  address: '123 Business Avenue, Suite 400',
  city: 'New York',
  state: 'NY',
  zip: '10001',
  country: 'United States',
  notes: 'Preferred client with excellent payment history. Specializes in corporate consulting services.',
  status: 'active',
  createdAt: '2023-06-01',
  lastContactDate: '2024-01-15',
  businessType: 'Consulting',
  taxId: 'TAX123456789'
})

// Sample invoice data for this client with comprehensive structure
const invoices = ref([
  { 
    id: 1, 
    number: 'INV-2024-001', 
    description: 'Web development services',
    date: '2024-01-15', 
    dueDate: '2024-02-15',
    amount: 1250.00, 
    status: 'paid',
    items: [
      { name: 'Frontend Development', quantity: 40, unitPrice: 25.00 },
      { name: 'Backend Integration', quantity: 20, unitPrice: 35.00 }
    ]
  },
  { 
    id: 5, 
    number: 'INV-2024-005', 
    description: 'Mobile app development',
    date: '2024-01-10', 
    dueDate: '2024-02-10',
    amount: 2850.00, 
    status: 'pending',
    items: [
      { name: 'Mobile App Development', quantity: 80, unitPrice: 30.00 },
      { name: 'Quality Assurance', quantity: 15, unitPrice: 45.00 }
    ]
  },
  { 
    id: 8, 
    number: 'INV-2024-008', 
    description: 'Consulting services',
    date: '2024-01-05', 
    dueDate: '2024-02-05',
    amount: 4200.00, 
    status: 'draft',
    items: [
      { name: 'Strategic Consulting', quantity: 100, unitPrice: 40.00 },
      { name: 'Implementation Support', quantity: 10, unitPrice: 80.00 }
    ]
  }
])

const totalAmount = computed(() => {
  return invoices.value.reduce((sum, invoice) => {
    return sum + invoice.amount
  }, 0).toLocaleString()
})

const outstandingAmount = computed(() => {
  return invoices.value
    .filter(invoice => invoice.status !== 'paid')
    .reduce((sum, invoice) => {
      return sum + invoice.amount
    }, 0).toLocaleString()
})

const getStatusBadgeClass = (status: string) => {
  const classes = {
    paid: 'bg-green-100 text-green-800',
    pending: 'bg-yellow-100 text-yellow-800',
    sent: 'bg-blue-100 text-blue-800',
    draft: 'bg-gray-100 text-gray-800',
    overdue: 'bg-red-100 text-red-800',
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800'
  }
  return classes[status as keyof typeof classes] || classes.active
}

onMounted(() => {
  // TODO: Fetch client data by ID
})
</script>