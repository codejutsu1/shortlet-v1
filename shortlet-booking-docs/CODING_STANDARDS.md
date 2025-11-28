# Coding Standards & Conventions

This document outlines the coding standards for the ShortletNG project. All code must adhere to these standards to ensure consistency, readability, and maintainability.

---

## General Principles

1. **Write clean, readable code** - Code is read more often than written
2. **Keep it simple** - Avoid over-engineering and premature optimization
3. **Be consistent** - Follow established patterns in the codebase
4. **Write self-documenting code** - Use clear names and simple logic
5. **Comment only when necessary** - Explain "why", not "what"
6. **DRY (Don't Repeat Yourself)** - Extract common logic into reusable functions
7. **SOLID principles** - Follow SOLID principles for OOP design

---

## Laravel Backend Standards

### 1. Laravel Conventions

Follow **Laravel's official conventions** and best practices as documented in the [Laravel Documentation](https://laravel.com/docs).

#### Naming Conventions

| What | How | Example |
|------|-----|---------|
| Controller | Singular, PascalCase, suffix "Controller" | `PropertyController` |
| Model | Singular, PascalCase | `Property`, `Booking` |
| Table | Plural, snake_case | `properties`, `bookings` |
| Pivot table | Singular models in alphabetical order, snake_case | `property_amenity` |
| Migration | Descriptive, snake_case | `create_properties_table` |
| Service | Singular, PascalCase, suffix "Service" | `BookingService` |
| Request | PascalCase, suffix with action | `StorePropertyRequest` |
| Resource | PascalCase, suffix "Resource" | `PropertyResource` |
| Job | PascalCase, verb-based | `SendBookingConfirmation` |
| Event | PascalCase, past tense | `BookingCreated` |
| Listener | PascalCase, action-based | `SendBookingNotification` |
| Middleware | PascalCase, descriptive | `EnsureUserIsGuest` |
| Trait | PascalCase, adjective | `Bookable`, `HasPayments` |

#### Variables & Methods

```php
// Variables: camelCase
$propertyPrice = 15000;
$checkInDate = '2025-01-15';

// Methods: camelCase, verb-based
public function calculateTotalPrice()
public function checkAvailability()
public function sendConfirmationEmail()

// Constants: UPPER_SNAKE_CASE
const BOOKING_STATUS_PENDING = 'pending';
const MAX_GUESTS_PER_PROPERTY = 10;

// Config keys: snake_case
config('payment.default_provider');
```

### 2. File Structure

```
app/
├── Http/
│   ├── Controllers/        # Route controllers (thin, delegate to services)
│   ├── Requests/          # Form request validation
│   ├── Resources/         # API resources (if needed)
│   └── Middleware/        # Custom middleware
├── Models/                # Eloquent models
├── Services/              # Business logic (core application logic)
├── Repositories/          # Data access layer (optional, for complex queries)
├── DTOs/                  # Data Transfer Objects
├── Actions/               # Single-action classes (optional)
├── Events/                # Event classes
├── Listeners/             # Event listeners
├── Jobs/                  # Queueable jobs
├── Mail/                  # Mailable classes
└── Policies/              # Authorization policies
```

### 3. Controller Standards

**Keep controllers thin** - Delegate business logic to services.

```php
<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Http\Requests\StoreBookingRequest;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService
    ) {}

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->createBooking(
            $request->validated()
        );

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully');
    }
}
```

**Rules:**
- Use dependency injection for services
- Use form requests for validation
- Return Inertia responses for pages
- Use route model binding when possible
- Use resource controllers for CRUD operations

### 4. Service Layer Standards

Services contain business logic and orchestrate models, events, jobs, etc.

```php
<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Property;
use App\Events\BookingCreated;
use Carbon\Carbon;

class BookingService
{
    public function createBooking(array $data): Booking
    {
        // Validate availability
        $this->checkAvailability(
            $data['property_id'],
            $data['check_in'],
            $data['check_out']
        );

        // Calculate pricing
        $totalPrice = $this->calculateTotalPrice(
            $data['property_id'],
            $data['check_in'],
            $data['check_out']
        );

        // Create booking
        $booking = Booking::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'],
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'guests' => $data['guests'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Dispatch event
        event(new BookingCreated($booking));

        return $booking;
    }

    private function checkAvailability(int $propertyId, string $checkIn, string $checkOut): void
    {
        // Implementation...
    }

    private function calculateTotalPrice(int $propertyId, string $checkIn, string $checkOut): float
    {
        // Implementation...
    }
}
```

**Rules:**
- One service per model/domain concept
- Public methods for main actions, private for helpers
- Type hint parameters and return types
- Throw exceptions for business rule violations
- Keep methods focused (Single Responsibility)

### 5. Model Standards

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'location',
        'price_per_night',
        'max_guests',
        'status',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'max_guests' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailableForDates($query, $checkIn, $checkOut)
    {
        // Implementation...
    }

    // Accessors & Mutators
    public function getPriceFormattedAttribute(): string
    {
        return '₦' . number_format($this->price_per_night, 2);
    }
}
```

**Rules:**
- Use `$fillable` or `$guarded`
- Use `$casts` for type casting
- Define relationships with return types
- Use query scopes for reusable queries
- Use accessors/mutators for data formatting
- Add model factories for testing

### 6. Database & Migrations

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->decimal('price_per_night', 10, 2);
            $table->integer('max_guests')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('location');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
```

