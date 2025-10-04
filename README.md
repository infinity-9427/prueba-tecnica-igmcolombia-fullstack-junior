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

### **2. Environment Setup**
```bash
# Copy environment file (it's already configured with defaults)
cp .env.example .env

# Or customize your environment
nano .env
```

> **📝 Note**: The `.env` file contains all Docker configuration. You can modify ports, memory limits, database credentials, and other settings as needed.

**Key Benefits:**
- ✅ **Centralized configuration** - All settings in one place
- ✅ **Environment-specific values** - Different configs for dev/staging/production
- ✅ **No hardcoded values** - Easy to change ports, credentials, memory limits
- ✅ **Default fallbacks** - Works without .env file using sensible defaults
- ✅ **Security** - Sensitive values in .env file, not in repository

### **3. Setup Backend**
```bash
cd backend/

# Start Docker services (with automatic migrations)
docker-compose up --build -d

# The container will automatically:
# - Wait for database connection
# - Run migrations
# - Cache configuration
# - Start PHP-FPM

# Optional: Seed sample data
docker-compose exec app php artisan db:seed

# Verify API health
curl http://localhost:8000/api/health
```

> **Note**: Migrations run automatically on container startup when `AUTO_MIGRATE=true` (default). This ensures the database is always ready without manual intervention.

### **4. Setup Frontend**
```bash
cd frontend/

# Install dependencies
pnpm install

# Start development server
pnpm dev
```

### **5. Access Applications**
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
| **app** | Laravel application (PHP 8.3) | - |
| **nginx** | Web server | 8000 |
| **db** | PostgreSQL 15 | 5432 |
| **redis** | Cache & sessions | 6379 |

### **🐳 Docker Optimizations**

The Dockerfile has been optimized following modern best practices:

#### **Multi-stage Build**
- **Builder stage**: Installs dependencies and builds the application
- **Production stage**: Creates minimal runtime image with only necessary components
- **Size reduction**: ~60% smaller final image compared to single-stage builds

#### **Security Enhancements**
- ✅ **Non-root user**: Application runs as `appuser` for security
- ✅ **Minimal attack surface**: Only runtime dependencies in final image
- ✅ **Layer optimization**: Combined RUN commands reduce layers and vulnerabilities
- ✅ **Latest base images**: PHP 8.3-fpm with security updates

#### **Performance Features**
- ✅ **OPcache enabled**: Pre-compiled PHP for faster execution
- ✅ **Composer optimization**: `--classmap-authoritative` for production
- ✅ **Layer caching**: Dependencies installed before copying source code
- ✅ **Configuration caching**: Laravel config/routes/views cached on startup

#### **Automatic Database Migrations**
- ✅ **Smart startup**: Waits for database connectivity before starting
- ✅ **Auto-migration**: Runs `php artisan migrate --force` when `AUTO_MIGRATE=true`
- ✅ **Zero-downtime**: No manual intervention required for database setup
- ✅ **Production-safe**: Can be disabled by setting `AUTO_MIGRATE=false`

#### **Health Monitoring & Error Handling**
- ✅ **FastCGI health check**: Monitors PHP-FPM process health
- ✅ **Database connectivity**: Ensures database connection before app startup
- ✅ **Connection timeout**: 60-second timeout with retry logic (30 attempts)
- ✅ **Comprehensive error handling**: Trap-based error detection with line numbers
- ✅ **Colored output**: Clear visual feedback for startup stages
- ✅ **Graceful degradation**: Non-critical failures don't stop container startup
- ✅ **Exit codes**: Proper exit codes for Docker health checks and monitoring

### **Environment Variables**

All Docker configuration is now managed through environment variables in the `.env` file:

#### **🔧 Application Configuration**
```bash
APP_ENV=production              # Application environment
APP_DEBUG=false                 # Debug mode (set to true for development)
AUTO_MIGRATE=true              # Automatic database migrations on startup
```

#### **🗄️ Database Configuration**
```bash
# Connection Settings
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=invoice_db
DB_USERNAME=postgres
DB_PASSWORD=secret

# PostgreSQL Performance Tuning
PG_MAX_CONNECTIONS=100
PG_SHARED_BUFFERS=256MB
PG_EFFECTIVE_CACHE_SIZE=1GB
PG_MAINTENANCE_WORK_MEM=64MB
```

#### **⚡ Redis Configuration**
```bash
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_MAXMEMORY=200mb
REDIS_MAXMEMORY_POLICY=allkeys-lru
```

#### **🐳 Container Configuration**
```bash
# Ports
NGINX_PORT=8000
POSTGRES_PORT=5432
REDIS_PORT=6379

# Memory Limits
LARAVEL_MEMORY_LIMIT=512M
NGINX_MEMORY_LIMIT=128M
POSTGRES_MEMORY_LIMIT=512M
REDIS_MEMORY_LIMIT=256M

# Container Names
LARAVEL_CONTAINER_NAME=laravel_app
NGINX_CONTAINER_NAME=laravel_nginx
POSTGRES_CONTAINER_NAME=laravel_postgres
REDIS_CONTAINER_NAME=laravel_redis
```

#### **🔍 Health Check Configuration**
```bash
# Intervals and timeouts for each service
APP_HEALTHCHECK_INTERVAL=30s
APP_HEALTHCHECK_TIMEOUT=10s
DB_HEALTHCHECK_INTERVAL=10s
REDIS_HEALTHCHECK_INTERVAL=10s
```

#### **🌐 Network Configuration**
```bash
DOCKER_NETWORK_NAME=laravel
DOCKER_SUBNET=172.20.0.0/16
```

> **💡 Customization**: You can override any of these values in your `.env` file. All variables have sensible defaults if not specified.

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