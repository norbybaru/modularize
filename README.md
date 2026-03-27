# Modularize

The package encourage implementation of modular pattern for your Laravel project.
You can easily start your modular journey with this simple package and generate only files you need.

A module is like a Laravel package, it has some views, controllers or models.

## Installation

Run the following command from your projects root
```php
composer require norbybaru/modularize
```
## Configuration
Publish the package configuration using the following command:
```php
php artisan vendor:publish --provider="NorbyBaru\Modularize\ModularizeServiceProvider"
```

### Autoloading
The default namespace is set as Modules this will apply the namespace for all classes the module will use when it's being created and later when generation additional classes.

For autoloading modules, add the following to your composer.json and execute composer dump-autoload:

```php
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/"
    }
  }
}
```

## Basic Usage

### Create module
Open your terminal and run command to list all possible commands:
```
php artisan module:make:
```

## Usage Examples

This section demonstrates real-world scenarios for building complete features using the modularize package.

### Example 1: Building a Complete Blog Module

Create a fully-functional blog module with posts, categories, and comments:

```bash
# Step 1: Create the Blog module structure
php artisan module:make Module Blog

# Step 2: Generate Post model with all related files
php artisan module:make:model Post --module=Blog --all
# This creates: Model, Migration, Factory, Seeder, Policy, and Resource Controller

# Step 3: Generate Category model with migration and factory
php artisan module:make:model Category --module=Blog --migration --factory

# Step 4: Generate Comment model with migration and policy
php artisan module:make:model Comment --module=Blog --migration --policy

# Step 5: Create pivot model for post-category relationship
php artisan module:make:model PostCategory --module=Blog --pivot --migration

# Step 6: Add request validation classes
php artisan module:make:request StorePostRequest --module=Blog
php artisan module:make:request UpdatePostRequest --module=Blog
php artisan module:make:request StoreCommentRequest --module=Blog

# Step 7: Create view components
php artisan module:make:component PostCard --module=Blog --test
php artisan module:make:component CommentList --module=Blog

# Step 8: Add views for post management
php artisan module:make:view posts.index --module=Blog
php artisan module:make:view posts.show --module=Blog
php artisan module:make:view posts.create --module=Blog

# Step 9: Verify the module structure
php artisan module:list
```

**Result:** A complete blog module with posts, categories, comments, validation, views, and components.

### Example 2: Building a RESTful API with Resources

Create a complete API module for product management:

```bash
# Step 1: Create API module
php artisan module:make Module Api

# Step 2: Generate Product model with API controller and related files
php artisan module:make:model Product --module=Api --all --api
# The --api flag ensures the controller has no create/edit methods (API-only)

# Step 3: Create API resources for JSON transformation
php artisan module:make:resource ProductResource --module=Api
php artisan module:make:resource ProductCollection --module=Api --collection

# Step 4: Add API request validation
php artisan module:make:request StoreProductRequest --module=Api
php artisan module:make:request UpdateProductRequest --module=Api

# Step 5: Create middleware for API authentication
php artisan module:make:middleware ValidateApiToken --module=Api

# Step 6: Generate feature tests for API endpoints
php artisan module:make:test ProductApiTest --module=Api --pest
php artisan module:make:test ProductAuthTest --module=Api --pest

# Step 7: Create event for product updates
php artisan module:make:event ProductUpdated --module=Api

# Step 8: Create listener for product update notifications
php artisan module:make:listener SendProductUpdateNotification --module=Api --event=ProductUpdated --queued
```

**Result:** A production-ready RESTful API with models, controllers, resources, validation, authentication, and events.

### Example 3: Creating a User Management System

Build a complete user management module with authentication and authorization:

```bash
# Step 1: Create User module
php artisan module:make Module User

# Step 2: Generate User model with all files
php artisan module:make:model User --module=User --all

# Step 3: Generate Role and Permission models
php artisan module:make:model Role --module=User --migration --factory --policy
php artisan module:make:model Permission --module=User --migration --factory

# Step 4: Create pivot models for relationships
php artisan module:make:model RoleUser --module=User --pivot --migration
php artisan module:make:model PermissionRole --module=User --pivot --migration

# Step 5: Add middleware for role checking
php artisan module:make:middleware CheckUserRole --module=User
php artisan module:make:middleware CheckPermission --module=User

# Step 6: Create request validation
php artisan module:make:request CreateUserRequest --module=User
php artisan module:make:request UpdateUserRequest --module=User
php artisan module:make:request AssignRoleRequest --module=User

# Step 7: Generate events and listeners
php artisan module:make:event UserRegistered --module=User
php artisan module:make:event UserRoleChanged --module=User
php artisan module:make:listener SendWelcomeEmail --module=User --event=UserRegistered --queued
php artisan module:make:listener ClearUserCache --module=User --event=UserRoleChanged

# Step 8: Create mail templates
php artisan module:make:mail WelcomeEmail --module=User --markdown=emails.user.welcome
php artisan module:make:mail PasswordReset --module=User --markdown=emails.user.password-reset

# Step 9: Add comprehensive tests
php artisan module:make:test UserRegistrationTest --module=User --pest
php artisan module:make:test RolePermissionTest --module=User --pest
php artisan module:make:test UserPolicyTest --module=User --pest --unit
```

**Result:** A complete user management system with roles, permissions, authentication, email notifications, and comprehensive tests.

### Example 4: E-commerce Product Catalog Module

Build a product catalog module for an e-commerce application:

```bash
# Step 1: Create Product module
php artisan module:make Module Product

# Step 2: Generate core models using --all flag
php artisan module:make:model Product --module=Product --all
php artisan module:make:model Category --module=Product --all

# Step 3: Create supporting models
php artisan module:make:model Brand --module=Product --migration --factory
php artisan module:make:model ProductImage --module=Product --migration --factory
php artisan module:make:model ProductVariant --module=Product --migration --factory

# Step 4: Create pivot model for product-category relationship
php artisan module:make:model CategoryProduct --module=Product --pivot --migration

# Step 5: Add request validation
php artisan module:make:request StoreProductRequest --module=Product
php artisan module:make:request UpdateProductRequest --module=Product
php artisan module:make:request StoreCategoryRequest --module=Product

# Step 6: Generate API resources
php artisan module:make:resource ProductResource --module=Product
php artisan module:make:resource ProductCollection --module=Product --collection
php artisan module:make:resource CategoryResource --module=Product

# Step 7: Create jobs for background processing
php artisan module:make:job ProcessProductImport --module=Product
php artisan module:make:job GenerateProductThumbnails --module=Product
php artisan module:make:job UpdateProductInventory --module=Product

# Step 8: Add events and listeners
php artisan module:make:event ProductCreated --module=Product
php artisan module:make:event ProductPriceChanged --module=Product
php artisan module:make:listener UpdateSearchIndex --module=Product --event=ProductCreated --queued
php artisan module:make:listener NotifyPriceWatchers --module=Product --event=ProductPriceChanged --queued

# Step 9: Create view components
php artisan module:make:component ProductCard --module=Product --test
php artisan module:make:component CategoryMenu --module=Product
php artisan module:make:component PriceDisplay --module=Product --inline

# Step 10: Generate comprehensive tests
php artisan module:make:test ProductCrudTest --module=Product --pest
php artisan module:make:test CategoryTest --module=Product --pest
php artisan module:make:test ProductSearchTest --module=Product --pest
```

**Result:** A complete e-commerce product catalog with categories, brands, variants, images, import jobs, and real-time notifications.

### Example 5: Order Processing Module

Create an order processing module with events and notifications:

