# 📦 Inventory & Purchase Management ERP System

A production-style **ERP (Enterprise Resource Planning)** application built with **Laravel** that streamlines inventory, purchasing, stock management, delivery, dispatch, invoicing, and reporting through a centralized dashboard.

The system is designed to automate business workflows, maintain inventory accuracy, and provide secure role-based access for different users within an organization.

---

# 🚀 Project Overview

This project helps businesses efficiently manage their complete inventory lifecycle, from purchasing products to delivering them to customers.

It follows a modular Laravel architecture with separate business modules, service classes, queue jobs, events, and role-based authentication to keep the application scalable and maintainable.

---

# ✨ Key Highlights

- Modular Laravel Architecture
- Role-Based Access Control (RBAC)
- Employee, Vendor & Customer Management
- Purchase Management
- Inventory & Stock Tracking
- Delivery Challan Management
- Dispatch Management
- Invoice Generation
- Packing Slip Generation
- Queue-Based Background Processing
- Event-Driven Architecture
- Activity Logs
- Report Generation
- Secure Authentication
- Scalable Service Layer Design

---

# 🏢 Business Workflow

```
Vendor
   │
   ▼
Purchase Entry
   │
   ▼
Stock In
   │
   ▼
Inventory Updated
   │
   ▼
Customer Order
   │
   ▼
Delivery Challan
   │
   ▼
Dispatch
   │
   ▼
Invoice Generation
   │
   ▼
Packing Slip Generation
   │
   ▼
Email Notification
```

---

# 📚 Modules

## 👨‍💼 Master Management

- Employee Management
- Role & Permission Management
- Category Management
- Product Management
- Vendor Management
- Customer Management

---

## 📦 Purchase Module

- Purchase Entry
- Purchase Tracking
- Vendor Purchases
- Purchase History
- Purchase Reports

---

## 🏬 Inventory Module

- Stock In Management
- Product Stock Monitoring
- Inventory Tracking
- Stock Movement History
- Inventory Reports

---

## 🚚 Delivery Module

- Delivery Challan Management
- Product Dispatch
- Delivery Records
- Product Return Handling
- Dispatch History

---

## 📄 Invoice Module

- Automatic Invoice Generation
- Invoice PDF Generation
- Packing Slip Generation
- Invoice History

---

## 📊 Reports Module

- Purchase Reports
- Inventory Reports
- Delivery Reports
- Customer Reports
- System Reports

---

## ⚙️ System Module

- Dashboard Analytics
- User Authentication
- Activity Logs
- System Settings
- Profile Management

---

# ⚡ Business Features

- Real-time Stock Validation
- Automatic Inventory Update
- Stock Ledger Management
- Customer Ledger Management
- Partial Dispatch Support
- Multi-item Delivery Challans
- Invoice Automation
- Packing Slip Generation
- Queue-Based Background Jobs
- Secure Role-Based Authorization
- Business Workflow Automation
- Report Generation

---

# 🏗️ Architecture

The project follows Laravel's MVC architecture while separating business logic into service classes.

```
Controller
      │
      ▼
Validation
      │
      ▼
Service Layer
      │
      ▼
Database Transaction
      │
      ▼
Events
      │
      ▼
Listeners
      │
      ▼
Queue Jobs
      │
      ▼
PDF Generation
      │
      ▼
Email Notification
```

---

# 🛠️ Tech Stack

## Backend

- Laravel 12
- PHP 8.x

## Frontend

- Blade
- Bootstrap
- Tailwind CSS
- JavaScript

## Database

- MySQL

## Authentication

- Laravel Authentication
- Role-Based Authorization

## Packages

- Spatie Laravel Permission
- Laravel Queue
- Laravel Events & Listeners
- DomPDF

---

# 📂 Project Structure

```
app/
│
├── Events/
├── Jobs/
├── Listeners/
├── Models/
├── Services/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
│
routes/
│
├── auth.php
├── master.php
├── purchase.php
├── inventory.php
├── delivery.php
├── reports.php
└── system.php

resources/
│
├── views/
├── js/
└── css/

database/
│
├── migrations/
├── seeders/
└── factories/
```

---

# 🔐 Authentication & Security

- Secure Login System
- Session Management
- Password Hashing
- Role-Based Access Control
- Route Protection
- Middleware Authorization
- CSRF Protection
- Input Validation

---

# 📊 Database Features

- Relational Database Design
- Foreign Key Constraints
- Eloquent Relationships
- Soft Deletes
- Database Transactions
- Data Integrity
- Optimized Queries

---

# ⚙️ Laravel Features Used

- MVC Architecture
- Blade Templating
- Eloquent ORM
- Resource Controllers
- Route Groups
- Middleware
- Service Layer
- Events & Listeners
- Queue Jobs
- Database Transactions
- Authentication
- Authorization
- Validation
- Pagination
- File Storage
- PDF Generation

---

# 📈 Reports Available

- Purchase Report
- Inventory Report
- Delivery Report
- Customer Report
- Vendor Report
- Product Stock Report
- Dispatch Report

--- 

# 🚀 Installation

## Clone Repository

```bash
git clone https://github.com/manasi2810/inventory-management-system.git
```

---

## Move to Project

```bash
cd inventory-management-system
```

---

## Install Dependencies

```bash
composer install
```

---

## Copy Environment File

```bash
cp .env.example .env
```

---

## Generate Application Key

```bash
php artisan key:generate
```

---

## Configure Database

Update your `.env` file.

```env
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

---

## Run Migrations

```bash
php artisan migrate --seed
```

---

## Storage Link

```bash
php artisan storage:link
```

---

## Start Development Server

```bash
php artisan serve
```

---

# 🎯 Learning Outcomes

This project helped strengthen my understanding of:

- Laravel Framework
- PHP Development
- MVC Architecture
- Service Layer Design
- Modular Application Development
- Database Design
- Authentication & Authorization
- Inventory Management Logic
- Business Workflow Automation
- Queue Processing
- Event-Driven Architecture
- CRUD Operations
- RESTful Routing
- Clean Code Practices
- System Design

---

# 💡 Challenges Solved

- Designed a scalable modular architecture.
- Implemented inventory tracking with stock validation.
- Built complete purchase-to-delivery business workflow.
- Automated invoice and packing slip generation.
- Integrated queue jobs for background processing.
- Applied role-based authorization across modules.
- Organized routes into multiple modules for maintainability.
- Improved application scalability using service classes.

---

# 🚀 Future Enhancements

- Barcode Scanner Integration
- Multi-Warehouse Support
- GST & Tax Reports
- REST API
- Email Notifications
- SMS Notifications
- Export Reports to Excel/PDF
- Dashboard Charts & Analytics

---

# 👩‍💻 Author

**Mansi Nikam**

🔗 GitHub  
https://github.com/manasi2810

🔗 LinkedIn  
https://linkedin.com/in/mansi-nikam-25b833259

---

# ⭐ Support

If you found this project helpful, consider giving it a **⭐ Star** on GitHub.
