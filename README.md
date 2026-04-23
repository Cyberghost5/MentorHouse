<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# MentorHouse

A Laravel marketplace connecting mentors and mentees for 1-on-1 sessions, paid engagements, and project-based collaboration.

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | ≥ 8.2 |
| Composer | ≥ 2.x |
| Node.js | ≥ 18 |
| MySQL | ≥ 8.0 |
| Laravel | 11.x |

---

## Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/your-org/mentorhouse.git
cd mentorhouse
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies and build assets

```bash
npm install
npm run build
```

> For development with hot-reload: `npm run dev`

### 4. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:

```ini
APP_URL=http://mentorhouse.test

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mentorhouse
DB_USERNAME=root
DB_PASSWORD=

# Payment gateways (set whichever you use)
PAYSTACK_SECRET_KEY=sk_test_xxxx
PAYSTACK_PUBLIC_KEY=pk_test_xxxx
KORAPAY_SECRET_KEY=sk_xxxx
KORAPAY_PUBLIC_KEY=pk_xxxx
KORAPAY_ENCRYPTION_KEY=enc_xxxx
```

### 5. Create the database

```sql
CREATE DATABASE mentorhouse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run migrations and seed

```bash
php artisan migrate --seed
```

This creates the schema and seeds four users:

| Name | Role | Email | Password |
|---|---|---|---|
| Alice Mentor | mentor | alice@example.com | password |
| Bob Mentor | mentor | bob@example.com | password |
| Carol Mentee | mentee | carol@example.com | password |
| Dave Mentee | mentee | dave@example.com | password |

> To reset everything: `php artisan migrate:fresh --seed`

### 7. Start the development server

```bash
php artisan serve
```

App will be at [http://localhost:8000](http://localhost:8000).

---

## Application Structure

### Roles

| Role | Access |
|---|---|
| **mentee** | Browse mentors, send session requests, pay for sessions, chat, leave reviews |
| **mentor** | Manage profile, accept/decline requests, mark sessions complete, view earnings, chat |
| **admin** | Full admin panel at `/admin` — users, sessions, reviews, stats, payment gateway settings |

### Key Features

- **Mentor discovery** — search by name/skill, filter by session type, paginated
- **Session requests** — adaptive form (free / paid / project-based), status lifecycle
- **Messaging** — per-conversation chat with Livewire 3s polling, unread badge in nav
- **Payments** — Paystack & Korapay integration; active gateway configurable from admin panel
- **Reviews** — star rating + comment, only after completed sessions; displayed on mentor profile
- **Admin panel** — `/admin` with stats, user management (suspend/activate), session/review moderation, gateway settings

---

## Payment Gateways

The active gateway is stored in the `settings` table and can be changed at **Admin → Settings**.

### Paystack

1. Create an account at [paystack.com](https://paystack.com)
2. Copy your **Secret Key** and **Public Key**
3. Add to `.env` — add webhook URL `https://your-domain.com/payments/webhook` in dashboard

### Korapay

1. Create an account at [korahq.com](https://korahq.com)
2. Copy your **Secret Key**, **Public Key**, and **Encryption Key**
3. Add to `.env` — add webhook URL `https://your-domain.com/payments/webhook` in dashboard

After changing gateway keys, run:

```bash
php artisan config:clear
```

---

## Admin Access

Promote a user to admin:

```bash
php artisan tinker
>>> App\Models\User::where('email', 'alice@example.com')->update(['role' => 'admin']);
```

Then visit `/admin`.

---

## Artisan Commands

```bash
# Reset and re-seed the database
php artisan migrate:fresh --seed

# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

---

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Livewire 3 + Volt, Alpine.js, Tailwind CSS
- **Auth**: Laravel Breeze (Livewire stack)
- **Database**: MySQL 8
- **Payments**: Paystack API & Korapay API (direct HTTP via Laravel `Http` facade — no Cashier required)
- **Notifications**: Database + Mail channels

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

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
