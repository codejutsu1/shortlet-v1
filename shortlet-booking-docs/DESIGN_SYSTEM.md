# Design System Specification

This document defines the visual design language for the ShortletNG platform. All UI components should strictly adhere to these specifications to ensure a consistent, professional user experience.

---

## Color Palette

### Primary Colors
The primary color represents trust, reliability, and professionalism.

```css
primary-50:  #EFF6FF  /* Lightest - backgrounds, hover states */
primary-100: #DBEAFE
primary-200: #BFDBFE
primary-300: #93C5FD
primary-400: #60A5FA
primary-500: #3B82F6  /* Main primary color */
primary-600: #2563EB  /* Buttons, links */
primary-700: #1D4ED8  /* Hover states */
primary-800: #1E40AF
primary-900: #1E3A8A  /* Darkest - text on light backgrounds */
```

**Usage:**
- Primary buttons
- Links
- Active states
- Call-to-action elements
- Navigation highlights

### Secondary Colors
Secondary color for accents and highlights.

```css
secondary-50:  #F0FDF4
secondary-100: #DCFCE7
secondary-200: #BBF7D0
secondary-300: #86EFAC
secondary-400: #4ADE80
secondary-500: #22C55E  /* Main secondary color */
secondary-600: #16A34A  /* Success states */
secondary-700: #15803D
secondary-800: #166534
secondary-900: #14532D
```

**Usage:**
- Success messages
- Confirmation states
- Available/verified badges
- Positive indicators

### Neutral Colors (Grays)
For text, backgrounds, borders, and subtle UI elements.

```css
gray-50:  #F9FAFB  /* Lightest backgrounds */
gray-100: #F3F4F6  /* Card backgrounds, subtle sections */
gray-200: #E5E7EB  /* Borders, dividers */
gray-300: #D1D5DB  /* Disabled elements */
gray-400: #9CA3AF  /* Placeholder text */
gray-500: #6B7280  /* Secondary text */
gray-600: #4B5563  /* Body text */
gray-700: #374151  /* Headings */
gray-800: #1F2937  /* Dark headings */
gray-900: #111827  /* Primary text, darkest */
```

**Usage:**
- Text (600-900)
- Backgrounds (50-100)
- Borders (200-300)
- Disabled states (300-400)

### Semantic Colors

#### Success
```css
success-light: #DCFCE7  /* Light background */
success:       #22C55E  /* Main success color */
success-dark:  #16A34A  /* Dark variant */
```

**Usage:** Success messages, confirmed bookings, verified badges

#### Warning
```css
warning-light: #FEF3C7  /* Light background */
warning:       #F59E0B  /* Main warning color */
warning-dark:  #D97706  /* Dark variant */
```

**Usage:** Warning messages, pending status, important notices

#### Error/Danger
```css
error-light: #FEE2E2  /* Light background */
error:       #EF4444  /* Main error color */
error-dark:  #DC2626  /* Dark variant */
```

**Usage:** Error messages, cancellations, critical warnings

#### Info
```css
info-light: #DBEAFE  /* Light background */
info:       #3B82F6  /* Main info color */
info-dark:  #2563EB  /* Dark variant */
```

**Usage:** Informational messages, tips, neutral notifications

### Background Colors

```css
bg-primary:   #FFFFFF  /* Main background (white) */
bg-secondary: #F9FAFB  /* Subtle section backgrounds (gray-50) */
bg-tertiary:  #F3F4F6  /* Cards, elevated surfaces (gray-100) */
```

---

## Typography

### Font Families

```css
font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', sans-serif;
font-mono: 'Fira Code', 'Monaco', 'Courier New', monospace;
```

**Primary Font:** Inter (Google Fonts)
- Clean, modern, excellent readability
- Great for UI and content
- Use for all text unless specified otherwise

**Fallback:** System fonts for performance

### Font Sizes

