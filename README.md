# Laravel 12 Starter Kit with AdminLTE

<p align="center">
<a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>
</p>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

A professional Laravel 12 starter kit featuring AdminLTE admin panel, comprehensive user management, role-based access control (RBAC), two-factor authentication, audit logging, and multi-language support. Perfect for building enterprise-level applications with a solid foundation.

## ✨ Features

### 🔐 Authentication & Security
- **Complete Authentication System**: Login, registration, password reset, email verification
- **Two-Factor Authentication (2FA)**: SMS/Email-based 2FA with configurable expiration
- **Laravel Sanctum**: API authentication ready
- **Password Security**: Secure password hashing and reset functionality
- **User Verification**: Email-based user verification system

### 👥 User & Role Management
- **User Management**: Full CRUD operations for user management
- **Role-Based Access Control (RBAC)**: Flexible permission and role system
- **Permission Management**: Granular permission control
- **Admin Panel**: Dedicated admin interface for managing users, roles, and permissions
- **Frontend Panel**: User-facing interface with profile management

### 📊 Data Management
- **DataTables Integration**: Server-side processing with Yajra DataTables
- **CSV Import**: Bulk import functionality for users, roles, and permissions
- **Soft Deletes**: Soft delete functionality for data recovery
- **Mass Operations**: Bulk delete operations

### 📝 Audit & Logging
- **Audit Logging**: Automatic tracking of model changes (create, update, delete)
- **Activity Tracking**: User activity monitoring with IP tracking
- **Auditable Trait**: Easy-to-use trait for model auditing

### 🌍 Internationalization
- **Multi-language Support**: 9 languages included
  - English, Russian, French, Spanish, Turkish, Arabic, Bengali, Chinese (Simplified), Hindi
- **Dynamic Language Switching**: Session-based language selection
- **Localized Date/Time**: Configurable date and time formats

### 🎨 Admin Panel
- **AdminLTE 4**: Modern admin panel interface (upgraded from v3)
- **Bootstrap 5**: Latest Bootstrap framework (upgraded from v4)
- **Responsive Design**: Mobile-friendly interface
- **Global Search**: Search functionality across the admin panel
- **All Assets Locally Hosted**: No CDN dependencies

### 🧪 Testing
- **Pest Testing Framework**: Modern PHP testing with Pest
- **PHPUnit Support**: Traditional PHPUnit tests included
- **Browser Tests**: Dusk-based browser testing for critical features
- **Test Coverage**: Tests for users, roles, and permissions

### 🛠️ Development Tools
- **Laravel Vite**: Modern asset bundling with Vite
- **Laravel Pint**: Code style fixer
- **Laravel Sail**: Docker development environment
- **Laravel Tinker**: Interactive REPL

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x and NPM
- SQLite (default) or MySQL/PostgreSQL
- Web server (Apache/Nginx) or PHP built-in server

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd base
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your database and application settings:

```env
APP_NAME="Laravel Starter Kit"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# Or use MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Database Setup

```bash
# Create database (if using SQLite, the file will be created automatically)
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

The seeder will create:
- Default permissions
- Default roles (Admin, User)
- Admin user (check `database/seeders/UsersTableSeeder.php` for credentials)
- Permission-role relationships

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 📁 Project Structure

```
base/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin panel controllers
│   │   │   ├── Api/             # API controllers
│   │   │   ├── Auth/            # Authentication controllers
│   │   │   └── Frontend/        # Frontend controllers
│   │   ├── Middleware/          # Custom middleware
│   │   └── Requests/            # Form request validation
│   ├── Models/                  # Eloquent models
│   ├── Notifications/           # Email notifications
│   ├── Providers/               # Service providers
│   └── Traits/                  # Reusable traits (Auditable, etc.)
├── config/
│   └── panel.php                # Panel configuration (languages, date formats)
├── database/
│   ├── migrations/              # Database migrations
│   └── seeders/                 # Database seeders
├── resources/
│   ├── lang/                    # Language files (9 languages)
│   └── views/
│       ├── admin/               # Admin panel views
│       ├── auth/                # Authentication views
│       └── frontend/            # Frontend views
├── routes/
│   ├── web.php                  # Web routes
│   └── api.php                  # API routes
└── tests/                       # Test files
```

