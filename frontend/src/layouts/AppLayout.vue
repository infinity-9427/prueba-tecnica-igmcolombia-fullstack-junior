<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Navigation Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
      <div class="max-w-7xl xl:max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo and Brand -->
          <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center">
              <Icon icon="mdi:receipt" class="h-8 w-8 text-blue-600" />
              <span class="ml-2 text-xl font-bold text-gray-900 hidden sm:block">Invoice App</span>
              <span class="ml-2 text-lg font-bold text-gray-900 sm:hidden">IA</span>
            </div>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:ml-8 md:flex md:space-x-8" v-if="isAuthenticated">
              <router-link
                v-for="item in visibleNavigation"
                :key="item.name"
                :to="item.href"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors duration-200"
                :class="isCurrentRoute(item.href) 
                  ? 'border-blue-500 text-gray-900' 
                  : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
              >
                <Icon :icon="item.icon" class="w-4 h-4 mr-2" />
                {{ item.name }}
              </router-link>
            </nav>
          </div>

          <!-- Right side content -->
          <div class="flex items-center space-x-4">
            <!-- Loading indicator -->
            <div v-if="loading" class="flex items-center">
              <Icon icon="mdi:loading" class="h-5 w-5 text-blue-600 animate-spin" />
            </div>

            <!-- Authenticated User Menu -->
            <div v-if="isAuthenticated" class="relative">
              <!-- Desktop User Menu -->
              <div class="hidden sm:flex items-center">
                <button
                  @click="toggleUserMenu"
                  class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <Icon icon="mdi:account" class="h-5 w-5 text-white" />
                  </div>
                  <div class="flex flex-col items-start">
                    <span class="text-sm font-medium text-gray-900">{{ currentUser?.name || 'User' }}</span>
                    <span class="text-xs text-gray-500 capitalize">{{ userRole }}</span>
                  </div>
                  <Icon icon="mdi:chevron-down" class="h-4 w-4 text-gray-500 transition-transform duration-200" 
                        :class="{ 'rotate-180': showUserMenu }" />
                </button>
              </div>

              <!-- Mobile User Avatar -->
              <div class="sm:hidden">
                <button
                  @click="toggleUserMenu"
                  class="flex items-center p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <Icon icon="mdi:account" class="h-5 w-5 text-white" />
                  </div>
                </button>
              </div>

              <!-- User Dropdown Menu -->
              <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <div
                  v-if="showUserMenu"
                  v-click-outside="closeUserMenu"
                  class="absolute right-0 mt-2 w-64 sm:w-72 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                >
                  <!-- User Info Header -->
                  <div class="px-4 py-3 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                      <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <Icon icon="mdi:account" class="h-6 w-6 text-white" />
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ currentUser?.name || 'User' }}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                          {{ currentUser?.email || 'user@example.com' }}
                        </p>
                        <p class="text-xs text-blue-600 capitalize font-medium">
                          {{ userRole }} Account
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Menu Items -->
                  <div class="py-1">
                    <button
                      v-if="canManageAll"
                      @click="handleAdmin"
                      class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150"
                    >
                      <Icon icon="mdi:shield-account" class="w-4 h-4 mr-3 text-gray-400" />
                      Admin Panel
                    </button>

                    <div v-if="canManageAll" class="border-t border-gray-100 my-1"></div>
                    
                    <button
                      @click="handleLogout"
                      class="w-full flex items-center px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors duration-150"
                    >
                      <Icon icon="mdi:logout" class="w-4 h-4 mr-3 text-red-500" />
                      Sign Out
                    </button>
                  </div>
                </div>
              </Transition>
            </div>

            <!-- Unauthenticated State -->
            <div v-else class="flex items-center space-x-3">
              <span class="text-sm text-gray-500 hidden sm:block">Not authenticated</span>
              <router-link
                to="/"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
              >
                <Icon icon="mdi:login" class="w-4 h-4 mr-2" />
                Sign In
              </router-link>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden" v-if="isAuthenticated">
              <button
                @click="toggleMobileMenu"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
              >
                <Icon :icon="showMobileMenu ? 'mdi:close' : 'mdi:menu'" class="h-6 w-6" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-2 opacity-0"
      >
        <div v-if="showMobileMenu && isAuthenticated" class="md:hidden border-t border-gray-200 bg-white">
          <div class="px-2 pt-2 pb-3 space-y-1">
            <router-link
              v-for="item in visibleNavigation"
              :key="item.name"
              :to="item.href"
              @click="closeMobileMenu"
              class="flex items-center px-3 py-2 rounded-md text-base font-medium transition-colors duration-200"
              :class="isCurrentRoute(item.href)
                ? 'bg-blue-50 text-blue-700 border border-blue-200'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
            >
              <Icon :icon="item.icon" class="w-5 h-5 mr-3" />
              {{ item.name }}
            </router-link>
          </div>
        </div>
      </Transition>
    </header>

    <!-- Main content -->
    <main class="flex-1">
      <div class="max-w-7xl xl:max-w-[1440px] mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const { 
  isAuthenticated, 
  currentUser, 
  userRole, 
  canManageAll, 
  loading, 
  logout: authLogout,
  initializeAuth 
} = useAuth()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

// Initialize authentication on mount
onMounted(() => {
  initializeAuth()
})

const navigation = [
  { name: 'Invoices', href: '/app/invoices', icon: 'mdi:receipt' },
  { name: 'Clients', href: '/app/clients', icon: 'mdi:account-group' },
  { name: 'Users', href: '/app/users', icon: 'mdi:account-multiple' }
]

// Filter navigation based on user permissions
const visibleNavigation = computed(() => {
  if (!isAuthenticated.value) return []
  
  // Admin can see all navigation items
  if (canManageAll.value) {
    return navigation
  }
  
  // Regular users can only see invoices and clients
  return navigation.filter(item => item.name !== 'Users')
})

const isCurrentRoute = (href: string) => {
  return route.path.startsWith(href)
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const closeUserMenu = () => {
  showUserMenu.value = false
}

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
}

const handleLogout = async () => {
  await authLogout()
  closeUserMenu()
}

const handleAdmin = () => {
  // TODO: Implement admin panel
  closeUserMenu()
}

// Click outside directive
const vClickOutside = {
  beforeMount(el: HTMLElement & { clickOutsideEvent?: (event: Event) => void }, binding: any) {
    el.clickOutsideEvent = (event: Event) => {
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value()
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el: HTMLElement & { clickOutsideEvent?: (event: Event) => void }) {
    if (el.clickOutsideEvent) {
      document.removeEventListener('click', el.clickOutsideEvent)
    }
  }
}
</script>