Laravel Order Management System
A simple Order Management System built with Laravel 11, allowing users to manage customers, products, and orders.

Features
✅ User Authentication (Laravel Breeze)
✅ Customer Management (CRUD)
✅ Product Management (CRUD)
✅ Order Management with Stock Updates
✅ REST API for Orders
✅ Search & Pagination for Products and Orders
✅ Email Confirmation on Order Placement (Laravel Queues)

Installation Guide

1. Clone the Repository
   git clone https://github.com/your-username/order-management.git
   cd order-management

2. Install Dependencies
   composer install
   npm install

3. Configure Environment
   cp .env.example .env
   Open .env and set your database credentials:
   env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

4. Generate Application Key
   php artisan key:generate

5. Run Database Migrations & Seeders
   php artisan migrate

6. Run the Development Server
   php artisan serve

Visit http://127.0.0.1:8000 in your browser.
