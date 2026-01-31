# Test Suite Documentation

## Overview

This document provides comprehensive documentation for the Restaurant Management System test suite. The tests are organized into three main categories: Unit Tests, Feature Tests, and Integration Tests.

## Test Structure

```
tests/
├── Unit/                          # Unit tests for models and services
│   └── Models/
│       ├── RestaurantTest.php    # Restaurant model tests
│       ├── ProductTest.php       # Product model tests
│       ├── OrderTest.php         # Order model tests
│       └── ConversationTest.php  # Conversation model tests
├── Feature/                       # API endpoint feature tests
│   └── API/
│       ├── RestaurantAPITest.php # Restaurant API tests
│       ├── OrderAPITest.php      # Order API tests
│       ├── MenuAPITest.php       # Menu/Product API tests
│       └── ConversationAPITest.php # Conversation API tests
└── Integration/                   # End-to-end workflow tests
    ├── OrderToConversationIntegrationTest.php
    ├── AIServiceIntegrationTest.php
    └── RestaurantWorkflowIntegrationTest.php
```

## Running Tests

### Run All Tests

```bash
php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests only
php artisan test tests/Unit

# Feature tests only
php artisan test tests/Feature

# Integration tests only
php artisan test tests/Integration
```

### Run Specific Test Class

```bash
php artisan test tests/Unit/Models/RestaurantTest.php
```

### Run Specific Test Method

```bash
php artisan test tests/Unit/Models/RestaurantTest.php --filter=test_can_create_restaurant
```

### Run Tests with Coverage Report

```bash
php artisan test --coverage --coverage-html=coverage
```

## Unit Tests

Unit tests verify individual model functionality in isolation.

### RestaurantTest (`tests/Unit/Models/RestaurantTest.php`)

**Tests:**

- `test_can_create_restaurant()` - Verify restaurant creation with required fields
- `test_restaurant_belongs_to_user()` - Verify owner relationship
- `test_restaurant_has_many_menus()` - Verify menu relationship
- `test_restaurant_ai_config_casting()` - Verify AI config array casting
- `test_restaurant_settings_casting()` - Verify settings array casting
- `test_restaurant_active_scope()` - Verify active restaurants query scope
- `test_restaurant_can_update_settings()` - Verify settings updates

**Coverage:** Restaurant model creation, relationships, casting, and scopes

### ProductTest (`tests/Unit/Models/ProductTest.php`)

**Tests:**

- `test_can_create_product()` - Verify product creation
- `test_product_belongs_to_category()` - Verify category relationship
- `test_product_belongs_to_restaurant()` - Verify restaurant relationship
- `test_product_price_formatting()` - Verify price data type
- `test_product_quantity_tracking()` - Verify quantity attribute
- `test_product_is_available_scope()` - Verify availability query scope
- `test_product_translations_casting()` - Verify translation fields
- `test_product_out_of_stock_handling()` - Verify zero quantity handling

**Coverage:** Product model relationships, pricing, quantity, and translations

### OrderTest (`tests/Unit/Models/OrderTest.php`)

**Tests:**

- `test_can_create_order()` - Verify order creation
- `test_order_belongs_to_user()` - Verify user relationship
- `test_order_belongs_to_restaurant()` - Verify restaurant relationship
- `test_order_has_many_items()` - Verify order items relationship
- `test_order_status_tracking()` - Verify status updates
- `test_order_payment_method()` - Verify payment method attribute
- `test_order_delivery_type()` - Verify delivery type attribute
- `test_order_total_amount_calculation()` - Verify total amount
- `test_pending_orders_scope()` - Verify pending orders query scope
- `test_order_can_be_cancelled()` - Verify order cancellation

**Coverage:** Order model creation, relationships, status management, and scopes

### ConversationTest (`tests/Unit/Models/ConversationTest.php`)

**Tests:**

- `test_can_create_conversation()` - Verify conversation creation
- `test_conversation_belongs_to_restaurant()` - Verify restaurant relationship
- `test_conversation_messages_casting()` - Verify messages array casting
- `test_conversation_sentiment_tracking()` - Verify sentiment attribute
- `test_conversation_token_counting()` - Verify token count tracking
- `test_conversation_escalation_status()` - Verify escalation status
- `test_add_message_to_conversation()` - Verify message addition
- `test_conversation_customer_identifier()` - Verify customer ID
- `test_unresolved_conversations_scope()` - Verify unresolved conversations query

