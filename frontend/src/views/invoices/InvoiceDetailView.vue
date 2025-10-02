<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Invoice {{ invoice?.number }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Invoice details and payment information
          </p>
        </div>
        <div class="flex space-x-3">
          <button class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700">
            <Icon icon="mdi:download" class="w-4 h-4 mr-2" />
            Download PDF
          </button>
          <router-link
            :to="`/app/invoices/${invoice?.id}/edit`"
            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
          >
            <Icon icon="mdi:pencil" class="w-4 h-4 mr-2" />
            Edit Invoice
          </router-link>
          <router-link
            to="/app/invoices"
            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
          >
            <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
            Back to Invoices
          </router-link>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Invoice Content -->
      <div class="lg:col-span-2">
        <div class="bg-white shadow-sm rounded-lg p-8">
          <!-- Invoice Header -->
          <div class="flex justify-between items-start mb-8">
            <div>
              <div class="flex items-center mb-4">
                <Icon icon="mdi:receipt" class="h-8 w-8 text-blue-600 mr-2" />
                <span class="text-2xl font-bold text-gray-900">Invoice App</span>
              </div>
              <div class="text-sm text-gray-600">
                <p>123 Business Street</p>
                <p>New York, NY 10001</p>
                <p>contact@invoiceapp.com</p>
              </div>
            </div>
            <div class="text-right">
              <h1 class="text-3xl font-bold text-gray-900">INVOICE</h1>
              <p class="text-lg font-semibold text-gray-700 mt-2">{{ invoice?.number }}</p>
              <span :class="getStatusBadgeClass(invoice?.status)" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full mt-2">
                {{ invoice?.status?.toUpperCase() }}
              </span>
            </div>
          </div>

          <!-- Bill To / Invoice Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Bill To</h3>
              <div class="text-gray-900">
                <p class="font-semibold">{{ client?.name }}</p>
                <p>{{ client?.email }}</p>
                <p v-if="client?.phone">{{ client?.phone }}</p>
                <div v-if="client?.address" class="mt-2">
                  <p>{{ client?.address }}</p>
                  <p v-if="client?.city || client?.state || client?.zip">
                    {{ client?.city }}{{ client?.city && client?.state ? ', ' : '' }}{{ client?.state }} {{ client?.zip }}
                  </p>
                </div>
              </div>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Invoice Details</h3>
              <div class="space-y-1">
                <div class="flex justify-between">
                  <span class="text-gray-600">Invoice Date:</span>
                  <span class="text-gray-900">{{ invoice?.issueDate }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Due Date:</span>
                  <span class="text-gray-900">{{ invoice?.dueDate }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Terms:</span>
                  <span class="text-gray-900">Net 30</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Line Items -->
          <div class="mb-8">
            <table class="w-full">
              <thead>
                <tr class="border-b-2 border-gray-200">
                  <th class="text-left py-2 font-semibold text-gray-700">Description</th>
                  <th class="text-right py-2 font-semibold text-gray-700">Qty</th>
                  <th class="text-right py-2 font-semibold text-gray-700">Rate</th>
                  <th class="text-right py-2 font-semibold text-gray-700">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in invoice?.lineItems" :key="index" class="border-b border-gray-100">
                  <td class="py-3 text-gray-900">
                    <div class="font-medium">{{ item.name }}</div>
                    <div class="text-sm text-gray-500">{{ item.description }}</div>
                  </td>
                  <td class="py-3 text-right text-gray-900">{{ item.quantity }}</td>
                  <td class="py-3 text-right text-gray-900">${{ item.unitPrice.toFixed(2) }}</td>
                  <td class="py-3 text-right text-gray-900">${{ (item.quantity * item.unitPrice).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totals -->
          <div class="flex justify-end mb-8">
            <div class="w-64">
              <div class="flex justify-between py-2">
                <span class="text-gray-600">Subtotal:</span>
                <span class="text-gray-900">${{ subtotal }}</span>
              </div>
              <div class="flex justify-between py-2">
                <span class="text-gray-600">Tax ({{ invoice?.taxRate }}%):</span>
                <span class="text-gray-900">${{ taxAmount }}</span>
              </div>
              <div class="flex justify-between py-2 border-t-2 border-gray-200 font-bold text-lg">
                <span>Total:</span>
                <span>${{ total }}</span>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="invoice?.notes" class="border-t border-gray-200 pt-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Notes</h3>
            <p class="text-gray-700">{{ invoice?.notes }}</p>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Payment Status -->
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Status</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Total Amount:</span>
              <span class="text-sm font-medium text-gray-900">${{ total }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Amount Paid:</span>
              <span class="text-sm font-medium text-gray-900">${{ invoice?.amountPaid || '0.00' }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-3">
              <span class="text-sm font-medium text-gray-600">Balance Due:</span>
              <span class="text-sm font-medium text-gray-900">${{ balanceDue }}</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
          <div class="space-y-3">
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
              <Icon icon="mdi:email" class="w-4 h-4 mr-2" />
              Send Invoice
            </button>
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              <Icon icon="mdi:cash" class="w-4 h-4 mr-2" />
              Record Payment
            </button>
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              <Icon icon="mdi:content-duplicate" class="w-4 h-4 mr-2" />
              Duplicate Invoice
            </button>
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
              <Icon icon="mdi:delete" class="w-4 h-4 mr-2" />
              Delete Invoice
            </button>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
          <div class="space-y-3">
            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3">
              <Icon :icon="activity.icon" :class="activity.iconColor" class="w-5 h-5 mt-0.5" />
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900">{{ activity.description }}</p>
                <p class="text-xs text-gray-500">{{ activity.timestamp }}</p>
              </div>
            </div>
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
const invoiceId = route.params.id

// Sample invoice data with comprehensive structure
const invoice = ref({
  id: 1,
  number: 'INV-2024-001',
  clientId: 1,
  description: 'Web development services for corporate website redesign',
  notes: 'Payment due within 30 days. Late fees may apply after due date. Thank you for your business!',
  issueDate: '2024-01-15',
  dueDate: '2024-02-15',
  status: 'pending',
  taxRate: 19,
  amountPaid: 500.00,
  lineItems: [
    { 
      name: 'Frontend Development', 
      description: 'React.js frontend development with responsive design', 
      quantity: 40, 
      unitPrice: 25.00,
      tax: 19,
      total: 1000.00
    },
    { 
      name: 'Backend Integration', 
      description: 'API development and database integration', 
      quantity: 20, 
      unitPrice: 35.00,
      tax: 19,
      total: 700.00
    },
    { 
      name: 'Testing & QA', 
      description: 'Comprehensive testing and quality assurance', 
      quantity: 10, 
      unitPrice: 45.00,
      tax: 19,
      total: 450.00
    }
  ],
  attachments: [
    { name: 'invoice_001.pdf', type: 'pdf', url: '/attachments/invoice_001.pdf', size: '156 KB' },
    { name: 'contract_agreement.pdf', type: 'pdf', url: '/attachments/contract_agreement.pdf', size: '89 KB' }
  ]
})

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
  country: 'United States'
})

const recentActivity = ref([
  { id: 1, description: 'Invoice created', timestamp: '2 days ago', icon: 'mdi:plus-circle', iconColor: 'text-green-500' },
  { id: 2, description: 'Invoice sent to client', timestamp: '1 day ago', icon: 'mdi:email', iconColor: 'text-blue-500' },
  { id: 3, description: 'Client viewed invoice', timestamp: '4 hours ago', icon: 'mdi:eye', iconColor: 'text-yellow-500' }
])

const subtotal = computed(() => {
  if (!invoice.value?.lineItems) return '0.00'
  return invoice.value.lineItems.reduce((sum, item) => {
    return sum + (item.quantity * item.unitPrice)
  }, 0).toFixed(2)
})

const taxAmount = computed(() => {
  if (!invoice.value?.taxRate) return '0.00'
  return (parseFloat(subtotal.value) * (invoice.value.taxRate / 100)).toFixed(2)
})

const total = computed(() => {
  return (parseFloat(subtotal.value) + parseFloat(taxAmount.value)).toFixed(2)
})

const balanceDue = computed(() => {
  const totalAmount = parseFloat(total.value)
  const paidAmount = parseFloat(invoice.value?.amountPaid?.toString() || '0')
  return (totalAmount - paidAmount).toFixed(2)
})

const getStatusBadgeClass = (status: string) => {
  const classes = {
    paid: 'bg-green-100 text-green-800',
    sent: 'bg-blue-100 text-blue-800',
    overdue: 'bg-red-100 text-red-800',
    draft: 'bg-gray-100 text-gray-800'
  }
  return classes[status as keyof typeof classes] || classes.draft
}

onMounted(() => {
  // TODO: Fetch invoice data by ID
})
</script>