```css
text-xs:   0.75rem   /* 12px - Small labels, captions */
text-sm:   0.875rem  /* 14px - Secondary text, small buttons */
text-base: 1rem      /* 16px - Body text, default */
text-lg:   1.125rem  /* 18px - Emphasized text */
text-xl:   1.25rem   /* 20px - Subheadings */
text-2xl:  1.5rem    /* 24px - Section headings */
text-3xl:  1.875rem  /* 30px - Page headings */
text-4xl:  2.25rem   /* 36px - Hero headings */
text-5xl:  3rem      /* 48px - Large hero text */
text-6xl:  3.75rem   /* 60px - Extra large displays */
```

### Font Weights

```css
font-light:     300  /* Rarely used, very light text */
font-normal:    400  /* Body text, default */
font-medium:    500  /* Emphasized text, navigation */
font-semibold:  600  /* Subheadings, important labels */
font-bold:      700  /* Headings, CTAs */
font-extrabold: 800  /* Large headings, hero text */
```

### Line Heights

```css
leading-none:    1      /* Tight, for large headings */
leading-tight:   1.25   /* Headings */
leading-snug:    1.375  /* Subheadings */
leading-normal:  1.5    /* Body text, default */
leading-relaxed: 1.625  /* Comfortable reading */
leading-loose:   2      /* Very spacious */
```

### Typography Usage

```html
<!-- Hero Heading -->
<h1 class="text-5xl font-extrabold leading-tight text-gray-900">

<!-- Page Heading -->
<h1 class="text-4xl font-bold leading-tight text-gray-900">

<!-- Section Heading -->
<h2 class="text-3xl font-bold leading-tight text-gray-800">

<!-- Subsection Heading -->
<h3 class="text-2xl font-semibold leading-snug text-gray-800">

<!-- Card Heading -->
<h4 class="text-xl font-semibold leading-snug text-gray-700">

<!-- Body Text -->
<p class="text-base font-normal leading-normal text-gray-600">

<!-- Small Text / Caption -->
<p class="text-sm font-normal leading-normal text-gray-500">

<!-- Label -->
<label class="text-sm font-medium leading-normal text-gray-700">
```

---

## Spacing Scale

Use consistent spacing throughout the application:

```css
0:    0       /* 0px */
px:   1px     /* 1px - Borders */
0.5:  0.125rem /* 2px */
1:    0.25rem  /* 4px */
1.5:  0.375rem /* 6px */
2:    0.5rem   /* 8px */
2.5:  0.625rem /* 10px */
3:    0.75rem  /* 12px */
3.5:  0.875rem /* 14px */
4:    1rem     /* 16px - Base unit */
5:    1.25rem  /* 20px */
6:    1.5rem   /* 24px - Common spacing */
7:    1.75rem  /* 28px */
8:    2rem     /* 32px - Section spacing */
10:   2.5rem   /* 40px */
12:   3rem     /* 48px - Large spacing */
16:   4rem     /* 64px - Extra large spacing */
20:   5rem     /* 80px - Hero spacing */
24:   6rem     /* 96px */
32:   8rem     /* 128px */
```

### Spacing Guidelines

```html
<!-- Component internal padding -->
<div class="p-4">       <!-- Small components -->
<div class="p-6">       <!-- Medium components, cards -->
<div class="p-8">       <!-- Large components -->

<!-- Section spacing -->
<section class="py-12"> <!-- Between sections -->
<section class="py-16"> <!-- Large sections -->

<!-- Element spacing -->
<div class="space-y-4"> <!-- Between related elements -->
<div class="space-y-6"> <!-- Between sections within a component -->
<div class="space-y-8"> <!-- Between distinct sections -->

<!-- Grid gaps -->
<div class="gap-4">     <!-- Small grids -->
<div class="gap-6">     <!-- Medium grids, property cards -->
<div class="gap-8">     <!-- Large grids -->
```

---

## Border Radius

```css
rounded-none: 0        /* No rounding */
rounded-sm:   0.125rem /* 2px - Subtle rounding */
rounded:      0.25rem  /* 4px - Default, buttons, inputs */
rounded-md:   0.375rem /* 6px - Cards, containers */
rounded-lg:   0.5rem   /* 8px - Large cards, modals */
rounded-xl:   0.75rem  /* 12px - Featured cards */
rounded-2xl:  1rem     /* 16px - Hero sections */
rounded-full: 9999px   /* Pills, avatars, badges */
```

