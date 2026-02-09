# Kasflow - Financial Recording SaaS for Warung

Multi-tenant financial recording system for small grocery stores with role-based access control.

## 🚀 Quick Start

### Prerequisites
- Laravel Herd installed
- MySQL running
- PHP 8.2+

### Installation

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasflow
DB_USERNAME=root
DB_PASSWORD=

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed

# Install Sanctum (if not already)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Sample Credentials
```
Owner:
- Email: owner@example.com
- Password: password

Cashier 1:
- Email: cashier1@example.com
- Password: password

Cashier 2:
- Email: cashier2@example.com
- Password: password
```

## 📚 API Documentation

Base URL: `http://kasflow-app.test/api`

### Authentication

#### Register
```http
POST /register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```http
POST /login
Content-Type: application/json

{
  "email": "owner@example.com",
  "password": "password"
}

Response:
{
  "user": {...},
  "token": "1|xxxxx..."
}
```

#### Logout
```http
POST /logout
Authorization: Bearer {token}
```

### Store Management

#### List Stores
```http
GET /stores
Authorization: Bearer {token}
```

#### Create Store
```http
POST /stores
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Warung Maju Jaya"
}
```

#### Update Store
```http
PUT /stores/{storeId}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Warung Berkah"
}
```

#### Delete Store
```http
DELETE /stores/{storeId}
Authorization: Bearer {token}
```

#### Assign Cashier
```http
POST /stores/{storeId}/cashiers
Authorization: Bearer {token}
Content-Type: application/json

{
  "user_id": 2
}
```

#### Remove Cashier
```http
DELETE /stores/{storeId}/cashiers/{userId}
Authorization: Bearer {token}
```

### Transactions

#### Record Income (Cashier/Owner)
```http
POST /stores/{storeId}/transactions/income
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 50000,
  "note": "Penjualan hari ini"
}
```

#### Record Expense (Owner Only)
```http
POST /stores/{storeId}/transactions/expense
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 100000,
  "category": "Supplies",
  "note": "Pembelian barang",
  "date": "2024-01-15"
}
```

#### List Transactions
```http
GET /stores/{storeId}/transactions?date=2024-01-15&type=income
Authorization: Bearer {token}
```

#### Today's Income
```http
GET /stores/{storeId}/transactions/today-income
Authorization: Bearer {token}
```

### Reports (Owner Only)

#### Daily Report
```http
GET /stores/{storeId}/reports/daily?date=2024-01-15
Authorization: Bearer {token}

Response:
{
  "data": {
    "date": "2024-01-15",
    "total_income": 500000,
    "total_expense": 150000,
    "profit": 350000,
    "income_count": 10,
    "expense_count": 3
  }
}
```

#### Weekly Report
```http
GET /stores/{storeId}/reports/weekly?start_date=2024-01-01&end_date=2024-01-07
Authorization: Bearer {token}
```

#### Monthly Report
```http
GET /stores/{storeId}/reports/monthly?month=1&year=2024
Authorization: Bearer {token}
```

## 🏗️ Architecture

### Database Schema

**users**
- id, name, email, password

**stores**
- id, owner_id, name

**store_users** (pivot)
- id, store_id, user_id, role (owner/cashier)

**transactions**
- id, store_id, user_id, type (income/expense), amount, category, note, transaction_date

### Key Design Patterns

1. **Service Layer Pattern**
   - `ReportService`: Business logic for financial calculations
   - `TransactionService`: Transaction creation logic

2. **Policy-Based Authorization**
   - `StorePolicy`: Centralized access control

3. **Form Request Validation**
   - Separate validation classes for each endpoint

4. **Multi-Tenancy**
   - Pivot table with roles
   - Query scoping by store_id

### Security Features

- Laravel Sanctum token authentication
- Policy-based authorization
- Role-based access control
- Store membership validation
- Input validation with positive amount checks

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ReportTest
```

## 📊 Performance Optimizations

1. **Database Indexes**
   - Composite index on (store_id, type, transaction_date)
   - Index on (store_id, transaction_date)
   - Index on (user_id, role) in pivot table

2. **Query Optimization**
   - Single query for report calculations using CASE statements
   - Eager loading relationships

3. **Caching Strategy** (Future)
   - Cache daily reports
   - Cache today's income totals

## 🔄 Future Enhancements

- Subscription billing integration
- Mobile app API endpoints
- Real-time notifications
- Export reports to PDF/Excel
- Multi-currency support
- Inventory management
- Receipt printing

## 📱 Mobile-First UI Considerations

For frontend implementation:
- Large touch targets for numeric input
- Minimal form fields
- Quick-add buttons
- Offline support with sync
- Progressive Web App (PWA)

## 🛠️ Troubleshooting

### Migration Issues
```bash
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Permission Errors
Ensure storage and bootstrap/cache are writable:
```bash
chmod -R 775 storage bootstrap/cache
```
