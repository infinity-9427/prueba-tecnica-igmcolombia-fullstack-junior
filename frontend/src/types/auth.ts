export enum AuthFormType {
  LOGIN = 'LOGIN',
  REGISTER = 'REGISTER'
}

export interface LoginData {
  email: string
  password: string
}

export interface RegisterData {
  name: string
  email: string
  password: string
  confirmPassword: string
  role: string
}

export enum UserRole {
  ADMIN = 'admin',
  USER = 'user'
}