### Border Radius Usage

```html
<!-- Buttons -->
<button class="rounded-lg">

<!-- Input fields -->
<input class="rounded-md">

<!-- Cards -->
<div class="rounded-lg">  <!-- Standard cards -->
<div class="rounded-xl">  <!-- Featured/hero cards -->

<!-- Badges -->
<span class="rounded-full">

<!-- Images -->
<img class="rounded-lg">  <!-- Property images -->
```

---

## Shadows

```css
shadow-sm:   0 1px 2px 0 rgb(0 0 0 / 0.05)
shadow:      0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)
shadow-md:   0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)
shadow-lg:   0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)
shadow-xl:   0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)
shadow-2xl:  0 25px 50px -12px rgb(0 0 0 / 0.25)
```

### Shadow Usage

```html
<!-- Cards at rest -->
<div class="shadow-md">

<!-- Cards on hover -->
<div class="hover:shadow-lg transition-shadow">

<!-- Modals, popovers -->
<div class="shadow-xl">

<!-- Floating action buttons -->
<button class="shadow-lg">

<!-- Sticky headers -->
<header class="shadow-sm">
```

---

## Component Patterns

### Buttons

#### Primary Button
```html
<button class="
  px-6 py-3
  bg-primary-600 hover:bg-primary-700
  text-white font-semibold
  rounded-lg
  shadow-md hover:shadow-lg
  transition-all duration-200
  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
">
  Book Now
</button>
```

#### Secondary Button
```html
<button class="
  px-6 py-3
  bg-white hover:bg-gray-50
  text-primary-600 font-semibold
  border-2 border-primary-600
  rounded-lg
  transition-all duration-200
  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
">
  View Details
</button>
```

#### Danger Button
```html
<button class="
  px-6 py-3
  bg-error hover:bg-error-dark
  text-white font-semibold
  rounded-lg
  shadow-md hover:shadow-lg
  transition-all duration-200
">
  Cancel Booking
</button>
```

#### Ghost Button
```html
<button class="
  px-6 py-3
  bg-transparent hover:bg-gray-100
  text-gray-700 font-medium
  rounded-lg
  transition-colors duration-200
">
  Learn More
</button>
```

### Input Fields

```html
<input type="text" class="
  w-full
  px-4 py-3
  text-gray-900
  bg-white
  border border-gray-300
  rounded-md
  focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent
  placeholder:text-gray-400
" placeholder="Enter location">
```

### Cards

#### Property Card
```html
<div class="
  bg-white
  rounded-lg
  shadow-md hover:shadow-lg
  transition-shadow duration-200
  overflow-hidden
">
  <img src="..." class="w-full h-48 object-cover">
  <div class="p-6">
    <h3 class="text-xl font-semibold text-gray-900">Property Name</h3>
    <p class="text-gray-600 mt-2">Property description...</p>
    <div class="mt-4 flex items-center justify-between">
      <span class="text-2xl font-bold text-primary-600">₦15,000</span>
      <span class="text-sm text-gray-500">per night</span>
    </div>
  </div>
</div>
```

### Badges

```html
<!-- Success Badge -->
<span class="
  inline-flex items-center
  px-3 py-1
  bg-success-light text-success-dark
  text-sm font-medium
  rounded-full
">
  Confirmed
</span>

<!-- Warning Badge -->
<span class="
  inline-flex items-center
  px-3 py-1
  bg-warning-light text-warning-dark
  text-sm font-medium
  rounded-full
">
  Pending
</span>
```

### Alerts

```html
<!-- Success Alert -->
<div class="
  p-4
  bg-success-light border-l-4 border-success
  rounded-md
">
  <p class="text-success-dark font-medium">Booking confirmed successfully!</p>
</div>

<!-- Error Alert -->
<div class="
  p-4
  bg-error-light border-l-4 border-error
  rounded-md
">
  <p class="text-error-dark font-medium">Unable to process booking. Please try again.</p>
</div>
```

---

