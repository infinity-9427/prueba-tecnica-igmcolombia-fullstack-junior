# 🧾 Invoice Management System

Full-stack invoice management application built with **Vue.js 3** frontend and **Laravel 11** backend. Features modern architecture, JWT authentication, role-based permissions, and complete invoice lifecycle management.

## 🚀 **Project Overview**

Complete invoice management solution with separate frontend and backend applications, designed for scalability and maintainability.

### **Frontend (Vue.js 3)**
- ⚡ **Vue 3** with Composition API
- 🎨 **TailwindCSS** for styling  
- 🛣️ **Vue Router** for navigation
- 📦 **Pinia** for state management
- ✅ **Vee-Validate** for form validation
- 🌐 **Axios** for API communication
- 📱 **Responsive design** with mobile support

### **Backend (Laravel 11)**
- 🏗️ **Modular architecture** with services & interfaces
- 🔐 **Laravel Sanctum** JWT authentication
- 👥 **Role-based authorization** (admin/user)
- 🗄️ **PostgreSQL 12** database
- 📦 **Docker** containerized environment
- ⚡ **Redis** caching & sessions
- 📝 **Comprehensive logging**
- 🛠️ **Artisan commands** for CLI management

---

## 📋 **Features**

### **Core Functionality**
- ✅ **Authentication & Authorization**
  - User registration/login with JWT tokens
  - Role-based access control (admin/user)
  - Protected routes and API endpoints

- ✅ **Invoice Management**
  - Create, edit, view, and delete invoices
  - Multi-item invoices with tax calculations
  - Invoice status tracking (pending/paid/overdue)
  - File attachments (PDF, images)
  - Advanced filtering and sorting

- ✅ **Client Management**
  - Complete client information management
  - Document validation (cedula/pasaporte/nit)
  - Client-invoice relationship tracking

- ✅ **User Management** (Admin only)
  - User creation and role management
  - User activity monitoring

### **Advanced Features**
- 🔍 **Advanced Search & Filters**
  - Filter by status, date ranges, client, invoice number
  - Sortable columns with pagination
  - Real-time search capabilities

- ⚡ **Performance**
  - Redis caching for frequent queries
  - Optimized database queries with eager loading
  - Pagination for large datasets

- 🛠️ **Administration**
  - CLI commands for invoice management
  - Automated overdue invoice detection
  - Comprehensive audit logging

---

## 🏗️ **Architecture**

### **Backend Architecture**
```
app/
├── Console/Commands/          # Artisan commands
├── Http/
│   ├── Controllers/          # API controllers
│   └── Requests/            # Form validation
├── Interfaces/              # Service interfaces
├── Models/                  # Eloquent models
├── Policies/               # Authorization policies
└── Services/               # Business logic
```

### **Frontend Architecture**
```
src/
├── components/              # Reusable Vue components
├── layouts/                # Layout components
├── router/                 # Vue Router configuration
├── types/                  # TypeScript type definitions
└── views/                  # Page components
```

---

## 🚀 **Quick Start**

### **Prerequisites**
- Docker & Docker Compose
- Node.js 18+ & PNPM
- Git

### **1. Clone Repository**
```bash
git clone <repository-url>
cd invoice-vue-laravel
```

### **2. Setup Backend**
```bash
cd backend/

# Start Docker services
docker-compose up -d

# Install dependencies & setup
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Verify API health
curl http://localhost:8000/api/health
```

### **3. Setup Frontend**
```bash
cd frontend/

# Install dependencies
pnpm install

# Start development server
pnpm dev
```

### **4. Access Applications**
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000/api
- **Database**: localhost:5432 (postgres/secret)

---

## 🔑 **Default Credentials**

### **Admin User**
- **Email**: admin@invoice.com
- **Password**: password123
- **Role**: admin (full access)

### **Regular User**
- **Email**: user@invoice.com  
- **Password**: password123
- **Role**: user (limited access)

---

## 📊 **Database Schema**

### **Main Entities**

#### **Users**
```sql
id, name, email, password, role (admin/user), timestamps
```

