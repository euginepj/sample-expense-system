# Expense System API Documentation

## Authentication
Use Bearer token authentication. Get token from user's `auth_key` field.

Header: `Authorization: Bearer <auth_key>`

## Endpoints

### GET /api/expense
List expenses. Admins see all, employees see only their own.

Query Parameters:
- `status` (optional): pending, approved, rejected
- `category` (optional): travel, meals, office_supplies, entertainment, other

### GET /api/expense/{id}
Get specific expense

### POST /api/expense
Create new expense

Request Body:
```json
{
    "amount": 100.50,
    "description": "Business lunch",
    "category": "meals"
}

### PUT /api/expense/{id}
Update expense (admins only for status changes)

### DELETE /api/expense/{id}
Delete expense

## Example Usage
`curl -H "Authorization: Bearer YOUR_AUTH_KEY" http://localhost/api/expense

# Create new expense
curl -X POST -H "Authorization: Bearer YOUR_AUTH_KEY" -H "Content-Type: application/json" \
-d '{"amount": 75.25, "description": "Office supplies", "category": "office_supplies"}' \
http://localhost/api/expense`