## ⚙️ Configuration

### Panel Configuration

Edit `config/panel.php` to customize:

- **Date/Time Format**: Default format is `d-m-Y` and `H:i:s`
- **Primary Language**: Default language (default: `en`)
- **Available Languages**: List of supported languages
- **Registration Default Role**: Role assigned to new users (default: `2`)

### Permissions & Roles

The system uses a many-to-many relationship:
- **Roles** can have multiple **Permissions**
- **Users** can have multiple **Roles**
- Permissions are checked via Laravel Gates

Default permissions include:
- `user_access`, `user_create`, `user_edit`, `user_show`, `user_delete`
- `role_access`, `role_create`, `role_edit`, `role_show`, `role_delete`
- `permission_access`, `permission_create`, `permission_edit`, `permission_show`, `permission_delete`
- `audit_log_access`, `audit_log_show`

## 🔑 Default Credentials

After running the seeders, check `database/seeders/UsersTableSeeder.php` for the default admin credentials. Typically:

- **Email**: admin@admin.com
- **Password**: password

**⚠️ Important**: Change these credentials immediately in production!

## 🎯 Key Features Explained

### Two-Factor Authentication

Users can enable 2FA from their profile. When enabled:
- A 6-digit code is generated and sent via email
- Code expires in 15 minutes
- Users must enter the code to access protected routes
- Middleware `2fa` enforces 2FA verification

### Audit Logging

Models using the `Auditable` trait automatically log:
- Model creation
- Model updates (with change tracking)
- Model deletion

View audit logs in the admin panel under "Audit Logs".

### CSV Import

Import users, roles, or permissions via CSV:
1. Navigate to the resource index page
2. Click "Import CSV"
3. Upload CSV file
4. Map columns to model fields
5. Review and confirm import

### Multi-language Support

Switch languages using the language selector or via URL parameter:
```
?change_language=es
```

Languages are stored in session and persist across requests.

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run with Pest
./vendor/bin/pest

# Run specific test suite
php artisan test --testsuite=Feature
```

### Browser Tests

```bash
php artisan dusk
```

## 📦 Key Dependencies

### Backend
- **Laravel Framework**: ^12.0
- **Laravel Sanctum**: ^4.2 (API authentication)
- **Laravel UI**: ^4.6 (Authentication scaffolding)
- **Yajra DataTables**: ^12.6 (Server-side DataTables)
- **Spreadsheet Reader**: ^0.5.11 (CSV import)

### Frontend
- **Vite**: ^4.0.0 (Asset bundler)
- **Axios**: ^1.1.2 (HTTP client)
- **AdminLTE 4**: Admin panel theme (locally hosted)
- **Bootstrap 5**: CSS framework (locally hosted)
- **Font Awesome 6**: Icon library (locally hosted)
- **Bootstrap Icons**: Icon library for welcome page (locally hosted)

### Development
- **Pest**: ^4.3 (Testing framework)
- **Laravel Pint**: ^1.26 (Code style)
- **Laravel Sail**: ^1.51 (Docker environment)

## 🔒 Security Features

- CSRF protection on all forms
- XSS protection
- SQL injection prevention (Eloquent ORM)
- Password hashing (bcrypt)
- Rate limiting on API routes
- Permission-based access control
- Two-factor authentication
- Secure session management

## 🌐 API Endpoints

API routes are protected with Sanctum authentication:

```
/api/v1/users          # User management (GET, POST, PUT, DELETE)
```

Access requires `auth:sanctum` middleware and valid API token.

## 📝 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Thank you for considering contributing to this project! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📞 Support

For issues, questions, or contributions, please open an issue on the repository.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [AdminLTE](https://adminlte.io) - Admin Dashboard Template
- [Bootstrap](https://getbootstrap.com) - CSS Framework
- [Yajra DataTables](https://yajrabox.com/docs/laravel-datatables) - Laravel DataTables Package

---

**Built with ❤️ using Laravel 12**
