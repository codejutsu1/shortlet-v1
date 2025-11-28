# Database Schema Documentation

This document defines the complete database schema for the ShortletNG platform. Follow this schema strictly when creating migrations.

---

## Entity Relationship Overview

```
┌─────────────┐
│    users    │
└──────┬──────┘
       │
       │ 1:N
       │
       ├──────────────────┐
       │                  │
       ▼                  ▼
┌─────────────┐    ┌─────────────┐
│  bookings   │    │   reviews   │
└──────┬──────┘    └──────┬──────┘
       │                  │
       │ N:1              │ N:1
       │                  │
       ▼                  │
┌─────────────┐◄──────────┘
│ properties  │
└──────┬──────┘
       │
       ├─────────────────┐
       │                 │
       │ 1:N             │ M:N
       │                 │
       ▼                 ▼
┌─────────────┐    ┌────────────────┐
│property_    │    │property_amenity│
│  images     │    │    (pivot)     │
└─────────────┘    └────────┬───────┘
                            │
                            │ M:N
                            │
                            ▼
                     ┌─────────────┐
                     │  amenities  │
                     └─────────────┘

┌─────────────┐
│  bookings   │
└──────┬──────┘
       │
       │ 1:1
       │
       ▼
┌─────────────┐
│  payments   │
└─────────────┘
```

---

## Table Schemas

### 1. users

Stores guest user information. Extended from Laravel's default users table.

