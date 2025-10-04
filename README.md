# 🧾 Invoice Management System

Full-stack invoice management application built with **Vue.js 3** frontend and **Laravel 12** backend. Features modern architecture, JWT authentication, role-based permissions, automatic PDF generation, and Cloudflare R2 cloud storage integration.

## 🚀 **Project Overview**

Complete invoice management solution with separate frontend and backend applications, designed for scalability and maintainability. Features automatic PDF generation with cloud storage and role-based access control.

### **Frontend (Vue.js 3 + TypeScript)**
- ⚡ **Vue 3** with Composition API and TypeScript
- 🎨 **TailwindCSS** + **PrimeVue** for modern UI/UX
- 🛣️ **Vue Router** for navigation
- 📦 **Pinia** for state management
- ✅ **Vee-Validate** + **Yup** for form validation
- 🌐 **Axios** for API communication
- 📱 **Responsive design** with 1440px max-width layout
- 🎯 **Beautiful toast notifications** with animations
- 🔧 **Development tools**: Vite, Vue DevTools

### **Backend (Laravel 12)**
- 🏗️ **Modular architecture** with services & interfaces
- 🔐 **Laravel Sanctum** JWT authentication
- 👥 **Role-based authorization** (admin/client)
- 🗄️ **PostgreSQL 15** database
- 📦 **Docker** containerized environment
- ⚡ **Redis** caching & sessions
- ☁️ **Cloudflare R2** S3-compatible cloud storage
- 📄 **DomPDF** automatic PDF generation
- 📝 **Comprehensive logging**
- 🛠️ **Artisan commands** for CLI management

---

## 📋 **Features**

### **Core Functionality**
- ✅ **Authentication & Authorization**
  - User registration/login with JWT tokens
  - Role-based access control (admin/client)
  - Protected routes and API endpoints
  - Secure token storage with auto-refresh

- ✅ **Invoice Management**
  - Create, edit, view, and delete invoices
  - Multi-item invoices with tax calculations
  - Invoice status tracking (pending/paid/overdue)
  - **Automatic PDF generation** on invoice create/update
  - **Cloudflare R2 cloud storage** for PDFs
  - Advanced filtering and sorting
  - Role-based access (admin: all features, client: create only)

- ✅ **Client Management**
  - Complete client information management
  - Document validation (cedula/pasaporte/nit)
  - Client-invoice relationship tracking

- ✅ **User Management** (Admin only)
  - User creation and role management
  - User activity monitoring

### **Cloud Storage & PDF Features**
- ✅ **Automatic PDF Generation**
  - PDFs automatically generated when invoices are created/updated
  - Professional invoice templates with company branding
  - Background queue processing for performance
  - Generated using DomPDF library

- ✅ **Cloudflare R2 Integration**
  - S3-compatible cloud storage for all invoice PDFs
  - Secure file access with proper authentication
  - Automatic file organization by date and invoice
  - Cost-effective storage with global CDN access
  - Environment-based configuration for multiple environments

### **Role-Based Access Control**
- ✅ **Admin Users (Full Access)**
  - Create, read, update, delete all invoices
  - Manage all clients and users
  - Access to all system features
  - View all invoices from all users
  - User management and role assignment

- ✅ **Client Users (Limited Access)**
  - Create new invoices only
  - View only their own invoices
  - Manage their own client information
  - Cannot delete invoices or access admin features
  - Cannot manage other users

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

The project includes a comprehensive `.env` file with all necessary configuration:

```bash
# The .env file is already configured with:
# - Database credentials (PostgreSQL)
# - Redis configuration
# - Cloudflare R2 storage settings
# - Docker container configuration
# - Health check settings

# Optional: customize your environment
nano .env
```

> **📝 Note**: The `.env` file contains all Docker and application configuration. You can modify ports, memory limits, database credentials, and other settings as needed.

