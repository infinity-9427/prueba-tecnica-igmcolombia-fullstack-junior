<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            Edit User
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            Update user information and permissions
          </p>
        </div>
        <router-link
          :to="`/app/users/${userId}`"
          class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
        >
          <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
          Back to User
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
              Full Name *
            </label>
            <input
              v-model="form.name"
              id="name"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter full name"
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

          <!-- Role -->
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
              Role *
            </label>
            <select
              v-model="form.role"
              id="role"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            >
              <option value="">Select role</option>
              <option value="user">User</option>
              <option value="manager">Manager</option>
              <option value="admin">Admin</option>
            </select>
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
              <option value="pending">Pending</option>
            </select>
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

          <!-- Department -->
          <div>
            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
              Department
            </label>
            <input
              v-model="form.department"
              id="department"
              type="text"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              placeholder="Enter department"
            />
          </div>
        </div>

        <!-- Password Reset Section -->
        <div class="border-t border-gray-200 pt-6">
          <div class="flex items-center mb-4">
            <input
              v-model="resetPassword"
              id="resetPassword"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
            <label for="resetPassword" class="ml-2 text-sm font-medium text-gray-700">
              Reset password
            </label>
          </div>
          
          <div v-if="resetPassword" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- New Password -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                New Password *
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="block w-full pr-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  placeholder="Enter new password"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                >
                  <Icon :icon="showPassword ? 'mdi:eye' : 'mdi:eye-off'" class="h-5 w-5" />
                </button>
              </div>
            </div>

            <!-- Confirm Password -->
            <div>
              <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                Confirm Password *
              </label>
              <div class="relative">
                <input
                  v-model="form.confirmPassword"
                  id="confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  class="block w-full pr-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  placeholder="Confirm new password"
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                >
                  <Icon :icon="showConfirmPassword ? 'mdi:eye' : 'mdi:eye-off'" class="h-5 w-5" />
                </button>
              </div>
            </div>
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
            placeholder="Additional notes about the user"
          />
        </div>

        <!-- Permissions -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-3">
            Permissions
          </label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label v-for="permission in permissions" :key="permission.key" class="flex items-center">
              <input
                v-model="form.permissions"
                :value="permission.key"
                type="checkbox"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
              />
              <span class="ml-2 text-sm text-gray-700">{{ permission.label }}</span>
            </label>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
          <router-link
            :to="`/app/users/${userId}`"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="isSubmitting || (resetPassword && !isPasswordValid)"
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

const route = useRoute()
const router = useRouter()
const userId = route.params.id
const isSubmitting = ref(false)
const resetPassword = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = ref({
  firstName: '',
  lastName: '',
  name: '',
  email: '',
  role: '',
  status: 'active',
  phone: '',
  department: '',
  password: '',
  confirmPassword: '',
  notes: '',
  permissions: [] as string[]
})

const permissions = [
  { key: 'view_invoices', label: 'View Invoices' },
  { key: 'create_invoices', label: 'Create Invoices' },
  { key: 'edit_invoices', label: 'Edit Invoices' },
  { key: 'delete_invoices', label: 'Delete Invoices' },
  { key: 'view_clients', label: 'View Clients' },
  { key: 'manage_clients', label: 'Manage Clients' },
  { key: 'view_users', label: 'View Users' },
  { key: 'manage_users', label: 'Manage Users' },
  { key: 'view_reports', label: 'View Reports' },
  { key: 'system_settings', label: 'System Settings' }
]

const isPasswordValid = computed(() => {
  if (!resetPassword.value) return true
  return form.value.password && 
         form.value.confirmPassword && 
         form.value.password === form.value.confirmPassword
})

const handleSubmit = async () => {
  if (resetPassword.value && form.value.password !== form.value.confirmPassword) {
    alert('Passwords do not match')
    return
  }

  isSubmitting.value = true
  
  try {
    // TODO: Implement API call
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Redirect to user detail
    router.push(`/app/users/${userId}`)
  } catch (error) {
    console.error('Error updating user:', error)
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  // TODO: Fetch user data by ID and populate form
  
  // Sample data with comprehensive user structure
  form.value = {
    firstName: 'John',
    lastName: 'Smith',
    name: 'John Smith',
    email: 'john.smith@invoiceapp.com',
    role: 'admin',
    status: 'active',
    phone: '+1-555-0201',
    department: 'Administration',
    password: '',
    confirmPassword: '',
    notes: 'System administrator with full access privileges. Responsible for user management and system configuration.',
    permissions: ['view_invoices', 'create_invoices', 'edit_invoices', 'delete_invoices', 'view_clients', 'manage_clients', 'view_users', 'manage_users', 'view_reports', 'system_settings']
  }
})
</script>