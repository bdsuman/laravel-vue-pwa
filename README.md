# Laravel Vue PWA Boilerplate

![Laravel](https://img.shields.io/badge/Laravel-11-red) ![Vue](https://img.shields.io/badge/Vue-3-green) ![PWA](https://img.shields.io/badge/PWA-Enabled-blue) ![Docker](https://img.shields.io/badge/Docker-Ready-blue)

A production-ready Laravel 11 + Vue 3 PWA boilerplate with modern architecture patterns.

## 🚀 Features

- **Laravel 11** with Sanctum, Horizon, Telescope
- **Vue 3** with Composition API (JavaScript)
- **TailwindCSS** for styling
- **PWA** with offline support
- **i18n** - Multilanguage (English, Bengali, Hindi)
- **File Upload** with image cropping
- **Docker** ready with Nginx, Redis, PostgreSQL
- **PSR-12** code standards

## 🐳 Quick Start with Docker

```bash
# Build and start containers
docker-compose up -d --build

# Run migrations
docker-compose exec php php artisan migrate

# View logs
docker-compose logs -f
```

### Services

| Service | Port | Description |
|---------|------|-------------|
| Nginx | 80 | Web server |
| PHP-FPM | 9000 | Application |
| Redis | 6379 | Cache/Queue |
| PostgreSQL | 5432 | Database |

## 📦 Manual Setup

### Requirements

- PHP 8.2+
- Node.js 20+
- Composer 2+
- Redis
- PostgreSQL/MySQL/SQLite

### Installation

```bash
# Clone repository
git clone https://github.com/bdsuman/laravel-vue-pwa.git
cd laravel-vue-pwa

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate keys
php artisan key:generate

# Run migrations
php artisan migrate

# Build frontend
npm run build

# Start server
php artisan serve
```

## 🐘 API Endpoints

### Authentication
- `POST /api/register` - Register user
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/user` - Get current user

### Posts
- `GET /api/posts` - List posts
- `POST /api/posts` - Create post
- `PUT /api/posts/{id}` - Update post
- `DELETE /api/posts/{id}` - Delete post

### Categories
- `GET /api/categories` - List categories
- `POST /api/categories` - Create category
- `PUT /api/categories/{id}` - Update category
- `DELETE /api/categories/{id}` - Delete category

### File Upload
- `POST /api/upload` - Upload file with optional crop params
- `DELETE /api/upload` - Delete file

### Language
- `GET /api/locale/{locale}` - Switch language (en/bn/hi)

## 🌍 Multilanguage

Frontend languages: English 🇬🇧, Bengali 🇧🇩, Hindi 🇮🇳

```js
import { setLocale } from './i18n'
setLocale('bn') // Switch to Bengali
```

## 📁 Project Structure

```
├── app/
│   ├── DTOs/           # Data Transfer Objects
│   ├── Models/          # Eloquent Models
│   ├── Services/        # Business Logic
│   ├── Observers/       # Model Observers
│   ├── QueryBuilders/   # Custom Query Builders
│   └── Http/
│       ├── Controllers/ # Controllers
│       ├── Requests/    # Form Requests
│       └── Resources/   # API Resources
├── resources/js/
│   ├── views/           # Vue Views
│   ├── components/     # Vue Components
│   ├── stores/         # Pinia Stores
│   └── i18n/           # Translations
├── docker/             # Docker configs
├── database/           # Migrations, Seeders, Factories
└── tests/              # Unit & Feature Tests
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## 📄 License

MIT License - feel free to use this boilerplate for your projects.
