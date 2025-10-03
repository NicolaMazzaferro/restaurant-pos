# 🍕 Restaurant POS - MVP (Laravel 12)

## 📌 Overview
This is the **MVP of a restaurant/pizzeria management system**, built with **Laravel 12** in an **API-first** approach.  
The initial release focuses exclusively on the **cash desk module**, enabling product management, order creation, and receipt generation.  
The project is designed to be **simple for now**, but **ready for future expansions** such as table orders, delivery management, and customer apps.

---

## 🎯 MVP Features
- **Products Management** (CRUD)
- **Orders Management** with multiple products
- **Automatic Total Calculation**
- **Receipts**
  - Courtesy receipt generation (PDF)
  - Placeholder for fiscal receipt integration via external Print Agent (C#)
- **Users with Roles**: `admin`, `cashier`

---

## 🏗️ Architecture
- **Framework**: Laravel 12
- **Database**: MySQL (via Eloquent ORM + Query Builder)
- **Pattern**: Service + Repository
- **API**: JSON-only (no Blade views)

---

## 🚀 Future Extensions
- Table orders (waiter app)
- Customer orders (delivery app)
- Online payments and delivery tracking
- Realtime notifications (Laravel Echo / Websockets)
- Fiscal receipt integration (Hydra SF20) via C# Print Agent
- Advanced reports & analytics