# Test Setup & Configuration Guide

## Quick Start

### Run All Tests

```bash
cd retorant-app
php artisan test
```

### Run Specific Test Suite

```bash
php artisan test tests/Unit           # Unit tests only
php artisan test tests/Feature        # Feature tests only
php artisan test tests/Integration    # Integration tests only
```

### Run with Coverage

```bash
php artisan test --coverage
php artisan test --coverage-html=coverage  # Generate HTML report
```

## Environment Configuration

Tests use special configuration in `phpunit.xml`:

### Database Configuration

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

**Benefits:**

- Fast in-memory SQLite database
- Clean state for each test
- No need for test database setup
- Automatic cleanup between tests

### Application Configuration

```xml
<env name="APP_ENV" value="testing"/>
<env name="APP_DEBUG" value="true"/>
<env name="APP_KEY" value="base64:..." />
```

### Service Configuration

```xml
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="SESSION_DRIVER" value="array"/>
<env name="MAIL_MAILER" value="array"/>
```

## Laravel Features Used in Tests

### RefreshDatabase Trait

Automatically migrates the database before each test and rolls back after:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;  // Database reset before each test
}
```

### Sanctum Authentication

For testing API endpoints that require authentication:

```php
use Laravel\Sanctum\Sanctum;

Sanctum::actingAs($user);  // Authenticate as $user
```

### Model Factories

Create test data with factories:

```php
$user = User::factory()->create();
$restaurant = Restaurant::factory()->count(5)->create();
```

## Directory Structure

```
tests/
├── TestCase.php                    # Base test class
├── Unit/
│   └── Models/
│       ├── RestaurantTest.php
│       ├── ProductTest.php
│       ├── OrderTest.php
│       └── ConversationTest.php
├── Feature/
│   └── API/
│       ├── RestaurantAPITest.php
│       ├── OrderAPITest.php
│       ├── MenuAPITest.php
│       └── ConversationAPITest.php
└── Integration/
    ├── OrderToConversationIntegrationTest.php
    ├── AIServiceIntegrationTest.php
    └── RestaurantWorkflowIntegrationTest.php
```

## Test Class Structure

All test classes extend the base `TestCase`:

```php
<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize test data
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create();
    }

    public function test_example(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/endpoint');
        $response->assertStatus(200);
    }
}
```

## HTTP Testing Methods

### GET Requests

```php
$response = $this->get('/api/restaurants');
$response = $this->getJson('/api/restaurants');  // With JSON headers
```

### POST Requests

```php
$response = $this->post('/api/restaurants', $data);
$response = $this->postJson('/api/restaurants', $data);
```

### PATCH/PUT Requests

```php
$response = $this->patch('/api/restaurants/1', $data);
$response = $this->patchJson('/api/restaurants/1', $data);

$response = $this->put('/api/restaurants/1', $data);
$response = $this->putJson('/api/restaurants/1', $data);
```

### DELETE Requests

```php
$response = $this->delete('/api/restaurants/1');
$response = $this->deleteJson('/api/restaurants/1');
```

## Response Assertions

### Status Codes

```php
$response->assertStatus(200);
$response->assertStatus(201);  // Created
$response->assertStatus(400);  // Bad Request
$response->assertStatus(401);  // Unauthorized
$response->assertStatus(403);  // Forbidden
$response->assertStatus(404);  // Not Found
$response->assertStatus(422);  // Unprocessable Entity
```

### JSON Structure

```php
$response->assertJsonStructure([
    'id',
    'name',
    'email',
    'items' => ['*' => ['id', 'name']],
]);
```

### JSON Path Assertions

```php
$response->assertJsonPath('id', 1);
$response->assertJsonPath('user.name', 'John Doe');
$response->assertJsonPath('items.0.price', 50.00);
```

### JSON Count

```php
$response->assertJsonCount(5);
$response->assertJsonCount(3, 'items');
```

### Validation Errors

```php
$response->assertJsonValidationErrors(['email', 'phone']);
$response->assertJsonValidationErrors(['email' => 'The email field is required']);
```

## Database Assertions

### Records Exist

```php
$this->assertDatabaseHas('restaurants', [
    'id' => 1,
    'name' => 'Test Restaurant',
]);
```

### Records Don't Exist

```php
$this->assertDatabaseMissing('restaurants', [
    'id' => 999,
]);
```

### Record Count

```php
$this->assertCount(5, User::all());
$this->assertEquals(5, User::count());
```

## Authentication Testing

### Authenticated Requests

```php
$user = User::factory()->create();
Sanctum::actingAs($user);

