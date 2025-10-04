<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
      <!-- Header -->
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 mb-2">
          {{ isLogin ? 'Welcome back' : 'Create account' }}
        </h2>
        <p class="text-sm text-gray-600">
          {{ isLogin ? 'Sign in to your account to continue' : 'Join us today and get started' }}
        </p>
      </div>

      <!-- Form Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <form @submit.prevent="onSubmit" class="space-y-6">
          <!-- Name Field (Register only) -->
          <div v-if="!isLogin" class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">
              Full Name
            </label>
            <div class="relative">
              <input
                v-model="name"
                id="name"
                name="name"
                type="text"
                autocomplete="name"
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-blue-500 transition-colors"
                :class="{ 
                  'border-red-300 focus:border-red-500': nameError,
                  'border-green-300 focus:border-green-500': name && !nameError
                }"
                placeholder="Enter your full name"
              />
              <div v-if="name && !nameError" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <Icon icon="mdi:check-circle" class="h-5 w-5 text-green-500" />
              </div>
            </div>
            <p v-if="nameError" class="text-sm text-red-600 flex items-center gap-1">
              <Icon icon="mdi:alert-circle" class="h-4 w-4" />
              {{ nameError }}
            </p>
          </div>

          <!-- Email Field -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email Address
            </label>
            <div class="relative">
              <input
                v-model="email"
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-blue-500 transition-colors"
                :class="{ 
                  'border-red-300 focus:border-red-500': emailError,
                  'border-green-300 focus:border-green-500': email && !emailError
                }"
                placeholder="Enter your email"
              />
              <div v-if="email && !emailError" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <Icon icon="mdi:check-circle" class="h-5 w-5 text-green-500" />
              </div>
            </div>
            <p v-if="emailError" class="text-sm text-red-600 flex items-center gap-1">
              <Icon icon="mdi:alert-circle" class="h-4 w-4" />
              {{ emailError }}
            </p>
          </div>

          <!-- Password Field -->
          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="relative">
              <input
                v-model="password"
                id="password"
                name="password"
                :type="showPassword ? 'text' : 'password'"
                :autocomplete="isLogin ? 'current-password' : 'new-password'"
                class="block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-blue-500 transition-colors"
                :class="{ 
                  'border-red-300 focus:border-red-500': passwordError,
                  'border-green-300 focus:border-green-500': password && !passwordError
                }"
                placeholder="Enter your password"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors"
              >
                <Icon 
                  :icon="showPassword ? 'mdi:eye' : 'mdi:eye-off'" 
                  class="h-5 w-5" 
                />
              </button>
            </div>
            <p v-if="passwordError" class="text-sm text-red-600 flex items-center gap-1">
              <Icon icon="mdi:alert-circle" class="h-4 w-4" />
              {{ passwordError }}
            </p>
          </div>

          <!-- Confirm Password Field (Register only) -->
          <div v-if="!isLogin" class="space-y-2">
            <label for="confirmPassword" class="block text-sm font-medium text-gray-700">
              Confirm Password
            </label>
            <div class="relative">
              <input
                v-model="confirmPassword"
                id="confirmPassword"
                name="confirmPassword"
                :type="showConfirmPassword ? 'text' : 'password'"
                autocomplete="new-password"
                class="block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:border-blue-500 transition-colors"
                :class="{ 
                  'border-red-300 focus:border-red-500': confirmPasswordError,
                  'border-green-300 focus:border-green-500': confirmPassword && !confirmPasswordError
                }"
                placeholder="Confirm your password"
              />
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors"
              >
                <Icon 
                  :icon="showConfirmPassword ? 'mdi:eye' : 'mdi:eye-off'" 
                  class="h-5 w-5" 
                />
              </button>
            </div>
            <p v-if="confirmPasswordError" class="text-sm text-red-600 flex items-center gap-1">
              <Icon icon="mdi:alert-circle" class="h-4 w-4" />
              {{ confirmPasswordError }}
            </p>
          </div>

          <!-- Role Field (Register only) -->
          <div v-if="!isLogin" class="space-y-2">
            <label for="role" class="block text-sm font-medium text-gray-700">
              Account Type
            </label>
            <div class="relative">
              <select
                v-model="role"
                id="role"
                name="role"
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-blue-500 transition-colors appearance-none"
                :class="{ 
                  'border-red-300 focus:border-red-500': roleError,
                  'border-green-300 focus:border-green-500': role && !roleError
                }"
              >
                <option value="">Select account type</option>
                <option value="admin">Administrator</option>
                <option value="user">Client</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <Icon icon="mdi:chevron-down" class="h-5 w-5 text-gray-400" />
              </div>
            </div>
            <p v-if="roleError" class="text-sm text-red-600 flex items-center gap-1">
              <Icon icon="mdi:alert-circle" class="h-4 w-4" />
              {{ roleError }}
            </p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isSubmitting || !isFormValid"
            class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          >
            <Icon 
              v-if="isSubmitting" 
              icon="mdi:loading" 
              class="h-4 w-4 animate-spin" 
            />
            <Icon 
              v-else
              :icon="isLogin ? 'mdi:login' : 'mdi:account-plus'" 
              class="h-4 w-4" 
            />
            {{ isSubmitting ? 'Please wait...' : (isLogin ? 'Sign In' : 'Create Account') }}
          </button>
        </form>

        <!-- Footer -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            {{ isLogin ? "Don't have an account?" : "Already have an account?" }}
            <router-link 
              :to="isLogin ? '/register' : '/'" 
              class="font-medium text-blue-600 hover:text-blue-500 transition-colors"
            >
              {{ isLogin ? 'Sign up' : 'Sign in' }}
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useForm, useField } from 'vee-validate'
import * as yup from 'yup'
import { Icon } from '@iconify/vue'
import { AuthFormType } from '@/types/auth'
import { useAuth } from '@/composables/useAuth'
import type { LoginCredentials, RegisterData } from '@/stores/userStore'