```bash
# Step 1: Create Order module
php artisan module:make Module Order

# Step 2: Generate Order model with all files
php artisan module:make:model Order --module=Order --all

# Step 3: Create related models
php artisan module:make:model OrderItem --module=Order --migration --factory
php artisan module:make:model OrderStatus --module=Order --migration --seeder
php artisan module:make:model ShippingAddress --module=Order --migration

# Step 4: Add request validation
php artisan module:make:request CreateOrderRequest --module=Order
php artisan module:make:request UpdateOrderStatusRequest --module=Order
php artisan module:make:request CancelOrderRequest --module=Order

# Step 5: Create jobs for order processing
php artisan module:make:job ProcessOrder --module=Order
php artisan module:make:job CalculateOrderTotals --module=Order
php artisan module:make:job SendOrderToWarehouse --module=Order

# Step 6: Create events for order lifecycle
php artisan module:make:event OrderPlaced --module=Order
php artisan module:make:event OrderShipped --module=Order
php artisan module:make:event OrderDelivered --module=Order
php artisan module:make:event OrderCancelled --module=Order

# Step 7: Create listeners for notifications
php artisan module:make:listener SendOrderConfirmation --module=Order --event=OrderPlaced --queued
php artisan module:make:listener UpdateInventory --module=Order --event=OrderPlaced --queued
php artisan module:make:listener SendShippingNotification --module=Order --event=OrderShipped --queued
php artisan module:make:listener ProcessRefund --module=Order --event=OrderCancelled --queued

# Step 8: Generate mail templates
php artisan module:make:mail OrderConfirmation --module=Order --markdown=emails.order.confirmation
php artisan module:make:mail ShippingUpdate --module=Order --markdown=emails.order.shipping
php artisan module:make:mail OrderCancellation --module=Order --markdown=emails.order.cancellation

# Step 9: Create notifications
php artisan module:make:notification OrderStatusChanged --module=Order
php artisan module:make:notification PaymentReceived --module=Order

# Step 10: Add API resources
php artisan module:make:resource OrderResource --module=Order
php artisan module:make:resource OrderCollection --module=Order --collection

# Step 11: Generate tests
php artisan module:make:test OrderProcessingTest --module=Order --pest
php artisan module:make:test OrderStatusTest --module=Order --pest
php artisan module:make:test OrderEventTest --module=Order --pest --unit
```

**Result:** A complete order processing module with lifecycle events, notifications, background jobs, and comprehensive testing.

### Example 6: Quick Prototyping with --all Flag

When you need to rapidly prototype a feature, use the `--all` flag to generate everything at once:

```bash
# Create a new Invoice module with complete CRUD in seconds
php artisan module:make Module Invoice

# Generate everything for the Invoice model in ONE command
php artisan module:make:model Invoice --module=Invoice --all

# What gets created:
# ✓ Invoice Model (modules/Invoice/Models/Invoice.php)
# ✓ CreateInvoicesTable Migration (modules/Invoice/Database/Migrations/...)
# ✓ InvoiceFactory (modules/Invoice/Database/Factories/InvoiceFactory.php)
# ✓ InvoiceSeeder (modules/Invoice/Database/Seeders/InvoiceSeeder.php)
# ✓ InvoicePolicy (modules/Invoice/Policies/InvoicePolicy.php)
# ✓ InvoiceController with all CRUD methods (modules/Invoice/Controllers/InvoiceController.php)

# Add validation and you're ready to go
php artisan module:make:request StoreInvoiceRequest --module=Invoice
php artisan module:make:request UpdateInvoiceRequest --module=Invoice

# Add a quick test
php artisan module:make:test InvoiceTest --module=Invoice --pest

# View your module
php artisan module:list
```

**Result:** A complete CRUD module ready for development in under a minute!

### Example 7: Multi-Tenant Application Structure

Organize a multi-tenant SaaS application with separate domain modules:

```bash
# Step 1: Create Tenant management module
php artisan module:make Module Tenant
php artisan module:make:model Tenant --module=Tenant --all
php artisan module:make:model TenantUser --module=Tenant --pivot --migration
php artisan module:make:middleware IdentifyTenant --module=Tenant

# Step 2: Create Subscription module
php artisan module:make Module Subscription
php artisan module:make:model Plan --module=Subscription --migration --seeder
php artisan module:make:model Subscription --module=Subscription --all
php artisan module:make:job ProcessSubscriptionRenewal --module=Subscription
php artisan module:make:event SubscriptionExpired --module=Subscription

# Step 3: Create Billing module
php artisan module:make Module Billing
php artisan module:make:model Invoice --module=Billing --all
php artisan module:make:model Payment --module=Billing --migration --factory
php artisan module:make:job ProcessPayment --module=Billing
php artisan module:make:mail InvoiceCreated --module=Billing --markdown=emails.billing.invoice

# Step 4: Create Dashboard module (tenant-specific)
php artisan module:make Module Dashboard
php artisan module:make:controller DashboardController --module=Dashboard --resource
php artisan module:make:view index --module=Dashboard --test
php artisan module:make:component StatsCard --module=Dashboard --test

# Step 5: Verify all modules
php artisan module:list
```

**Result:** A well-organized multi-tenant application with clear separation of concerns across Tenant, Subscription, Billing, and Dashboard modules.

## Advance Usage

### Generate Controller

Create a controller for a module using the `module:make:controller` command.

#### Basic Controller