$response = $this->getJson('/api/protected-endpoint');
```

### Unauthenticated Requests

```php
// No authentication
$response = $this->getJson('/api/protected-endpoint');
$response->assertStatus(401);  // Unauthorized
```

### Authorization Testing

```php
$user1 = User::factory()->create();
$user2 = User::factory()->create();
$resource = Resource::factory()->create(['user_id' => $user1->id]);

Sanctum::actingAs($user2);
$response = $this->patchJson("/api/resources/{$resource->id}", $data);
$response->assertStatus(403);  // Forbidden - different user
```

## Test Data Management

### Factory Usage

```php
// Create single record
$user = User::factory()->create();

// Create multiple records
$restaurants = Restaurant::factory()->count(5)->create();

// Create with specific attributes
$user = User::factory()->create([
    'email' => 'test@example.com',
    'name' => 'Test User',
]);
```

### Seeding in Tests

```php
// Run specific seeder
$this->seed(SpecificSeeder::class);

// Run all seeders
$this->seed();
```

## Common Test Patterns

### Complete CRUD Test

```php
public function test_complete_crud_workflow(): void
{
    // CREATE
    $response = $this->postJson('/api/items', ['name' => 'Test']);
    $response->assertStatus(201);
    $itemId = $response->json('id');

    // READ
    $response = $this->getJson("/api/items/{$itemId}");
    $response->assertStatus(200);
    $response->assertJsonPath('name', 'Test');

    // UPDATE
    $response = $this->patchJson("/api/items/{$itemId}", ['name' => 'Updated']);
    $response->assertStatus(200);

    // DELETE
    $response = $this->deleteJson("/api/items/{$itemId}");
    $response->assertStatus(200);

    $this->assertDatabaseMissing('items', ['id' => $itemId]);
}
```

### Authorization Test

```php
public function test_user_cannot_modify_others_resource(): void
{
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $resource = Resource::factory()->create(['user_id' => $user1->id]);

    Sanctum::actingAs($user2);
    $response = $this->patchJson("/api/resources/{$resource->id}", $data);

    $response->assertStatus(403);
}
```

### Validation Test

```php
public function test_validation_errors(): void
{
    $response = $this->postJson('/api/items', [
        'name' => '',  // Required field
        'price' => 'invalid',  // Invalid format
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'price']);
}
```

## Debugging Tests

### Print Response

```php
$response->dump();  // Pretty print response
$response->dumpHeaders();  // Print headers only
$response->dumpSession();  // Print session data
```

### Assert on Dumped Content

```php
$response->dd();  // Dump and die
```

### Log Assertions

```php
$this->assertTrue($condition, 'Custom failure message');
$this->fail('Custom failure message');
```

## CI/CD Considerations

### Running Tests in CI

```bash
# Install dependencies
composer install --no-interaction --prefer-dist

# Generate app key
php artisan key:generate --env=testing

# Run tests
php artisan test --coverage

# Generate coverage report
php artisan test --coverage-html=coverage
```

### GitHub Actions Workflow

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest

        services:
            mysql:
                image: mysql:8.0
                env:
                    MYSQL_DATABASE: test
                    MYSQL_ROOT_PASSWORD: password
                options: >-
                    --health-cmd="mysqladmin ping"
                    --health-interval=10s
                    --health-timeout=5s
                    --health-retries=3
                ports:
                    - 3306:3306

        steps:
            - uses: actions/checkout@v2

            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.2"
                  extensions: mbstring, bcmath

            - name: Install dependencies
              run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

            - name: Generate key
              run: php artisan key:generate --env=testing

            - name: Run tests
              run: php artisan test
```

## Performance Optimization

### Optimize Database Queries

```php
public function test_optimized_query(): void
{
    $response = $this->getJson('/api/restaurants');

    // Check query count
    $this->assertQueryCount(5);  // Adjust as needed
}
```

### Use Database Transactions

```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FastTest extends TestCase
{
    use DatabaseTransactions;  // Faster than RefreshDatabase for simple tests
}
```

## Useful Commands

```bash
# Run tests with verbose output
php artisan test --verbose

# Run specific test file
php artisan test tests/Feature/API/RestaurantAPITest.php

# Run tests matching pattern
php artisan test --filter=testName

# Generate coverage report
php artisan test --coverage --coverage-html

# Run without coverage (faster)
php artisan test --no-coverage
```

## Resources

- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [PHPUnit Documentation](https://phpunit.de/manual/)
