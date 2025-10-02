<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Create Invoice
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Create a new invoice for your client
          </p>
        </div>
        <router-link
          to="/app/invoices"
          class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
        >
          <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
          Back to Invoices
        </router-link>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Invoice Number -->
          <div>
            <label for="number" class="block text-sm font-medium text-gray-700 mb-2">
              Invoice Number *
            </label>
            <input
              v-model="form.number"
              id="number"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="INV-001"
            />
          </div>

          <!-- Client -->
          <div>
            <label for="client" class="block text-sm font-medium text-gray-700 mb-2">
              Client *
            </label>
            <select
              v-model="form.clientId"
              id="client"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">Select client</option>
              <option v-for="client in clients" :key="client.id" :value="client.id">
                {{ client.name }}
              </option>
            </select>
          </div>

          <!-- Invoice Date -->
          <div>
            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
              Invoice Date *
            </label>
            <input
              v-model="form.date"
              id="date"
              type="date"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            />
          </div>

          <!-- Due Date -->
          <div>
            <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-2">
              Due Date *
            </label>
            <input
              v-model="form.dueDate"
              id="dueDate"
              type="date"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            />
          </div>

          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
              Status
            </label>
            <select
              v-model="form.status"
              id="status"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="draft">Draft</option>
              <option value="sent">Sent</option>
              <option value="paid">Paid</option>
              <option value="overdue">Overdue</option>
            </select>
          </div>
        </div>

        <!-- Line Items -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Line Items</h3>
            <button
              type="button"
              @click="addLineItem"
              class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              <Icon icon="mdi:plus" class="w-4 h-4 mr-2" />
              Add Item
            </button>
          </div>

          <div class="overflow-hidden border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Description
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Quantity
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Rate
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Amount
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item, index) in form.lineItems" :key="index">
                  <td class="px-4 py-3">
                    <input
                      v-model="item.description"
                      type="text"
                      class="block w-full border-0 p-0 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm"
                      placeholder="Item description"
                    />
                  </td>
                  <td class="px-4 py-3">
                    <input
                      v-model.number="item.quantity"
                      type="number"
                      min="0"
                      step="0.01"
                      class="block w-full border-0 p-0 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm"
                      placeholder="0"
                    />
                  </td>
                  <td class="px-4 py-3">
                    <input
                      v-model.number="item.rate"
                      type="number"
                      min="0"
                      step="0.01"
                      class="block w-full border-0 p-0 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm"
                      placeholder="0.00"
                    />
                  </td>
                  <td class="px-4 py-3 text-gray-900 font-medium">
                    ${{ getLineItemTotal(item) }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button
                      type="button"
                      @click="removeLineItem(index)"
                      class="text-red-600 hover:text-red-900"
                    >
                      <Icon icon="mdi:delete" class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Totals -->
        <div class="border-t border-gray-200 pt-6">
          <div class="flex justify-end">
            <div class="w-64 space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal:</span>
                <span class="font-medium">${{ subtotal }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Tax ({{ form.taxRate }}%):</span>
                <span class="font-medium">${{ taxAmount }}</span>
              </div>
              <div class="flex justify-between text-lg font-semibold border-t border-gray-200 pt-2">
                <span>Total:</span>
                <span>${{ total }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tax Rate -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="taxRate" class="block text-sm font-medium text-gray-700 mb-2">
              Tax Rate (%)
            </label>
            <input
              v-model.number="form.taxRate"
              id="taxRate"
              type="number"
              min="0"
              max="100"
              step="0.01"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="0.00"
            />
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
            Notes
          </label>
          <textarea
            v-model="form.notes"
            id="notes"
            rows="3"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Additional notes for the invoice"
          />
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <router-link
            to="/app/invoices"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
          >
            <Icon v-if="isSubmitting" icon="mdi:loading" class="w-4 h-4 mr-2 animate-spin" />
            <Icon v-else icon="mdi:content-save" class="w-4 h-4 mr-2" />
            {{ isSubmitting ? 'Creating...' : 'Create Invoice' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'

const router = useRouter()
const route = useRoute()
const isSubmitting = ref(false)

const form = ref({
  number: '',
  clientId: '',
  date: new Date().toISOString().split('T')[0],
  dueDate: '',
  status: 'draft',
  taxRate: 0,
  notes: '',
  lineItems: [
    { description: 'Web Development Services', quantity: 1, rate: 0 }
  ]
})

// Sample clients with comprehensive data
const clients = ref([
  { 
    id: 1, 
    name: 'John Smith', 
    email: 'john.smith@email.com',
    company: 'Smith Consulting LLC',
    documentType: 'passport',
    documentNumber: 'P123456789'
  },
  { 
    id: 2, 
    name: 'Sarah Johnson', 
    email: 'sarah.johnson@techcorp.com',
    company: 'TechCorp Solutions',
    documentType: 'driver_license',
    documentNumber: 'DL987654321'
  },
  { 
    id: 3, 
    name: 'Michael Brown', 
    email: 'michael.brown@startup.io',
    company: 'StartupXYZ Inc',
    documentType: 'national_id',
    documentNumber: 'ID456789123'
  }
])

const subtotal = computed(() => {
  return form.value.lineItems.reduce((sum, item) => {
    return sum + (item.quantity * item.rate)
  }, 0).toFixed(2)
})

const taxAmount = computed(() => {
  return (parseFloat(subtotal.value) * (form.value.taxRate / 100)).toFixed(2)
})

const total = computed(() => {
  return (parseFloat(subtotal.value) + parseFloat(taxAmount.value)).toFixed(2)
})

const getLineItemTotal = (item: any) => {
  return (item.quantity * item.rate).toFixed(2)
}

const addLineItem = () => {
  form.value.lineItems.push({
    description: '',
    quantity: 1,
    rate: 0
  })
}

const removeLineItem = (index: number) => {
  if (form.value.lineItems.length > 1) {
    form.value.lineItems.splice(index, 1)
  }
}

const handleSubmit = async () => {
  isSubmitting.value = true
  
  try {
    // TODO: Implement API call
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Redirect to invoice list
    router.push('/app/invoices')
  } catch (error) {
    console.error('Error creating invoice:', error)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  // Generate invoice number
  form.value.number = `INV-${String(Date.now()).slice(-6)}`
  
  // Set due date to 30 days from now
  const dueDate = new Date()
  dueDate.setDate(dueDate.getDate() + 30)
  form.value.dueDate = dueDate.toISOString().split('T')[0]
  
  // Pre-select client if passed as query param
  const clientId = route.query.client
  if (clientId) {
    form.value.clientId = String(clientId)
  }
})
</script>