**Key Configuration Sections:**
- ✅ **Application Settings** - Environment, debug mode, auto-migration
- ✅ **Database Configuration** - PostgreSQL connection and performance tuning
- ✅ **Redis Setup** - Caching and session management
- ✅ **Cloudflare R2** - Cloud storage credentials and bucket configuration
- ✅ **Container Limits** - Memory and resource allocation
- ✅ **Health Checks** - Service monitoring and startup validation
- ✅ **Network Configuration** - Docker networking and ports

### **3. Setup Backend (Docker)**
```bash
cd backend/

# Start all Docker services (database, redis, app, nginx)
docker-compose up --build -d

# The containers will automatically:
# - Start PostgreSQL database with optimized settings
# - Start Redis for caching and sessions
# - Wait for database connection
# - Run Laravel migrations (when AUTO_MIGRATE=true)
# - Cache Laravel configuration
# - Start PHP-FPM and Nginx

# Verify all services are running
docker-compose ps

# Optional: Seed sample data (includes admin and test users)
docker-compose exec app php artisan db:seed

# Verify API health
curl http://localhost:8000/api/health
```

**Docker Services:**
- 🐘 **PostgreSQL 15** (port 5432) - Main database
- 🔴 **Redis** (port 6379) - Caching and sessions  
- 🐘 **Laravel App** - PHP 8.3 with automatic migrations
- 🌐 **Nginx** (port 8000) - Web server and reverse proxy

> **Note**: Migrations run automatically on container startup when `AUTO_MIGRATE=true` (default). This ensures the database is always ready without manual intervention.

### **4. Setup Frontend**
```bash
cd frontend/

# Install dependencies
pnpm install

# Start development server with hot reload
pnpm dev

# Or build for production
pnpm build

# Preview production build
pnpm preview

# Type checking
pnpm type-check
```

**Frontend Development Stack:**
- ⚡ **Vite** - Fast build tool and dev server
- 🔧 **TypeScript** - Type safety and better DX
- 🎨 **Tailwind CSS** - Utility-first CSS framework
- 🎯 **PrimeVue** - Modern Vue.js UI component library
- 📱 **Responsive Design** - Mobile-first with 1440px max-width

### **5. Access Applications**
- **Frontend**: http://localhost:5173 (Vue.js app)
- **Backend API**: http://localhost:8000/api (Laravel API)
- **Database**: localhost:5432 (PostgreSQL - postgres/secret)
- **Redis**: localhost:6379 (Cache and sessions)

---

## 🔑 **Default Credentials**

### **Admin User (Full System Access)**
- **Email**: admin@invoice.com
- **Password**: password123
- **Role**: admin
- **Permissions**: 
  - Create, read, update, delete all invoices
  - Manage all clients and users
  - Access admin panel and user management
  - View system-wide statistics and reports

### **Client User (Limited Access)**
- **Email**: user@invoice.com  
- **Password**: password123
- **Role**: client
- **Permissions**:
  - Create new invoices only
  - View only their own invoices
  - Manage their own client information
  - Cannot delete invoices or access admin features

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

## 🛠️ **API Documentation & Testing**

### **Authentication Endpoints**
```bash
POST   /api/auth/register      # User registration
POST   /api/auth/login         # User login
POST   /api/auth/logout        # User logout
GET    /api/auth/me            # Get current user
POST   /api/auth/refresh       # Refresh token
```

### **📋 Curl API Examples**

#### **User Registration**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "client"
  }'
```

#### **User Login**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@invoice.com",
    "password": "password123"
  }'
```

#### **Get Current User**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept: application/json"
```

#### **Create Invoice (with automatic PDF generation)**
```bash
curl -X POST http://localhost:8000/api/invoices \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "client_id": 1,
    "description": "Web Development Services",
    "issue_date": "2025-01-15",
    "due_date": "2025-02-15",
    "items": [
      {
        "name": "Frontend Development",
        "quantity": 40,
        "unit_price": 50.00,
        "tax_rate": 19.00
      },
      {
        "name": "Backend API Development",
        "quantity": 30,
        "unit_price": 60.00,
        "tax_rate": 19.00
      }
    ]
  }'