**Rules:**
- One migration per table/change
- Use descriptive names
- Add indexes for frequently queried columns
- Use appropriate data types
- Include both `up()` and `down()` methods
- Use foreign key constraints

### 7. Request Validation

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'check_in' => ['required', 'date', 'after:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'check_in.after' => 'Check-in date must be in the future',
            'check_out.after' => 'Check-out date must be after check-in',
        ];
    }
}
```

### 8. Error Handling

```php
// Use specific exceptions
throw new PropertyNotAvailableException(
    "Property is not available for selected dates"
);

// Catch and handle gracefully
try {
    $booking = $this->bookingService->createBooking($data);
} catch (PropertyNotAvailableException $e) {
    return back()->with('error', $e->getMessage());
}
```

---

## Vue.js Frontend Standards

### 1. Vue Style Guide

Follow the **[Official Vue.js Style Guide](https://vuejs.org/style-guide/)** (Priority A & B rules are mandatory).

### 2. Component Naming

```javascript
// PascalCase for component files
PropertyCard.vue
BookingForm.vue
SearchFilterBar.vue

// Multi-word component names (required)
PropertyList.vue  // ✓ Good
Property.vue      // ✗ Bad (single word)
```

### 3. Component Structure

Use **Composition API** with `<script setup>`:

```vue
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

// Props
const props = defineProps({
  property: {
    type: Object,
    required: true
  },
  maxGuests: {
    type: Number,
    default: 2
  }
})

// Emits
const emit = defineEmits(['booking-created'])

// Reactive state
const checkIn = ref('')
const checkOut = ref('')

// Computed properties
const nightsCount = computed(() => {
  // Calculate nights between dates
})

const totalPrice = computed(() => {
  return props.property.price_per_night * nightsCount.value
})

// Methods
const submitBooking = () => {
  form.post(route('bookings.store'), {
    onSuccess: () => {
      emit('booking-created')
    }
  })
}

// Lifecycle
onMounted(() => {
  // Initialize component
})
</script>

<template>
  <div class="booking-widget">
    <h3>{{ property.name }}</h3>
    <p>{{ priceFormatted }}</p>
    <!-- Template content -->
  </div>
</template>

<style scoped>
/* Component-specific styles */
.booking-widget {
  padding: 1rem;
}
</style>
```

**Component Organization:**
1. Imports
2. Props definition
3. Emits definition
4. Composables
5. Reactive state (ref, reactive)
6. Computed properties
7. Methods
8. Lifecycle hooks
9. Template
10. Styles

### 4. Naming Conventions

```javascript
// Variables: camelCase
const propertyList = ref([])
const isLoading = ref(false)

// Components: PascalCase
import PropertyCard from './PropertyCard.vue'

// Events: kebab-case
emit('booking-created')
emit('filter-changed')

// Props: camelCase
defineProps({
  propertyId: Number,
  checkInDate: String
})
```

### 5. Inertia.js Standards

```vue
<script setup>
import { useForm, router } from '@inertiajs/vue3'

// Use useForm for form handling
const form = useForm({
  name: '',
  email: '',
  check_in: '',
  check_out: ''
})

const submit = () => {
  form.post(route('bookings.store'), {
    onSuccess: () => {
      // Handle success
    },
    onError: (errors) => {
      // Handle errors
    }
  })
}