Generate a plain controller:

```bash
php artisan module:make:controller UserController --module=User
```

This creates a basic controller class in `modules/User/Controllers/UserController.php`.

#### Controller Options

##### API Controller (`--api`)

Generate an API controller without `create` and `edit` methods:

```bash
php artisan module:make:controller ApiUserController --module=User --api
```

##### Invokable Controller (`--invokable` or `-i`)

Generate a single-action controller with an `__invoke` method:

```bash
php artisan module:make:controller ProcessPayment --module=Payment --invokable
```

##### Resource Controller (`--resource` or `-r`)

Generate a resource controller with all CRUD methods:

```bash
php artisan module:make:controller ProductController --module=Product --resource
```

##### Model-Based Resource Controller (`--model` or `-m`)

Generate a resource controller with type-hinted model:

```bash
php artisan module:make:controller OrderController --module=Order --model=Order
```

##### Force Creation (`--force`)

Overwrite existing controller:

```bash
php artisan module:make:controller UserController --module=User --force
```

#### Combined Options

You can combine multiple options:

```bash
# API resource controller with model
php artisan module:make:controller ProductController --module=Product --api --model=Product

# Invokable controller (force overwrite)
php artisan module:make:controller SendNotification --module=Notification --invokable --force
```

### Generate Model

Create a model for a module using the `module:make:model` command.

#### Basic Model

Generate a plain model:

```bash
php artisan module:make:model User --module=User
```

This creates a basic model class in `modules/User/Models/User.php`.

#### Model Options

##### Generate All Related Files (`--all` or `-a`)

Generate a model with migration, seeder, factory, policy, and resource controller:

```bash
php artisan module:make:model Product --module=Product --all
```

This creates:
- Model: `modules/Product/Models/Product.php`
- Migration: `modules/Product/Database/Migrations/YYYY_MM_DD_HHMMSS_create_products_table.php`
- Factory: `modules/Product/Database/Factories/ProductFactory.php`
- Seeder: `modules/Product/Database/Seeders/ProductSeeder.php`
- Policy: `modules/Product/Policies/ProductPolicy.php`
- Resource Controller: `modules/Product/Controllers/ProductController.php`

##### Migration (`--migration` or `-m`)

Generate a model with a migration file:

```bash
php artisan module:make:model Post --module=Blog --migration
```

##### Factory (`--factory` or `-f`)

Generate a model with a factory:

```bash
php artisan module:make:model Comment --module=Blog --factory
```

##### Seeder (`--seed` or `-s`)

Generate a model with a seeder:

```bash
php artisan module:make:model Category --module=Blog --seed
```

##### Controller (`--controller` or `-c`)

Generate a model with a controller:

```bash
php artisan module:make:model Order --module=Order --controller
```

##### Policy (`--policy`)

Generate a model with a policy:

```bash
php artisan module:make:model Invoice --module=Billing --policy
```

##### Pivot Model (`--pivot` or `-p`)

Generate a custom intermediate table model:

```bash
php artisan module:make:model RoleUser --module=User --pivot
```

##### API Controller (`--api`)

When used with `--controller`, excludes `create` and `edit` methods:

```bash
php artisan module:make:model Product --module=Product --controller --api
```

##### Invokable Controller (`--invokable` or `-i`)

When used with `--controller`, generates a single-action controller:

```bash
php artisan module:make:model Report --module=Reporting --controller --invokable
```

##### Resource Controller (`--resource` or `-r`)

When used with `--controller`, generates a resource controller with all CRUD methods:

```bash
php artisan module:make:model Customer --module=Customer --controller --resource
```

##### Force Creation (`--force`)

Overwrite existing model:

```bash
php artisan module:make:model User --module=User --force
```

#### Combined Options

You can combine multiple options:

```bash
# Model with migration and factory
php artisan module:make:model Product --module=Product --migration --factory

# Model with API resource controller and policy
php artisan module:make:model Order --module=Order --controller --api --resource --policy

# Complete model setup with all files
php artisan module:make:model Article --module=Blog --all

# Pivot model with migration
php artisan module:make:model PostTag --module=Blog --pivot --migration
```

### Generate Migration

Create a database migration for a module using the `module:make:migration` command.

#### Basic Migration

Generate a plain migration:

```bash
php artisan module:make:migration create_users_table --module=User
```

This creates a migration file in `modules/User/Database/Migrations/`.