```

#### **Get Invoice (includes PDF download URL)**
```bash
curl -X GET http://localhost:8000/api/invoices/1 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept: application/json"
```

#### **List Invoices with Filters**
```bash
# Get all invoices (admin only)
curl -X GET "http://localhost:8000/api/invoices?status=pending&limit=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept: application/json"

# Client users only see their own invoices
curl -X GET "http://localhost:8000/api/invoices" \
  -H "Authorization: Bearer CLIENT_JWT_TOKEN" \
  -H "Accept: application/json"
```

#### **Create Client**
```bash
curl -X POST http://localhost:8000/api/clients \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@example.com",
    "phone": "+1234567890",
    "document_type": "cedula",
    "document_number": "12345678"
  }'
```

#### **Health Check**
```bash
curl -X GET http://localhost:8000/api/health \
  -H "Accept: application/json"
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

### **Cloudflare R2 Storage Endpoints**
```bash
# Note: PDF files are automatically generated and stored in R2
# when invoices are created or updated. Manual file uploads
# for additional attachments:

POST   /api/files/upload/invoice-attachment    # Upload additional files
GET    /api/files/download/invoice-attachment/{filename} # Download files
DELETE /api/files/delete/invoice-attachment    # Delete files

# Automatic PDF storage paths in R2:
# invoices/invoice_{invoice_number}_{date}_{unique_id}.pdf
```

#### **PDF Download Example**
```bash
# Download automatically generated PDF
curl -X GET "http://localhost:8000/api/invoices/1/pdf" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept: application/pdf" \
  --output "invoice_1.pdf"
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

### **🐳 Docker Setup & Architecture**

The project uses a multi-container Docker setup optimized for development and production:

#### **Container Architecture**
```
┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │     Backend     │
│  (Vue.js SPA)   │    │  (Laravel API)  │
│  Port: 5173     │    │                 │
└─────────────────┘    └─────────────────┘
                              │
                    ┌─────────────────┐
                    │     Nginx       │
                    │   Port: 8000    │
                    └─────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   PostgreSQL    │    │      Redis      │    │  Cloudflare R2  │
│   Port: 5432    │    │   Port: 6379    │    │  (Cloud Storage) │
│   (Database)    │    │   (Cache)       │    │   (PDF Files)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

#### **Docker Optimizations**

**Multi-stage Build**
- **Builder stage**: Installs dependencies and builds the application
- **Production stage**: Creates minimal runtime image with only necessary components
- **Size reduction**: ~60% smaller final image compared to single-stage builds

**Security Enhancements**
- ✅ **Non-root user**: Application runs as `appuser` for security
- ✅ **Minimal attack surface**: Only runtime dependencies in final image
- ✅ **Layer optimization**: Combined RUN commands reduce layers and vulnerabilities
- ✅ **Latest base images**: PHP 8.3-fpm with security updates

**Performance Features**
- ✅ **OPcache enabled**: Pre-compiled PHP for faster execution
- ✅ **Composer optimization**: `--classmap-authoritative` for production
- ✅ **Layer caching**: Dependencies installed before copying source code
- ✅ **Configuration caching**: Laravel config/routes/views cached on startup

**Automatic Database Migrations**
- ✅ **Smart startup**: Waits for database connectivity before starting
- ✅ **Auto-migration**: Runs `php artisan migrate --force` when `AUTO_MIGRATE=true`
- ✅ **Zero-downtime**: No manual intervention required for database setup
- ✅ **Production-safe**: Can be disabled by setting `AUTO_MIGRATE=false`

**Health Monitoring & Error Handling**
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

#### **☁️ Cloudflare R2 Configuration**
```bash
# S3-Compatible Cloud Storage for PDFs
CLOUDFLARE_R2_ACCESS_KEY_ID=your_access_key
CLOUDFLARE_R2_SECRET_ACCESS_KEY=your_secret_key
CLOUDFLARE_R2_ENDPOINT=https://your-account-id.r2.cloudflarestorage.com
CLOUDFLARE_R2_BUCKET=invoices
CLOUDFLARE_R2_URL=https://your-account-id.r2.cloudflarestorage.com
CLOUDFLARE_R2_DEFAULT_REGION=auto
FILESYSTEM_DISK=r2
```

