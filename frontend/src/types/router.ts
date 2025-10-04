import 'vue-router'

declare module 'vue-router' {
  interface RouteMeta {
    // Routes that require authentication
    requiresAuth?: boolean
    // Routes that require guest (unauthenticated) access
    requiresGuest?: boolean
    // Routes that require admin role
    requiresAdmin?: boolean
  }
}