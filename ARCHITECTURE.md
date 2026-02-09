# Kasflow Architecture Documentation

## 📐 System Architecture

### Multi-Tenant Design

**Approach: Shared Database with Row-Level Isolation**

```
users (1) ----< store_users >---- (N) stores
                   |
                 role: owner/cashier
```

**Why this approach?**
- ✅ Flexible: Users can belong to multiple stores with different roles
- ✅ Scalable: No database per tenant overhead
- ✅ Cost-effective: Single database maintenance
- ✅ Query performance: Proper indexing on store_id
- ✅ Data isolation: Enforced at application layer

**Alternative considered:**
- Database per tenant: Rejected due to complexity and cost for small warung use case
- Schema per tenant: Overkill for this scale

### Authorization Strategy

**Policy-Based Authorization (Laravel Gates & Policies)**

```php
StorePolicy
├── view()          // Any store member
├── update()        // Owner only
├── delete()        // Owner only
├── manageCashiers() // Owner only
├── viewReports()   // Owner only
└── createExpense() // Owner only
```

**Why Policies?**
- ✅ Centralized authorization logic
- ✅ Testable and maintainable
- ✅ Automatic integration with controllers via authorize()
- ✅ Clear separation of concerns

**Security Layers:**
1. Sanctum authentication (API token)
2. Store membership validation (middleware)
3. Role-based authorization (policies)
4. Input validation (Form Requests)

### Service Layer Pattern

**Business Logic Separation**

```
Controller → Service → Model → Database
```

**Services:**
- `ReportService`: Financial calculations and aggregations
- `TransactionService`: Transaction creation with business rules

**Why Service Layer?**
- ✅ Thin controllers (single responsibility)
- ✅ Reusable business logic
- ✅ Easier testing (mock services)
- ✅ Clear separation: Controllers handle HTTP, Services handle business logic

**Example:**
```php
// Controller: HTTP concerns
public function storeIncome(StoreIncomeRequest $request, Store $store)
{
    $transaction = $this->transactionService->createIncome(...);
    return response()->json($transaction, 201);
}

// Service: Business logic
public function createIncome(int $storeId, User $user, array $data)
{
    return Transaction::create([...]);
}
```

## 🗄️ Database Design

### Indexing Strategy

**Critical Indexes:**

```sql
-- transactions table
INDEX idx_store_type_date (store_id, type, transaction_date)
INDEX idx_store_date (store_id, transaction_date)

-- store_users table
UNIQUE idx_store_user (store_id, user_id)
INDEX idx_user_role (user_id, role)

-- stores table
INDEX idx_owner (owner_id)
```

**Why these indexes?**
- Report queries filter by store_id + date range → Composite index
- Transaction listing by store → store_id index
- Role checking → user_id + role index
- Unique constraint prevents duplicate assignments

### Query Optimization

**Report Calculation (Single Query)**

```php
DB::table('transactions')
    ->where('store_id', $storeId)
    ->where('transaction_date', $date)
    ->select(
        DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income'),
        DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense')
    )
    ->first();
```

**Why single query?**
- ✅ 1 database round-trip vs 2 separate queries
- ✅ Atomic calculation
- ✅ Better performance under load

**Alternative (rejected):**
```php
// Two queries - slower
$income = Transaction::where('type', 'income')->sum('amount');
$expense = Transaction::where('type', 'expense')->sum('amount');
```

## 🔐 Security Architecture

### Authentication Flow

```
1. User registers/logs in
2. Sanctum generates API token
3. Token stored client-side
4. Every request includes: Authorization: Bearer {token}
5. Sanctum middleware validates token
6. Request proceeds with authenticated user
```

**Why Sanctum?**
- ✅ Built for SPA/mobile apps
- ✅ Lightweight (no OAuth complexity)
- ✅ Token-based (stateless)
- ✅ Laravel native integration

### Authorization Flow

```
1. Request arrives with token
2. Sanctum authenticates user
3. Middleware checks store membership
4. Policy checks specific permission
5. Action allowed/denied
```

**Example:**
```
POST /stores/1/transactions/expense
↓
auth:sanctum → User authenticated
↓
Route model binding → Store loaded
↓
StorePolicy::createExpense() → Check if user is owner
↓
Action executed or 403 Forbidden
```

## 📊 Data Flow

### Income Recording (Cashier)

```
Mobile App
    ↓ POST /stores/1/transactions/income
    ↓ {amount: 50000, note: "..."}
API Route (auth:sanctum)
    ↓
TransactionController::storeIncome()
    ↓ Validate: User has store access
    ↓ Validate: Amount > 0
    ↓
TransactionService::createIncome()
    ↓ Set type = 'income'
    ↓ Set transaction_date = today
    ↓ Set user_id, store_id
    ↓
Transaction Model → Database
    ↓
Response: 201 Created
```

### Report Generation (Owner)

```
Dashboard
    ↓ GET /stores/1/reports/daily?date=2024-01-15
API Route (auth:sanctum)
    ↓
ReportController::dailyReport()
    ↓ Policy: Check if owner
    ↓
ReportService::dailyReport()
    ↓ Single optimized query
    ↓ Calculate: income, expense, profit
    ↓
Response: JSON report
```

## 🎯 Design Decisions & Trade-offs

### 1. Enum vs Lookup Table for Transaction Type