## Responsive Breakpoints

```css
sm:  640px   /* Small devices, large phones */
md:  768px   /* Tablets */
lg:  1024px  /* Laptops, small desktops */
xl:  1280px  /* Desktops */
2xl: 1536px  /* Large desktops */
```

### Mobile-First Approach

```html
<!-- Stack on mobile, grid on larger screens -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Full width on mobile, constrained on larger screens -->
<div class="w-full lg:w-1/2 xl:w-1/3">

<!-- Hide on mobile, show on desktop -->
<div class="hidden lg:block">
```

---

## Icons

**Recommended:** [Heroicons](https://heroicons.com/) (outline style for most UI, solid for emphasis)

**Size Guidelines:**
```html
<!-- Small icons (navigation, inline) -->
<svg class="w-4 h-4">

<!-- Medium icons (buttons, cards) -->
<svg class="w-5 h-5">
<svg class="w-6 h-6">

<!-- Large icons (feature sections) -->
<svg class="w-8 h-8">
<svg class="w-12 h-12">

<!-- Hero icons (empty states) -->
<svg class="w-16 h-16">
<svg class="w-24 h-24">
```

---

## Layout Containers

### Max Width Containers
```html
<!-- Page container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<!-- Content container -->
<div class="max-w-4xl mx-auto">

<!-- Narrow content (forms, articles) -->
<div class="max-w-2xl mx-auto">
```

### Grid Layouts
```html
<!-- Property grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

<!-- Two-column layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
```

---

## Animation & Transitions

```css
/* Standard duration */
transition-all duration-200

/* Slower transitions */
transition-all duration-300

/* Color transitions only */
transition-colors duration-200

/* Shadow transitions */
transition-shadow duration-200
```

**Usage:**
- Use transitions for interactive elements (buttons, links, cards)
- Keep durations short (200-300ms) for snappy feel
- Use `ease-in-out` for most transitions (Tailwind default)

---

## Accessibility

### Focus States
Always include visible focus states:
```html
<button class="focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
```

### Color Contrast
- Ensure text has sufficient contrast (WCAG AA minimum)
- Body text: `gray-600` or darker on white backgrounds
- Headings: `gray-700` or darker
- Never use light gray text smaller than 16px

### Interactive Elements
- Minimum touch target size: 44x44px (mobile)
- Minimum click target size: 32x32px (desktop)

---

## Image Guidelines

### Property Images
- **Aspect Ratio:** 4:3 (landscape)
- **Minimum Size:** 1200x900px
- **Format:** WebP (with JPG fallback)
- **Compression:** Optimize for web (<200KB per image)

### Avatars
- **Size:** 40x40px (small), 64x64px (medium), 128x128px (large)
- **Shape:** Circular (`rounded-full`)

### Logos
- **Format:** SVG preferred, PNG fallback
- **Size:** Scalable, but display at reasonable sizes

---

## Design Principles

1. **Clarity over Cleverness** - Prioritize clear communication
2. **Consistency** - Use design system components consistently
3. **White Space** - Don't be afraid of empty space
4. **Hierarchy** - Use size, weight, and color to create visual hierarchy
5. **Mobile First** - Design for mobile, enhance for desktop
6. **Performance** - Optimize images, minimize animations
7. **Accessibility** - Design for all users, all abilities

---

## Tailwind Configuration

Add this to your `tailwind.config.js`:

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#EFF6FF',
          100: '#DBEAFE',
          200: '#BFDBFE',
          300: '#93C5FD',
          400: '#60A5FA',
          500: '#3B82F6',
          600: '#2563EB',
          700: '#1D4ED8',
          800: '#1E40AF',
          900: '#1E3A8A',
        },
        secondary: {
          50: '#F0FDF4',
          100: '#DCFCE7',
          200: '#BBF7D0',
          300: '#86EFAC',
          400: '#4ADE80',
          500: '#22C55E',
          600: '#16A34A',
          700: '#15803D',
          800: '#166534',
          900: '#14532D',
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

---

**Remember:** This design system is a living document. As the project evolves, update this file to reflect new patterns and decisions.
