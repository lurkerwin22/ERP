# Anypli ERP

A full-featured, web-based ERP (Enterprise Resource Planning) application built with **Laravel** and **Tailwind CSS**, developed as part of a summer internship project at Anypli. The project covers the full lifecycle from *cahier des charges* (functional requirements, use cases, class diagrams) through to a working, professionalized ERP system.

## About the Project

This ERP was chosen and defined independently as part of an internship at Anypli, then designed and developed from the ground up:

**Idea → Cahier des charges → Laravel ERP → Core modules → AI integration → Testing/debugging → Professionalization & missing features → Final project**

Development follows a feature-by-feature, vertical-slice approach for each module:

```
Database → Model → Controller → Routes → Validation → Blade/Tailwind UI → Test
```

## Tech Stack

- **Backend:** Laravel 13
- **Frontend:** Blade templates + Tailwind CSS v4, plain JavaScript (no Alpine.js)
- **AI:** Custom AI chatbot module with a rule-based/regex intent router, falling back to an LLM for complex queries

## Features / Modules

### Core ERP
- **Authentication & Users** — login system, Super Admin vs. employee/user roles, permissions
- **Products & Categories** — full CRUD, category relationships, purchase price, selling price, tax (17%)
- **Stock** — inventory tracking, quantities linked to products and sales
- **Customers** — customer management linked to sales
- **Sales** — sale creation/management, sale details, filtering & search, payments/debts tracking
- **Quotes (Devis)** — quote creation and management
- **Suppliers** — supplier management linked to purchases
- **Purchases** — purchase management
- **Invoices** — invoice/sale-detail generation
- **Dashboard** — statistics and reporting around sales, stock, etc.

### AI Chatbot
- ERP-specific assistant for sales, inventory, debts, and business advice
- Rule-based/regex intent router to handle simple queries deterministically, without always calling an LLM
- Persistent, history-aware conversations
- Internal reasoning/"thinking process" hidden from end users

### Localization
- Ongoing frontend-only translation of the UI into French, using proper ERP/business terminology (e.g. *Chiffre d'affaires*, *Devis*, *Créances*, *Fournisseur*)

## Project Status

The core ERP functionality is complete. The project is currently in its **correction & professionalization phase**, focused on:

1. **Forms & UX** — clear input borders, proper labels, frontend validation, disabling submit buttons after first click, consistent professional styling
2. **Authentication** — improved login page, forgot password, professional background/design
3. **User management** — clarifying Super Admin vs. employee roles and permissions
4. **Profile/settings** — name, email, password, and photo management
5. **AI chatbot improvements** — hiding internal reasoning, chat history, better ERP-specific commands
6. **Products** — pricing consistency (purchase price, selling price, tax), better validation and category handling
7. **Sales & invoices** — correct filtering/search, accurate tax/payment calculations, professional invoice presentation
8. **General UI consistency** — unified buttons, inputs, labels, tables, modals, alerts, validation messages, navigation, empty states, and loading/disabled states across the whole app

## Development Principles

- **Frontend-only scope** for localization and most UI work — no new routes, migrations, or controllers unless explicitly required
- **Shared-component-first fixes** — changes cascade from shared Blade components (buttons, labels, panels, layout) outward to avoid redundant page-by-page work
- **Targeted, surgical changes** over full rewrites — clean, minimal UI, no overdesigning
- **No new dependencies where avoidable** — Alpine.js was dropped in favor of plain JavaScript (`classList.toggle`) for interactivity like the mobile sidebar toggle

## Getting Started

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy and configure environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Build frontend assets
npm run build

# Serve the application
php artisan serve
```

## Roadmap

- Complete French translation of remaining modules: quotes (create/show), stock, debts, users, profile, vendor pagination views
- Continued UI/UX refinements across all modules
- Further AI chatbot enhancements
