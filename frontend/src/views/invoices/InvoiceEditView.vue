<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Invoice
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Update invoice information and line items
          </p>
        </div>
        <div class="flex space-x-3">
          <router-link
            :to="`/app/invoices/${invoiceId}`"
            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
          >
            <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
            Back to Invoice
          </router-link>
        </div>
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

          <!-- Amount Paid -->
          <div>
            <label for="amountPaid" class="block text-sm font-medium text-gray-700 mb-2">
              Amount Paid
            </label>
            <input
              v-model.number="form.amountPaid"
              id="amountPaid"
              type="number"
              min="0"
              step="0.01"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="0.00"
            />
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
              <div class="flex justify-between text-sm text-green-600">
                <span>Amount Paid:</span>
                <span>${{ form.amountPaid.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm font-medium text-red-600">
                <span>Balance Due:</span>
                <span>${{ balanceDue }}</span>
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
            :to="`/app/invoices/${invoiceId}`"
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
            {{ isSubmitting ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

const route = useRoute()
const router = useRouter()
const invoiceId = route.params.id
const isSubmitting = ref(false)

const form = ref({
  number: '',
  clientId: '',
  date: '',
  dueDate: '',
  status: 'draft',
  taxRate: 0,
  amountPaid: 0,
  notes: '',
  lineItems: [
    { description: '', quantity: 1, rate: 0 }
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

const balanceDue = computed(() => {
  return (parseFloat(total.value) - form.value.amountPaid).toFixed(2)
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
    
    // Redirect to invoice detail
    router.push(`/app/invoices/${invoiceId}`)
  } catch (error) {
    console.error('Error updating invoice:', error)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  // TODO: Fetch invoice data by ID and populate form
  
  // Sample data with comprehensive structure
  form.value = {
    number: 'INV-2024-001',
    clientId: '1',
    date: '2024-01-15',
    dueDate: '2024-02-15',
    status: 'pending',
    taxRate: 19,
    amountPaid: 500.00,
    notes: 'Payment due within 30 days. Late fees may apply after due date.',
    lineItems: [
      { description: 'Frontend Development - React.js with responsive design', quantity: 40, rate: 25.00 },
      { description: 'Backend Integration - API development and database setup', quantity: 20, rate: 35.00 },
      { description: 'Testing & QA - Comprehensive testing and quality assurance', quantity: 10, rate: 45.00 }
    ]
  }
})
</script>