**Decision: ENUM('income', 'expense')**

**Pros:**
- ✅ Simple and fast
- ✅ Database-level constraint
- ✅ Only 2 fixed types

**Cons:**
- ❌ Schema change needed to add types

**Why chosen:** Only 2 types, unlikely to change. Simplicity wins.

### 2. Soft Deletes vs Hard Deletes

**Decision: Hard deletes (can add soft deletes later)**

**Reasoning:**
- MVP simplicity
- Can add `deleted_at` column later if needed
- Financial data should be preserved → Add soft deletes in production

### 3. Category as String vs Foreign Key

**Decision: VARCHAR(100) nullable**

**Pros:**
- ✅ Flexible (owners can type anything)
- ✅ No category management UI needed
- ✅ Faster MVP

**Cons:**
- ❌ No standardization
- ❌ Typos possible

**Future:** Add categories table + autocomplete

### 4. Transaction Date vs Created At

**Decision: Separate transaction_date field**

**Why:**
- ✅ Owner can backdate expenses
- ✅ Reports based on transaction date, not entry date
- ✅ Audit trail preserved (created_at still exists)

### 5. Pagination Strategy

**Decision: 50 items per page**

**Reasoning:**
- Mobile-friendly
- Balance between requests and data transfer
- Can be adjusted via query param

## 🚀 Performance Considerations

### Current Optimizations

1. **Database Indexes** - Fast lookups on common queries
2. **Single Query Reports** - Reduced database round-trips
3. **Eager Loading** - Prevent N+1 queries
4. **Query Scopes** - Reusable query logic

### Future Optimizations

1. **Caching**
   ```php
   Cache::remember("store.{$storeId}.today-income", 300, function() {
       return $this->transactionService->getTodayIncome($storeId);
   });
   ```

2. **Queue Jobs**
   - Monthly report generation
   - Email notifications

3. **Database Partitioning**
   - Partition transactions by year when data grows

4. **Read Replicas**
   - Reports from read replica
   - Writes to primary

## 🧪 Testing Strategy

### Test Pyramid

```
        /\
       /  \  E2E Tests (Few)
      /____\
     /      \  Integration Tests (Some)
    /________\
   /          \  Unit Tests (Many)
  /__________\
```

**Current Coverage:**
- ✅ Feature tests for critical paths
- ✅ Authorization tests
- ✅ Validation tests

**Future:**
- Unit tests for services
- Browser tests for UI

## 📱 API Design Principles

### RESTful Structure

```
GET    /stores              → List stores
POST   /stores              → Create store
GET    /stores/{id}         → Show store
PUT    /stores/{id}         → Update store
DELETE /stores/{id}         → Delete store

POST   /stores/{id}/cashiers           → Assign cashier
DELETE /stores/{id}/cashiers/{userId}  → Remove cashier

POST   /stores/{id}/transactions/income   → Record income
POST   /stores/{id}/transactions/expense  → Record expense
GET    /stores/{id}/transactions          → List transactions

GET    /stores/{id}/reports/daily    → Daily report
GET    /stores/{id}/reports/weekly   → Weekly report
GET    /stores/{id}/reports/monthly  → Monthly report
```

**Why nested routes?**
- ✅ Clear resource hierarchy
- ✅ Store context in URL
- ✅ Automatic route model binding

### Response Format

**Success:**
```json
{
  "data": {...},
  "message": "Success"
}
```

**Error:**
```json
{
  "message": "Validation failed",
  "errors": {
    "amount": ["Amount must be greater than 0"]
  }
}
```

## 🔄 Scalability Path

### Phase 1: MVP (Current)
- Single server
- MySQL database
- File-based sessions

### Phase 2: Growth (1000+ stores)
- Redis for caching
- Queue workers
- Database indexing optimization

### Phase 3: Scale (10,000+ stores)
- Load balancer
- Database read replicas
- CDN for assets
- Horizontal scaling

### Phase 4: Enterprise (100,000+ stores)
- Microservices (if needed)
- Database sharding
- Event-driven architecture
- Multi-region deployment

## 📚 Code Organization

```
app/
├── Http/
│   ├── Controllers/        # HTTP layer
│   ├── Requests/           # Validation
│   └── Middleware/         # Request filtering
├── Models/                 # Eloquent models
├── Policies/               # Authorization
└── Services/               # Business logic

database/
├── migrations/             # Schema
└── seeders/                # Sample data

tests/
└── Feature/                # Integration tests
```

**Why this structure?**
- ✅ Laravel conventions
- ✅ Clear separation of concerns
- ✅ Easy to navigate
- ✅ Scalable as app grows

## 🎓 Best Practices Applied

1. **SOLID Principles**
   - Single Responsibility: Controllers, Services, Policies
   - Open/Closed: Extendable via policies
   - Dependency Injection: Services injected into controllers

2. **DRY (Don't Repeat Yourself)**
   - Query scopes for reusable queries
   - Service layer for shared logic

3. **Security First**
   - Input validation
   - Authorization checks
   - SQL injection prevention (Eloquent)
   - XSS prevention (JSON responses)

4. **Performance Aware**
   - Database indexes
   - Optimized queries
   - Pagination

5. **Testable Code**
   - Service layer easily mockable
   - Policies testable in isolation
   - Feature tests for critical paths
