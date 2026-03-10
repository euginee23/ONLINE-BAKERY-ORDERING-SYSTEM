# Online Bakery Ordering System

<div align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12.0">
  <img src="https://img.shields.io/badge/Livewire-4.0-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire 4.0">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS 4.0">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge" alt="License">
</div>

## 📋 Overview

**Online Bakery Ordering System** is a modern web-based platform that enables customers to browse bakery products, place orders, and manage their accounts through an elegant and intuitive interface. Built as a thesis project, the system addresses the challenges faced by small bakery businesses in managing orders, inventory, and customer engagement in the digital age.

The platform features a beautifully crafted landing page showcasing the bakery's offerings, seamless modal-based authentication, a configurable settings system, and a comprehensive order management foundation built on Laravel's robust ecosystem.

### Key Features

- 🍞 **Product Browsing** — Categorized menu with descriptions and pricing
- 🛒 **Order Management** — Complete ordering lifecycle from browse to delivery
- 👤 **User Accounts** — Secure authentication with profile and password management
- 🔐 **Two-Factor Authentication** — Enhanced security with 2FA support via Fortify
- 📱 **Responsive Design** — Fully mobile-optimized interface for ordering on any device
- 🎨 **Modern UI** — Elegant zinc and gold design system with Flux UI components
- ⚡ **Real-time Updates** — Dynamic interface powered by Livewire
- 🎯 **Modal Authentication** — Seamless login/register without page reloads
- 🌙 **Theme Support** — Light, Dark, and System theme selection
- ⚙️ **Configurable Settings** — Key-value settings system with caching

## 🚀 Tech Stack

### Backend
- **Laravel 12.x** — PHP web application framework
- **PHP 8.4** — Server-side scripting language
- **Laravel Fortify** — Authentication backend with 2FA support
- **Livewire 4.x** — Full-stack framework for dynamic interfaces

### Frontend
- **Livewire Flux 2.x (Free)** — Official Livewire UI component library
- **Tailwind CSS 4.x** — Utility-first CSS framework with custom gold theme
- **Alpine.js** — Lightweight JavaScript framework for interactivity
- **Vite** — Modern frontend build tool

### Database
- **MySQL 8.0+** — Primary database for production
- **SQLite** — Option for local development

### Testing & Quality
- **Pest PHP 4.x** — Modern PHP testing framework
- **Laravel Pint** — Opinionated PHP code style fixer
- **PHPUnit 12.x** — Unit testing framework

## 📋 Requirements

- **PHP**: 8.4 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18.0 or higher
- **NPM**: 8.0 or higher
- **MySQL**: 8.0 or higher (or SQLite for development)
- **Web Server**: Apache/Nginx with mod_rewrite or Laravel Sail

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd BAKERY-ORDERING-SYSTEM
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Configuration

Copy the environment file and configure it:

```bash
cp .env.example .env
```

Edit `.env` with your database and application settings:

```env
APP_NAME="Online Bakery Ordering System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bakery_ordering_system
DB_USERNAME=root
DB_PASSWORD=your_password

# Or use SQLite for development
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@bakery.test"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Database Setup

Create the database and run migrations:

```bash
# For MySQL, create the database first:
mysql -u root -p
CREATE DATABASE bakery_ordering_system;
exit;

# Run migrations
php artisan migrate
```

Seed the database with default settings and sample data:

```bash
php artisan db:seed
```

### 7. Storage Link

Create the symbolic link for public file storage:

```bash
php artisan storage:link
```

### 8. Build Frontend Assets

For development (with hot reload):

```bash
npm run dev
```

For production:

```bash
npm run build
```

### 9. Start the Development Server

**Option 1: PHP Built-in Server**
```bash
php artisan serve
```

**Option 2: Laravel Sail (Docker)**
```bash
./vendor/bin/sail up
```

**Option 3: Full Dev Environment (recommended)**
```bash
composer run dev
```

The application will be available at `http://localhost:8000`.

## 🛠️ Development

### Running Development Servers

```bash
# Laravel development server
php artisan serve

# Vite development server (hot reload) — run in a separate terminal
npm run dev
```

### Database Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Refresh database (drop all tables and re-run)
php artisan migrate:fresh --seed

# Seed the database
php artisan db:seed
```

### Cache Management

```bash
# Clear all caches (recommended before running tests)
php artisan optimize:clear

# Clear individual caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Running Tests

```bash
# Run all tests (compact output)
php artisan test --compact

# Run a specific test file
php artisan test tests/Feature/LandingPageTest.php

# Filter by test name
php artisan test --compact --filter=setting

# Run with code coverage
php artisan test --coverage
```

> **Note**: Always run `php artisan optimize:clear` before running tests if you previously ran `php artisan optimize`.

### Code Quality

```bash
# Fix code style with Pint
vendor/bin/pint

# Check and fix only dirty files
vendor/bin/pint --dirty

# Format specific path
vendor/bin/pint app/Models
```

## 📁 Project Structure

