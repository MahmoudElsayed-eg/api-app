# Laravel Application Installation Guide

## Prerequisites
Before you begin, ensure you have the following installed:
- PHP 8.1+
- Composer
- MySQL or PostgreSQL
- Node.js & npm (if using frontend assets)
- Laravel 12

## Installation Steps

### 1. Install Dependencies
```bash
composer install
npm install  # If using frontend assets
```

### 2. Configure Environment Variables
Copy the example `.env` file and update your database credentials:
```bash
cp .env.example .env
```
Edit `.env` and set up database connection:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations & Seed Database
```bash
php artisan migrate --seed
```

### 5. Install & Configure Passport
```bash
php artisan passport:keys
php artisan passport:client --personal
```

### 6. Serve the Application
```bash
php artisan serve
```
The application should now be accessible at [http://127.0.0.1:8000](http://127.0.0.1:8000).

## API Documentation
To view API documentation, visit:
```
http://127.0.0.1:8000/docs
```

## Test Credentials
You can use the following test credentials:
```
Email: test@example.com
Password: password
```

---

### 🎉 Your Laravel application with Passport authentication is ready!

