# ShortletNG - Project Documentation

Welcome to the **ShortletNG** project documentation! This folder contains all the essential documentation for building a modern shortlet booking platform for the Nigerian market.

---

## Project Overview

**ShortletNG** is an MVP shortlet booking platform similar to Booking.com and Hotels.ng, built with:
- **Backend**: Laravel 11.x
- **Frontend**: Vue.js 3 with Inertia.js
- **Admin Panel**: FilamentPHP 3.x
- **Styling**: Tailwind CSS
- **Payment**: Paystack & Flutterwave (manager pattern)
- **Target Market**: Nigeria

This is a **single-host platform** where you manage all properties, and guests can browse and book seamlessly.

---

## Documentation Index

### 1. [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md)
**What it contains:**
- Project description and vision
- Complete tech stack
- MVP feature list
- Target market and constraints
- Success metrics

**When to read:** Start here to understand the big picture and project scope.

---

### 2. [FEATURE_ROADMAP.md](FEATURE_ROADMAP.md) ⭐ MOST IMPORTANT
**What it contains:**
- Complete feature breakdown by phases
- Detailed task lists with checkboxes
- Progress tracking system
- Git commit recommendations for each feature
- Notes section for logging decisions

**When to use:**
- **Daily** - Track your progress as you build
- Before starting any feature - Check the roadmap first
- After completing tasks - Mark checkboxes as complete
- Update progress percentages regularly

**This is your primary working document!**

---

### 3. [CODING_STANDARDS.md](CODING_STANDARDS.md)
**What it contains:**
- Laravel coding conventions
- Vue.js style guide and component structure
- Inertia.js best practices
- Tailwind CSS organization
- Naming conventions for everything
- Code quality guidelines
- Testing standards

**When to read:**
- Before writing any code
- During code reviews
- When in doubt about naming or structure

---

### 4. [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md)
**What it contains:**
- Complete color palette (primary, secondary, semantic)
- Typography specifications (fonts, sizes, weights)
- Spacing scale
- Component patterns (buttons, cards, forms, badges)
- Responsive breakpoints
- Shadow and border radius values
- Tailwind configuration

**When to use:**
- When building any UI component
- Before choosing colors or spacing
- To ensure design consistency
- Reference for Tailwind classes

---

### 5. [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)
**What it contains:**
- Complete database schema with all tables
- Entity relationship diagrams
- Field definitions and constraints
- Indexes and foreign keys
- Sample queries
- Migration order
- Seed data requirements

**When to use:**
- Before creating migrations
- When defining model relationships
- When writing complex queries
- To understand data structure

---

### 6. [GIT_WORKFLOW.md](GIT_WORKFLOW.md)
**What it contains:**
- Branching strategy (feature, bugfix, hotfix)
- Commit message conventions
- Pull request guidelines
- Git commands cheat sheet
- Common scenarios and solutions

**When to use:**
- Before creating a branch
- Before committing code
- Before creating pull requests
- When collaborating with others

---

### 7. [SETUP_GUIDE.md](SETUP_GUIDE.md)
**What it contains:**
- Prerequisites (PHP, Composer, Node.js, MySQL, etc.)
- Step-by-step setup instructions
- Environment configuration
- External service setup (Google OAuth, Paystack, Flutterwave)
- Common issues and troubleshooting
- Useful commands

**When to use:**
- First time setting up the project
- When onboarding new developers
- When encountering setup issues

---

### 8. [TESTING_STRATEGY.md](TESTING_STRATEGY.md)
**What it contains:**
- Testing philosophy for MVP
- Types of tests (unit, feature, integration)
- Critical test cases for each feature
- Testing best practices
- Manual testing checklist
- Coverage goals

**When to use:**
- When writing tests for features
- Before releasing features
- To understand what to test and what to skip

---

## Quick Start Guide

### For First-Time Setup:

1. **Read** [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) - Understand the project
2. **Follow** [SETUP_GUIDE.md](SETUP_GUIDE.md) - Set up your development environment
3. **Review** [FEATURE_ROADMAP.md](FEATURE_ROADMAP.md) - See what needs to be built
4. **Study** [CODING_STANDARDS.md](CODING_STANDARDS.md) and [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) - Learn the conventions
5. **Start building!**

### Daily Workflow:

