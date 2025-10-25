# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Bagisto** e-commerce platform - an open-source Laravel-based e-commerce framework built on Laravel 11, Vue.js, and Tailwind CSS. Bagisto uses a modular package architecture where functionality is organized into self-contained packages under `packages/Webkul/`.

## Development Commands

### Installation & Setup

```bash
# Initial setup
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan optimize:clear

# Full installation with seeders (fresh install only)
php artisan bagisto:install

# Development server
php artisan serve
# Accessible at http://localhost:8000
# Admin panel at http://localhost:8000/admin (default: admin@example.com / admin123)
```

### Frontend Assets

```bash
# Install npm dependencies
npm install

# Development mode with hot reload
npm run dev

# Production build
npm run build
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite="Admin Feature Test"
php artisan test --testsuite="Core Unit Test"
php artisan test --testsuite="DataGrid Unit Test"
php artisan test --testsuite="Shop Feature Test"

# Run with PHPUnit directly
vendor/bin/phpunit

# Run with Pest
vendor/bin/pest
```

### Database Operations

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration with seeders (WARNING: drops all tables)
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Code Quality

```bash
# Run Laravel Pint (code formatter)
vendor/bin/pint

# Check code style without fixing
vendor/bin/pint --test
```

### Cache Management

```bash
# Clear all caches (use frequently during development)
php artisan optimize:clear

# Individual cache clearing
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Package Development

```bash
# Generate a new package
php artisan package:make-package Webkul/YourPackage

# Generate package components
php artisan package:make-model ModelName Webkul/PackageName
php artisan package:make-repository RepositoryName Webkul/PackageName
php artisan package:make-controller ControllerName Webkul/PackageName
php artisan package:make-migration migration_name Webkul/PackageName

# Update composer autoload after creating packages
composer dump-autoload
```

## Architecture Overview

### Modular Package System

Bagisto uses **Konekt Concord** for modular package architecture. All core functionality is organized into packages under `packages/Webkul/`:

**Core Packages:**

- `Admin` - Admin panel interface
- `Attribute` - Product attributes system
- `Category` - Category management
- `Checkout` - Checkout process
- `Core` - Core functionality and helpers
- `Customer` - Customer management
- `DataGrid` - Data grid component system
- `Product` - Product management
- `Sales` - Order and sales management
- `Shop` - Storefront interface
- `User` - Admin user management

**Optional Packages:**

- `BookingProduct` - Booking/appointment products
- `CartRule` - Shopping cart rules
- `CatalogRule` - Catalog pricing rules
- `MagicAI` - AI-powered features (GPT, Gemini, etc.)
- `Payment` - Payment method integrations
- `Paypal` - PayPal integration
- `Shipping` - Shipping method integrations
- `SocialLogin` - Social authentication

### Package Structure Pattern

Each package follows this structure:

```text
packages/Webkul/PackageName/
├── src/
│   ├── Config/          # Package configuration
│   ├── Contracts/       # Model contracts (interfaces)
│   ├── Database/
│   │   ├── Migrations/  # Database migrations
│   │   └── Seeders/     # Database seeders
│   ├── Http/
│   │   ├── Controllers/ # HTTP controllers
│   │   └── Requests/    # Form requests
│   ├── Models/          # Eloquent models
│   ├── Providers/       # Service providers
│   │   ├── ModuleServiceProvider.php  # Concord registration
│   │   └── PackageServiceProvider.php # Main provider
│   ├── Repositories/    # Repository pattern classes
│   ├── Resources/
│   │   ├── assets/      # CSS, JS assets
│   │   ├── lang/        # Translations
│   │   └── views/       # Blade templates
│   └── Routes/          # Route definitions
└── composer.json
```

### Repository Pattern

**All database operations MUST go through repositories**, not direct model access. Repositories extend `Webkul\Core\Eloquent\Repository` (powered by Prettus L5 Repository).

**Creating a repository:**

```php
namespace Webkul\Package\Repositories;

use Webkul\Core\Eloquent\Repository;

class EntityRepository extends Repository
{
    public function model(): string
    {
        return 'Webkul\Package\Contracts\Entity';
    }

    // Custom query methods here
}
```

**Using repositories in controllers:**

```php
public function __construct(
    protected EntityRepository $entityRepository
) {}

public function index()
{
    $entities = $this->entityRepository->all();
}
```

### Model Registration with Concord

Models must be registered in `ModuleServiceProvider.php`:

```php
namespace Webkul\Package\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Package\Models\Entity::class,
    ];
}
```

Then register the `ModuleServiceProvider` in `config/concord.php`:

```php
'modules' => [
    \Webkul\Package\Providers\ModuleServiceProvider::class,
],
```

### Service Provider Registration

Main package service providers are registered in `config/app.php`:

```php
'providers' => [
    // ...
    Webkul\Package\Providers\PackageServiceProvider::class,
],
```

### Model Contracts & Proxies

Models implement contracts (interfaces) and use the Proxy pattern:

```php
// Contract
namespace Webkul\Package\Contracts;
interface Entity extends ConsoleCatalogue {}

