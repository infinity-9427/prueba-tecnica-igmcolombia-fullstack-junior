<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit Client
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Update client information
          </p>
        </div>
        <div class="flex space-x-3">
          <router-link
            :to="`/app/clients/${clientId}`"
            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
          >
            <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
            Back to Client
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- First Name -->
          <div>
            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
              First Name *
            </label>
            <input
              v-model="form.firstName"
              id="firstName"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter first name"
            />
          </div>

          <!-- Last Name -->
          <div>
            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
              Last Name *
            </label>
            <input
              v-model="form.lastName"
              id="lastName"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter last name"
            />
          </div>

          <!-- Document Type -->
          <div>
            <label for="documentType" class="block text-sm font-medium text-gray-700 mb-2">
              Document Type *
            </label>
            <select
              v-model="form.documentType"
              id="documentType"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">Select document type</option>
              <option value="passport">Passport</option>
              <option value="driver_license">Driver's License</option>
              <option value="national_id">National ID</option>
            </select>
          </div>

          <!-- Document Number -->
          <div>
            <label for="documentNumber" class="block text-sm font-medium text-gray-700 mb-2">
              Document Number *
            </label>
            <input
              v-model="form.documentNumber"
              id="documentNumber"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter document number"
            />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email Address *
            </label>
            <input
              v-model="form.email"
              id="email"
              type="email"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter email address"
            />
          </div>

          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
              Phone Number
            </label>
            <input
              v-model="form.phone"
              id="phone"
              type="tel"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter phone number"
            />
          </div>

          <!-- Company -->
          <div>
            <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
              Company
            </label>
            <input
              v-model="form.company"
              id="company"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter company name"
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
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Address -->
        <div>
          <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
            Address
          </label>
          <textarea
            v-model="form.address"
            id="address"
            rows="3"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            placeholder="Enter full address"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- City -->
          <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
              City
            </label>
            <input
              v-model="form.city"
              id="city"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter city"
            />
          </div>

          <!-- State -->
          <div>
            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
              State/Province
            </label>
            <input
              v-model="form.state"
              id="state"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter state"
            />
          </div>

          <!-- ZIP -->
          <div>
            <label for="zip" class="block text-sm font-medium text-gray-700 mb-2">
              ZIP/Postal Code
            </label>
            <input
              v-model="form.zip"
              id="zip"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter ZIP code"
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
            placeholder="Additional notes about the client"
          />
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <router-link
            :to="`/app/clients/${clientId}`"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
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
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

const route = useRoute()
const router = useRouter()
const clientId = route.params.id
const isSubmitting = ref(false)

const form = ref({
  firstName: '',
  lastName: '',
  name: '',
  documentType: '',
  documentNumber: '',
  email: '',
  phone: '',
  company: '',
  address: '',
  city: '',
  state: '',
  zip: '',
  notes: '',
  status: 'active'
})

const handleSubmit = async () => {
  isSubmitting.value = true
  
  try {
    // TODO: Implement API call
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Redirect to client detail
    router.push(`/app/clients/${clientId}`)
  } catch (error) {
    console.error('Error updating client:', error)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  // TODO: Fetch client data by ID and populate form
  
  // Sample data with comprehensive client structure
  form.value = {
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
    notes: 'Preferred client with excellent payment history. Specializes in corporate consulting services.',
    status: 'active'
  }
})
</script>