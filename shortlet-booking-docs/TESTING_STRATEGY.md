# Testing Strategy for MVP

This document outlines the testing approach for the ShortletNG MVP. As an MVP, we focus on testing critical features while balancing development speed.

---

## Testing Philosophy

### For MVP, We Prioritize:

1. **Critical path testing** - Test features that directly impact user experience and business logic
2. **Risk-based testing** - Test high-risk areas (payments, bookings, authentication)
3. **Pragmatic coverage** - Aim for reasonable coverage, not 100%
4. **Manual testing** - Supplement automated tests with manual testing for UI/UX

### What We Test:

✅ **Must Test:**
- Authentication flows
- Booking logic (availability, pricing, validation)
- Payment processing
- Data integrity (bookings, payments)
- Critical API endpoints
- Business rules and validations

✅ **Should Test:**
- Property search and filtering
- Review submission
- Email notifications
- Admin panel critical features

❌ **Skip for MVP:**
- UI component unit tests (unless complex logic)
- Edge cases with low probability
- Third-party package internals
- Simple CRUD operations without business logic

---

## Testing Stack

### Backend (Laravel)

**Framework**: [PHPUnit](https://phpunit.de/) (built into Laravel)
**Tools**:
- **Pest** (optional, more expressive syntax than PHPUnit)
- **Laravel Factories** for test data
- **Database transactions** for test isolation

**Installation**:
```bash
# PHPUnit is already included
# To use Pest (optional)
composer require pestphp/pest --dev
composer require pestphp/pest-plugin-laravel --dev
```

### Frontend (Vue.js)

**Framework**: [Vitest](https://vitest.dev/)
**Tools**:
- **@vue/test-utils** for component testing
- **happy-dom** or **jsdom** for DOM simulation

**Installation**:
```bash
npm install --save-dev vitest @vue/test-utils happy-dom
```

---

## Backend Testing

### Test Types

#### 1. Unit Tests
Test individual methods and classes in isolation.

**Location**: `tests/Unit/`

**Example**: Testing BookingService price calculation

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\BookingService;
use App\Models\Property;

class BookingServiceTest extends TestCase
{
    public function test_calculates_total_price_correctly()
    {
        // Arrange
        $property = Property::factory()->create([
            'price_per_night' => 10000,
            'cleaning_fee' => 2000,
            'service_fee_percent' => 10
        ]);

        $bookingService = new BookingService();

        // Act
        $total = $bookingService->calculateTotalPrice(
            property: $property,
            checkIn: '2025-01-15',
            checkOut: '2025-01-20'  // 5 nights
        );

        // Assert
        // 5 nights * 10000 = 50000
        // Cleaning fee = 2000
        // Service fee = 5200 (10% of 52000)
        // Total = 57200
        $this->assertEquals(57200, $total);
    }

    public function test_validates_checkout_after_checkin()
    {
        $bookingService = new BookingService();

        $this->expectException(\InvalidArgumentException::class);

        $bookingService->validateDates(
            checkIn: '2025-01-20',
            checkOut: '2025-01-15'  // Invalid: before check-in
        );
    }
}
```

#### 2. Feature Tests
Test complete features from HTTP request to response.

**Location**: `tests/Feature/`

**Example**: Testing booking creation flow

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_booking()
    {
        // Arrange
        $user = User::factory()->create();
        $property = Property::factory()->create([
            'price_per_night' => 15000,
            'is_active' => true
        ]);

        // Act
        $response = $this->actingAs($user)->post('/bookings', [
            'property_id' => $property->id,
            'check_in' => '2025-01-15',
            'check_out' => '2025-01-20',
            'guests' => 2,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => '08012345678'
        ]);

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'property_id' => $property->id,
            'user_id' => $user->id,
            'check_in' => '2025-01-15',
            'check_out' => '2025-01-20',
            'status' => 'pending'
        ]);
    }

    public function test_cannot_book_inactive_property()
    {
        $user = User::factory()->create();
        $property = Property::factory()->create(['is_active' => false]);

        $response = $this->actingAs($user)->post('/bookings', [
            'property_id' => $property->id,
            'check_in' => '2025-01-15',
            'check_out' => '2025-01-20',
            'guests' => 2
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('bookings', [
            'property_id' => $property->id
        ]);
    }

    public function test_cannot_create_overlapping_booking()
    {
        $user = User::factory()->create();
        $property = Property::factory()->create();

        // Create existing booking
        Booking::factory()->create([
            'property_id' => $property->id,
            'check_in' => '2025-01-15',
            'check_out' => '2025-01-20',
            'status' => 'confirmed'
        ]);

        // Try to book overlapping dates
        $response = $this->actingAs($user)->post('/bookings', [
            'property_id' => $property->id,
            'check_in' => '2025-01-17',  // Overlaps with existing
            'check_out' => '2025-01-22',
            'guests' => 2
        ]);

        $response->assertSessionHasErrors();
    }
}
```

#### 3. Integration Tests
Test interactions between multiple components (database, payment gateway, etc.)

**Example**: Testing payment flow

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Booking;
use App\Services\PaymentManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_payment_confirms_booking()
    {
        // Arrange
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'total_price' => 57200
        ]);

        // Mock payment gateway response
        $this->mock(PaymentManager::class, function ($mock) {
            $mock->shouldReceive('verifyPayment')
                ->once()
                ->andReturn([
                    'status' => 'successful',
                    'reference' => 'PAY-TEST-123'
                ]);
        });

        // Act
        $response = $this->get("/payments/callback?reference=PAY-TEST-123");

        // Assert
        $response->assertRedirect('/bookings/' . $booking->id);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'status' => 'successful',
            'payment_reference' => 'PAY-TEST-123'
        ]);
    }
}
```

---

## Critical Test Cases

### 1. Authentication

```php
✅ User can register with email and password
✅ User can login with valid credentials
✅ User cannot login with invalid credentials
✅ User can login with Google OAuth
✅ User receives email verification
✅ User can reset password
✅ Authenticated user can access protected routes
✅ Unauthenticated user is redirected to login
```

### 2. Property Search & Discovery

```php
✅ Can search properties by location
✅ Can filter properties by date range
✅ Can filter properties by price range
✅ Can filter properties by amenities
✅ Only active properties are shown
✅ Search results are paginated
✅ Property detail page displays correct information
```

### 3. Booking System

```php
✅ User can create booking for available property
✅ Cannot book property with past check-in date
✅ Cannot book property with check-out before check-in
✅ Cannot create overlapping bookings
✅ Booking calculates price correctly
✅ Booking creates unique reference number
✅ User can view their booking history
✅ User can cancel booking (within policy)
```

### 4. Payment Processing

```php
✅ Payment initialization creates payment record
✅ Successful payment confirms booking
✅ Failed payment keeps booking as pending
✅ Payment verification validates reference
✅ Payment amount matches booking total
✅ Duplicate payment verification is prevented
✅ Refund updates booking and payment status
```

### 5. Reviews & Ratings

```php
✅ User can submit review after completed booking
✅ User cannot review without completed booking
✅ User cannot submit duplicate review for same booking
✅ Review updates property average rating
✅ Reviews are displayed on property page
✅ Only verified bookings show verified badge
```

### 6. Admin Panel

```php
✅ Admin can create property
✅ Admin can edit property details
✅ Admin can upload property images
✅ Admin can manage bookings
✅ Admin can view all users
✅ Admin can moderate reviews
✅ Non-admin cannot access admin panel
```

---

## Frontend Testing

### Component Testing (Optional for MVP)

**Location**: `resources/js/tests/`

**Example**: Testing PropertyCard component

```javascript
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import PropertyCard from '@/Components/PropertyCard.vue'

