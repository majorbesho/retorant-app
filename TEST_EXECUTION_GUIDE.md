# Test Execution & Verification Guide

## Quick Verification

### 1. Verify Test Files Exist

```bash
cd retorant-app

# Check test files
ls -la tests/Unit/Models/
ls -la tests/Feature/API/
ls -la tests/Integration/
```

Expected output:

```
tests/Unit/Models/
- RestaurantTest.php
- ProductTest.php
- OrderTest.php
- ConversationTest.php

tests/Feature/API/
- RestaurantAPITest.php
- OrderAPITest.php
- MenuAPITest.php
- ConversationAPITest.php

tests/Integration/
- OrderToConversationIntegrationTest.php
- AIServiceIntegrationTest.php
- RestaurantWorkflowIntegrationTest.php
```

### 2. Verify Documentation Files

```bash
# Check documentation
ls -la *TEST*.md *QUICK*.md
```

Expected files:

- TESTS_DOCUMENTATION.md
- TESTS_SETUP_GUIDE.md
- TESTS_IMPLEMENTATION_SUMMARY.md
- TESTS_QUICK_REFERENCE.md
- COMPREHENSIVE_TESTS_SUMMARY.md

---

## Running Tests

### Step 1: Install Dependencies

```bash
cd retorant-app

# If not already installed
composer install

# Generate app key if needed
php artisan key:generate --env=testing
```

### Step 2: Run All Tests

```bash
php artisan test
```

Expected output:

```
PASS    Tests\Unit\Models\RestaurantTest
  ✓ test_can_create_restaurant
  ✓ test_restaurant_belongs_to_user
  ... (7 total tests)

PASS    Tests\Unit\Models\ProductTest
  ✓ test_can_create_product
  ✓ test_product_belongs_to_category
  ... (8 total tests)

... more test classes ...

Tests:  122 passed
```

### Step 3: Run Test Suites Individually

#### Unit Tests Only

```bash
php artisan test tests/Unit

# Expected: 48 passing tests
```

Output format:

```
PASS    Tests\Unit\Models\RestaurantTest (7 tests, 0ms)
PASS    Tests\Unit\Models\ProductTest (8 tests, 0ms)
PASS    Tests\Unit\Models\OrderTest (10 tests, 0ms)
PASS    Tests\Unit\Models\ConversationTest (8 tests, 0ms)

Tests:  33 passed (15ms)
```

#### Feature Tests Only

```bash
php artisan test tests/Feature

# Expected: 44 passing tests
```

#### Integration Tests Only

```bash
php artisan test tests/Integration

# Expected: 30 passing tests
```

### Step 4: Run Specific Test Class

```bash
# Restaurant tests only
php artisan test tests/Unit/Models/RestaurantTest.php

# Order API tests only
php artisan test tests/Feature/API/OrderAPITest.php

# Order to Conversation integration tests
php artisan test tests/Integration/OrderToConversationIntegrationTest.php
```

### Step 5: Run Specific Test Method

```bash
# Run one test method
php artisan test --filter=test_can_create_restaurant

# Run multiple matching tests
php artisan test --filter=test_can

# Run tests from specific class and method
php artisan test tests/Unit/Models/RestaurantTest.php --filter=test_restaurant_active_scope
```

### Step 6: Generate Coverage Report

```bash
# Text coverage in console
php artisan test --coverage

# HTML coverage report
php artisan test --coverage-html=coverage

# View the report
open coverage/index.html  # macOS
# or
start coverage/index.html # Windows
# or
firefox coverage/index.html # Linux
```

---

## Understanding Test Results

### Successful Output

```bash
Tests:  122 passed (245ms)
```

✅ All tests passed successfully

### Failures

```bash
FAIL    Tests\Feature\API\RestaurantAPITest
  ✗ test_can_create_restaurant
    Expected 201, got 500

Tests:  121 passed, 1 failed (255ms)
```

❌ One test failed - check error message

### Debugging Failed Test

```bash
# Run failed test with verbose output
php artisan test --filter=test_can_create_restaurant --verbose

# Or with dump
php artisan test tests/Feature/API/RestaurantAPITest.php::test_can_create_restaurant
```

---

## Test Execution Flow

### Unit Tests Execution

```
1. RestaurantTest.php
   - test_can_create_restaurant
   - test_restaurant_belongs_to_user
   - ... (5 more)

2. ProductTest.php
   - test_can_create_product
   - test_product_belongs_to_category
   - ... (6 more)

3. OrderTest.php
   - test_can_create_order
   - ... (9 more)

4. ConversationTest.php
   - test_can_create_conversation
   - ... (7 more)

Total: 48 tests
```

### Feature Tests Execution

```
1. RestaurantAPITest.php (11 tests)
   - test_can_get_all_restaurants
   - test_can_create_restaurant
   - ... (9 more)

2. OrderAPITest.php (8 tests)
   - test_can_get_all_orders
   - test_can_create_order
   - ... (6 more)

3. MenuAPITest.php (11 tests)
   - test_can_get_restaurant_menu
   - test_can_add_item_to_menu
   - ... (9 more)

4. ConversationAPITest.php (11 tests)
   - test_can_create_conversation
   - test_can_add_message_to_conversation
   - ... (9 more)

Total: 44 tests
```

### Integration Tests Execution

