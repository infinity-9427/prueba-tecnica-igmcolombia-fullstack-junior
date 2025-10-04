<template>
  <div id="app">
    <!-- Global Toast Component -->
    <Toast position="bottom-right" group="br" />
    <RouterView />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { useUserStore } from '@/stores/userStore'

// Initialize auth state from storage on app start
const userStore = useUserStore()
onMounted(() => {
  userStore.initializeAuth()
})
</script>

<style>
#app {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100vh;
}

/* Custom Toast Styling */
:deep(.custom-success-toast) {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  border: none !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3) !important;
  backdrop-filter: blur(10px) !important;
}

:deep(.custom-success-toast .p-toast-message-content) {
  padding: 1rem 1.25rem !important;
}

:deep(.custom-success-toast .p-toast-message-text) {
  margin: 0 !important;
}

:deep(.custom-success-toast .p-toast-summary) {
  color: white !important;
  font-weight: 600 !important;
  font-size: 0.95rem !important;
  margin-bottom: 0.25rem !important;
}

:deep(.custom-success-toast .p-toast-detail) {
  color: rgba(255, 255, 255, 0.9) !important;
  font-size: 0.85rem !important;
  line-height: 1.4 !important;
}

:deep(.custom-success-toast .p-toast-icon-close) {
  color: rgba(255, 255, 255, 0.8) !important;
  width: 1.25rem !important;
  height: 1.25rem !important;
}

:deep(.custom-success-toast .p-toast-icon-close:hover) {
  color: white !important;
  background: rgba(255, 255, 255, 0.1) !important;
  border-radius: 50% !important;
}

:deep(.custom-error-toast) {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
  border: none !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3) !important;
  backdrop-filter: blur(10px) !important;
}

:deep(.custom-error-toast .p-toast-message-content) {
  padding: 1rem 1.25rem !important;
}

:deep(.custom-error-toast .p-toast-message-text) {
  margin: 0 !important;
}

:deep(.custom-error-toast .p-toast-summary) {
  color: white !important;
  font-weight: 600 !important;
  font-size: 0.95rem !important;
  margin-bottom: 0.25rem !important;
}

:deep(.custom-error-toast .p-toast-detail) {
  color: rgba(255, 255, 255, 0.9) !important;
  font-size: 0.85rem !important;
  line-height: 1.4 !important;
}

:deep(.custom-error-toast .p-toast-icon-close) {
  color: rgba(255, 255, 255, 0.8) !important;
  width: 1.25rem !important;
  height: 1.25rem !important;
}

:deep(.custom-error-toast .p-toast-icon-close:hover) {
  color: white !important;
  background: rgba(255, 255, 255, 0.1) !important;
  border-radius: 50% !important;
}

:deep(.custom-warning-toast) {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
  border: none !important;
  border-radius: 12px !important;
  box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3) !important;
  backdrop-filter: blur(10px) !important;
}

:deep(.custom-warning-toast .p-toast-message-content) {
  padding: 1rem 1.25rem !important;
}

:deep(.custom-warning-toast .p-toast-message-text) {
  margin: 0 !important;
}

:deep(.custom-warning-toast .p-toast-summary) {
  color: white !important;
  font-weight: 600 !important;
  font-size: 0.95rem !important;
  margin-bottom: 0.25rem !important;
}

:deep(.custom-warning-toast .p-toast-detail) {
  color: rgba(255, 255, 255, 0.9) !important;
  font-size: 0.85rem !important;
  line-height: 1.4 !important;
}

:deep(.custom-warning-toast .p-toast-icon-close) {
  color: rgba(255, 255, 255, 0.8) !important;
  width: 1.25rem !important;
  height: 1.25rem !important;
}

:deep(.custom-warning-toast .p-toast-icon-close:hover) {
  color: white !important;
  background: rgba(255, 255, 255, 0.1) !important;
  border-radius: 50% !important;
}

/* Toast container styling */
:deep(.p-toast-bottom-right) {
  right: 1.5rem !important;
  bottom: 1.5rem !important;
}

/* Toast animations */
:deep(.p-toast-message-enter-from) {
  opacity: 0 !important;
  transform: translateX(100%) !important;
}

:deep(.p-toast-message-enter-to) {
  opacity: 1 !important;
  transform: translateX(0) !important;
}

:deep(.p-toast-message-enter-active) {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
}

:deep(.p-toast-message-leave-from) {
  opacity: 1 !important;
  transform: translateX(0) scale(1) !important;
}

:deep(.p-toast-message-leave-to) {
  opacity: 0 !important;
  transform: translateX(100%) scale(0.8) !important;
}

:deep(.p-toast-message-leave-active) {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
</style>