**Coverage:** Conversation model, message management, sentiment, and escalation

## Feature Tests

Feature tests verify API endpoints and their interactions.

### RestaurantAPITest (`tests/Feature/API/RestaurantAPITest.php`)

**Tests:**

- `test_can_get_all_restaurants()` - GET /api/restaurants
- `test_can_get_active_restaurants()` - GET /api/restaurants/active
- `test_can_get_restaurants_by_city()` - GET /api/restaurants/city/{city}
- `test_can_get_restaurant_by_id()` - GET /api/restaurants/{id}
- `test_can_create_restaurant()` - POST /api/restaurants (authenticated)
- `test_can_update_restaurant()` - PATCH /api/restaurants/{id}
- `test_cannot_create_restaurant_without_auth()` - Auth validation
- `test_cannot_update_restaurant_without_ownership()` - Authorization validation
- `test_can_delete_restaurant()` - DELETE /api/restaurants/{id}
- `test_can_search_restaurants()` - GET /api/restaurants/search
- `test_can_get_top_rated_restaurants()` - GET /api/restaurants/top-rated

**Coverage:** Restaurant CRUD operations, authentication, authorization, and filtering

### OrderAPITest (`tests/Feature/API/OrderAPITest.php`)

**Tests:**

- `test_can_get_all_orders()` - GET /api/orders (authenticated)
- `test_can_get_order_by_id()` - GET /api/orders/{id}
- `test_can_create_order()` - POST /api/orders
- `test_can_update_order_status()` - PATCH /api/orders/{id}
- `test_can_cancel_order()` - POST /api/orders/{id}/cancel
- `test_cannot_create_order_with_insufficient_stock()` - Inventory validation
- `test_cannot_view_other_user_orders()` - Authorization check
- `test_order_includes_items()` - Relationship loading
- `test_can_get_user_active_orders()` - Filtering by status
- `test_order_total_amount_validation()` - Input validation

**Coverage:** Order CRUD, inventory management, status transitions, and validation

### MenuAPITest (`tests/Feature/API/MenuAPITest.php`)

**Tests:**

- `test_can_get_restaurant_menu()` - GET /api/restaurants/{id}/menu
- `test_can_filter_menu_by_category()` - Category filtering
- `test_can_search_menu_items()` - Menu search
- `test_can_get_menu_with_pricing()` - Pricing information
- `test_can_get_available_items_only()` - Availability filtering
- `test_can_add_item_to_menu()` - POST menu item
- `test_can_update_menu_item()` - PATCH menu item
- `test_can_delete_menu_item()` - DELETE menu item
- `test_cannot_modify_menu_without_ownership()` - Authorization
- `test_menu_items_have_translations()` - Multi-language support
- `test_can_get_menu_with_images()` - Image loading

**Coverage:** Menu management, product availability, search/filtering, and translations

### ConversationAPITest (`tests/Feature/API/ConversationAPITest.php`)

**Tests:**

- `test_can_create_conversation()` - POST /api/conversations
- `test_can_get_conversation()` - GET /api/conversations/{id}
- `test_can_get_conversation_messages()` - Message retrieval
- `test_can_update_conversation()` - PATCH /api/conversations/{id}
- `test_can_add_message_to_conversation()` - POST message
- `test_can_list_conversations()` - GET /api/conversations
- `test_can_filter_conversations_by_sentiment()` - Sentiment filtering
- `test_conversation_tracks_token_count()` - Token counting
- `test_can_escalate_conversation()` - POST escalate
- `test_cannot_view_other_restaurant_conversations()` - Authorization
- `test_conversation_message_structure()` - Message format validation

**Coverage:** Conversation CRUD, message management, sentiment analysis, and escalation

## Integration Tests

Integration tests verify complete workflows across multiple services.

### OrderToConversationIntegrationTest

**Tests:**

- `test_complete_order_workflow_with_conversation()` - Full order + conversation flow
- `test_restaurant_context_retrieval_for_ai()` - Context loading for AI
- `test_multi_step_order_creation_with_validation()` - Multi-item orders
- `test_inventory_management_across_orders()` - Stock deduction validation
- `test_conversation_analytics()` - Analytics calculation
- `test_order_to_conversation_mapping()` - Order-conversation linking
- `test_concurrent_order_handling()` - Parallel order creation

**Coverage:** Order creation, inventory, conversation logging, and analytics

### AIServiceIntegrationTest

**Tests:**

