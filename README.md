# MT Billing
MT Billing is a Laravel-based store billing and operations system. It provides separate administrator and employee workspaces for managing products, inventory, customers, employees, users, orders, invoices, audit history, and invoice email delivery.

## Features

- Separate admin and employee authentication with role-based access control.
- Employee billing desk for creating invoices and decrementing stock atomically.
- Walk-in billing and searchable active-customer selection.
- Employee-first customer ordering based on attended customer history.
- Product tax rates, line-level tax calculations, payment totals, and balance calculation.
- Low-stock alerts for selected products while creating a bill.
- Admin management for users, employees, customers, products, and stock.
- Paginated order lists with search and status filters.
- Paginated mail-log page with recipient, invoice, subject, and status filters.
- PDF invoice generation and download using Dompdf.
- Responsive HTML invoice email with the PDF attached.
- Database-backed mail queue with retries and failed-delivery logging.
- Order audit timeline and invoice email history.
- Live health indicators based on database query latency, queued jobs, failed jobs, inventory, and order activity.
- Full-page employee action loader for billing and navigation actions.

## Technology

- PHP 8.2+
- Laravel 12
- MySQL or SQLite
- Laravel database queue
- Vite
- Tailwind CSS
- Alpine.js
- SweetAlert2
- Dompdf

## Requirements

Install the following before setup:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL 8+ or another Laravel-supported database
- SMTP credentials for real invoice delivery

The project has been developed and tested on Windows with XAMPP PHP.

## Installation

Clone the project and enter its directory:

```bash
git clone <repository-url> mt-billing
cd mt-billing
```

Install PHP and JavaScript dependencies:

```bash
composer install
npm install
```

Create the environment file and application key:

```bash
copy .env.example .env
php artisan key:generate
```

On macOS/Linux, use:

```bash
cp .env.example .env
php artisan key:generate
```

## Environment Configuration

### Database

For MySQL, update `.env` with your database details:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mt_billing
DB_USERNAME=root
DB_PASSWORD=
```

For a local SQLite setup:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

Create the SQLite file if necessary:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

The application uses `Asia/Kolkata` by default. Change it when required:

```env
APP_TIMEZONE=Asia/Kolkata
```

### SMTP

Invoice delivery uses the Laravel `smtp` mailer. Configure real SMTP credentials in `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=billing@example.com
MAIL_FROM_NAME="MT Billing"
```

For Mailtrap testing, use the SMTP credentials shown by your Mailtrap inbox. Do not commit credentials to source control.

After changing `.env`, clear configuration before running a long-lived queue worker:

```bash
php artisan config:clear
```

Queue workers load configuration into memory. Restart them after every mail or queue configuration change.

## Database Setup

Run migrations:

```bash
php artisan migrate
```

Run the seeders:

```bash
php artisan db:seed
```

The seed process includes:

- Existing default/demo records.
- Ten additive sample users, employees, customers, products, stock records, orders, order items, audit logs, and mail logs.

The sample seeder uses deterministic keys and is safe to run repeatedly. Existing records are preserved.

### Demo Accounts

The demo seeder creates these accounts:

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin@mtbilling.test` | `Admin@12345` |
| Employee | `employee@mtbilling.test` | `Employee@12345` |

Sample employees use the password `Employee@12345`.

Change or remove demo credentials before deploying to a shared or production environment.

## Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

Start the Vite development server in a second terminal:

```bash
npm run dev
```

