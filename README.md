# ShortletNG

A modern, single-host shortlet booking platform targeting the Nigerian market. Built with Laravel, Vue.js, and Inertia.js, ShortletNG provides a seamless experience for guests to discover, search, and book short-term rental properties.

## Features

### Guest Features
- **Authentication & Authorization**
  - Google OAuth integration for quick signup/login
  - Traditional email/password authentication as fallback
  - Secure user profile management

- **Property Discovery**
  - Advanced search with multiple filters (location, dates, price, amenities)
  - Interactive property detail pages with image galleries
  - Real-time availability checking
  - Comprehensive property information and reviews

- **Booking System**
  - Intuitive date selection and guest count management
  - Dynamic price calculation with transparent breakdowns
  - Secure payment processing via Paystack and Flutterwave
  - Booking history and management dashboard
  - Email confirmations and reminders

- **Reviews & Ratings**
  - Post-checkout review submission
  - 5-star rating system
  - Verified booking badges
  - Review statistics and distribution

### Admin Features (FilamentPHP)
- **Property Management**
  - Complete CRUD operations for properties
  - Multi-image upload and gallery management
  - Amenities configuration
  - Availability calendar management
  - Pricing and booking rules setup

- **Booking Management**
  - Real-time booking tracking
  - Status management and updates
  - Cancellation and refund handling
  - Booking calendar visualization

- **User Management**
  - Guest account overview
  - User activity tracking
  - Dispute resolution tools

- **Analytics Dashboard**
  - Revenue tracking and reporting
  - Booking statistics and trends
  - Property performance metrics
  - Visual charts and insights

## Tech Stack

### Backend
- **Framework**: Laravel 12.x
- **Database**: MySQL 8.0+
- **Admin Panel**: FilamentPHP v4
- **Authentication**: Laravel Sanctum + Google OAuth (Socialite v5)
- **Queue System**: Redis
- **Testing**: Pest v4 (PHPUnit v12)
- **Code Quality**: Laravel Pint, Larastan

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **Integration**: Inertia.js (SPA-like experience)
- **State Management**: Pinia / Vue Composables
- **Styling**: Tailwind CSS v4
- **Build Tool**: Vite

### Payment Integration
- **Primary**: Paystack
- **Secondary**: Flutterwave
- **Architecture**: Manager pattern for easy provider switching

### DevOps
- **Version Control**: Git
- **Package Managers**: Composer, npm/pnpm
- **Code Formatting**: PHP CS Fixer, ESLint, Prettier

## Prerequisites

Before setting up the project, ensure you have the following installed:

- **PHP**: 8.3 or higher
- **Composer**: 2.x
- **Node.js**: 18.x or higher
- **MySQL**: 8.0 or higher
- **Redis**: Latest stable version (optional but recommended)
- **Git**: Latest stable version

### Required PHP Extensions
- `php_openssl`
- `php_pdo`
- `php_pdo_mysql`
- `php_mbstring`
- `php_tokenizer`
- `php_xml`
- `php_ctype`
- `php_json`
- `php_bcmath`
- `php_fileinfo`

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/codejutsu1/shortletng.git
cd shortletng
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
# or
pnpm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit `.env` file with your configuration:

```env
# Application
APP_NAME=ShortletNG
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shortletng
DB_USERNAME=root
DB_PASSWORD=your_password

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Payment Gateway
PAYMENT_PROVIDER=paystack

# Paystack
PAYSTACK_PUBLIC_KEY=your_paystack_public_key
PAYSTACK_SECRET_KEY=your_paystack_secret_key

# Mail
MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=noreply@shortletng.com
```

### 5. Database Setup

# Run migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed
```

### 6. Storage Setup

```bash
# Create symbolic link for public storage
php artisan storage:link
```

### 7. Build Frontend Assets

```bash
# Development build with hot reload
npm run dev

# Production build
npm run build
```

## Usage

### Running the Application

**Terminal 1: Laravel Server**
```bash
php artisan serve
```
Application will be available at: `http://localhost:8000`

**Terminal 2: Vite Dev Server** (for hot module replacement)
```bash
npm run dev
```

**Terminal 3: Queue Worker** (optional)
```bash
php artisan queue:work
```

### Access Points

- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **API**: http://localhost:8000/api (if needed)

### Create Admin User

```bash
# Using Filament command
php artisan make:filament-user

# Or run admin seeder
php artisan db:seed --class=AdminUserSeeder
```