#### Migration Options

##### Create Table (`--create`)

Generate a migration to create a new table:

```bash
php artisan module:make:migration create_products_table --module=Product --create=products
```

The table name will automatically be pluralized.

##### Update Table (`--table`)

Generate a migration to modify an existing table:

```bash
php artisan module:make:migration add_status_to_orders --module=Order --table=orders
```

The table name will automatically be pluralized.

#### Combined Options

```bash
# Create users table migration
php artisan module:make:migration create_users_table --module=User --create=user

# Modify existing products table
php artisan module:make:migration add_price_to_products --module=Product --table=product
```

### Generate Request

Create a form request validation class for a module using the `module:make:request` command.

#### Basic Request

Generate a form request:

```bash
php artisan module:make:request StoreUserRequest --module=User
```

This creates a request class in `modules/User/Requests/StoreUserRequest.php`.

#### Request Options

##### Force Creation (`--force`)

Overwrite existing request:

```bash
php artisan module:make:request UpdateProductRequest --module=Product --force
```

#### Example Usage

```bash
# Create store request
php artisan module:make:request StoreOrderRequest --module=Order

# Create update request
php artisan module:make:request UpdateOrderRequest --module=Order
```

### Generate Factory

Create a model factory for a module using the `module:make:factory` command.

#### Basic Factory

Generate a factory:

```bash
php artisan module:make:factory UserFactory --module=User
```

This creates a factory in `modules/User/Database/Factories/UserFactory.php`.

#### Factory Options

##### Model-Based Factory (`--model`)

Generate a factory for a specific model:

```bash
php artisan module:make:factory ProductFactory --module=Product --model=Product
```

##### Force Creation (`--force`)

Overwrite existing factory:

```bash
php artisan module:make:factory UserFactory --module=User --force
```

#### Combined Options

```bash
# Factory with model
php artisan module:make:factory OrderFactory --module=Order --model=Order

# Force overwrite factory with model
php artisan module:make:factory ProductFactory --module=Product --model=Product --force
```

### Generate Seeder

Create a database seeder for a module using the `module:make:seeder` command.

#### Basic Seeder

Generate a seeder:

```bash
php artisan module:make:seeder UserSeeder --module=User
```

This creates a seeder in `modules/User/Database/Seeders/UserSeeder.php`.

#### Seeder Options

##### Model-Based Seeder (`--model`)

Generate a seeder for a specific model:

```bash
php artisan module:make:seeder ProductSeeder --module=Product --model=Product
```

##### Force Creation (`--force`)

Overwrite existing seeder:

```bash
php artisan module:make:seeder UserSeeder --module=User --force
```

#### Combined Options

```bash
# Seeder with model
php artisan module:make:seeder CategorySeeder --module=Blog --model=Category

# Force overwrite seeder
php artisan module:make:seeder ProductSeeder --module=Product --model=Product --force
```

### Generate Policy

Create an authorization policy for a module using the `module:make:policy` command.

#### Basic Policy

Generate a policy:

```bash
php artisan module:make:policy UserPolicy --module=User
```

This creates a policy in `modules/User/Policies/UserPolicy.php`.

#### Policy Options

##### Model-Based Policy (`--model`)

Generate a policy with model methods (view, create, update, delete, etc.):

```bash
php artisan module:make:policy ProductPolicy --module=Product --model=Product
```

##### Guard Option (`--guard`)

Specify the guard for the policy:

```bash
php artisan module:make:policy AdminPolicy --module=User --guard=admin
```

#### Combined Options

```bash
# Policy with model
php artisan module:make:policy OrderPolicy --module=Order --model=Order

# Policy with model and guard
php artisan module:make:policy PostPolicy --module=Blog --model=Post --guard=web
```

### Generate Job

Create a queueable job for a module using the `module:make:job` command.

#### Basic Job

Generate a job:

```bash
php artisan module:make:job ProcessPayment --module=Payment
```

This creates a job in `modules/Payment/Jobs/ProcessPayment.php`.

#### Job Options

##### Synchronous Job (`--sync`)

Generate a synchronous job (not queued):

```bash
php artisan module:make:job GenerateReport --module=Reporting --sync
```

##### Force Creation (`--force`)

Overwrite existing job:

```bash
php artisan module:make:job ProcessPayment --module=Payment --force
```

#### Combined Options

