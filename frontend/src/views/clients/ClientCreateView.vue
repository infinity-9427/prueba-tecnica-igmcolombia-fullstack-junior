<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Create Client
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Add a new client to your system
          </p>
        </div>
        <router-link
          to="/app/clients"
          class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
        >
          <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
          Back to Clients
        </router-link>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg p-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
              Client Name *
            </label>
            <input
              v-model="form.name"
              id="name"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter client name"
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
            to="/app/clients"
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
            {{ isSubmitting ? 'Creating...' : 'Create Client' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

const router = useRouter()
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
  notes: ''
})

const handleSubmit = async () => {
  isSubmitting.value = true
  
  try {
    // TODO: Implement API call
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Redirect to client list
    router.push('/app/clients')
  } catch (error) {
    console.error('Error creating client:', error)
  } finally {
    isSubmitting.value = false
  }
}
</script>