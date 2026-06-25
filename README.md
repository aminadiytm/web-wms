# ✨ Features

## Dashboard

- Dashboard Summary
- Inventory Summary
- Transaction Summary

---

## Master Data

- Category Management
- Product Management
- Warehouse Management
- Location Management
- Routing Approval

---

## Transactions

### Inbound

- Create Inbound
- Edit Inbound
- Receive Item
- Approval Process

### Outbound

- Create Outbound
- Edit Outbound
- Shipment Confirmation

---

## Inventory

- Stock Inventory
- Available Stock Monitoring

---

## Security

### Role Permission

- Dynamic Permission Matrix
- View/Create/Edit/Delete Permission
- Multiple Roles
- Dynamic Sidebar Menu

### Admin Users

- User CRUD
- Multiple Role Assignment
- Password Reset
- Dynamic Permission Rendering

---

# 🔐 Role Based Access Control (RBAC)

This application uses:

- Spatie Laravel Permission
- Custom Dynamic Permission Middleware
- Dynamic Sidebar Menu
- Permission Matrix

Permission flow:

```
User
   │
   ▼
Role
   │
   ▼
Permissions
   │
   ▼
Menu
   │
   ▼
Middleware
   │
   ▼
Authorized Route
```

---

# 👥 Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| ADMIN | admin@example.com | password |
| STAFF INBOUND | staff.inbound@example.com | password |
| STAFF OUTBOUND | staff.outbound@example.com | password |

---

# Permission Matrix

## 👑 ADMIN

```
Dashboard
✔ View

Master Data
✔ View
✔ Create
✔ Edit
✔ Delete

Transactions
✔ Full Access

Inventory
✔ Full Access

Security
✔ Full Access
```

---

## 📥 STAFF INBOUND

```
Dashboard
✔ View

Category
✔ View

Product
✔ View

Stock Inventory
✔ View

Inbound
✔ View
✔ Create
✔ Edit
✔ Delete

Outbound
✔ View
```

---

## 📤 STAFF OUTBOUND

```
Dashboard
✔ View

Category
✔ View

Product
✔ View

Stock Inventory
✔ View

Inbound
✔ View

Outbound
✔ View
✔ Create
✔ Edit
✔ Delete
```

---

# 🛠 Tech Stack

## Backend

- Laravel 10
- PHP 8.2
- MySQL

## Frontend

- Soft UI Dashboard Laravel
- Bootstrap 5
- jQuery
- DataTables
- Select2
- SweetAlert2
- Font Awesome

## Authorization

- Spatie Laravel Permission
- Custom Dynamic Permission Middleware

---

# 🧩 Architecture

```
app
│
├── Http
│   ├── Controllers
│   │
│   ├── Middleware
│   │      CheckMenuPermission.php
│   │
│   └── Helpers
│          MenuPermissionHelper.php
│
├── Models
│      User.php
│      MenuList.php
│
├── Master Data
│
├── Transaction
│
├── Inventory
│
└── Security
```

---

# ⭐ Key Features

## Dynamic Menu

Sidebar menu is generated dynamically from database configuration.

```
menu_lists
        │
        ▼
Role Permission
        │
        ▼
Sidebar
```

---

## Dynamic Permission

Permission is generated automatically using menu code.

Example

```
MNU101_view
MNU101_create
MNU101_edit
MNU101_delete
```

No hardcoded permission.

---

## Route Authorization

Every route is protected using custom middleware.

```
Route

↓

Route Name

↓

Menu Route

↓

Menu Code

↓

Permission

↓

User Authorization
```

---

## UI Authorization

Buttons such as

- Create
- Edit
- Delete

are rendered dynamically based on user permission.

Instead of showing **403 Forbidden**, unavailable actions are automatically hidden from the interface.

---

# 🎨 User Interface

This project uses **Soft UI Dashboard Laravel** by **Creative Tim** as the frontend template.

The template has been customized and integrated with the application's business logic.

UI Template:

https://www.creative-tim.com/product/soft-ui-dashboard-laravel

---

# 👨‍💻 My Contributions

This project demonstrates my implementation of:

✅ Warehouse Management System Architecture

✅ Dynamic Role-Based Access Control (RBAC)

✅ Dynamic Sidebar Menu

✅ Dynamic Permission Matrix

✅ Custom Authorization Middleware

✅ User Management

✅ Inventory Module

✅ Warehouse Transactions

✅ CRUD Architecture

✅ DataTables Server-side Integration

✅ Responsive Admin Interface

---

# 🚀 Installation

Clone repository

```bash
git clone https://github.com/YOUR_USERNAME/laravel-wms-demo.git
```

Enter project

```bash
cd laravel-wms-demo
```

Install dependencies

```bash
composer install
```

Copy environment

```bash
cp .env.example .env
```

Generate key

```bash
php artisan key:generate
```

Configure database inside

```
.env
```

Run migration

```bash
php artisan migrate:fresh --seed
```

Run application

```bash
php artisan serve
```

Open

```
http://127.0.0.1:8000
```

---

# 📌 Notes

This repository is intended for educational and portfolio purposes.

Sensitive information such as API credentials, production environment configuration, and proprietary business data has been removed or replaced with demo data.

---