```bash
# Sync job with force
php artisan module:make:job SendEmail --module=Notification --sync --force
```

### Generate Event

Create an event class for a module using the `module:make:event` command.

#### Basic Event

Generate an event:

```bash
php artisan module:make:event UserRegistered --module=User
```

This creates an event in `modules/User/Events/UserRegistered.php`.

#### Event Options

##### Force Creation (`--force`)

Overwrite existing event:

```bash
php artisan module:make:event OrderPlaced --module=Order --force
```

### Generate Listener

Create an event listener for a module using the `module:make:listener` command.

#### Basic Listener

Generate a listener:

```bash
php artisan module:make:listener SendWelcomeEmail --module=User
```

This creates a listener in `modules/User/Listeners/SendWelcomeEmail.php`.

#### Listener Options

##### Event-Based Listener (`--event`)

Generate a listener for a specific event:

```bash
php artisan module:make:listener SendWelcomeEmail --module=User --event=UserRegistered
```

##### Queued Listener (`--queued`)

Generate a queued listener:

```bash
php artisan module:make:listener SendNotification --module=Notification --queued
```

##### Force Creation (`--force`)

Overwrite existing listener:

```bash
php artisan module:make:listener SendWelcomeEmail --module=User --force
```

#### Combined Options

```bash
# Queued listener for specific event
php artisan module:make:listener SendOrderConfirmation --module=Order --event=OrderPlaced --queued

# Event listener with force
php artisan module:make:listener ProcessPayment --module=Payment --event=PaymentReceived --force
```

### Generate Middleware

Create a middleware for a module using the `module:make:middleware` command.

#### Basic Middleware

Generate a middleware:

```bash
php artisan module:make:middleware CheckUserRole --module=User
```

This creates a middleware in `modules/User/Middleware/CheckUserRole.php`.

#### Middleware Options

##### Force Creation (`--force`)

Overwrite existing middleware:

```bash
php artisan module:make:middleware AuthenticateApi --module=Auth --force
```

### Generate Provider

Create a service provider for a module using the `module:make:provider` command.

#### Basic Provider

Generate a provider:

```bash
php artisan module:make:provider RepositoryServiceProvider --module=User
```

This creates a provider in `modules/User/Providers/RepositoryServiceProvider.php`.

#### Provider Options

##### Force Creation (`--force`)

Overwrite existing provider:

```bash
php artisan module:make:provider EventServiceProvider --module=Blog --force
```

### Generate Test

Create a test class for a module using the `module:make:test` command.

#### Basic Test

Generate a feature test:

```bash
php artisan module:make:test UserTest --module=User
```

This creates a feature test in `modules/User/Tests/Feature/UserTest.php`.

#### Test Options

##### Unit Test (`--unit` or `-u`)

Generate a unit test:

```bash
php artisan module:make:test CalculatorTest --module=Math --unit
```

##### Pest Test (`--pest` or `-p`)

Generate a Pest test:

```bash
php artisan module:make:test UserTest --module=User --pest
```

##### View Test (`--view`)

Generate a view test:

```bash
php artisan module:make:test dashboard --module=Admin --view
```

This creates a test in `modules/Admin/Tests/Feature/View/DashboardTest.php`.

##### Force Creation (`--force`)

Overwrite existing test:

```bash
php artisan module:make:test UserTest --module=User --force
```

#### Combined Options

```bash
# Pest unit test
php artisan module:make:test HelperTest --module=Utilities --pest --unit

# View Pest test
php artisan module:make:test home --module=Frontend --view --pest

# Feature test with force
php artisan module:make:test OrderTest --module=Order --force
```

### Generate View

Create a Blade view for a module using the `module:make:view` command.

#### Basic View

Generate a view:

```bash
php artisan module:make:view index --module=User
```

This creates a view in `modules/User/Views/index.blade.php`.

#### View Options

##### With Test (`--test`)

Generate a view with a PHPUnit test:

```bash
php artisan module:make:view dashboard --module=Admin --test
```

##### With Pest Test (`--pest`)

Generate a view with a Pest test:

```bash
php artisan module:make:view profile --module=User --pest
```

##### Force Creation (`--force`)

Overwrite existing view:

```bash
php artisan module:make:view index --module=Product --force
```

#### Combined Options

```bash
# View with test
php artisan module:make:view checkout --module=Order --test

# View with Pest test (force)
php artisan module:make:view home --module=Frontend --pest --force
```