#### **Clients**
```sql
id, first_name, last_name, document_type, document_number, 
email, phone, timestamps
```

#### **Invoices**
```sql
id, invoice_number, client_id, user_id, description, 
additional_notes, issue_date, due_date, total_amount, 
status (pending/paid/overdue), attachment_path, timestamps
```

#### **Invoice Items**
```sql
id, invoice_id, name, quantity, unit_price, tax_rate, 
tax_amount, total_amount, timestamps
```

---

## 🛠️ **API Documentation**

### **Authentication Endpoints**
```bash
POST   /api/auth/register      # User registration
POST   /api/auth/login         # User login
POST   /api/auth/logout        # User logout
GET    /api/auth/me            # Get current user
POST   /api/auth/refresh       # Refresh token
```

### **Invoice Endpoints**
```bash
GET    /api/invoices           # List invoices (with filters)
POST   /api/invoices           # Create invoice
GET    /api/invoices/{id}      # Get invoice details
PUT    /api/invoices/{id}      # Update invoice
DELETE /api/invoices/{id}      # Delete invoice
PATCH  /api/invoices/{id}/status # Update invoice status
GET    /api/invoices-recent    # Get recent invoices (cached)
```

### **Client Endpoints**
```bash
GET    /api/clients            # List clients
POST   /api/clients            # Create client
GET    /api/clients/{id}       # Get client details
PUT    /api/clients/{id}       # Update client
DELETE /api/clients/{id}       # Delete client (admin only)
```

### **User Endpoints** (Admin only)
```bash
GET    /api/users              # List users
POST   /api/users              # Create user
GET    /api/users/{id}         # Get user details
PUT    /api/users/{id}         # Update user
DELETE /api/users/{id}         # Delete user
PATCH  /api/users/{id}/role    # Update user role
```

### **File Upload Endpoints**
```bash
POST   /api/files/upload/invoice-attachment    # Upload file
GET    /api/files/download/invoice-attachment/{filename} # Download file
DELETE /api/files/delete/invoice-attachment    # Delete file
```

---

## 🖥️ **Frontend Screens**

### **Authentication**
- ✅ Login/Register forms with validation
- ✅ Password confirmation and email validation
- ✅ Automatic redirects based on authentication state

### **Dashboard**
- ✅ Invoice statistics and recent activity
- ✅ Quick actions for common tasks
- ✅ Role-based dashboard content

### **Invoice Management**
- ✅ **Invoice List**: Filterable, sortable, paginated table
- ✅ **Create Invoice**: Multi-step form with item management
- ✅ **Edit Invoice**: Update existing invoices
- ✅ **Invoice Details**: Complete invoice view with actions
- ✅ **File Attachments**: Upload and download invoice documents

### **Client Management**
- ✅ **Client List**: Searchable client directory
- ✅ **Create/Edit Client**: Complete client information forms
- ✅ **Client Details**: View client and associated invoices

### **User Management** (Admin)
- ✅ **User List**: User directory with role management
- ✅ **Create/Edit User**: User account management
- ✅ **Role Assignment**: Admin role management tools

---

## 🛠️ **CLI Commands**

### **Invoice Management**
```bash
# List invoices with filters
php artisan invoice:manage list --filter-status=pending --limit=10

# View specific invoice
php artisan invoice:manage show --id=1

# Update invoice status
php artisan invoice:manage update-status --id=1 --status=paid

# Check for overdue invoices
php artisan invoice:manage check-overdue
```

---

## 📈 **Performance Features**

### **Caching Strategy**
- ✅ **Recent invoices** cached for 5 minutes
- ✅ **Redis** as cache driver
- ✅ **Cache invalidation** on data changes

### **Database Optimization**
- ✅ **Eager loading** for related models
- ✅ **Database indexes** on frequently queried columns
- ✅ **Pagination** for large datasets

### **Frontend Optimization**
- ✅ **Lazy loading** for routes
- ✅ **Component optimization** with Vue 3 features
- ✅ **TailwindCSS** purging for smaller builds

---

## 🔒 **Security Features**

