# ERP Management System

A modern web-based **Enterprise Resource Planning (ERP)** application designed to help small businesses manage their products, inventory, customers, sales, payments, debts, and business activities from a centralized platform.

The project was developed as part of a summer internship at **Anypli**, using **Laravel** for the backend and **Tailwind CSS** for the frontend.

---

## 📋 Table of Contents

* [About the Project](#-about-the-project)
* [Objectives](#-objectives)
* [Main Features](#-main-features)
* [User Roles](#-user-roles)
* [AI Business Assistant](#-ai-business-assistant)
* [Technology Stack](#-technology-stack)
* [Project Architecture](#-project-architecture)
* [Installation](#-installation)
* [Configuration](#-configuration)
* [Database](#-database)
* [Development Approach](#-development-approach)
* [Testing](#-testing)
* [Future Improvements](#-future-improvements)
* [Project Context](#-project-context)

---

## 🚀 About the Project

This project is a web-based ERP solution intended to centralize and simplify the management of daily business operations.

The application provides a single interface for managing:

* Products
* Categories
* Stock
* Customers
* Sales
* Payments
* Customer debts
* Invoices
* Dashboard statistics
* Business analysis

The application also integrates an **AI Business Assistant** capable of interacting with business data and providing useful information and summaries.

The project follows a modular, feature-by-feature development approach, where each major functionality is developed across the database, backend, frontend, and testing layers.

---

## 🎯 Objectives

The main objectives of the project are to:

* Centralize business data in one application.
* Simplify product and inventory management.
* Manage customers and their purchase history.
* Record and manage sales.
* Track payments and outstanding debts.
* Generate and manage invoices.
* Provide useful business statistics through a dashboard.
* Improve access to business information using an AI assistant.
* Provide a simple and responsive user interface.
* Reduce manual management and improve data organization.

---

## ✨ Main Features

### 🔐 Authentication

The application provides user authentication and protected access to the ERP system.

Users can securely access the application's management features according to their role.

---

### 📦 Product Management

Users can manage the products available in the business.

Main operations include:

* Create products
* View products
* Update products
* Delete products
* Associate products with categories
* Manage product prices
* Track available quantities
* Search products
* Filter products
* Paginate product lists

---

### 🗂️ Category Management

Products can be organized into categories.

Users can:

* Create categories
* Edit categories
* Delete categories
* View categories
* Associate products with categories

---

### 📊 Stock Management

The ERP provides stock management functionality to help businesses monitor product quantities.

Stock information can be used to identify:

* Available products
* Low-stock products
* Stock movements caused by sales
* Products requiring restocking

---

### 👥 Customer Management

The customer module allows the business to maintain customer information.

Users can:

* Add customers
* Edit customers
* Delete customers
* View customer information
* View customer sales
* Track customer debts
* Review payment history

---

### 🛒 Sales Management

The sales module allows users to record and manage business transactions.

A sale can contain:

* Customer information
* Products
* Quantities
* Unit prices
* Total amount
* Payment information
* Sale status
* Sale date

Users can also search, filter and review previous sales.

---

### 💰 Payments & Debts

The application supports payment and debt management.

For partially paid sales, the system can track:

```text
Sale amount:       1,000 TND
Amount paid:         300 TND
Remaining amount:    700 TND
```

The system supports:

* Full payments
* Partial payments
* Remaining balances
* Payment history
* Payment dates
* Payment methods
* Marking debts as paid
* Identifying customers with outstanding debts

This allows the business to keep track of unpaid amounts without relying on manual records.

---

### 🧾 Invoices

The system provides invoice functionality for recorded sales.

Invoices contain relevant information about:

* Customer
* Sale
* Products
* Quantities
* Prices
* Total amount
* Payment information
* Date

---

### 📈 Dashboard & Statistics

The dashboard provides an overview of the business activity.

It can display information such as:

* Total sales
* Sales activity
* Product information
* Stock information
* Customer information
* Outstanding debts
* Business performance indicators

The goal is to provide users with a quick overview of the current state of the business.

---

### 🔎 Search, Filtering & Pagination

Large datasets can be easier to manage using:

* Search
* Filters
* Pagination

These features are available for relevant management modules such as products, customers and sales.

---

### 🤖 AI Business Assistant

The application includes an AI-powered business assistant designed to make business information easier to access.

Instead of manually navigating through different pages, users can ask questions about their business data using natural language.

Example questions:

```text
What are my sales this month?

Which customers have unpaid debts?

Show me the products with low stock.

Who are my customers with overdue payments?

Give me a summary of my sales.

What were my best-selling products?
```

The assistant retrieves relevant business information and uses AI to provide a natural-language response.

The AI assistant is designed to act as a **business information and analysis assistant**, rather than simply being a generic chatbot.

---

## 👤 User Roles

The application is designed around authenticated ERP users.

The main user can access the management features of the system, including:

* Product management
* Category management
* Stock management
* Customer management
* Sales management
* Payment management
* Debt management
* Invoices
* Dashboard
* AI Business Assistant

Additional roles and permissions can be extended in future versions if required.

---

## 🧠 AI Architecture

The AI Business Assistant follows a data-aware workflow:

```text
User Question
      ↓
AI Assistant
      ↓
Understand the request
      ↓
Select the required business operation
      ↓
Retrieve ERP data
      ↓
Process / analyze the data
      ↓
Generate response
      ↓
User
```

This approach allows the assistant to work with actual ERP data instead of generating answers based only on general knowledge.

---

## 🛠️ Technology Stack

### Backend

* **PHP**
* **Laravel**
* **Eloquent ORM**
* **Laravel Controllers**
* **Laravel Routing**
* **Laravel Validation**

### Frontend

* **Blade**
* **Tailwind CSS**
* **HTML**
* **JavaScript**

### Database

The application uses a relational database managed through Laravel migrations and Eloquent models.

### AI

The application integrates an external Large Language Model API for the AI Business Assistant.

---

## 🏗️ Project Architecture

The application follows the Laravel MVC architecture.

```text
ERP Application
│
├── Authentication
│
├── Products
│   ├── Models
│   ├── Controllers
│   ├── Routes
│   └── Views
│
├── Categories
│
├── Stock
│
├── Customers
│
├── Sales
│   ├── Sales
│   ├── Sale Details
│   └── Invoices
│
├── Payments
│
├── Debts
│
├── Dashboard
│
└── AI Business Assistant
    ├── User Question
    ├── AI Processing
    ├── Business Data Tools
    └── AI Response
```

---

## 💻 Installation

### Requirements

Before installing the project, make sure the following are installed:

* PHP 8.x or compatible version
* Composer
* Node.js
* npm
* MySQL or another supported relational database
* Git

---

### 1. Clone the repository

```bash
git clone <repository-url>

cd <project-directory>
```

---

### 2. Install PHP dependencies

```bash
composer install
```

---

### 3. Install frontend dependencies

```bash
npm install
```

---

### 4. Create the environment file

```bash
cp .env.example .env
```

On Windows:

```bash
copy .env.example .env
```

---

### 5. Generate the application key

```bash
php artisan key:generate
```

---

### 6. Configure the database

Open the `.env` file and configure the database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

### 7. Run migrations

```bash
php artisan migrate
```

If the project contains seeders:

```bash
php artisan db:seed
```

Or:

```bash
php artisan migrate --seed
```

---

### 8. Build frontend assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

---

### 9. Start the Laravel server

```bash
php artisan serve
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

---

## ⚙️ Configuration

### AI Assistant

If the AI assistant is enabled, configure the required API credentials in the `.env` file.

Example:

```env
AI_API_KEY=your_api_key
```

The exact environment variable depends on the AI provider and configuration used by the project.

**Do not commit API keys or other sensitive credentials to the repository.**

---

## 🗄️ Database

The ERP database is structured around the main business entities.

The core entities include:

```text
User
 │
 ├── Sales
 │     ├── Sale Details
 │     └── Payments
 │
 ├── Products
 │     └── Category
 │
 └── Customers
       └── Sales
```

The database is managed using Laravel migrations, while relationships between entities are handled using Eloquent ORM.

---

## 🔄 Development Approach

The project was developed **feature-by-feature**.

Instead of separating frontend and backend development completely, each feature was developed vertically:

```text
Database
   ↓
Migration
   ↓
Model
   ↓
Controller
   ↓
Routes
   ↓
Validation
   ↓
Blade / Tailwind UI
   ↓
Testing
   ↓
Feature completed
```

This approach makes it easier to identify problems early and ensures that each completed feature is functional before moving to the next one.

---

## 🧪 Testing

Before considering a feature complete, the following aspects should be tested:

### Functional Testing

* Create records
* Edit records
* Delete records
* View records
* Search records
* Filter records
* Validate forms
* Test relationships

### Business Logic Testing

* Stock updates after sales
* Sale totals
* Payment calculations
* Remaining debts
* Partial payments
* Invoice information

### AI Assistant Testing

Test different types of questions:

```text
Sales questions
Customer questions
Debt questions
Stock questions
Product questions
Business summaries
```

Also test invalid, incomplete or unsupported questions to ensure that the assistant responds safely and clearly.

---

## 🔒 Security Considerations

The application should follow standard Laravel security practices, including:

* Authentication
* Authorization
* Form validation
* CSRF protection
* Password hashing
* Environment variables for secrets
* Protection of API credentials
* Validation of user input

AI-related functionality should also avoid exposing sensitive internal information or credentials.

---

## 🚧 Future Improvements

Possible future improvements include:

* Advanced role and permission management
* More detailed financial reports
* Export to PDF and Excel
* Advanced sales analytics
* Automated stock alerts
* Email notifications
* More AI-powered business recommendations
* AI-generated reports
* Automated business insights
* Predictive sales analysis
* Multi-company support
* Multi-language support
* Advanced audit logs
* Mobile application

---

## 🎓 Project Context

This ERP application was developed as part of a **summer internship at Anypli**.

The project workflow consisted of:

```text
Project Idea
     ↓
Cahier des Charges
     ↓
Requirements Analysis
     ↓
System / Database Design
     ↓
Laravel Development
     ↓
Feature Implementation
     ↓
Testing
     ↓
UI Improvements
     ↓
AI Integration
```

The project documentation covers the context and objectives, study of existing solutions, comparison of existing solutions, functional requirements, user roles, use-case modeling and class modeling.

---

## 👨‍💻 Development

Developed during a summer internship at **Anypli**.

**Project:** ERP Web Application
**Backend:** Laravel / PHP
**Frontend:** Blade / Tailwind CSS
**Database:** Relational database
**AI:** AI-powered Business Assistant

---

## 📄 License

This project was developed as an internship project.

Unless otherwise specified by the project owner, the source code should not be redistributed or used commercially without permission.