### Generate Component

Create a Blade component for a module using the `module:make:component` command.

#### Basic Component

Generate a component:

```bash
php artisan module:make:component Alert --module=UI
```

This creates:
- Component class: `modules/UI/Components/Alert.php`
- Component view: `modules/UI/Views/Components/alert.blade.php`

#### Component Options

##### Inline Component (`--inline`)

Generate a component with inline view:

```bash
php artisan module:make:component Button --module=UI --inline
```

##### Anonymous Component (`--view`)

Generate an anonymous component (view only, no class):

```bash
php artisan module:make:component Card --module=UI --view
```

##### With Test (`--test`)

Generate a component with a PHPUnit test:

```bash
php artisan module:make:component Modal --module=UI --test
```

##### With Pest Test (`--pest`)

Generate a component with a Pest test:

```bash
php artisan module:make:component Dropdown --module=UI --pest
```

##### Force Creation (`--force`)

Overwrite existing component:

```bash
php artisan module:make:component Alert --module=UI --force
```

#### Combined Options

```bash
# Inline component with Pest test
php artisan module:make:component Badge --module=UI --inline --pest

# Component with test (force)
php artisan module:make:component Table --module=UI --test --force
```

### Generate Console Command

Create an Artisan console command for a module using the `module:make:console` command.

#### Basic Console Command

Generate a console command:

```bash
php artisan module:make:console SendEmails --module=Email
```

This creates a command in `modules/Email/Console/SendEmails.php`.

#### Console Command Options

##### Force Creation (`--force`)

Overwrite existing command:

```bash
php artisan module:make:console ProcessQueue --module=Queue --force
```

### Generate Mail

Create a mailable class for a module using the `module:make:mail` command.

#### Basic Mail

Generate a mailable:

```bash
php artisan module:make:mail WelcomeEmail --module=User
```

This creates a mailable in `modules/User/Mail/WelcomeEmail.php`.

#### Mail Options

##### Markdown Mail (`--markdown`)

Generate a mailable with a Markdown template:

```bash
php artisan module:make:mail OrderShipped --module=Order --markdown=emails.orders.shipped
```

##### Force Creation (`--force`)

Overwrite existing mailable:

```bash
php artisan module:make:mail WelcomeEmail --module=User --force
```

#### Combined Options

```bash
# Markdown mail with force
php artisan module:make:mail InvoicePaid --module=Billing --markdown=emails.invoice --force
```

### Generate Notification

Create a notification class for a module using the `module:make:notification` command.

#### Basic Notification

Generate a notification:

```bash
php artisan module:make:notification InvoicePaid --module=Billing
```

This creates a notification in `modules/Billing/Notifications/InvoicePaid.php`.

#### Notification Options

##### Force Creation (`--force`)

Overwrite existing notification:

```bash
php artisan module:make:notification OrderShipped --module=Order --force
```

### Generate Resource

Create an API resource for a module using the `module:make:resource` command.

#### Basic Resource

Generate a resource:

```bash
php artisan module:make:resource UserResource --module=User
```

This creates a resource in `modules/User/Resources/UserResource.php`.

#### Resource Options

##### Resource Collection (`--collection`)

Generate a resource collection:

```bash
php artisan module:make:resource UserCollection --module=User --collection
```

##### Force Creation (`--force`)

Overwrite existing resource:

```bash
php artisan module:make:resource ProductResource --module=Product --force
```

#### Combined Options

```bash
# Collection with force
php artisan module:make:resource OrderCollection --module=Order --collection --force
```

### List Modules

View all existing modules in your project using the `module:list` command.

#### Basic Usage

List all modules:

```bash
php artisan module:list
```

This displays a detailed overview of all modules in your project with the following information:

- **Module Name**: The name of each module
- **Path**: The relative path to the module directory
- **Service Provider**: Whether the module has a service provider (✓ or ✗)
- **Routes**: Number of route files in the module
- **Components**: Number of component files in the module

#### Example Output

```
Module                          Details

User                            Path: modules/User
  Service Provider              ✓
  Routes                        2
  Components                    0

Product                         Path: modules/Product
  Service Provider              ✓
  Routes                        1
  Components                    3

Blog                            Path: modules/Blog
  Service Provider              ✓
  Routes                        2
  Components                    5

Total modules: 3
```