describe('PropertyCard', () => {
  it('renders property information correctly', () => {
    const property = {
      id: 1,
      name: 'Luxury Apartment',
      city: 'Lagos',
      price_per_night: 15000,
      average_rating: 4.5,
      total_reviews: 12
    }

    const wrapper = mount(PropertyCard, {
      props: { property }
    })

    expect(wrapper.text()).toContain('Luxury Apartment')
    expect(wrapper.text()).toContain('Lagos')
    expect(wrapper.text()).toContain('₦15,000')
  })

  it('emits event when clicked', async () => {
    const property = { id: 1, name: 'Test Property' }
    const wrapper = mount(PropertyCard, {
      props: { property }
    })

    await wrapper.find('.property-card').trigger('click')

    expect(wrapper.emitted()).toHaveProperty('click')
  })
})
```

---

## Test Data Management

### Factories

Use Laravel factories to create test data:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'city' => $this->faker->city,
            'state' => 'Lagos',
            'price_per_night' => $this->faker->randomFloat(2, 5000, 50000),
            'max_guests' => $this->faker->numberBetween(1, 10),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 3),
            'property_type' => $this->faker->randomElement(['apartment', 'house', 'villa']),
            'is_active' => true,
        ];
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
```

**Usage**:
```php
// Create single property
$property = Property::factory()->create();

// Create multiple properties
$properties = Property::factory()->count(10)->create();

// Create with specific attributes
$property = Property::factory()->create([
    'price_per_night' => 20000
]);

// Use state
$inactiveProperty = Property::factory()->inactive()->create();
```