### **Authentication & Authorization**
- ✅ **JWT tokens** with Laravel Sanctum
- ✅ **Role-based access control** with policies
- ✅ **Protected routes** on frontend and backend
- ✅ **Password hashing** with bcrypt

### **Data Validation**
- ✅ **Server-side validation** with Form Requests
- ✅ **Client-side validation** with Vee-Validate
- ✅ **SQL injection protection** with Eloquent ORM
- ✅ **XSS protection** with Laravel's built-in features

### **File Security**
- ✅ **File type validation** (PDF, JPG, PNG only)
- ✅ **File size limits** (10MB maximum)
- ✅ **Secure file storage** in private directories

---

## 🧪 **Testing**

### **Backend Testing**
```bash
# Run all tests
docker-compose exec app php artisan test

# Run with coverage
docker-compose exec app php artisan test --coverage
```

### **Frontend Testing**
```bash
# Run unit tests
pnpm test

# Run e2e tests
pnpm test:e2e
```

---

## 📦 **Docker Services**

| Service | Description | Port |
|---------|-------------|------|
| **app** | Laravel application (PHP 8.2) | - |
| **nginx** | Web server | 8000 |
| **db** | PostgreSQL 12 | 5432 |
| **redis** | Cache & sessions | 6379 |

---

## 🔧 **Development**

### **Backend Development**
```bash
# Access Laravel container
docker-compose exec app bash

# Run migrations
php artisan migrate

# Create new migration
php artisan make:migration create_example_table

# Create new controller
php artisan make:controller ExampleController

# Run tinker
php artisan tinker
```

### **Frontend Development**
```bash
# Install new package
pnpm add package-name

# Build for production
pnpm build

# Preview production build
pnpm preview

# Type check
pnpm type-check
```

---

## 📂 **Project Structure**

```
invoice-vue-laravel/
├── backend/                 # Laravel API backend
│   ├── app/                # Application code
│   ├── database/           # Migrations, seeders, factories
│   ├── docker/            # Docker configuration
│   ├── routes/            # API routes
│   └── docker-compose.yml # Docker services
├── frontend/               # Vue.js frontend
│   ├── src/               # Source code
│   ├── public/            # Static assets
│   └── package.json       # Dependencies
└── README.md              # This file
```

---

## 🐛 **Troubleshooting**

### **Common Issues**

#### **Backend not starting**
```bash
# Check Docker logs
docker-compose logs app

# Rebuild containers
docker-compose down
docker-compose up --build
```

#### **Database connection issues**
```bash
# Check PostgreSQL status
docker-compose ps

# Reset database
docker-compose exec app php artisan migrate:fresh --seed
```

#### **Frontend build issues**
```bash
# Clear node modules and reinstall
rm -rf node_modules pnpm-lock.yaml
pnpm install
```

#### **Permission issues**
```bash
# Fix Laravel permissions
docker-compose exec app chown -R www-data:www-data storage/
docker-compose exec app chmod -R 755 storage/
```

---

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📝 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 **Technical Requirements Met**

### **Backend Requirements** ✅
- ✅ Modular architecture with services
- ✅ JWT authentication with Laravel Sanctum
- ✅ Complete CRUD endpoints for invoices, clients, users
- ✅ Role-based authorization system
- ✅ Advanced filtering and sorting
- ✅ Database migrations (no SQL files)
- ✅ Data validation and business logic
- ✅ Artisan command with business logic
- ✅ Redis caching implementation
- ✅ Comprehensive event logging
- ✅ Database seeders for setup

### **Frontend Requirements** ✅
- ✅ Vue Router for navigation
- ✅ Pinia for state management
- ✅ Login and registration screens
- ✅ Invoice listing with filters and pagination
- ✅ Invoice create/edit forms
- ✅ Invoice detail screens
- ✅ Client listing with filters and pagination
- ✅ Client update forms
- ✅ User listing with filters and pagination
- ✅ Vee-Validate for form validation
- ✅ Axios for API communication

---

🎉 **Ready to manage invoices like a pro!** 🚀