This command is helpful for:
- Getting an overview of your modular architecture
- Verifying module structure and completeness
- Checking if service providers are properly configured
- Auditing routes and components across modules

### Practical Workflow Examples

#### Scaffolding a Complete Module

To quickly scaffold a complete module with all essential components:

```bash
# Create the module structure
php artisan module:make Module YourModuleName

# Generate a complete model with all related files
php artisan module:make:model Article --module=Blog --all

# This creates:
# - Model with migrations
# - Factory for testing
# - Seeder for sample data
# - Policy for authorization
# - Resource controller with CRUD methods
```

#### Building a REST API Module

Create a complete API module with all necessary components:

```bash
# Step 1: Create the module
php artisan module:make Module Api

# Step 2: Generate model with API controller and related files
php artisan module:make:model Product --module=Api --migration --factory --controller --api

# Step 3: Generate API resources for JSON responses
php artisan module:make:resource ProductResource --module=Api
php artisan module:make:resource ProductCollection --module=Api --collection

# Step 4: Add request validation
php artisan module:make:request StoreProductRequest --module=Api
php artisan module:make:request UpdateProductRequest --module=Api

# Step 5: List modules to verify structure
php artisan module:list
```

#### Creating an Event-Driven Module

Build a module with events, listeners, and jobs:

```bash
# Step 1: Create the module
php artisan module:make Module Notification

# Step 2: Generate event and listeners
php artisan module:make:event OrderPlaced --module=Notification
php artisan module:make:listener SendOrderConfirmation --module=Notification --event=OrderPlaced --queued
php artisan module:make:listener UpdateInventory --module=Notification --event=OrderPlaced --queued

# Step 3: Generate jobs for background processing
php artisan module:make:job SendEmailNotification --module=Notification
php artisan module:make:job ProcessOrderNotification --module=Notification

# Step 4: Generate mailable
php artisan module:make:mail OrderConfirmation --module=Notification --markdown=emails.order-confirmation
```

#### Building a Feature Module with Testing

Create a module with comprehensive test coverage:

```bash
# Step 1: Create module and model
php artisan module:make Module Payment
php artisan module:make:model Transaction --module=Payment --migration --factory --policy

# Step 2: Generate controllers with tests
php artisan module:make:controller PaymentController --module=Payment --resource
php artisan module:make:test PaymentControllerTest --module=Payment

# Step 3: Generate unit tests
php artisan module:make:test TransactionModelTest --module=Payment --unit

# Step 4: Generate feature tests with Pest
php artisan module:make:test ProcessPaymentTest --module=Payment --pest
```

#### Using the --all Flag for Rapid Development

The `--all` flag is perfect for rapid prototyping and development:

```bash
# Generate a complete CRUD module in one command
php artisan module:make:model Post --module=Blog --all

# This single command creates:
# ✓ Model class
# ✓ Database migration
# ✓ Model factory for testing
# ✓ Database seeder
# ✓ Authorization policy
# ✓ Resource controller with all CRUD methods

# Follow up with additional components as needed
php artisan module:make:request StorePostRequest --module=Blog
php artisan module:make:request UpdatePostRequest --module=Blog
php artisan module:make:resource PostResource --module=Blog
php artisan module:make:test PostTest --module=Blog --pest
```

#### Multi-Module Project Structure

Organize a large application with multiple domain modules:

```bash
# User management module
php artisan module:make Module User
php artisan module:make:model User --module=User --all
php artisan module:make:middleware CheckUserRole --module=User

# Product catalog module
php artisan module:make Module Product
php artisan module:make:model Product --module=Product --all
php artisan module:make:model Category --module=Product --migration --factory

# Order processing module
php artisan module:make Module Order
php artisan module:make:model Order --module=Order --all
php artisan module:make:event OrderPlaced --module=Order
php artisan module:make:job ProcessOrder --module=Order

# Payment processing module
php artisan module:make Module Payment
php artisan module:make:model Transaction --module=Payment --migration --factory
php artisan module:make:job ProcessPayment --module=Payment

# Verify all modules are properly set up
php artisan module:list
```

Credits to:
- ["Modular Structure in Laravel 5" tutorial](http://ziyahanalbeniz.blogspot.com.tr/2015/03/modular-structure-in-laravel-5.html)
- ["Artem Schander - L5 Modular"](https://github.com/Artem-Schander/L5Modular)
