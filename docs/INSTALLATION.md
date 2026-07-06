# LandReg Pro - Installation Guide

## Requirements

- **XAMPP** (Apache + MySQL + PHP 8.0+)
- **Composer** (optional, for PDF, Excel, QR code libraries)
- Web browser (Chrome, Firefox, Edge)

## Step 1: Install XAMPP

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start **Apache** and **MySQL** from the XAMPP Control Panel

## Step 2: Deploy Project

1. Copy the `land_regis` folder to:
   ```
   C:\xampp\htdocs\land_regis
   ```
2. Ensure the folder path matches `BASE_URL` in `config/config.php` (default: `/land_regis`)

## Step 3: Install PHP Dependencies (Recommended)

Open terminal in the project folder:

```bash
cd C:\xampp\htdocs\land_regis
composer install
```

This installs:
- **dompdf/dompdf** – PDF certificate and report generation
- **phpoffice/phpspreadsheet** – Excel export
- **endroid/qr-code** – QR code generation

> Without Composer, the system falls back to print-friendly HTML and CSV export, and uses an online QR API.

## Step 4: Database Setup

### Option A: Web Installer (Recommended)

1. Open: `http://localhost/land_regis/install.php`
2. Enter database credentials (default: host `localhost`, user `root`, no password)
3. Set admin email and password
4. Click **Install Now**
5. **Delete `install.php`** after successful installation

### Option B: Manual Import

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Import `database/landreg_pro.sql`
3. Update `config/database.php` with your credentials
4. Default admin: `admin@landreg.com` / run install or reset password via PHP:
   ```php
   echo password_hash('admin123', PASSWORD_DEFAULT);
   ```

## Step 5: File Permissions

Ensure these directories are writable by Apache:

```
assets/uploads/photos/
assets/uploads/logos/
assets/uploads/qr_codes/
```

## Step 6: Access the System

| URL | Purpose |
|-----|---------|
| `http://localhost/land_regis/` | Login page |
| `http://localhost/land_regis/register.php` | User registration |
| `http://localhost/land_regis/verify.php` | Public certificate verification |
| `http://localhost/land_regis/admin/dashboard.php` | Admin dashboard |

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@landreg.com | admin123 (if using installer/SQL default) |

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Database connection error | Check MySQL is running; verify `config/database.php` |
| 404 Not Found | Confirm project is in `htdocs/land_regis` and Apache is running |
| Upload fails | Check folder permissions on `assets/uploads/` |
| PDF not downloading | Run `composer install` |
| QR codes not generating | Run `composer install` or ensure server has internet access |

## Security Checklist (Production)

- [ ] Delete `install.php`
- [ ] Change default admin password
- [ ] Use strong MySQL password
- [ ] Enable HTTPS
- [ ] Restrict `config/` and `database/` from web access
