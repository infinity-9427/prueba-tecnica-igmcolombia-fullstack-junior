<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo and main navigation -->
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <Icon icon="mdi:receipt" class="h-8 w-8 text-blue-600" />
              <span class="ml-2 text-xl font-bold text-gray-900">Invoice App</span>
            </div>
            
            <!-- Main navigation -->
            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <router-link
                v-for="item in navigation"
                :key="item.name"
                :to="item.href"
                class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors"
                :class="isCurrentRoute(item.href) 
                  ? 'border-blue-500 text-gray-900' 
                  : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
              >
                <Icon :icon="item.icon" class="w-4 h-4 mr-2" />
                {{ item.name }}
              </router-link>
            </div>
          </div>

          <!-- User menu -->
          <div class="flex items-center">
            <div class="relative">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                  <Icon icon="mdi:account" class="h-5 w-5 text-white" />
                </div>
                <span class="ml-2 text-gray-700">Admin User</span>
                <Icon icon="mdi:chevron-down" class="ml-1 h-4 w-4 text-gray-500" />
              </button>

              <!-- User dropdown -->
              <div
                v-if="showUserMenu"
                v-click-outside="() => showUserMenu = false"
                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
              >
                <div class="py-1">
                  <a
                    href="#"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    <Icon icon="mdi:account-cog" class="w-4 h-4 mr-2 inline" />
                    Profile Settings
                  </a>
                  <button
                    @click="logout"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                  >
                    <Icon icon="mdi:logout" class="w-4 h-4 mr-2 inline" />
                    Sign Out
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="sm:hidden flex items-center">
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
            >
              <Icon :icon="showMobileMenu ? 'mdi:close' : 'mdi:menu'" class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="showMobileMenu" class="sm:hidden border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
          <router-link
            v-for="item in navigation"
            :key="item.name"
            :to="item.href"
            @click="showMobileMenu = false"
            class="flex items-center pl-3 pr-4 py-2 text-base font-medium transition-colors"
            :class="isCurrentRoute(item.href)
              ? 'bg-blue-50 border-blue-500 text-blue-700 border-l-4'
              : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-800'"
          >
            <Icon :icon="item.icon" class="w-5 h-5 mr-3" />
            {{ item.name }}
          </router-link>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="flex-1">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

const route = useRoute()
const router = useRouter()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

const navigation = [
  { name: 'Invoices', href: '/app/invoices', icon: 'mdi:receipt' },
  { name: 'Clients', href: '/app/clients', icon: 'mdi:account-group' },
  { name: 'Users', href: '/app/users', icon: 'mdi:account-multiple' }
]

const isCurrentRoute = (href: string) => {
  return route.path.startsWith(href)
}

const logout = () => {
  // TODO: Implement logout logic
  router.push('/')
  showUserMenu.value = false
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