1. **Check** [FEATURE_ROADMAP.md](FEATURE_ROADMAP.md) - Pick next task
2. **Review** [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - If working with database
3. **Reference** [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) - When building UI
4. **Follow** [CODING_STANDARDS.md](CODING_STANDARDS.md) - While writing code
5. **Commit** using [GIT_WORKFLOW.md](GIT_WORKFLOW.md) - When done
6. **Update** [FEATURE_ROADMAP.md](FEATURE_ROADMAP.md) - Mark tasks complete

---

## Project Phases

The project is divided into phases (see FEATURE_ROADMAP.md for details):

- **Phase 0**: Project Setup & Foundation
- **Phase 1**: Database Schema & Models
- **Phase 2**: Authentication System
- **Phase 3**: Admin Panel (FilamentPHP)
- **Phase 4**: Property Discovery (Frontend)
- **Phase 5**: Booking System
- **Phase 6**: Payment Integration
- **Phase 7**: Reviews & Ratings
- **Phase 8**: Email Notifications
- **Phase 9**: Polish & Testing
- **Phase 10**: Deployment

---

## Key Features

### Guest Features
✅ Google OAuth & email/password authentication
✅ Property search with filters (location, dates, price, amenities)
✅ Property detail pages with image galleries
✅ Booking system with availability checking
✅ Secure payment processing (Paystack/Flutterwave)
✅ Booking history and management
✅ Reviews and ratings after checkout

### Admin Features (FilamentPHP)
✅ Property management (CRUD, images, amenities)
✅ Booking management and tracking
✅ User management
✅ Review moderation
✅ Analytics dashboard

---

## Technology Stack

### Backend
- Laravel 11.x
- MySQL 8.0+
- FilamentPHP 3.x
- Laravel Sanctum (auth)
- Laravel Socialite (OAuth)

### Frontend
- Vue.js 3 (Composition API)
- Inertia.js
- Tailwind CSS
- Vite

### Integrations
- Paystack (primary payment)
- Flutterwave (secondary payment)
- Google OAuth
- Email notifications

---

## Important Notes

### For Agentic Coding:

When using an AI agent to help build this project:

1. **Always reference the roadmap** - Use FEATURE_ROADMAP.md to track progress
2. **Follow the standards** - Ensure code adheres to CODING_STANDARDS.md
3. **Use the design system** - Match DESIGN_SYSTEM.md specifications exactly
4. **Commit frequently** - Follow GIT_WORKFLOW.md conventions
5. **Update tracking** - Mark completed tasks in FEATURE_ROADMAP.md
6. **Ask questions** - If something in the docs is unclear, ask before proceeding

### For MVP Development:

- ⚡ **Speed over perfection** - Ship fast, iterate based on feedback
- 🎯 **Focus on core features** - Don't add features not in the roadmap
- 🧪 **Test critical paths** - Follow TESTING_STRATEGY.md priorities
- 📝 **Document decisions** - Log important decisions in FEATURE_ROADMAP.md notes section
- 🚀 **Deploy early** - Get to production as soon as core features work

---

## File Structure

```
shortlet-booking-docs/
├── README.md                    # This file - Start here
├── PROJECT_OVERVIEW.md          # Project scope and tech stack
├── FEATURE_ROADMAP.md          # Task tracking (update daily!)
├── CODING_STANDARDS.md         # Code conventions and style guide
├── DESIGN_SYSTEM.md            # UI/UX specifications
├── DATABASE_SCHEMA.md          # Database structure
├── GIT_WORKFLOW.md             # Git and commit conventions
├── SETUP_GUIDE.md              # Development environment setup
└── TESTING_STRATEGY.md         # Testing approach and guidelines
```

---

## Development Workflow

### 1. Start New Feature

```bash
# Pull latest code
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/property-search

# Check roadmap for tasks
# Open FEATURE_ROADMAP.md and mark task as in_progress
```

### 2. Build Feature

```
# Reference DATABASE_SCHEMA.md if working with database
# Reference DESIGN_SYSTEM.md if building UI
# Follow CODING_STANDARDS.md while coding
# Add tests per TESTING_STRATEGY.md
```

### 3. Commit Changes

```bash
# Stage changes
git add .

# Commit using convention from GIT_WORKFLOW.md
git commit -m "feat: Add property search filtering by location"

# Push to remote
git push origin feature/property-search
```

### 4. Complete Feature

```
# Create pull request (see GIT_WORKFLOW.md)
# Update FEATURE_ROADMAP.md - Mark tasks as completed
# Merge to develop
# Delete feature branch
```

---

## Common Questions

### Q: Where do I start?
**A:** Read PROJECT_OVERVIEW.md, then follow SETUP_GUIDE.md to set up your environment.

### Q: How do I track my progress?
**A:** Use FEATURE_ROADMAP.md - check off tasks as you complete them.

### Q: What colors should I use?
**A:** Check DESIGN_SYSTEM.md for the complete color palette.

### Q: How should I name my variables/functions?
**A:** See CODING_STANDARDS.md for naming conventions.

### Q: How do I structure the database?
**A:** Everything is defined in DATABASE_SCHEMA.md.

### Q: What should I test?
**A:** See TESTING_STRATEGY.md for MVP testing priorities.

### Q: How do I write commit messages?
**A:** Follow the format in GIT_WORKFLOW.md.

---

## Getting Help

### Documentation Issues
If you find errors or unclear sections in this documentation:
1. Document the issue
2. Update the relevant file
3. Add a note in FEATURE_ROADMAP.md

### Technical Issues
- Laravel: https://laravel.com/docs
- Vue.js: https://vuejs.org/guide/
- Inertia.js: https://inertiajs.com/
- FilamentPHP: https://filamentphp.com/docs
- Tailwind CSS: https://tailwindcss.com/docs

---

## Version History

- **v1.0** (2025-11-23): Initial documentation created
  - All core documentation files
  - Complete feature roadmap
  - Database schema defined
  - Design system specified

---

## Next Steps

1. ✅ **You are here** - Reading the documentation
2. ⬜ Follow SETUP_GUIDE.md to set up your environment
3. ⬜ Review FEATURE_ROADMAP.md and start with Phase 0
4. ⬜ Build features following the roadmap
5. ⬜ Keep FEATURE_ROADMAP.md updated as you progress
6. ⬜ Ship the MVP!

---

## Contributing

When working on this project:

1. **Read the docs first** - Don't guess, check the documentation
2. **Follow the standards** - Consistency is key
3. **Update the roadmap** - Keep FEATURE_ROADMAP.md current
4. **Write meaningful commits** - Follow GIT_WORKFLOW.md
5. **Test your code** - Use TESTING_STRATEGY.md as a guide
6. **Document decisions** - Add notes to FEATURE_ROADMAP.md

---

**Let's build something amazing!** 🚀

For questions or clarification, refer to the specific documentation file or ask your team/AI agent.

---

**Documentation last updated:** 2025-11-23
**Project status:** Documentation complete, ready for development
