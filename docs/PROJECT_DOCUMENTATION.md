# LandReg Pro – Project Documentation

## 1. Introduction

**LandReg Pro** is a web-based Land Registration and Property Record Management System designed for government land authorities. It digitizes land registration, property ownership records, transfer processing, certificate generation, verification, and reporting.

| Attribute | Detail |
|-----------|--------|
| **Project Title** | LandReg Pro |
| **Type** | Government Land & Property Record Management |
| **Version** | 1.0 |
| **Technology Stack** | PHP 8, MySQL, HTML5, CSS3, JavaScript, Bootstrap 5 |

## 2. Objectives

- Digitize land and property registration workflows
- Maintain accurate ownership records with audit trails
- Automate ownership certificate generation with QR verification
- Enable secure ownership transfer requests and admin approval
- Provide bilingual (English/Somali) interface with dark mode
- Generate exportable reports in PDF and Excel formats

## 3. System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Presentation Layer                    │
│  Bootstrap 5 UI │ Dark Mode │ i18n (EN/SO) │ Responsive │
├─────────────────────────────────────────────────────────┤
│                    Application Layer                     │
│  PHP 8 Modules │ Session Auth │ RBAC │ CSRF │ Validation │
├─────────────────────────────────────────────────────────┤
│                      Data Layer                          │
│              MySQL (PDO) │ File Uploads                  │
└─────────────────────────────────────────────────────────┘
```

## 4. User Roles

### Administrator
Full access to manage users, owners, lands, properties, approve transfers, view reports, send notifications, and configure system settings.

### Property Owner (User)
Register/login, view own properties and certificates, download certificates, request ownership transfers, verify certificates, receive notifications, update profile.

## 5. Module Overview

| Module | Description | Key Files |
|--------|-------------|-----------|
| Authentication | Register, login, logout, RBAC, CSRF | `index.php`, `register.php`, `includes/auth.php` |
| Admin Dashboard | Statistics and recent activities | `admin/dashboard.php` |
| Owner Management | CRUD for property owners | `admin/owners/` |
| Land Management | CRUD for land parcels | `admin/lands/` |
| Property Management | CRUD + auto certificate | `admin/properties/` |
| Transfer Module | Request, approve, reject | `admin/transfers/`, `user/transfers.php` |
| Certificate Module | Auto-generate, PDF, QR | `admin/certificates/`, `includes/functions.php` |
| Search & Verify | Multi-field search, public verify | `search.php`, `verify.php` |
| Reports | Filtered reports, PDF/Excel | `admin/reports/index.php` |
| Notifications | Admin broadcast, user inbox | `admin/notifications/` |
| Settings | Office info, logo, password | `admin/settings/index.php` |

## 6. Database Design

Database name: **`landreg_pro`**

See [ERD.md](ERD.md) for the full entity relationship diagram.

**Core tables:** `users`, `owners`, `lands`, `properties`, `ownership_transfers`, `certificates`, `notifications`, `settings`, `audit_logs`

## 7. Security Features

- **Password hashing** – bcrypt via `password_hash()` / `password_verify()`
- **Session management** – session regeneration on login
- **Role-based access control** – `requireAdmin()`, `requireUser()`
- **CSRF protection** – token on all POST forms
- **Prepared statements** – PDO parameterized queries
- **Input sanitization** – `htmlspecialchars()` via `e()` helper
- **Activity logging** – all major actions recorded in `audit_logs`

## 8. Brand Colors

| Name | Hex |
|------|-----|
| Primary | `#006D77` |
| Secondary | `#0B2545` |
| Accent Gold | `#D4A017` |
| Success Green | `#2E8B57` |
| Warning Orange | `#F4A261` |
| Danger Red | `#D62828` |
| Background | `#F8FAFC` |
| Cards | `#FFFFFF` |

## 9. Folder Structure

```
land_regis/
├── admin/              # Admin modules
├── user/               # User portal
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── uploads/        # Photos, logos, QR codes
├── config/
│   ├── config.php
│   └── database.php
├── database/
│   └── landreg_pro.sql
├── docs/               # Documentation & diagrams
├── includes/           # Shared PHP components
├── index.php           # Login
├── register.php
├── verify.php          # Public certificate verification
├── search.php
├── install.php         # Setup wizard (delete after use)
└── composer.json
```

## 10. Certificate Workflow

1. Admin creates a property record linking an owner to a land parcel
2. System auto-generates unique certificate number (e.g., `CERT-2026-A1B2C3`)
3. QR code encodes verification URL: `/verify.php?cert=CERT-...`
4. Certificate stored in `certificates` table with QR image path
5. Owner notified and can download PDF certificate
6. On ownership transfer approval, old certificate cancelled, new one issued

## 11. Report Types

| Report | Data Source | Filters |
|--------|-------------|---------|
| Land Report | `lands` | Daily, weekly, monthly, yearly, custom |
| Owner Report | `owners` | Same |
| Property Report | `properties` + joins | Same |
| Transfer Report | `ownership_transfers` | Same |
| Certificate Report | `certificates` | Same |

Export formats: **PDF** (Dompdf), **Excel** (PhpSpreadsheet)

## 12. Internationalization

Language files in `includes/lang.php` support:
- **English (en)** – default
- **Somali (so)**

Toggle via navbar dropdown or `?lang=en` / `?lang=so`

## 13. Future Enhancements

- GIS map integration for land parcels
- SMS/email notification delivery
- Digital signature on certificates
- Multi-office/branch support
- API for third-party integrations

## 14. References

- [Installation Guide](INSTALLATION.md)
- [ERD Diagram](ERD.md)
- [Use Case Diagram](USE_CASE.md)
- [Activity Diagrams](ACTIVITY.md)
- [DFD Level 0 & 1](DFD.md)

---

**LandReg Pro** © 2026 – Ministry of Land Registration