```
BAKERY-ORDERING-SYSTEM/
├── app/
│   ├── Actions/              # Custom action classes (Fortify)
│   │   └── Fortify/
│   ├── Concerns/             # Shared trait concerns
│   ├── Http/
│   │   └── Controllers/      # HTTP controllers
│   ├── Livewire/             # Livewire components
│   │   └── Actions/          # Livewire action components
│   ├── Models/               # Eloquent models
│   │   ├── Setting.php       # Key-value settings with caching
│   │   └── User.php
│   └── Providers/            # Service providers
├── bootstrap/                # Bootstrap files
├── config/                   # Configuration files
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders (incl. SettingSeeder)
├── public/
│   └── build/                # Compiled assets (generated)
├── resources/
│   ├── css/
│   │   └── app.css           # Main stylesheet (Tailwind + gold palette)
│   ├── js/
│   │   └── app.js            # Main JavaScript
│   └── views/
│       ├── components/       # Reusable Blade components
│       │   ├── auth-login-modal.blade.php
│       │   ├── auth-register-modal.blade.php
│       │   ├── category-card.blade.php
│       │   ├── feature-card.blade.php
│       │   ├── hero-section.blade.php
│       │   ├── public-footer.blade.php
│       │   └── public-navbar.blade.php
│       ├── layouts/          # Layout templates
│       ├── pages/            # Page views
│       └── welcome.blade.php # Landing page
├── routes/
│   ├── console.php           # Console routes
│   ├── settings.php          # Settings routes
│   └── web.php               # Web routes
├── storage/                  # Application storage
├── tests/
│   ├── Feature/              # Feature tests (with DB access)
│   │   ├── LandingPageTest.php
│   │   ├── SettingTest.php
│   │   └── Auth/
│   └── Unit/                 # Pure unit tests
├── .env.example              # Example environment file
├── artisan                   # Artisan CLI
├── composer.json             # PHP dependencies
├── package.json              # Node.js dependencies
├── pint.json                 # Laravel Pint configuration
├── phpunit.xml               # PHPUnit configuration
└── vite.config.js            # Vite configuration
```

## 🎨 Brand Colors

The system uses a custom zinc and gold color palette:

- **Zinc**: `zinc-50` → `zinc-950` — Neutral backgrounds and text
- **Gold**: `gold-50` → `gold-950` — Primary accent (warmth and elegance)

Color usage:
- Primary buttons and CTAs: Gold
- Backgrounds (light): White / Zinc-50
- Backgrounds (dark): Zinc-900 / Zinc-950
- Footer: Zinc-950 (always dark)
- Headline gradients: Gold-600 → Amber-500

## ⚙️ Configurable Settings

The system includes a flexible key-value settings model with database persistence and automatic caching:

```php
// Get a setting with an optional default
Setting::get('bakery_name', 'My Bakery');

// Update or create a setting
Setting::set('bakery_name', 'Artisan Bakes');
```

Default settings seeded out of the box:

| Key              | Default Value                        |
|------------------|--------------------------------------|
| `bakery_name`    | ONLINE BAKERY ORDERING SYSTEM        |
| `bakery_tagline` | Fresh Baked with Love, Delivered...  |
| `bakery_address` | 123 Baker Street, Manila, Philippines|
| `bakery_phone`   | +63 912 345 6789                     |
| `bakery_email`   | orders@bakerysystem.test             |

## 🔒 Security Features

- **Laravel Fortify Authentication** — Secure login, registration, and password reset
- **Two-Factor Authentication** — Optional 2FA for enhanced account security
- **CSRF Protection** — All forms are protected against cross-site request forgery
- **XSS Protection** — Blade templating automatically escapes all output
- **SQL Injection Protection** — Eloquent ORM with parameterized queries
- **Password Hashing** — bcrypt hashing via Laravel's built-in hashing layer
- **Rate Limiting** — Login and registration route rate limiting built-in

## 🚢 Deployment

### Production Build

```bash
# Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# Build optimized frontend assets
npm run build

# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Seed settings if first deploy
php artisan db:seed --class=SettingSeeder --force
```

### Environment Configuration

Ensure your production `.env` has:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=strong-password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
# ... other mail settings
```

### Server Requirements

- PHP 8.4+ with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer
- MySQL 8.0+
- Web server (Apache/Nginx) with URL rewriting enabled
- SSL certificate for HTTPS

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style

- Follow PSR-12 code style (enforced by Pint)
- Write Pest tests for all new features
- Update documentation as needed

## 📄 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**CodeHub.Site**
- Copyright © 2026 Online Bakery Ordering System

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI powered by [Livewire](https://livewire.laravel.com) and [Livewire Flux](https://flux.laravel.com)
- Styled with [Tailwind CSS](https://tailwindcss.com)
- Icons and interactions powered by [Alpine.js](https://alpinejs.dev)

---

<div align="center">
  <strong>Built with ❤️ using Laravel 12 and Livewire</strong>
  <br>
  <sub>Online Bakery Ordering System — Fresh Baked with Love, Delivered to Your Door</sub>
</div>