## Project Structure

```
shortletng/
├── app/
│   ├── Filament/          # FilamentPHP admin resources
│   ├── Http/
│   │   ├── Controllers/   # Application controllers
│   │   └── Requests/      # Form request validation
│   ├── Models/            # Eloquent models
│   └── Services/          # Business logic services
├── database/
│   ├── factories/         # Model factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/    # Vue components
│   │   ├── Layouts/       # Layout components
│   │   └── Pages/         # Inertia pages
│   └── views/             # Blade templates
├── routes/
│   ├── web.php            # Web routes
│   └── api.php            # API routes
├── tests/
│   ├── Feature/           # Feature tests
│   └── Unit/              # Unit tests
└── shortlet-booking-docs/ # Comprehensive project documentation
```

## Development

### Code Quality

```bash
# Format PHP code with Pint
vendor/bin/pint

# Run static analysis
vendor/bin/phpstan analyse

# Lint Vue/JavaScript code
npm run lint

# Format with Prettier
npm run format
```

### Database Management

```bash
# Create new migration
php artisan make:migration create_table_name

# Reset database
php artisan migrate:fresh --seed

# Create new model with migration and factory
php artisan make:model ModelName -mf
```

## Testing

### Backend Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run with coverage
php artisan test --coverage

# Filter by test name
php artisan test --filter=testBookingCreation
```

### Frontend Tests

```bash
# Run Vitest tests
npm run test

# Run with coverage
npm run test:coverage
```

## Documentation

Comprehensive documentation is available in the `shortlet-booking-docs/` directory:

- **[PROJECT_OVERVIEW.md](shortlet-booking-docs/PROJECT_OVERVIEW.md)** - Project vision, tech stack, and MVP features
- **[SETUP_GUIDE.md](shortlet-booking-docs/SETUP_GUIDE.md)** - Detailed setup instructions and troubleshooting
- **[FEATURE_ROADMAP.md](shortlet-booking-docs/FEATURE_ROADMAP.md)** - Complete feature breakdown and task tracking
- **[CODING_STANDARDS.md](shortlet-booking-docs/CODING_STANDARDS.md)** - Code conventions and style guide
- **[DESIGN_SYSTEM.md](shortlet-booking-docs/DESIGN_SYSTEM.md)** - UI/UX specifications and design tokens
- **[DATABASE_SCHEMA.md](shortlet-booking-docs/DATABASE_SCHEMA.md)** - Database structure and relationships
- **[GIT_WORKFLOW.md](shortlet-booking-docs/GIT_WORKFLOW.md)** - Branching strategy and commit conventions
- **[TESTING_STRATEGY.md](shortlet-booking-docs/TESTING_STRATEGY.md)** - Testing approach and guidelines

## Deployment

### Production Checklist

- [ ] Configure production environment variables
- [ ] Set up production database
- [ ] Configure file storage (S3 or similar)
- [ ] Set up production mail service
- [ ] Configure production queue worker
- [ ] Set up SSL certificate
- [ ] Run migrations on production
- [ ] Build and optimize frontend assets
- [ ] Set up monitoring and error tracking

### Build for Production

```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
npm run build
```

## Contributing

We welcome contributions! Please follow these guidelines:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Follow coding standards** (see [CODING_STANDARDS.md](shortlet-booking-docs/CODING_STANDARDS.md))
4. **Write tests** for new features
5. **Commit your changes** following [GIT_WORKFLOW.md](shortlet-booking-docs/GIT_WORKFLOW.md)
6. **Push to the branch** (`git push origin feature/amazing-feature`)
7. **Open a Pull Request**

### Commit Message Convention

```
feat: Add property search filtering by amenities
fix: Resolve booking date validation issue
docs: Update setup guide with Redis configuration
test: Add tests for payment processing
refactor: Simplify booking service logic
```

## License

This project is proprietary software. All rights reserved.

## Contact & Support

For questions, issues, or feature requests:

- **Documentation**: Check the `shortlet-booking-docs/` directory
- **Issues**: Open an issue on GitHub
- **Email**: support@shortletng.com (placeholder)

## Acknowledgments

Built with:
- [Laravel](https://laravel.com/)
- [Vue.js](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/)
- [FilamentPHP](https://filamentphp.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Paystack](https://paystack.com/)
- [Flutterwave](https://flutterwave.com/)

---

**Made with ❤️ for the Nigerian market**
