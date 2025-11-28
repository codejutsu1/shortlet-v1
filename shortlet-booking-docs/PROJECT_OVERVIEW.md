# Shortlet Booking Platform - Project Overview

## Project Name
**ShortletNG** (Working Title - Feel free to rename)

## Description
A modern, single-host shortlet booking platform targeting the Nigerian market. This platform allows guests to discover, search, and book short-term rental properties with a seamless user experience similar to Booking.com and Hotels.ng.

## Vision
To provide a streamlined, trustworthy platform for short-term property rentals in Nigeria, with smooth booking experience, secure payments, and transparent reviews.

## Tech Stack

### Frontend
- **Framework**: Vue.js 3 (Composition API)
- **Routing**: Handled by Inertia.js
- **State Management**: Pinia (for complex state) or Vue Composables
- **UI Components**: Custom components following design system
- **Styling**: Tailwind CSS
- **Build Tool**: Vite

### Backend
- **Framework**: Laravel 11.x
- **Database**: MySQL 8.0+
- **Admin Panel**: FilamentPHP 3.x
- **Authentication**: Laravel Sanctum + Google OAuth
- **Image Storage**: Laravel Media Library or direct S3 integration
- **Queue System**: Redis (for emails, notifications)

### Integration Layer
- **Inertia.js**: Server-side routing with SPA-like experience
- **API Communication**: Inertia props and shared data

### Payment Integration
- **Primary**: Paystack
- **Secondary**: Flutterwave
- **Architecture**: Manager pattern for easy provider switching via environment configuration

### DevOps & Tools
- **Version Control**: Git + GitHub
- **Package Manager**: Composer (backend), npm/pnpm (frontend)
- **Code Quality**: PHP CS Fixer, ESLint, Prettier
- **Testing**: PHPUnit (backend), Vitest (frontend)

## Target Market
- **Primary Market**: Nigeria
- **Currency**: Nigerian Naira (NGN)
- **Payment Methods**: Card payments, bank transfer, USSD (via Paystack/Flutterwave)
- **Language**: English

## MVP Features

### 1. Guest Features
- **Authentication**
  - Google OAuth integration for seamless login
  - Email/password option as fallback
  - Profile management

- **Property Discovery**
  - Browse all available properties
  - Search by location, dates, price range
  - Filter by amenities, property type, number of guests
  - View property details, photos, amenities

- **Booking System**
  - Check availability calendar
  - Book properties for specific dates
  - Secure payment processing (Paystack/Flutterwave)
  - Booking confirmation via email
  - View booking history

- **Reviews & Ratings**
  - Leave reviews after checkout
  - Rate properties (1-5 stars)
  - View existing reviews from other guests

### 2. Admin Features (FilamentPHP Dashboard)
- **Property Management**
  - Add new properties
  - Edit property details, photos, amenities
  - Set pricing, availability, booking rules
  - Manage property status (active/inactive)

- **Booking Management**
  - View all bookings (upcoming, ongoing, completed)
  - Manage booking status
  - Handle cancellations/refunds

- **User Management**
  - View all registered guests
  - Manage user accounts
  - Handle disputes

- **Reviews Moderation**
  - Review submitted reviews
  - Moderate/respond to reviews

- **Analytics Dashboard**
  - Booking statistics
  - Revenue tracking
  - Property performance metrics

### 3. System Features
- **Email Notifications**
  - Booking confirmations
  - Payment receipts
  - Booking reminders
  - Review requests after checkout

- **Payment Processing**
  - Secure payment gateway integration
  - Payment confirmation
  - Refund handling

- **Image Management**
  - Property photo uploads
  - Image optimization
  - Gallery management

## Non-MVP Features (Future Enhancements)
- Multi-host marketplace
- In-app messaging between guests and host
- Advanced analytics and reporting
- Mobile apps (iOS/Android)
- Multi-currency support
- Promotional codes and discounts
- Loyalty program
- Social media integration

## Project Constraints
- **MVP Timeline**: Focus on core features only
- **Single Host**: You are the only property host (no marketplace features)
- **Nigeria-focused**: Optimize for Nigerian market needs
- **Budget-conscious**: Use cost-effective solutions where possible

## Success Metrics for MVP
- Users can successfully find and book properties
- Payment processing works smoothly with local gateways
- Admin can manage properties and bookings efficiently
- Positive user feedback on booking experience
- Minimal bugs and smooth performance

## Next Steps
Refer to `FEATURE_ROADMAP.md` for detailed feature breakdown and implementation tracking.
