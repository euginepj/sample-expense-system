# Expense Submission & Review System

A Yii2-based internal tool for company expense management with web interface and REST API.

## Features

- Employee expense submission with receipt upload
- Admin review and approval system
- RESTful API for programmatic access
- Secure file handling and validation
- Role-based access control

## Setup

1. Clone the repository
2. Run `composer install`
3. Configure database in `config/db.php`
4. Run migrations: `./yii migrate`
5. Create uploads directory: `mkdir -p web/uploads/receipts`

## Default Users

- **Admin**: username: `admin`, password: `admin123`
- **Employee 1**: username: `employee1`, password: `emp123`
- **Employee 2**: username: `employee2`, password: `emp123`


## API URLS 

- GET list expense: `api/expense/index`
- GET view expense: `api/expense/view`
- DELETE delete expense: `api/expense/delete`
