# Test Suite Quick Reference Card

## Running Tests

```bash
# All tests
php artisan test

# Specific suite
php artisan test tests/Unit
php artisan test tests/Feature
php artisan test tests/Integration

# Specific test
php artisan test tests/Unit/Models/RestaurantTest.php

# Specific test method
php artisan test --filter=test_can_create_restaurant

# With coverage
php artisan test --coverage
php artisan test --coverage-html=coverage
```

## Test Structure

### Unit Tests (4 classes, 48 tests)

```
tests/Unit/Models/
├── RestaurantTest.php      → Restaurant model tests
├── ProductTest.php         → Product model tests
├── OrderTest.php           → Order model tests
└── ConversationTest.php    → Conversation model tests
```

### Feature Tests (4 classes, 44 tests)

```
tests/Feature/API/
├── RestaurantAPITest.php   → Restaurant API endpoints
├── OrderAPITest.php        → Order API endpoints
├── MenuAPITest.php         → Menu/Product API endpoints
└── ConversationAPITest.php → Conversation API endpoints
```

### Integration Tests (3 classes, 30 tests)

```
tests/Integration/
├── OrderToConversationIntegrationTest.php    → Order workflow
├── AIServiceIntegrationTest.php              → AI integration
└── RestaurantWorkflowIntegrationTest.php     → Restaurant lifecycle
```

## Common Test Patterns

### Basic Test Structure

```php
public function test_example(): void
{
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->getJson('/api/endpoint');

    // Assert
    $response->assertStatus(200);
}
```

### Authenticated Test

```php
public function test_authenticated(): void
{
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/protected');
    $response->assertStatus(200);
}
```

### Authorization Test

```php
public function test_authorization(): void
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
public function test_validation(): void
{
    $response = $this->postJson('/api/items', ['name' => '']);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name']);
}
```

## HTTP Test Methods

```php
// GET
$this->get('/path')
$this->getJson('/path')

// POST
$this->post('/path', $data)
$this->postJson('/path', $data)

// PATCH
$this->patch('/path', $data)
$this->patchJson('/path', $data)

// PUT
$this->put('/path', $data)
$this->putJson('/path', $data)

// DELETE
$this->delete('/path')
$this->deleteJson('/path')
```

## Response Assertions

```php
// Status
$response->assertStatus(200)
$response->assertStatus(201)  // Created
$response->assertStatus(400)  // Bad Request
$response->assertStatus(401)  // Unauthorized
$response->assertStatus(403)  // Forbidden
$response->assertStatus(404)  // Not Found
$response->assertStatus(422)  // Validation Error

// JSON
$response->assertJson(['id' => 1])
$response->assertJsonPath('id', 1)
$response->assertJsonStructure(['id', 'name', 'email'])
$response->assertJsonCount(5)

// Validation
$response->assertJsonValidationErrors(['email', 'phone'])
```

## Database Assertions

```php
// Record exists
$this->assertDatabaseHas('restaurants', ['id' => 1, 'name' => 'Test'])

// Record doesn't exist
$this->assertDatabaseMissing('restaurants', ['id' => 999])

// Count
$this->assertCount(5, User::all())
$this->assertEquals(5, User::count())
```

## Factory Usage

```php
// Create single
$user = User::factory()->create()

// Create multiple
$users = User::factory()->count(5)->create()

// Create with attributes
$user = User::factory()->create([
    'email' => 'test@example.com'
])

// Make without saving
$user = User::factory()->make()
```

## Authentication & Authorization

```php
// Authenticate user
Sanctum::actingAs($user)

// Unauthenticated
// (just make request without actingAs)

// Test both
public function test_requires_auth(): void
{
    $response = $this->getJson('/api/protected');
    $response->assertStatus(401);  // Not authenticated

    Sanctum::actingAs(User::factory()->create());
    $response = $this->getJson('/api/protected');
    $response->assertStatus(200);  // Authenticated
}
```

## Debugging

```php
// Dump response
$response->dump()

// Dump and die
$response->dd()

// Print headers
$response->dumpHeaders()

// Print session
$response->dumpSession()

// Assert with message
$this->assertTrue($condition, 'Custom message')
```

## Common Endpoint Tests

### Restaurants