- `test_conversation_creation_for_ai_processing()` - AI conversation setup
- `test_conversation_message_formatting_for_ai()` - Message format validation
- `test_restaurant_context_loading()` - Context retrieval
- `test_sentiment_analysis_integration()` - Sentiment updates
- `test_token_counting_for_conversations()` - Token tracking
- `test_escalation_handling()` - Escalation workflow
- `test_multi_language_conversation_support()` - Multi-language support
- `test_conversation_metadata_tracking()` - Metadata storage
- `test_ai_response_caching()` - Cache behavior
- `test_conversation_context_building()` - Context assembly

**Coverage:** AI service integration, language support, escalation, and caching

### RestaurantWorkflowIntegrationTest

**Tests:**

- `test_complete_restaurant_setup_workflow()` - Setup flow
- `test_restaurant_profile_completeness()` - Profile updates
- `test_restaurant_statistics_tracking()` - Statistics aggregation
- `test_reservation_workflow()` - Reservation creation
- `test_multi_tenant_isolation()` - Tenant separation
- `test_restaurant_deactivation_workflow()` - Deactivation flow
- `test_restaurant_reactivation_workflow()` - Reactivation flow
- `test_restaurant_deletion_workflow()` - Deletion flow
- `test_restaurant_listing_per_user()` - User-specific listing
- `test_restaurant_settings_management()` - Settings updates

**Coverage:** Complete restaurant lifecycle, multi-tenancy, and settings

## Test Configuration

### Database Setup

Tests use an in-memory SQLite database (configured in `phpunit.xml`):

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Traits Used

- **RefreshDatabase**: Resets database before each test
- **Sanctum**: For API authentication testing

### Example Test Structure

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

    public function test_example(): void
    {
        // Arrange: Setup test data
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act: Execute the operation
        $response = $this->getJson('/api/endpoint');

        // Assert: Verify the result
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'name']);
    }
}
```

## Best Practices

### 1. Test Isolation

- Use `RefreshDatabase` trait to ensure clean state
- Create fresh test data using factories
- Avoid test interdependencies

### 2. Descriptive Names

- Test method names should clearly describe what is being tested
- Format: `test_should_[expected_behavior]_when_[condition]`

### 3. Arrange-Act-Assert Pattern

```php
// Arrange: Setup
$user = User::factory()->create();

// Act: Execute
$response = $this->getJson('/api/endpoint');

// Assert: Verify
$response->assertStatus(200);
```

### 4. Authentication Testing

```php
// Authenticated request
Sanctum::actingAs($user);
$response = $this->getJson('/api/protected');

// Unauthenticated request
$response = $this->getJson('/api/protected');
$response->assertStatus(401);
```

### 5. Error Handling

```php
// Test validation errors
$response = $this->postJson('/api/endpoint', ['invalid' => 'data']);
$response->assertStatus(422);
$response->assertJsonValidationErrors(['field']);
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Tests
on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v2
            - name: Setup PHP
              uses: shivammathur/setup-php@v2
              with:
                  php-version: "8.2"
            - name: Install dependencies
              run: composer install
            - name: Run tests
              run: php artisan test
            - name: Upload coverage
              run: php artisan test --coverage
```

## Troubleshooting

### Common Issues

**1. Database Connection Error**

```
Solution: Ensure SQLite is available in PHP configuration
```

**2. Factory Not Found**

```
Solution: Create corresponding factory or use factory() helper
```

**3. Sanctum Not Generating Token**

```
Solution: Ensure User model uses HasApiTokens trait
```

**4. Assertion Failures**

```
Solution: Check response structure with $response->dump()
```

## Test Metrics

Current test coverage:

- **Unit Tests**: 4 model test classes with 30+ test methods
- **Feature Tests**: 4 API test classes with 40+ test methods
- **Integration Tests**: 3 workflow test classes with 30+ test methods
- **Total**: 100+ test methods covering core functionality

## Future Enhancements

1. **Performance Tests**: Add tests for query optimization
2. **Load Testing**: Verify concurrent user handling
3. **Security Tests**: Add SQL injection and XSS prevention tests
4. **API Documentation Tests**: Generate API docs from tests
5. **Mutation Testing**: Verify test effectiveness

## Related Documentation

- [Architecture Guide](API_CONTROLLERS_GUIDE.md)
- [Database Schema](DATABASE_ARCHITECTURE.md)
- [API Reference](QUICK_API_REFERENCE.md)