// Model
namespace Webkul\Package\Models;
class Entity extends Model implements EntityContract {}

// Proxy (auto-generated)
namespace Webkul\Package\Models;
class EntityProxy extends \Konekt\Concord\Proxies\ModelProxy {}
```

**Always use proxies for relationships:**

```php
public function items()
{
    return $this->hasMany(EntityProxy::modelClass());
}
```

## Theme System

### Theme Configuration

Themes are configured in `config/themes.php`:

```php
return [
    'shop-default' => 'default',  // Active shop theme
    'admin-default' => 'default', // Active admin theme

    'shop' => [
        'default' => [
            'name'        => 'Default',
            'assets_path' => 'public/themes/shop/default',
            'views_path'  => 'resources/themes/default/views',
            'vite'        => [...],
        ],
    ],
];
```

### Creating Custom Themes

**Resources Directory Approach:**

1. Add theme to `config/themes.php`
2. Create directory: `resources/themes/theme-name/views/`
3. Create templates following shop package structure
4. Activate in admin or set as `shop-default`

**Package Approach:**
Create as a Webkul package under `packages/Webkul/ThemeName/` with proper service provider registration.

## Database Conventions

### Naming Conventions

- **Tables**: Use package prefix (e.g., `rma_requests`, `product_`, `category_`)
- **Migrations**: `YYYY_MM_DD_HHMMSS_create_table_name_table.php`
- **Foreign keys**: `{related_table}_id` (e.g., `customer_id`, `order_id`)
- **Timestamps**: Laravel's `created_at`, `updated_at` (automatic)

### Migration Best Practices

```php
Schema::create('package_entities', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedInteger('customer_id')->nullable();
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    $table->timestamps();
});
```

## Testing Structure

Tests are organized per package under `packages/Webkul/{Package}/tests/`:

```
packages/Webkul/Package/tests/
├── Feature/     # Feature tests
├── Unit/        # Unit tests
└── TestCase.php # Base test case
```

Test suites are configured in `phpunit.xml`.

## Key Bagisto Concepts

### Product Types

Bagisto supports multiple product types via the Type Pattern. Each type extends `Webkul\Product\Type\AbstractType`:

- Simple
- Configurable
- Virtual
- Grouped
- Downloadable
- Bundle
- Booking (optional package)

Custom product types can be created by extending `AbstractType` and registering in package config.

### Events & Listeners

Bagisto uses Laravel events extensively. Register listeners in service providers:

```php
protected $listen = [
    'checkout.order.save.after' => [
        'Webkul\Package\Listeners\OrderListener@process',
    ],
];
```

### DataGrid System

Use `Webkul\DataGrid\DataGrid` for listing pages:

```php
namespace Webkul\Package\DataGrids;

use Webkul\DataGrid\DataGrid;

class EntityDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('entities')->select('id', 'name');
        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'integer',
            'searchable' => true,
            'sortable'   => true,
        ]);
    }
}
```

### ACL (Access Control List)

Admin permissions are defined in package config and registered via service provider:

```php
// Config
'acl' => [
    'key'   => 'package.entities',
    'name'  => 'Entities',
    'route' => 'admin.package.entities.index',
],
```

## Important Notes

### Autoloading

After creating new packages or classes, always run:
```bash
composer dump-autoload
```

### Cache Clearing

During development, frequently clear caches:
```bash
php artisan optimize:clear
```

### Model Overriding

To extend core models without modifying core files, register overrides in service provider:

```php
$this->app->concord->registerModel(
    \Webkul\Product\Contracts\Product::class,
    \Webkul\CustomPackage\Models\Product::class
);
```

### Blade Components

Bagisto provides reusable Blade components:
- `<x-shop::layouts>` - Shop layout wrapper
- `<x-admin::layouts>` - Admin layout wrapper
- `<x-admin::form>` - Admin form component
- `<x-admin::datagrid>` - DataGrid component

### Translation System

Use Laravel's translation system with package namespacing:
```php
// In views
{{ __('package::app.path.to.key') }}

// Language files at
packages/Webkul/Package/src/Resources/lang/en/app.php
```

## Common Pitfalls

1. **Direct Model Access**: Always use repositories, never query models directly in controllers
2. **Missing Concord Registration**: Models won't work without proper `ModuleServiceProvider` registration
3. **Cache Issues**: Always clear cache after config changes or new routes
4. **Table Naming**: Use package prefixes to avoid conflicts
5. **Migration Seeds**: Don't run seeders on existing installations (only fresh installs)
6. **Proxy Usage**: Use model proxies in relationships, not direct model classes

## Resources

- **Documentation**: <https://devdocs.bagisto.com>
- **GitHub**: <https://github.com/bagisto/bagisto>
- **Forums**: <https://forums.bagisto.com>