```
GET    /api/restaurants              → List all
GET    /api/restaurants/{id}         → Get one
POST   /api/restaurants              → Create
PATCH  /api/restaurants/{id}         → Update
DELETE /api/restaurants/{id}         → Delete
GET    /api/restaurants/active       → Filter active
GET    /api/restaurants/city/{city}  → Filter by city
```

### Orders

```
GET    /api/orders                   → List user's orders
GET    /api/orders/{id}              → Get one
POST   /api/orders                   → Create
PATCH  /api/orders/{id}              → Update status
POST   /api/orders/{id}/cancel       → Cancel
```

### Menu

```
GET    /api/restaurants/{id}/menu    → Get menu
POST   /api/restaurants/{id}/menu    → Add item
PATCH  /api/restaurants/{id}/menu/{id} → Update item
DELETE /api/restaurants/{id}/menu/{id} → Delete item
```

### Conversations

```
GET    /api/conversations            → List
GET    /api/conversations/{id}       → Get one
POST   /api/conversations            → Create
PATCH  /api/conversations/{id}       → Update
POST   /api/conversations/{id}/messages → Add message
```

## Test Configuration

### Database (phpunit.xml)

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Services (phpunit.xml)

```xml
<env name="CACHE_STORE" value="array"/>
<env name="QUEUE_CONNECTION" value="sync"/>
<env name="MAIL_MAILER" value="array"/>
<env name="APP_ENV" value="testing"/>
```

## Useful Traits

```php
// Refresh database before each test
use Illuminate\Foundation\Testing\RefreshDatabase;

// Use database transactions (faster)
use Illuminate\Foundation\Testing\DatabaseTransactions;

// For API testing with Sanctum
use Laravel\Sanctum\Sanctum;
```

## Test Naming Convention

```php
// GOOD ✓
test_can_create_restaurant()
test_cannot_update_without_ownership()
test_user_must_be_authenticated()
test_order_status_updates_correctly()

// POOR ✗
testCreateRestaurant()
test1()
test_restaurant()
```

## Status Codes Reference

| Code | Meaning       | Use Case                         |
| ---- | ------------- | -------------------------------- |
| 200  | OK            | Successful GET/PATCH/DELETE      |
| 201  | Created       | Successful POST                  |
| 400  | Bad Request   | Invalid data format              |
| 401  | Unauthorized  | Not authenticated                |
| 403  | Forbidden     | Authenticated but not authorized |
| 404  | Not Found     | Resource doesn't exist           |
| 422  | Unprocessable | Validation failed                |
| 500  | Server Error  | Unexpected error                 |

## Performance Tips

- Use in-memory SQLite for fast tests
- Use `sync` queue driver to avoid delays
- Use array cache driver for testing
- Create only necessary test data with factories
- Use `DatabaseTransactions` for faster tests (if no rollback issues)

## Documentation Files

| File                            | Purpose                  |
| ------------------------------- | ------------------------ |
| TESTS_DOCUMENTATION.md          | Comprehensive test guide |
| TESTS_SETUP_GUIDE.md            | Setup and configuration  |
| TESTS_IMPLEMENTATION_SUMMARY.md | Overview and statistics  |
| TESTS_QUICK_REFERENCE.md        | This file                |

## Common Issues & Solutions

| Issue                                | Solution                                     |
| ------------------------------------ | -------------------------------------------- |
| Tests fail with "Connection refused" | Ensure SQLite is configured in phpunit.xml   |
| Factory not found                    | Create factory or use factory() helper       |
| Sanctum token not working            | Ensure User model has HasApiTokens trait     |
| Database not clearing                | Verify RefreshDatabase trait is used         |
| Slow tests                           | Use DatabaseTransactions or optimize queries |

## Quick Start

1. **Run all tests**: `php artisan test`
2. **Check specific test**: `php artisan test tests/Unit/Models/RestaurantTest.php`
3. **Generate coverage**: `php artisan test --coverage-html=coverage`
4. **View coverage**: Open `coverage/index.html` in browser

## Support

- See TESTS_DOCUMENTATION.md for detailed explanations
- See TESTS_SETUP_GUIDE.md for configuration details
- Check Laravel docs: https://laravel.com/docs/testing

---

**Total Tests**: 122 methods across 11 classes
**Coverage**: Models, API endpoints, workflows, authentication, authorization
**Status**: ✅ Ready to use
