🛒 Mini E-Commerce REST API

A production-ready RESTful API built with Laravel 12.

🚀 Features

🔐 Authentication using Laravel Sanctum

👥 Role-based Authorization (Admin & User)

📦 Product & Category Management

🛒 Shopping Cart System

💳 Transactional Checkout

📊 Order Status Workflow

❌ Order Cancellation Logic

📈 Admin Dashboard Summary

🧪 Full Feature API Testing

⚙️ CI/CD with GitHub Actions

🏗️ Tech Stack

Laravel 12

MySQL

Laravel Sanctum

Pest Feature Testing

GitHub Actions CI/CD

📌 API Structure
Auth

POST /api/register

POST /api/login

POST /api/logout

Products

GET /api/products

GET /api/products/{id}

POST (admin)

PUT (admin)

DELETE (admin)

Categories

GET /api/categories

POST/PUT/DELETE (admin)

Cart

GET /api/cart

POST /api/cart

PUT /api/cart/{item}

DELETE /api/cart/{item}

POST /api/cart/checkout

Orders

GET /api/orders

PATCH /api/orders/{id}/cancel

PATCH /api/orders/{id}/status (admin)

🧪 Running Tests
php artisan test
⚙️ Setup
git clone https://github.com/alfinf2/mini-ecommerce-api.git
cd mini-ecommerce-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
📈 CI/CD

Every push to main branch automatically runs:

Composer Install

Database Migration

Full Feature Testing

👨‍💻 Author

Alvin F