```sql
CREATE TABLE users (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(255) NOT NULL,
    email                VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at    TIMESTAMP NULL,
    password             VARCHAR(255) NULL,  -- Null for OAuth users
    google_id            VARCHAR(255) NULL UNIQUE,  -- Google OAuth ID
    avatar               VARCHAR(255) NULL,  -- Profile picture URL
    phone                VARCHAR(20) NULL,
    remember_token       VARCHAR(100) NULL,
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,

    INDEX idx_email (email),
    INDEX idx_google_id (google_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- `password` can be null for users who sign up via Google OAuth
- `google_id` stores the unique Google user identifier
- `email_verified_at` is automatically set by Laravel's email verification

---

### 2. properties

Stores property/shortlet information.

```sql
CREATE TABLE properties (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(255) NOT NULL,
    slug                 VARCHAR(255) NOT NULL UNIQUE,
    description          TEXT NOT NULL,
    address              VARCHAR(500) NOT NULL,
    city                 VARCHAR(100) NOT NULL,
    state                VARCHAR(100) NOT NULL,
    country              VARCHAR(100) DEFAULT 'Nigeria',
    latitude             DECIMAL(10, 8) NULL,
    longitude            DECIMAL(11, 8) NULL,
    price_per_night      DECIMAL(10, 2) NOT NULL,
    cleaning_fee         DECIMAL(10, 2) DEFAULT 0,
    service_fee_percent  DECIMAL(5, 2) DEFAULT 10.00,  -- Platform service fee %
    max_guests           SMALLINT UNSIGNED NOT NULL DEFAULT 2,
    bedrooms             SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    beds                 SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    bathrooms            SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    property_type        ENUM('apartment', 'house', 'villa', 'studio', 'penthouse') NOT NULL,
    is_active            BOOLEAN DEFAULT true,
    featured             BOOLEAN DEFAULT false,  -- For homepage featured section
    minimum_nights       SMALLINT UNSIGNED DEFAULT 1,
    maximum_nights       SMALLINT UNSIGNED DEFAULT 90,
    check_in_time        TIME DEFAULT '14:00:00',
    check_out_time       TIME DEFAULT '11:00:00',
    house_rules          TEXT NULL,
    cancellation_policy  ENUM('flexible', 'moderate', 'strict') DEFAULT 'moderate',
    average_rating       DECIMAL(3, 2) DEFAULT 0.00,  -- Calculated from reviews
    total_reviews        INT UNSIGNED DEFAULT 0,      -- Count of reviews
    total_bookings       INT UNSIGNED DEFAULT 0,      -- Count of bookings
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,
    deleted_at           TIMESTAMP NULL,  -- Soft deletes

    INDEX idx_slug (slug),
    INDEX idx_city (city),
    INDEX idx_state (state),
    INDEX idx_is_active (is_active),
    INDEX idx_featured (featured),
    INDEX idx_price (price_per_night),
    INDEX idx_location (city, state),
    INDEX idx_rating (average_rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- `slug` is generated from property name for SEO-friendly URLs
- `latitude` and `longitude` for map integration (optional for MVP)
- `average_rating` and `total_reviews` are denormalized for performance
- Soft deletes enabled for data retention

---

### 3. property_images

Stores property photos.

```sql
CREATE TABLE property_images (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id          BIGINT UNSIGNED NOT NULL,
    image_path           VARCHAR(500) NOT NULL,  -- Relative path or full URL
    is_primary           BOOLEAN DEFAULT false,  -- Main/cover image
    display_order        SMALLINT UNSIGNED DEFAULT 0,
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,

    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    INDEX idx_property_id (property_id),
    INDEX idx_is_primary (property_id, is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- One image should be marked as `is_primary` (cover photo)
- `display_order` determines the sequence in gallery
- Cascade delete: images are deleted when property is deleted

---

### 4. amenities

Master table of available amenities.

```sql
CREATE TABLE amenities (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                 VARCHAR(100) NOT NULL,
    slug                 VARCHAR(100) NOT NULL UNIQUE,
    icon                 VARCHAR(50) NULL,  -- Icon identifier (e.g., 'wifi', 'pool')
    category             ENUM('basic', 'facilities', 'safety') DEFAULT 'basic',
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,

    INDEX idx_slug (slug),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- Seeded with common amenities (Wi-Fi, AC, Pool, Parking, etc.)
- `icon` can reference Heroicons or custom icon set
- Categories: basic (Wi-Fi, AC), facilities (Pool, Gym), safety (Smoke alarm, First aid)

**Sample Data:**
```sql
-- Basic
- Wi-Fi
- Air Conditioning
- Heating
- TV
- Kitchen

-- Facilities
- Swimming Pool
- Parking
- Gym
- Elevator
- Balcony/Terrace

-- Safety
- Smoke Alarm
- Carbon Monoxide Alarm
- Fire Extinguisher
- First Aid Kit
- Security Cameras
```

---

### 5. property_amenity (Pivot Table)

Many-to-many relationship between properties and amenities.

```sql
CREATE TABLE property_amenity (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id          BIGINT UNSIGNED NOT NULL,
    amenity_id           BIGINT UNSIGNED NOT NULL,
    created_at           TIMESTAMP NULL,

    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE,
    UNIQUE KEY unique_property_amenity (property_id, amenity_id),
    INDEX idx_property_id (property_id),
    INDEX idx_amenity_id (amenity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### 6. bookings

Stores all property bookings.

```sql
CREATE TABLE bookings (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_reference    VARCHAR(20) NOT NULL UNIQUE,  -- e.g., BKG-20250115-ABCD
    property_id          BIGINT UNSIGNED NOT NULL,
    user_id              BIGINT UNSIGNED NOT NULL,
    check_in             DATE NOT NULL,
    check_out            DATE NOT NULL,
    guests               SMALLINT UNSIGNED NOT NULL,
    nights               SMALLINT UNSIGNED NOT NULL,  -- Calculated
    price_per_night      DECIMAL(10, 2) NOT NULL,  -- Snapshot at booking time
    subtotal             DECIMAL(10, 2) NOT NULL,  -- nights * price_per_night
    cleaning_fee         DECIMAL(10, 2) DEFAULT 0,
    service_fee          DECIMAL(10, 2) DEFAULT 0,
    total_price          DECIMAL(10, 2) NOT NULL,  -- subtotal + fees
    status               ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_status       ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    guest_name           VARCHAR(255) NOT NULL,
    guest_email          VARCHAR(255) NOT NULL,
    guest_phone          VARCHAR(20) NOT NULL,
    special_requests     TEXT NULL,
    cancelled_at         TIMESTAMP NULL,
    cancellation_reason  TEXT NULL,
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,

    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_booking_reference (booking_reference),
    INDEX idx_property_id (property_id),
    INDEX idx_user_id (user_id),
    INDEX idx_check_in (check_in),
    INDEX idx_check_out (check_out),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_dates (property_id, check_in, check_out)  -- For availability checks
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- `booking_reference` is a unique human-readable identifier
- Price fields are snapshots at booking time (in case property prices change later)
- `nights` is calculated: `check_out - check_in`
- Status flow: `pending` → `confirmed` → `completed` (or `cancelled`)
- Payment status tracked separately
- ON DELETE RESTRICT prevents deletion of properties/users with bookings

**Status Definitions:**
- **pending**: Booking created, payment not yet processed
- **confirmed**: Payment successful, booking confirmed
- **cancelled**: Booking cancelled by guest or admin
- **completed**: Guest has checked out

---

### 7. payments

Stores payment transaction records.

```sql
CREATE TABLE payments (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id           BIGINT UNSIGNED NOT NULL UNIQUE,
    payment_reference    VARCHAR(100) NOT NULL UNIQUE,  -- Gateway reference
    payment_provider     ENUM('paystack', 'flutterwave') NOT NULL,
    amount               DECIMAL(10, 2) NOT NULL,
    currency             VARCHAR(3) DEFAULT 'NGN',
    status               ENUM('pending', 'successful', 'failed', 'refunded') DEFAULT 'pending',
    gateway_response     JSON NULL,  -- Store full response from payment gateway
    paid_at              TIMESTAMP NULL,
    refunded_at          TIMESTAMP NULL,
    refund_amount        DECIMAL(10, 2) NULL,
    refund_reason        TEXT NULL,
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,

    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT,
    INDEX idx_payment_reference (payment_reference),
    INDEX idx_booking_id (booking_id),
    INDEX idx_status (status),
    INDEX idx_payment_provider (payment_provider)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- One payment per booking (1:1 relationship)
- `payment_reference` is from Paystack/Flutterwave
- `gateway_response` stores the full JSON response for debugging
- `currency` is NGN for MVP but allows future expansion

---

### 8. reviews

Stores guest reviews and ratings.

```sql
CREATE TABLE reviews (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    property_id          BIGINT UNSIGNED NOT NULL,
    user_id              BIGINT UNSIGNED NOT NULL,
    booking_id           BIGINT UNSIGNED NOT NULL UNIQUE,  -- One review per booking
    rating               TINYINT UNSIGNED NOT NULL,  -- 1-5 stars
    title                VARCHAR(255) NULL,
    comment              TEXT NOT NULL,
    cleanliness_rating   TINYINT UNSIGNED NULL,  -- Optional detailed ratings
    accuracy_rating      TINYINT UNSIGNED NULL,
    location_rating      TINYINT UNSIGNED NULL,
    value_rating         TINYINT UNSIGNED NULL,
    is_verified          BOOLEAN DEFAULT true,  -- Verified booking
    admin_response       TEXT NULL,  -- Admin can respond to reviews
    responded_at         TIMESTAMP NULL,
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,

    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_property_id (property_id),
    INDEX idx_user_id (user_id),
    INDEX idx_booking_id (booking_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Notes:**
- One review per booking (prevents duplicate reviews)
- `rating` is required (1-5 stars)
- Detailed ratings (cleanliness, accuracy, etc.) are optional for MVP
- `is_verified` is always true since reviews are tied to bookings
- Admin can respond to reviews

---

## Additional Considerations

### Indexes Strategy

Indexes are added for:
1. **Foreign keys** - For join performance
2. **Frequently queried columns** - status, dates, location
3. **Unique constraints** - email, slug, booking_reference
4. **Composite indexes** - For complex queries (property availability)

### Data Integrity

1. **Foreign Key Constraints**:
   - `ON DELETE CASCADE` for dependent data (images, reviews)
   - `ON DELETE RESTRICT` for critical data (bookings, payments)

2. **Unique Constraints**:
   - User email
   - Property slug
   - Booking reference
   - Payment reference

3. **Validation**:
   - Enforce at application level (Laravel validation)
   - Use ENUM for fixed choices
   - Use appropriate data types

### Performance Optimizations

1. **Denormalization**:
   - `average_rating` and `total_reviews` in properties table
   - Price snapshots in bookings table

2. **Eager Loading**:
   - Use Eloquent eager loading to prevent N+1 queries
   - Example: `Property::with('images', 'amenities')->get()`

3. **Caching**:
   - Cache property listings
   - Cache amenities (rarely change)
   - Cache search results

---

## Migration Order

Create migrations in this order to avoid foreign key errors:

1. users (already exists in Laravel)
2. properties
3. property_images
4. amenities
5. property_amenity
6. bookings
7. payments
8. reviews

---

## Seed Data Required

### 1. Amenities Seeder
Populate the amenities table with common amenities:
- Wi-Fi, Air Conditioning, TV, Kitchen, etc.

### 2. Admin User Seeder (for FilamentPHP)
Create an admin user for accessing the admin panel.

### 3. Sample Properties Seeder (Development Only)
Create sample properties with images for testing.

---

## Sample Queries

### Check Property Availability
```sql
SELECT p.id, p.name
FROM properties p
WHERE p.is_active = 1
  AND p.id NOT IN (
    SELECT b.property_id
    FROM bookings b
    WHERE b.status IN ('pending', 'confirmed')
      AND (
        (b.check_in <= '2025-01-20' AND b.check_out > '2025-01-15')
        OR (b.check_in < '2025-01-20' AND b.check_out >= '2025-01-20')
        OR (b.check_in >= '2025-01-15' AND b.check_out <= '2025-01-20')
      )
  );
```

### Get Property with Average Rating
```sql
SELECT p.*, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as review_count
FROM properties p
LEFT JOIN reviews r ON p.id = r.property_id
WHERE p.id = 1
GROUP BY p.id;
```

### Get User's Booking History
```sql
SELECT b.*, p.name as property_name, p.city
FROM bookings b
JOIN properties p ON b.property_id = p.id
WHERE b.user_id = 1
ORDER BY b.created_at DESC;
```

---

## Notes for Developers

1. **Always use migrations** - Never modify database directly in production
2. **Use Eloquent relationships** - Define all relationships in models
3. **Validate dates** - Check-out must be after check-in
4. **Prevent overlapping bookings** - Implement availability checking logic
5. **Use transactions** - For booking + payment operations
6. **Soft deletes** - Use for properties to retain booking history
7. **Price snapshots** - Always store prices at booking time
8. **Generate references** - Use unique, human-readable references for bookings

---

**Next Steps:**
Refer to `FEATURE_ROADMAP.md` Phase 1 to implement these migrations in the project.
