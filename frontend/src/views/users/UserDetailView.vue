<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
            {{ user?.name }}
          </h2>
          <p class="mt-1 text-sm text-gray-500">
            User details and activity overview
          </p>
        </div>
        <div class="flex space-x-3">
          <router-link
            :to="`/app/users/${user?.id}/edit`"
            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
          >
            <Icon icon="mdi:pencil" class="w-4 h-4 mr-2" />
            Edit User
          </router-link>
          <router-link
            to="/app/users"
            class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
          >
            <Icon icon="mdi:arrow-left" class="w-4 h-4 mr-2" />
            Back to Users
          </router-link>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- User Information -->
      <div class="lg:col-span-2">
        <div class="bg-white shadow-sm rounded-lg p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">User Information</h3>
          
          <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <dt class="text-sm font-medium text-gray-500">Name</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.name }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Email</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.email }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Role</dt>
              <dd class="mt-1">
                <span :class="getRoleBadgeClass(user?.role)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  {{ user?.role }}
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Status</dt>
              <dd class="mt-1">
                <span :class="getStatusBadgeClass(user?.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  {{ user?.status }}
                </span>
              </dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Phone</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.phone || 'Not provided' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Department</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.department || 'Not specified' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Last Login</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.lastLogin || 'Never' }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Created</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.createdAt }}</dd>
            </div>
            <div class="sm:col-span-2" v-if="user?.notes">
              <dt class="text-sm font-medium text-gray-500">Notes</dt>
              <dd class="mt-1 text-sm text-gray-900">{{ user?.notes }}</dd>
            </div>
          </dl>
        </div>

        <!-- Permissions -->
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="permission in userPermissions" :key="permission.key" class="flex items-center">
              <Icon 
                :icon="permission.granted ? 'mdi:check-circle' : 'mdi:close-circle'" 
                :class="permission.granted ? 'text-green-500' : 'text-red-500'"
                class="w-5 h-5 mr-2"
              />
              <span class="text-sm text-gray-700">{{ permission.label }}</span>
            </div>
          </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
          <div class="flow-root">
            <ul class="-mb-8">
              <li v-for="(activity, index) in recentActivity" :key="activity.id">
                <div class="relative pb-8" :class="{ 'pb-0': index === recentActivity.length - 1 }">
                  <span v-if="index !== recentActivity.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                  <div class="relative flex space-x-3">
                    <div>
                      <span :class="getActivityIconClass(activity.type)" class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                        <Icon :icon="getActivityIcon(activity.type)" class="w-4 h-4 text-white" />
                      </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div>
                        <p class="text-sm text-gray-500">{{ activity.description }}</p>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-gray-500">
                        {{ activity.timestamp }}
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
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
              <dt class="text-sm font-medium text-gray-500">Invoices Created</dt>
              <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.invoicesCreated }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Clients Managed</dt>
              <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.clientsManaged }}</dd>
            </div>
            <div>
              <dt class="text-sm font-medium text-gray-500">Login Sessions</dt>
              <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ stats.loginSessions }}</dd>
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
              <Icon icon="mdi:key" class="w-4 h-4 mr-2" />
              Reset Password
            </button>
            <button class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              <Icon icon="mdi:file-export" class="w-4 h-4 mr-2" />
              Export Data
            </button>
            <button 
              v-if="user?.status === 'active'"
              class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50"
            >
              <Icon icon="mdi:account-off" class="w-4 h-4 mr-2" />
              Deactivate User
            </button>
            <button 
              v-else
              class="w-full inline-flex justify-center items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50"
            >
              <Icon icon="mdi:account-check" class="w-4 h-4 mr-2" />
              Activate User
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
const userId = route.params.id

// Sample user data with comprehensive structure
const user = ref({
  id: 1,
  firstName: 'John',
  lastName: 'Smith',
  name: 'John Smith',
  email: 'john.smith@invoiceapp.com',
  phone: '+1-555-0201',
  department: 'Administration',
  role: 'admin',
  status: 'active',
  lastLogin: '2024-01-15',
  createdAt: '2023-06-01',
  notes: 'System administrator with full access privileges. Responsible for user management and system configuration.',
  avatar: null,
  timezone: 'America/New_York',
  language: 'English',
  loginCount: 245,
  lastLoginIp: '192.168.1.100',
  twoFactorEnabled: true,
  accountLocked: false
})

const stats = ref({
  invoicesCreated: 89,
  clientsManaged: 15,
  loginSessions: 245,
  lastActivityDays: 2,
  totalWorkingHours: 1240,
  completedTasks: 156
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

// Sample user permissions (admin has all)
const userPermissionKeys = ['view_invoices', 'create_invoices', 'edit_invoices', 'delete_invoices', 'view_clients', 'manage_clients', 'view_users', 'manage_users', 'view_reports', 'system_settings']

const userPermissions = computed(() => {
  return permissions.map(permission => ({
    ...permission,
    granted: userPermissionKeys.includes(permission.key)
  }))
})

const recentActivity = ref([
  { id: 1, type: 'login', description: 'Logged into the system', timestamp: '2 hours ago' },
  { id: 2, type: 'invoice', description: 'Created invoice INV-045', timestamp: '4 hours ago' },
  { id: 3, type: 'client', description: 'Updated client information for Acme Corp', timestamp: '1 day ago' },
  { id: 4, type: 'login', description: 'Logged into the system', timestamp: '2 days ago' }
])

const getStatusBadgeClass = (status: string) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800'
  }
  return classes[status as keyof typeof classes] || classes.active
}

const getRoleBadgeClass = (role: string) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    manager: 'bg-blue-100 text-blue-800',
    user: 'bg-gray-100 text-gray-800'
  }
  return classes[role as keyof typeof classes] || classes.user
}

const getActivityIconClass = (type: string) => {
  const classes = {
    login: 'bg-green-500',
    invoice: 'bg-blue-500',
    client: 'bg-yellow-500',
    system: 'bg-purple-500'
  }
  return classes[type as keyof typeof classes] || classes.system
}

const getActivityIcon = (type: string) => {
  const icons = {
    login: 'mdi:login',
    invoice: 'mdi:receipt',
    client: 'mdi:account-group',
    system: 'mdi:cog'
  }
  return icons[type as keyof typeof icons] || icons.system
}

onMounted(() => {
  // TODO: Fetch user data by ID
})
</script>