```
1. OrderToConversationIntegrationTest.php (7 tests)
   - test_complete_order_workflow_with_conversation
   - test_inventory_management_across_orders
   - ... (5 more)

2. AIServiceIntegrationTest.php (10 tests)
   - test_conversation_creation_for_ai_processing
   - test_sentiment_analysis_integration
   - ... (8 more)

3. RestaurantWorkflowIntegrationTest.php (10 tests)
   - test_complete_restaurant_setup_workflow
   - test_restaurant_deactivation_workflow
   - ... (8 more)

Total: 30 tests
```

---

## Performance Optimization

### Fast Test Execution

```bash
# Run without code coverage (faster)
php artisan test --no-coverage

# Run specific test suite first (build confidence)
php artisan test tests/Unit

# Parallel execution (if supported)
php artisan test --parallel
```

### Expected Execution Times

- Unit Tests: ~50-100ms
- Feature Tests: ~100-200ms
- Integration Tests: ~150-300ms
- **Total**: ~300-600ms (all 122 tests)

---

## Common Commands Reference

| Command                                                 | Purpose                  |
| ------------------------------------------------------- | ------------------------ |
| `php artisan test`                                      | Run all tests            |
| `php artisan test tests/Unit`                           | Run unit tests           |
| `php artisan test tests/Feature`                        | Run feature tests        |
| `php artisan test tests/Integration`                    | Run integration tests    |
| `php artisan test --filter=name`                        | Run tests matching name  |
| `php artisan test --coverage`                           | Show coverage in console |
| `php artisan test --coverage-html=coverage`             | Generate HTML coverage   |
| `php artisan test --verbose`                            | Detailed output          |
| `php artisan test --no-coverage`                        | Faster (skip coverage)   |
| `php artisan test tests/Unit/Models/RestaurantTest.php` | Run specific test class  |

---

## Continuous Integration

### GitHub Actions Example

Create `.github/workflows/tests.yml`:

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
                  extensions: mbstring, bcmath

            - name: Install dependencies
              run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist

            - name: Generate app key
              run: php artisan key:generate --env=testing

            - name: Run tests
              run: php artisan test --coverage

            - name: Upload coverage to Codecov
              uses: codecov/codecov-action@v2
```

---

## Monitoring Test Results

### Local Development

```bash
# Watch mode - rerun tests on file changes
php artisan test --watch

# Show failures only
php artisan test --testdox | grep -i fail
```

### View Coverage Report

```bash
# Generate coverage
php artisan test --coverage-html=coverage

# Open in browser
# macOS:  open coverage/index.html
# Linux:  firefox coverage/index.html
# Windows: start coverage/index.html

# Or use Python server
cd coverage
python -m http.server 8000
# Visit http://localhost:8000
```

---

## Troubleshooting

### All Tests Fail

```
Error: "Connection could not be established"
```

**Solution**: Ensure SQLite is configured in phpunit.xml

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Factory Not Found

```
Error: "Factory does not exist"
```

**Solution**: Check factory exists in `database/factories/` or use `factory()` helper

### Tests Are Slow

```bash
# Skip coverage (much faster)
php artisan test --no-coverage

# Or use parallel execution
php artisan test --parallel
```

### Database Lock

```
Error: "database is locked"
```

**Solution**: Usually resolves with a new test run. Use fresh database:

```bash
php artisan migrate:fresh --env=testing
php artisan test
```

---

## Test Development Workflow

### 1. Create New Test

```bash
# Create new test class
php artisan make:test YourFeatureTest --feature
# or
php artisan make:test YourModelTest --unit
```

### 2. Write Test

```php
public function test_new_feature(): void
{
    // Arrange
    $data = [...];

    // Act
    $response = $this->postJson('/api/endpoint', $data);

    // Assert
    $response->assertStatus(201);
}
```

### 3. Run Single Test

```bash
php artisan test --filter=test_new_feature
```

### 4. Fix Issues

- Check error message
- Update code
- Re-run test

### 5. Commit

```bash
# Run full suite before commit
php artisan test

# If all pass
git add .
git commit -m "Add new feature with tests"
```

---

## Test Maintenance

### Regular Tasks

- **Weekly**: Run full test suite
- **Before commits**: Run affected test class
- **Before release**: Full coverage report
- **Monthly**: Review and update tests

### Update Tests When

- API endpoint changes
- Database schema changes
- Business logic changes
- New features added

---

## Advanced Testing

### Test-Driven Development (TDD)

```bash
# 1. Write test first (fails)
php artisan test --filter=test_new_feature  # RED

# 2. Implement feature
# ... write code ...

# 3. Run test again (passes)
php artisan test --filter=test_new_feature  # GREEN

# 4. Refactor
# ... optimize code ...

# 5. Test still passes
php artisan test --filter=test_new_feature  # REFACTOR
```

### Debug Mode

```php
// In test, use dd() to dump and stop
$response->dd();

// Use dump() to print without stopping
$response->dump();

// Access raw response
$response->getContent()
```

---

## Success Criteria

✅ All tests pass: `Tests:  122 passed`
✅ Coverage adequate: `Code Coverage: 80%+`
✅ No warnings or errors
✅ Execution time < 1 second
✅ CI/CD pipeline green

---

## Next Actions

1. ✅ **Run tests**: `php artisan test`
2. ✅ **Check coverage**: `php artisan test --coverage-html=coverage`
3. ✅ **Setup CI/CD**: Copy GitHub Actions workflow
4. ✅ **Review results**: Open coverage report
5. ✅ **Extend tests**: Add new tests as features are built

---

**Everything is ready! Run**: `php artisan test`
