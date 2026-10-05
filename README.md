# Inventory & Purchase Management System

A Laravel web application that manages the full stock cycle of a business: purchase from vendors, goods receipt, stock tracking, delivery challans with approval and dispatch, invoicing, returns, vendor payments and ledgers, with role-based access and reports in one dashboard.

## Screenshots
 

| Dashboard | Purchase Entry |
|---|---|
| ![Dashboard](docs/Dashboard.JPG) | ![Purchase](docs/Purchase.JPG) |

| Delivery Challan | Challan View |
|---|---|
| ![Delivery Challan](docs/Delivery%20Challan.JPG) | ![Challan View](docs/DC%20View.JPG) |

| Ledger Report |
|---|
| ![Ledger Report](docs/Ledger%20Report.JPG) |

## Workflow

```
Vendor ──► Purchase ──► Receive Goods ──► Stock In ──► Inventory
                │                                          │
                └──► Purchase Return (to vendor)           ▼
                                              Delivery Challan ──► Approve ──► Dispatch ──► Invoice (PDF)
                                                       ▲                                        │
                                                       └────────── DC Return (from customer) ◄─┘
```

1. **Purchase:** create a purchase order for a vendor, print it, edit it, or short-close it when the rest of the order will not arrive.
2. **Receive:** record goods received against a purchase.
3. **Stock in / out:** stock-in entries update inventory, and a stock-out view shows what has left.
4. **Delivery challan:** create a challan, approve it, then dispatch it. Print single or bulk challans.
5. **Invoice:** generate an invoice from a dispatch and download it as PDF.
6. **Returns:** handle customer returns against a delivery challan and returns to vendors against a purchase.
7. **Payments & ledgers:** record vendor payments and view vendor and customer ledgers, statements and aging.

## Implemented features

**Authentication & access**
- Login, registration, logout, password reset by email, email verification and password confirmation
- Profile management (edit, update password, delete account)
- Role management with permissions, and employee management
- Customer activate / deactivate toggle

**Master data**
- Full CRUD for categories, products, customers, vendors and employees
- Soft delete and restore for products and vendors

**Purchase management**
- Purchase create / edit / view / delete, print and multi-purchase print
- Goods receipt (receive) against a purchase
- Short-close for partially fulfilled purchases
- Purchase return to vendor

**Inventory**
- Stock-in entry management
- Stock-out view
- Stock ledger report

**Delivery & dispatch**
- Delivery challan CRUD with approval step before dispatch
- Dispatch page with dispatch records, view and print
- Single and bulk challan printing
- Trash, restore and permanent delete for challans
- Delivery challan returns (DC return)

**Invoicing**
- Create an invoice from a dispatch, view it and download it as PDF

**Vendor & customer accounts**
- Vendor ledger, vendor payments, vendor statement and vendor aging report
- Customer ledger

**Reports (with export)**
- Stock, product, vendor, customer, ledger, delivery challan and DC return reports

**Mobile attendance API**
- REST endpoints for login, check-in, check-out and today's attendance, so field employees can mark attendance from a mobile app

**Dashboard**
- Summary view after login

## Modules

| Module | What it covers |
|---|---|
| **Access control** | Login, registration, password reset, email verification, roles, permissions, employees |
| **Master data** | Categories, products, vendors, customers (with soft delete and restore) |
| **Purchase** | Purchase orders, receive, short-close, print, return to vendor |
| **Inventory** | Stock in, stock out, stock ledger |
| **Delivery** | Challans with approval, dispatch, bulk print, trash / restore, returns |
| **Invoice** | Invoice from dispatch, PDF download |
| **Accounts** | Vendor ledger, payments, statement, aging, customer ledger |
| **Reports** | Stock, product, vendor, customer, ledger, DC, DC return, all exportable | 

## Technical highlights

- **Modular routing:** one route file per domain (`auth`, `master`, `purchase`, `inventory`, `delivery`, `reports`, `system`) instead of one large `web.php`
- **Resource controllers:** RESTful CRUD for master data and documents, plus custom actions (approve, dispatch, receive, short-close, restore)
- **Role-based access:** roles and permissions decide which modules and actions each employee can use
- **Soft deletes:** products, vendors and delivery challans can be trashed and restored, so history is not lost
- **Document printing:** print views for purchases, delivery challans and dispatches, and PDF generation for invoices
- **Report exports:** export endpoints on stock, product, vendor, customer, ledger and DC reports
- **Auth flows:** email-based password reset and email verification 
- **MVC structure:** controllers for requests, Eloquent models for data, Blade views with AdminLTE

## Tech stack

| Layer | Technology |
|---|---|
| Backend | Laravel, PHP |
| Database | MySQL |
| Frontend | Blade, AdminLTE, Bootstrap / Tailwind CSS, JavaScript |
| Build tool | Vite |

## Database design

![ER Diagram](docs/er-diagram.png)

## Getting started

(keep your existing Getting started section)

## Project structure

(keep your existing Project structure section)

## Roadmap

- [ ] Database transactions around purchase, receive, dispatch and return saves
- [ ] Stock movement history per product
- [ ] Low-stock alerts
- [ ] Automated tests with GitHub Actions

## Author

**Mansi Nikam**, Laravel Developer
[GitHub](https://github.com/manasi2810) · [LinkedIn](https://linkedin.com/in/mansi-nikam-25b833259)