---

## Running Tests

### Run All Tests

```bash
# PHPUnit
php artisan test

# Pest (if installed)
./vendor/bin/pest
```

### Run Specific Tests

```bash
# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run specific test method
php artisan test --filter test_user_can_create_booking

# Run tests in parallel (faster)
php artisan test --parallel
```

### Frontend Tests

```bash
# Run all frontend tests
npm run test

# Watch mode (re-run on file changes)
npm run test:watch

# Coverage report
npm run test:coverage
```

---

## Test Coverage

### What Coverage Should We Aim For?

For MVP, aim for:
- **Critical business logic**: 80-90% coverage
- **Controllers**: 60-70% coverage
- **Models**: 50-60% coverage
- **Overall**: 60-70% coverage

**Note**: Don't chase 100% coverage. Focus on meaningful tests.

### Generate Coverage Report

```bash
# Generate coverage report
php artisan test --coverage

# Generate HTML coverage report
php artisan test --coverage-html coverage
```

---

## Testing Best Practices

### 1. AAA Pattern (Arrange, Act, Assert)

```php
public function test_example()
{
    // Arrange: Set up test data
    $user = User::factory()->create();
    $property = Property::factory()->create();

    // Act: Perform the action
    $response = $this->actingAs($user)->get("/properties/{$property->id}");

    // Assert: Verify the outcome
    $response->assertOk();
    $response->assertSee($property->name);
}
```

### 2. Use Descriptive Test Names

```php
// ✓ Good
test_authenticated_user_can_create_booking()
test_cannot_book_inactive_property()

// ✗ Bad
test_booking()
test_case_1()
```

### 3. Test One Thing Per Test

```php
// ✓ Good: Separate tests for different scenarios
test_user_can_login_with_valid_credentials()
test_user_cannot_login_with_invalid_credentials()

// ✗ Bad: Testing multiple things
test_user_login()
{
    // Test valid login
    // Test invalid login
    // Test locked account
    // Test email verification
}
```

### 4. Use Database Transactions

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;  // Rolls back database after each test

    // Your tests...
}
```

### 5. Mock External Services

```php
// Mock payment gateway to avoid real API calls
$this->mock(PaystackService::class, function ($mock) {
    $mock->shouldReceive('initializePayment')
        ->andReturn(['reference' => 'TEST-REF-123']);
});
```

---

## Manual Testing Checklist

For UI/UX and end-to-end flows:

### Before Each Release

- [ ] Test user registration and login
- [ ] Test Google OAuth login
- [ ] Test property search with various filters
- [ ] Test complete booking flow (search → details → booking → payment)
- [ ] Test payment with test card (Paystack/Flutterwave)
- [ ] Test booking history page
- [ ] Test review submission
- [ ] Test admin panel login
- [ ] Test property management in admin panel
- [ ] Test booking management in admin panel
- [ ] Test responsive design on mobile/tablet
- [ ] Test all email notifications are sent
- [ ] Test error messages are user-friendly

---

## CI/CD Integration (Future)

When ready to automate testing:

### GitHub Actions Example

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on:
  push:
    branches: [ develop, main ]
  pull_request:
    branches: [ develop, main ]

jobs:
  tests:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: shortletng_test
          MYSQL_ROOT_PASSWORD: password
        ports:
          - 3306:3306

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'

      - name: Install Dependencies
        run: composer install

      - name: Run Tests
        env:
          DB_DATABASE: shortletng_test
          DB_USERNAME: root
          DB_PASSWORD: password
        run: php artisan test
```

---

## Testing Tools Reference

### Backend
- **PHPUnit**: https://phpunit.de/
- **Pest**: https://pestphp.com/
- **Laravel Testing**: https://laravel.com/docs/testing

### Frontend
- **Vitest**: https://vitest.dev/
- **Vue Test Utils**: https://test-utils.vuejs.org/

### Code Coverage
- **Xdebug** (PHP): https://xdebug.org/
- **c8** (JavaScript): https://github.com/bcoe/c8

---

## Summary

For the MVP:

1. **Focus on critical features** - Authentication, bookings, payments
2. **Write feature tests first** - Cover complete user flows
3. **Add unit tests for complex logic** - Pricing, availability, validation
4. **Supplement with manual testing** - UI/UX, end-to-end flows
5. **Don't over-test** - Balance coverage with development speed

**Remember**: The goal is confidence in your code, not perfect coverage. Test what matters, ship fast, iterate based on real user feedback.