// Use router for navigation
const goToProperty = (id) => {
  router.visit(route('properties.show', id))
}
</script>
```

**Rules:**
- Use `useForm` for forms with validation
- Use `router.visit()` for navigation
- Use `usePage()` to access shared data
- Define props for page data

### 6. Props Validation

```javascript
defineProps({
  property: {
    type: Object,
    required: true
  },
  maxGuests: {
    type: Number,
    default: 2,
    validator: (value) => value > 0
  },
  amenities: {
    type: Array,
    default: () => []
  }
})
```

### 7. Event Handling

```vue
<script setup>
// Define events
const emit = defineEmits(['update:modelValue', 'submit', 'cancel'])

// Emit events with descriptive names
const handleSubmit = () => {
  emit('submit', formData)
}
</script>

<template>
  <!-- Use kebab-case in templates -->
  <button @click="handleSubmit">Submit</button>
</template>
```

---

## Tailwind CSS Standards

### 1. Class Organization

Order classes logically:

```vue
<template>
  <div class="
    <!-- Layout -->
    flex items-center justify-between
    <!-- Spacing -->
    p-4 mb-6
    <!-- Sizing -->
    w-full max-w-4xl
    <!-- Typography -->
    text-lg font-semibold
    <!-- Colors -->
    bg-white text-gray-900
    <!-- Borders -->
    border border-gray-200 rounded-lg
    <!-- Effects -->
    shadow-md hover:shadow-lg
    <!-- Transitions -->
    transition-shadow duration-200
  ">
    Content
  </div>
</template>
```

### 2. Extract Repeated Patterns

```vue
<!-- Bad: Repeated classes -->
<div class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
<div class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

<!-- Good: Extract to component -->
<BaseButton variant="primary">Click me</BaseButton>
```

### 3. Use Design System Values

Always use values from DESIGN_SYSTEM.md:

```vue
<!-- Use design system colors -->
<div class="bg-primary-600 text-white">  <!-- ✓ -->
<div class="bg-blue-600 text-white">     <!-- ✗ -->

<!-- Use design system spacing -->
<div class="space-y-6">  <!-- ✓ From design system -->
<div class="space-y-5">  <!-- ✗ Not in design system -->
```

---

## Code Quality

### 1. Comments

```php
// Bad: Obvious comment
// Get the user
$user = User::find($id);

// Good: Explain why
// Check cache first to reduce database queries
$user = Cache::remember("user.{$id}", 3600, function () use ($id) {
    return User::find($id);
});
```

```javascript
// Bad: Restating the code
// Set isLoading to true
const isLoading = ref(true)

// Good: Explain business logic
// Prevent double-booking by disabling submit during processing
const isProcessing = ref(false)
```

### 2. Function Length

- Keep functions short and focused (max 20-30 lines)
- Extract complex logic into helper functions
- One function should do one thing

### 3. Avoid Magic Numbers

```php
// Bad
if ($booking->status === 2) {

}

// Good
if ($booking->status === Booking::STATUS_CONFIRMED) {

}
```

### 4. Early Returns

```php
// Good
public function checkAvailability($property, $checkIn, $checkOut)
{
    if (!$property->is_active) {
        return false;
    }

    if ($checkIn < now()) {
        return false;
    }

    // Main logic here
    return true;
}
```

---

## Testing Standards

### 1. Test Naming

```php
// Feature tests
test('user can create a booking for available property')
test('user cannot book unavailable property')

// Unit tests
test('calculates total price correctly')
test('validates check-out date is after check-in')
```

### 2. Test Structure (AAA Pattern)

```php
test('user can create a booking', function () {
    // Arrange
    $user = User::factory()->create();
    $property = Property::factory()->create();

    // Act
    $response = $this->actingAs($user)->post('/bookings', [
        'property_id' => $property->id,
        'check_in' => '2025-01-15',
        'check_out' => '2025-01-20',
    ]);

    // Assert
    $response->assertRedirect('/bookings');
    $this->assertDatabaseHas('bookings', [
        'user_id' => $user->id,
        'property_id' => $property->id,
    ]);
});
```

---

## Git Standards

See `GIT_WORKFLOW.md` for detailed Git conventions including:
- Branch naming
- Commit message format
- Pull request guidelines

---

## Additional Resources

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue.js Style Guide](https://vuejs.org/style-guide/)
- [Tailwind CSS Best Practices](https://tailwindcss.com/docs/reusing-styles)
- [Inertia.js Documentation](https://inertiajs.com/)

---

## Enforcement

- Use **PHP CS Fixer** for Laravel code formatting
- Use **ESLint** for Vue.js code linting
- Use **Prettier** for code formatting
- Review code during pull requests
- Automated checks in CI/CD pipeline (future)