Open the application at [http://localhost:8000](http://localhost:8000).

The root URL redirects to the employee login page.

### Run All Development Services

The Composer `dev` script starts the Laravel server, queue listener, log viewer, and Vite together:

```bash
composer run dev
```

If you prefer separate processes, use:

```bash
php artisan serve
php artisan queue:work --queue=mail --tries=3 --backoff=30
npm run dev
```

The mail worker is required for queued invoice delivery.

## Application Areas

### Employee Workspace

| Area | URL |
| --- | --- |
| Employee login | `/employee/login` |
| Dashboard | `/employee/dashboard` |
| Create bill | `/employee/billing` |
| Orders | `/employee/orders` |
| Order details | `/employee/orders/{order}` |
| Download invoice | `/employee/orders/{order}/download` |
| Print invoice | `/employee/orders/{order}/print` |

Successful employee login opens the create-bill screen by default.

Employees can view and manage only orders belonging to their employee account.

### Admin Workspace

| Area | URL |
| --- | --- |
| Admin login | `/admin/login` |
| Dashboard | `/admin/dashboard` |
| Users | `/admin/users` |
| Employees | `/admin/employees` |
| Customers | `/admin/customers` |
| Products | `/admin/products` |
| Stock | `/admin/stocks` |
| Orders | `/admin/orders` |
| Mail logs | `/admin/mail-logs` |

The audit timeline is available in order details. It is intentionally not shown as a separate sidebar page.

## Billing Flow

1. An employee opens the create-bill page.
2. The customer defaults to Walk-in Customer.
3. Existing customers can be searched by name, phone, or email.
4. The initial customer list prioritizes customers previously attended by the logged-in employee.
5. The employee adds products and quantities.
6. Product price, tax rate, stock, line tax, subtotal, and grand total are calculated in the browser.
7. The server repeats stock and payment validation inside a database transaction.
8. The order, order items, stock decrement, and audit record are committed together.
9. If the customer has an email address, a `MailLog` row is created with `queued` status.
10. A `SendOrderInvoiceEmail` job is placed on the `mail` queue.
11. The queue worker generates the PDF if necessary, sends the SMTP email, and updates the mail log to `sent` or `failed`.

Stock is locked during order creation to prevent concurrent billing from overselling inventory.

## Invoice Documents and Email

Invoices are generated from:

```text
resources/views/pdf/invoice.blade.php
```

The PDF uses a fixed table layout designed for Dompdf. Generated files are stored under:

```text
storage/app/public/invoices/v2/
```

The HTML email template is:

```text
resources/views/emails/invoice.blade.php
```

It includes invoice branding, customer details, payment status, item details, taxes, totals, and an attached PDF.

Queued invoice jobs are handled by:

```text
app/Jobs/SendOrderInvoiceEmail.php
```

The job attempts delivery up to three times with a 30-second backoff. Permanent failures are written to `mail_logs` and Laravel's `failed_jobs` table.

## Queue Operations

View queued and failed jobs through Tinker:

```bash
php artisan tinker
```

```php
DB::table('jobs')->count();
DB::table('failed_jobs')->count();
DB::table('mail_logs')->where('status', 'queued')->count();
DB::table('mail_logs')->where('status', 'failed')->count();
```

Start the invoice worker:

```bash
php artisan queue:work --queue=mail --tries=3 --backoff=30
```

Retry failed jobs:

```bash
php artisan queue:retry all
```

Clear failed jobs after investigating them:

```bash
php artisan queue:flush
```

When using a long-running worker, restart it after changing `.env`, mail settings, queue settings, or application code that affects job behavior.

## Health Indicators

The admin and employee layouts show live system health data. The health service checks:

- Whether a database query succeeds.
- Database query execution time.
- Number of queued jobs.
- Number of failed jobs.

The admin dashboard also reflects low-stock and current-day order activity. The health service is implemented in:

```text
app/Services/SystemHealthService.php
```

## Testing and Validation

Run the full test suite:

```bash
php artisan test
```

Compile Blade templates:

```bash
php artisan view:cache
```

Build production frontend assets:

```bash
npm run build
```

Check routes:

```bash
php artisan route:list
php artisan route:list --path=employee
php artisan route:list --path=admin
```

Check PHP syntax for a changed file:

```bash
php -l app/Services/OrderDocumentService.php
```

## Project Structure

```text
app/
	Actions/Orders/              Transactional order creation
	Http/Controllers/Admin/      Administrator workflows
	Http/Controllers/Employee/   Employee workflows
	Http/Requests/                Form validation
	Jobs/                         Queue jobs, including invoice email
	Models/                       Eloquent models
	Services/                     Audit, document, and health services
database/
	migrations/                  Schema definitions
	seeders/                     Demo and sample data
resources/
	js/                          Alpine/Vite frontend behavior
	css/                         Tailwind application styles
	views/                       Blade pages, invoices, and email templates
routes/
	web.php                      Web routes and role groups
storage/
	app/public/invoices/v2/      Generated invoice PDFs
```

## Security Notes

- Never commit `.env` or SMTP credentials.
- Use a dedicated SMTP account for production delivery.
- Change all demo passwords before deployment.
- Keep `APP_DEBUG=false` in production.
- Use HTTPS in production and configure secure session cookies.
- Run queue workers under a process manager in production.
- Review failed mail logs because they may contain provider error details.

## Troubleshooting

### Mail still targets `127.0.0.1:2525`

The queue worker probably started before the SMTP configuration changed. Run:

```bash
php artisan config:clear
php artisan queue:restart
```

Then start a fresh worker:

```bash
php artisan queue:work --queue=mail --tries=3 --backoff=30
```

Verify the active host without printing the password:

```bash
php artisan tinker --execute="dump(config('mail.default')); dump(config('mail.mailers.smtp.host')); dump(config('mail.mailers.smtp.port'));"
```

### Mail is queued but not sent

Confirm that:

1. `QUEUE_CONNECTION=database` is configured.
2. The `jobs` table exists and contains the mail job.
3. A worker is running with `--queue=mail`.
4. SMTP host, port, username, password, and encryption settings are correct.
5. The recipient provider has not rate-limited the SMTP account.

### Invoice still shows an old layout

Invoice PDFs use the `invoices/v2/` path. If a generated file is still cached, delete only that invoice PDF and download it again, or regenerate it through the download/email flow.

### Configuration changes are not visible

Clear cached configuration and views:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan view:cache
```

## License

This project is based on Laravel and is intended for the MT Billing application. Add the project-specific license terms before public distribution.
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
