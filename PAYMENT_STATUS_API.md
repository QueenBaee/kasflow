# Customer Payment Status API Documentation

## Endpoint
```
GET /api/stores/{store}/customers/payment-status
```

## Authentication
Requires Bearer token (Sanctum)

## Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| status | string | No | Filter by payment status: `lunas`, `belum_bayar`, `jatuh_tempo` |

## Response Format

```json
[
  {
    "id": 1,
    "name": "Budi Santoso",
    "phone": "081234567890",
    "address": "Jl. Merdeka No. 10, Jakarta",
    "speed_package": "10 Mbps",
    "monthly_fee": 250000,
    "join_date": "2024-01-15",
    "due_date": 5,
    "last_payment_date": "2026-03-01",
    "payment_status": "LUNAS"
  }
]
```

## Payment Status Logic

### LUNAS
Customer has made payment in current month

### BELUM_BAYAR
- Today's date > billing_date (due_date)
- No payment in current month

### JATUH_TEMPO
- Today's date <= billing_date (due_date)
- No payment in current month

## Example Requests

### Get all customers with payment status
```bash
curl -X GET "http://localhost/api/stores/1/customers/payment-status" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Filter by LUNAS
```bash
curl -X GET "http://localhost/api/stores/1/customers/payment-status?status=lunas" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Filter by BELUM_BAYAR
```bash
curl -X GET "http://localhost/api/stores/1/customers/payment-status?status=belum_bayar" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Filter by JATUH_TEMPO
```bash
curl -X GET "http://localhost/api/stores/1/customers/payment-status?status=jatuh_tempo" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Model Scopes

You can also use query scopes directly in your code:

```php
// Get customers who have paid this month
$lunasCustomers = Customer::lunas()->get();

// Get customers who haven't paid and passed due date
$belumBayarCustomers = Customer::belumBayar()->get();

// Get customers approaching due date
$jatuhTempoCustomers = Customer::jatuhTempo()->get();
```

## Frontend Implementation

The payment status is automatically displayed in the Customers page with color-coded badges:

- **LUNAS**: Green badge
- **BELUM BAYAR**: Red badge
- **JATUH TEMPO**: Yellow badge

Status is calculated in real-time based on:
1. Customer's billing date (due_date)
2. Last payment transaction date
3. Current server date/time
