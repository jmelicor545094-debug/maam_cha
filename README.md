# Davao Medcare Operations

Laravel application for internal pharmaceutical inventory and distribution workflows. Interface Design and Prototypes content from the capstone document was excluded as requested.

## Implemented modules

- Session login, logout, and admin/staff route access
- Product catalog with SKU, category, brand, price, quantity, and reorder level
- Supplier records
- Category management and archive/restore for products, categories, suppliers, and user accounts
- Stock-in encoding and admin approval, including batch and expiry tracking
- Purchase orders, low-stock reorder planning, and delivery reconciliation
- Stock adjustment requests for spoilage, damage, or loss with admin approval/rejection
- Sales order drafts, confirmation, cancellation, FEFO batch dispensing, 12% VAT, payment ledger, and receipts
- Expired-stock preview and controlled cleanup with inventory movement audit
- Inventory, expiry, sales, supplier, activity, and stock movement reports
- Account profile settings, staff change requests, activity logs, and administrator user/role management

## Local setup

Requirements: PHP 8.2+, Composer, and XAMPP with its MySQL/MariaDB service running.

Create the `davao_medcare` database in phpMyAdmin (or MySQL) before the first migration. The local `.env` is configured for XAMPP's default `root` account with no password; update `DB_USERNAME` and `DB_PASSWORD` if your XAMPP credentials differ.

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000` after starting the local server. The seed data includes a single administrator account:

- Email: `admin@gmail.com`
- Password: `admin123`

Sample products and supplier are demo records.