interface Props {
  formType: AuthFormType
}

const props = defineProps<Props>()

const isLogin = computed(() => props.formType === AuthFormType.LOGIN)
const isSubmitting = ref<boolean>(false)
const showPassword = ref<boolean>(false)
const showConfirmPassword = ref<boolean>(false)

// Define validation schema with yup
const validationSchema = computed(() => {
  const baseSchema: any = {
    email: yup.string()
      .required('Email is required')
      .test('email-format', 'Please enter a valid email address', (value) => {
        if (!value) return false
        
        // Must contain @ and have domain with TLD
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
          return false
        }
        
        // Check for valid characters only
        if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(value)) {
          return false
        }
        
        // Check for consecutive dots
        if (/\.{2,}/.test(value)) {
          return false
        }
        
        // Check for dots at start/end of local part
        const [localPart, domain] = value.split('@')
        if (localPart.startsWith('.') || localPart.endsWith('.')) {
          return false
        }
        
        // Check domain has valid TLD
        const tld = domain.split('.').pop()
        if (!tld || tld.length < 2) {
          return false
        }
        
        // Check reasonable lengths
        if (value.length > 254 || localPart.length > 64) {
          return false
        }
        
        return true
      }),
    password: yup.string().required('Password is required').min(6, 'Password must be at least 6 characters')
  }
  
  if (!isLogin.value) {
    baseSchema.name = yup.string().required('Name is required').min(2, 'Name must be at least 2 characters')
    baseSchema.confirmPassword = yup.string()
      .required('Please confirm your password')
      .oneOf([yup.ref('password')], 'Passwords must match')
    baseSchema.role = yup.string()
      .required('Account type is required')
      .oneOf(['admin', 'user'], 'Please select a valid account type')
  }
  
  return yup.object(baseSchema)
})

const { handleSubmit, errors, meta, resetForm } = useForm({
  validationSchema
})

const { value: name } = useField('name')
const { value: email } = useField('email') 
const { value: password } = useField('password')
const { value: confirmPassword } = useField('confirmPassword')
const { value: role } = useField('role')

// Reset form when switching between login/register
watch(() => props.formType, (newType, oldType) => {
  if (newType !== oldType) {
    // Reset password visibility states
    showPassword.value = false
    showConfirmPassword.value = false
    
    // Only reset form if not currently submitting
    if (!isSubmitting.value) {
      // Properly reset the entire form including validation state
      resetForm({
        values: {
          name: '',
          email: '',
          password: '',
          confirmPassword: '',
          role: ''
        },
        errors: {}, // Clear all errors
        touched: {} // Clear all touched states
      })
    }
  }
}, { immediate: false })

const nameError = computed(() => errors.value.name)
const emailError = computed(() => errors.value.email)
const passwordError = computed(() => errors.value.password)
const confirmPasswordError = computed(() => errors.value.confirmPassword)
const roleError = computed(() => errors.value.role)

// Check if form is valid for submit button state
const isFormValid = computed(() => {
  // Check if form has been touched and is valid
  if (!meta.value.dirty) return false
  
  // For login: email and password must be valid
  if (isLogin.value) {
    return email.value && 
           password.value && 
           !emailError.value && 
           !passwordError.value
  }
  
  // For register: all fields must be valid
  return name.value && 
         email.value && 
         password.value && 
         confirmPassword.value &&
         role.value &&
         !nameError.value &&
         !emailError.value && 
         !passwordError.value && 
         !confirmPasswordError.value &&
         !roleError.value
})

const { login, register } = useAuth()

const onSubmit = handleSubmit(async (values: any) => {
  if (isSubmitting.value) return // Prevent double submission
  
  isSubmitting.value = true
  
  try {
    let success = false
    
    if (isLogin.value) {
      const loginData: LoginCredentials = {
        email: values.email,
        password: values.password
      }
      
      success = await login(loginData)
    } else {
      const registerData: RegisterData = {
        name: values.name,
        email: values.email,
        password: values.password,
        password_confirmation: values.confirmPassword,
        role: values.role
      }
      
      success = await register(registerData)
    }
    
    // Only reset form on successful authentication
    if (success) {
      resetForm({
        values: {
          name: '',
          email: '',
          password: '',
          confirmPassword: '',
          role: ''
        },
        errors: {},
        touched: {}
      })
    }
    // If not successful, keep form data for user to retry
    
  } catch (error) {
    console.error('Authentication error:', error)
  } finally {
    isSubmitting.value = false
  }
})
</script>