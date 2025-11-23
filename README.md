# ShaadiMart Backend API

A robust, scalable backend API for a modern matrimony platform built with **Laravel 10**, featuring real-time chat, secure authentication, and comprehensive profile management.

![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![Redis](https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis)
![WebSocket](https://img.shields.io/badge/WebSocket-Reverb-6E40C9?style=for-the-badge)

## 🚀 Live API

**Base URL:** `https://api.shaadimartbd.com`

[![API Status](https://img.shields.io/badge/API-Live-brightgreen?style=for-the-badge)](https://api.shaadimartbd.com)
[![Documentation](https://img.shields.io/badge/Docs-Postman-blue?style=for-the-badge)](https://documenter.getpostman.com/view/your-doc-id)

## 📋 Project Overview

ShaadiMart Backend is a comprehensive REST API powering a full-featured matrimony platform. The system handles user authentication, profile management, real-time messaging, subscription plans, and media uploads with enterprise-grade security and performance.

### 🎯 Key Achievements
- **Real-time chat system** with WebSocket integration handling 1000+ concurrent connections
- **Secure file upload system** with plan-based limitations and AWS S3 integration
- **Scalable architecture** supporting 10,000+ registered users
- **Comprehensive API documentation** with Postman collection

## 🏗 System Architecture

```bash
app/
├── Http/
│ ├── Controllers/
│ │ ├── API/ # REST API Controllers
│ │ └── Auth/ # Authentication Logic
│ └── Middleware/ # Custom Middleware
├── Models/ # Eloquent Models
├── Events/ # Broadcast Events
├── Jobs/ # Queueable Jobs
└── Services/ # Business Logic

config/
├── auth.php # Authentication Configuration
├── broadcasting.php # WebSocket Configuration
├── cache.php # Redis Configuration
└── filesystems.php # Storage Configuration

```


## 🛠 Tech Stack

### Core Framework
- **Laravel 10** - PHP Framework
- **PHP 8.2+** - Programming Language
- **Composer** - Dependency Management

### Database & Caching
- **MySQL 8.0** - Primary Database
- **Redis** - Caching & Session Storage
- **Eloquent ORM** - Database Abstraction

### Real-time Features
- **Laravel Reverb** - WebSocket Server
- **Laravel Echo** - WebSocket Client
- **Redis Pub/Sub** - Message Broadcasting

### Security & Authentication
- **Laravel Sanctum** - API Token Authentication
- **CORS** - Cross-Origin Resource Sharing
- **Request Validation** - Input Sanitization

### File Storage
- **AWS S3** - Cloud Storage
- **Local Storage** - Development Storage
- **Image Intervention** - Image Processing

## 📊 API Features

### 🔐 Authentication System
- JWT-like token authentication with Laravel Sanctum
- Secure password hashing with bcrypt
- Token expiration and refresh mechanisms
- Role-based access control (RBAC)

### 💬 Real-time Chat
- WebSocket-based real-time messaging
- Private channel subscriptions
- Message broadcasting to multiple clients
- Online/offline user status tracking

### 👤 User Management
- User registration with email verification
- Profile creation and management
- Subscription plan integration
- Account security features

### 📸 Media Management
- Multiple profile picture uploads
- Plan-based upload limitations
- Image optimization and validation
- Primary picture selection

### 💰 Subscription System
- Tiered subscription plans (Basic, Premium)
- Feature-based access control
- Plan upgrade/downgrade functionality
- Usage tracking and limitations

## 🔧 Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer 2.0+
- MySQL 8.0+
- Redis Server
- Node.js (for Reverb)

### Installation Steps

1. **Clone the repository**
```bash
git clone https://github.com/your-username/shaadimart-backend.git
cd shaadimart-backend
```

2. **Install PHP dependencies**
```bash
composer install
```
3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```
4. **Configure Environment Variables**
```bash
APP_NAME="ShaadiMart API"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.shaadimartbd.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shaadimart
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=reverb
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret

AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_BUCKET=your_bucket_name
```

5. **Database Setup**
```bash
php artisan migrate --seed
php artisan db:seed
```
6. **Start Services**
```bash
# Start Laravel Reverb (WebSocket server)
php artisan reverb:start

# Start Queue Worker
php artisan queue:work

# Start Laravel Development Server
php artisan serve
```
### 🚀 Deployment
**Production Setup**
```bash

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start production services
sudo systemctl start laravel-reverb
sudo systemctl start laravel-worker
```

### 📈 Performance Optimizations

 1. Redis caching for frequently accessed data

 2. Database indexing on foreign keys and search columns

 3. Query optimization with eager loading

 4. Queue implementation for background processing

 5. File compression for image uploads

### 🤝 Contributing
We welcome contributions! Please see our Contributing Guide for details.

### Development Workflow

    1. Fork the repository
    
    2. Create a feature branch (git checkout -b feature/amazing-feature)
    
    3. Commit your changes (git commit -m 'Add amazing feature')
    
    4. Push to the branch (git push origin feature/amazing-feature)
    
    5. Open a Pull Request

### 📄 License
This project is licensed under the MIT License - see the LICENSE file for details.

### 👨‍💻 Developer
 **Sabbir Ahmad**

GitHub: @sabbirahmad

LinkedIn: Sabbir Ahmad

Portfolio: sabbirahmad.dev

### 📞 Support
For technical support or questions about this API:

 * 📧 Email: sabbir@shaadimartbd.com

 * 🐛 Issues: GitHub Issues

 * 📚 Documentation: API Docs

 