> **🔐 Security Note**: The provided R2 credentials in the .env file are for development only. Replace with your own Cloudflare R2 credentials for production use.

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

## 🔧 **Development Workflow**

### **Backend Development**
```bash
# Access Laravel container for development
docker-compose exec app bash

# Run migrations
php artisan migrate

# Create new migration
php artisan make:migration create_example_table

# Create new controller
php artisan make:controller ExampleController

# Test PDF generation manually
php artisan tinker
>>> $invoice = App\Models\Invoice::find(1);
>>> $pdfService = new App\Services\InvoicePdfService();
>>> $pdfService->generatePdf($invoice);

# View Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### **Frontend Development**
```bash
# Install new package
pnpm add package-name

# Development with hot reload
pnpm dev

# Build for production
pnpm build

# Preview production build
pnpm preview

# Type check all files
pnpm type-check

# Lint and format code
npm run lint
```

### **Testing PDF Generation**
```bash
# Create a test invoice via API to trigger PDF generation
curl -X POST http://localhost:8000/api/invoices \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "description": "Test Invoice",
    "issue_date": "2025-01-15",
    "due_date": "2025-02-15",
    "items": [{
      "name": "Test Service",
      "quantity": 1,
      "unit_price": 100.00,
      "tax_rate": 19.00
    }]
  }'

# Check if PDF was generated in R2 storage
# PDFs are stored as: invoices/invoice_{number}_{date}_{id}.pdf
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

## 👨‍💻 **Technical Stack & Features**

### **Backend (Laravel 12) Features** ✅
- ✅ **Modular architecture** with services and interfaces
- ✅ **JWT authentication** with Laravel Sanctum
- ✅ **Complete CRUD endpoints** for invoices, clients, users
- ✅ **Role-based authorization** (admin/client permissions)
- ✅ **Advanced filtering and sorting** with query builders
- ✅ **Database migrations** (no SQL files)
- ✅ **Data validation** and business logic
- ✅ **Artisan commands** with business logic
- ✅ **Redis caching** implementation
- ✅ **Comprehensive event logging**
- ✅ **Database seeders** for setup
- ✅ **DomPDF integration** for automatic PDF generation
- ✅ **Cloudflare R2** S3-compatible storage
- ✅ **Observer pattern** for automatic PDF creation
- ✅ **Docker containerization** with health checks

### **Frontend (Vue.js 3 + TypeScript) Features** ✅
- ✅ **Vue Router** for client-side navigation
- ✅ **Pinia** for state management with persistence
- ✅ **TypeScript** for type safety and better DX
- ✅ **PrimeVue** modern UI component library
- ✅ **Login and registration** screens with validation
- ✅ **Invoice listing** with filters and pagination
- ✅ **Invoice create/edit** forms with multi-item support
- ✅ **Invoice detail** screens with PDF download
- ✅ **Client listing** with filters and pagination
- ✅ **Client update** forms with validation
- ✅ **User listing** with filters and pagination (admin only)
- ✅ **Vee-Validate + Yup** for form validation
- ✅ **Axios** for API communication
- ✅ **Beautiful toast notifications** with animations
- ✅ **Responsive design** with 1440px max-width layout
- ✅ **Role-based UI** (admin vs client views)

### **Technology Highlights**
- 🐘 **PHP 8.3** with modern Laravel 12 features
- ⚡ **Vue.js 3** with Composition API and TypeScript
- 🗄️ **PostgreSQL 15** with performance optimizations
- 🔴 **Redis** for caching and session management
- ☁️ **Cloudflare R2** for scalable cloud storage
- 📄 **DomPDF** for professional invoice PDFs
- 🐳 **Docker** with multi-stage builds and health checks
- 🎨 **TailwindCSS + PrimeVue** for modern UI/UX
- 🔐 **JWT authentication** with secure token management
- 📱 **Mobile-first responsive design**

---

🎉 **Ready to manage invoices like a pro!** 🚀