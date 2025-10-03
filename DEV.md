# Invoice Management System - Developer API Documentation

## 🚀 Quick Start

```bash
# Start Docker containers
cd backend
docker compose up -d

# Run migrations and seeders
docker compose exec app php artisan migrate --seed

# Test API
curl http://localhost:8000/api/auth/login
```

## 🔐 Authentication

All API endpoints require authentication except login and register.

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@invoice.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@invoice.com",
    "roles": ["admin"]
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Register
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Developer",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 👥 User Management

### Get Current User
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### List Users (Admin only)
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update User Role (Admin only)
```bash
curl -X PATCH http://localhost:8000/api/users/2/role \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "role": "manager"
  }'
```

## 🏢 Client Management

### List Clients
```bash
curl -X GET http://localhost:8000/api/clients \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Client
```bash
curl -X POST http://localhost:8000/api/clients \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Juan",
    "last_name": "Pérez",
    "document_type": "cedula",
    "document_number": "12345678",
    "email": "juan.perez@example.com",
    "phone": "+57 300 123 4567"
  }'
```

### Get Client Details
```bash
curl -X GET http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update Client
```bash
curl -X PUT http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Juan Carlos",
    "last_name": "Pérez García",
    "phone": "+57 300 987 6543"
  }'
```

### Delete Client
```bash
curl -X DELETE http://localhost:8000/api/clients/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📄 Invoice Management

### List Invoices
```bash
curl -X GET http://localhost:8000/api/invoices \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Filter Invoices
```bash
# By status
curl -X GET "http://localhost:8000/api/invoices?status=pending" \
  -H "Authorization: Bearer YOUR_TOKEN"

# By client
curl -X GET "http://localhost:8000/api/invoices?client_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN"

# By date range
curl -X GET "http://localhost:8000/api/invoices?date_from=2025-01-01&date_to=2025-12-31" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Search by invoice number or description
curl -X GET "http://localhost:8000/api/invoices?search=INV-2025" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Invoice
```bash
curl -X POST http://localhost:8000/api/invoices \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "client_id": 1,
    "description": "Servicios de desarrollo web",
    "additional_notes": "Proyecto completado según especificaciones",
    "issue_date": "2025-10-03",
    "due_date": "2025-11-03",
    "items": [
      {
        "name": "Desarrollo Frontend React",
        "quantity": 40,
        "unit_price": 50000,
        "tax_rate": 19.0
      },
      {
        "name": "Desarrollo Backend Laravel",
        "quantity": 60,
        "unit_price": 60000,
        "tax_rate": 19.0
      },
      {
        "name": "Base de datos PostgreSQL",
        "quantity": 1,
        "unit_price": 300000,
        "tax_rate": 19.0
      }
    ]
  }'
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "invoice_number": "INV-202510-0001",
    "description": "Servicios de desarrollo web",
    "additional_notes": "Proyecto completado según especificaciones",
    "issue_date": "2025-10-03",
    "due_date": "2025-11-03",
    "total_amount": 6069000.00,
    "status": "pending",
    "client": {
      "id": 1,
      "full_name": "Juan Pérez",
      "email": "juan.perez@example.com"
    },
    "items": [
      {
        "id": 1,
        "name": "Desarrollo Frontend React",
        "quantity": 40,
        "unit_price": 50000.00,
        "tax_rate": 19.00,
        "tax_amount": 380000.00,
        "total_amount": 2380000.00
      }
    ]
  }
}
```

### Get Invoice Details
```bash
curl -X GET http://localhost:8000/api/invoices/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update Invoice
```bash
curl -X PUT http://localhost:8000/api/invoices/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Servicios de desarrollo web - ACTUALIZADO",
    "due_date": "2025-12-03",
    "items": [
      {
        "name": "Desarrollo Full-Stack",
        "quantity": 100,
        "unit_price": 55000,
        "tax_rate": 19.0
      }
    ]
  }'
```

### Update Invoice Status
```bash
curl -X PATCH http://localhost:8000/api/invoices/1/status \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "paid"
  }'
```

### Delete Invoice
```bash
curl -X DELETE http://localhost:8000/api/invoices/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 📎 File Upload

### Upload Invoice Attachment
```bash
curl -X POST http://localhost:8000/api/upload/invoice-attachment \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "file=@/path/to/invoice.pdf"
```

**Response:**
```json
{
  "message": "File uploaded successfully",
  "public_id": "invoices/attachments/invoice_abc123",
  "secure_url": "https://res.cloudinary.com/your-cloud/image/upload/v1696345200/invoices/attachments/invoice_abc123.pdf",
  "original_name": "invoice.pdf",
  "file_size": 245760,
  "file_type": "raw",
  "format": "pdf"
}
```

### Get Optimized File URL
```bash
curl -X POST http://localhost:8000/api/files/optimized-url \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "public_id": "invoices/attachments/invoice_abc123",
    "width": 800,
    "height": 600,
    "quality": "auto"
  }'
```

### Delete File
```bash
curl -X DELETE http://localhost:8000/api/files \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "public_id": "invoices/attachments/invoice_abc123"
  }'
```

## 📊 Reports & Analytics

### Dashboard Statistics
```bash
curl -X GET http://localhost:8000/api/dashboard/stats \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Response:**
```json
{
  "total_invoices": 25,
  "pending_invoices": 8,
  "paid_invoices": 15,
  "overdue_invoices": 2,
  "total_amount": 15750000.00,
  "paid_amount": 9450000.00,
  "pending_amount": 6300000.00,
  "recent_invoices": [...]
}
```

### Export Invoices (PDF/Excel)
```bash
curl -X GET "http://localhost:8000/api/invoices/export?format=pdf&date_from=2025-01-01&date_to=2025-12-31" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  --output invoices.pdf
```

## 🛡️ Roles & Permissions

### Default Users (from seeder)
- **Admin:** `admin@invoice.com` / `password123`
- **Manager:** `manager@invoice.com` / `password123` 
- **User:** `user@invoice.com` / `password123`
- **Accountant:** `accountant@invoice.com` / `password123`

### Permission Levels
- **Admin:** Full access to everything
- **Manager:** Manage invoices and clients (no user management)
- **Accountant:** View/create invoices, reports, limited client access
- **User:** Basic invoice and client operations

## 🔍 Search & Filtering

### Advanced Invoice Search
```bash
curl -X GET "http://localhost:8000/api/invoices?search=desarrollo&status=pending&total_amount_min=1000000&sort_by=total_amount&sort_direction=desc" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Available Filters
- `search` - Search in invoice number, description, client name
- `status` - pending, paid, overdue
- `client_id` - Filter by specific client
- `date_from` / `date_to` - Issue date range
- `due_date_from` / `due_date_to` - Due date range
- `total_amount_min` / `total_amount_max` - Amount range
- `sort_by` - Field to sort by
- `sort_direction` - asc or desc

## 🐛 Error Handling

### Common HTTP Status Codes
- `200` - Success
- `201` - Created successfully
- `204` - No content (successful deletion)
- `400` - Bad request
- `401` - Unauthorized (invalid/missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not found
- `422` - Validation error
- `500` - Server error

### Error Response Format
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "client_id": ["The selected client id is invalid."]
  }
}
```

## 🔧 Environment Setup

### Required Environment Variables
```bash
# Database
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=invoice_db
DB_USERNAME=postgres
DB_PASSWORD=secret

# Redis Cache
REDIS_HOST=redis
REDIS_PORT=6379

# Cloudinary (Sign up at cloudinary.com)
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

## 🧪 Testing

### Run Tests
```bash
# All tests
docker compose exec app php artisan test

# Specific test file
docker compose exec app php artisan test tests/Unit/InvoiceServiceTest.php

# With coverage
docker compose exec app php artisan test --coverage
```

### Test Database
Tests use a separate SQLite database and are automatically reset between runs.

## 📝 Development Notes

### API Rate Limiting
- 60 requests per minute for authenticated users
- 10 requests per minute for unauthenticated users

### Pagination
All list endpoints support pagination:
```bash
curl -X GET "http://localhost:8000/api/invoices?page=2&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### CORS
Configured to allow requests from your frontend domain. Update in `config/cors.php` if needed.

### Logging
All API requests are logged with:
- Request/response details
- User context
- Performance metrics
- Error tracking

Check logs in `storage/logs/laravel.log`

---

## 🚀 Production Deployment

1. Set up environment variables
2. Configure Cloudinary account
3. Run migrations: `php artisan migrate --seed`
4. Set up SSL certificates
5. Configure web server (Nginx/Apache)
6. Set up monitoring and backups

For detailed deployment instructions, see the main README.md file.