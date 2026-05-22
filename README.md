# Cabeleila --- Lightweight Salon Management

## Description

Cabeleila is a lightweight appointment and customer management system built exclusively for solo beauty professionals. It focuses on simplifying daily operations with an emphasis on extremely fast customer registration and login using only the customer's phone number.

## Table of Contents

- [Target Audience & Objective](#target-audience--objective)
- [How it Works (Features)](#how-it-works-features)
- [Tech Stack](#tech-stack)
- [Business Rules](#business-rules)
- [Installation Guide](#installation-guide)
- [Screenshots](#screenshots)
- [Deployment Tips](#deployment-tips)
- [Contributing](#contributing)
- [About](#about)
- [License](#license)

## Target Audience & Objective

- **Audience:** Solo beauticians and independent beauty professionals who need a no-fuss, centralized scheduling and client-management tool.
- **Objective:** Reduce friction for customer onboarding and appointment booking. Customers sign in or register with a single phone number, minimizing data entry for walk-ins and repeat clients.

## How it Works (Features)

- **Phone-first login/registration:** Customers are identified by phone number. Entering a phone number either finds an existing client or creates a new, minimal client record instantly.
- **Appointments & Services:** Create appointments linked to one or more services. Services are defined in the system and attached to appointments via a pivot table.
- **Client Autonomy:** Clients can easily schedule their visits, view their upcoming appointments, and make changes if needed (subject to time constraints).
- **Availability management:** The admin declares their availability windows, and the system automatically enforces scheduling rules against those specific hours.
- **Simple admin UI:** A single centralized dashboard to manage clients, services, availabilities, and appointments through a responsive Vue + Blade interface.

## Tech Stack

This project was built with a modern monolith architecture, leveraging Inertia.js to seamlessly bridge the Laravel backend with a reactive Vue frontend without building a separate API.

**Backend:**
- PHP 8.3
- Laravel (v13.x)
- PostgreSQL (Database)

**Frontend:**
- Vue.js 3 (Composition API)
- Inertia.js (v2.0)
- Tailwind CSS (Utility-first styling)
- PrimeVue (UI Components)

**Tooling & Environment:**
- Vite (Frontend bundling)
- Node.js & NPM
- Composer

## Business Rules

1. **Client identity by phone:** The phone number is the unique identifier for customers. If a provided phone number exists, the system logs the customer in; otherwise, it creates a new client profile with that phone number.
2. **No password barrier for customers:** The customer flow is intentionally lightweight --- phone number only --- suitable for in-salon quick sign-ins.
3. **Single-Admin Architecture:** The system is designed for a single professional. All appointments, services, and availabilities belong to one central schedule managed by the sole admin account.
4. **Appointment constraints:**
    - Appointments must be scheduled within the admin's configured availability windows (`availabilities` table).
    - Appointments cannot overlap.
    - Each appointment may include multiple services.
5. **Rescheduling & Cancellations:** Clients have the autonomy to view, modify, or cancel their upcoming appointments, but **only up to 48 hours (2 days) before** the scheduled time. After this window, changes must be made directly with the administrator.
6. **Automated Status Updates:** Past appointments are automatically processed by the system and marked as "completed" or "canceled" once their scheduled time has passed, keeping the dashboard clean and up-to-date.
7. **Data integrity:** Deleting a service or client cascades or is prevented according to the database policy (review migration files in `database/migrations` to adjust).

## Installation Guide

### Prerequisites

- PHP 8.1+
- Composer
- Node.js 18+ and `npm`
- PostgreSQL (Database)

### Local setup (development)

1. Clone the repo and enter the project folder:
```bash
git clone [https://github.com/DaviJat/cabeleila-app.git](https://github.com/DaviJat/cabeleila-app.git) cabeleila-app && cd cabeleila-app

```

1.  Install PHP dependencies:

Bash

```
composer install

```

1.  Install Node dependencies and build frontend assets:

Bash

```
npm install
npm run dev

```

1.  Environment file and app key:

Bash

```
cp .env.example .env
php artisan key:generate

```

1.  Configure `.env`: Set up your database connection and admin contact info.

Snippet de código

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=leila_db_example
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Admin contact number for customer support
WHATSAPP_ADMIN_NUMBER="(11) 99999-9999"

```

1.  Migrate and seed the database:

Bash

```
php artisan migrate --seed

```

1.  Run the application:

Bash

```
php artisan serve --host=127.0.0.1 --port=8000

```

### Production build

Bash

```
composer install --optimize-autoloader --no-dev
npm ci
npm run build
php artisan migrate --force

```

Screenshots
-----------

### 🏠 Home Page

<img width="1477" height="903" alt="image" src="https://github.com/user-attachments/assets/65de59b6-4785-4a1f-8dc0-5873564d83d8" />

### 📱 Client Flow

<img width="1477" height="903" alt="image" src="https://github.com/user-attachments/assets/d8b7fad8-9ac7-40ca-a413-4b34c4571d80" />

<img width="1477" height="903" alt="image" src="https://github.com/user-attachments/assets/43ee4708-4e01-44b6-ae3a-3c725f95b37a" />

<img width="1477" height="903" alt="image" src="https://github.com/user-attachments/assets/d9838336-9307-412d-b95f-a3c7e0840602" />

### 💻 Admin Dashboard

<img width="1477" height="903" alt="image" src="https://github.com/user-attachments/assets/d55ae3ed-4e3d-48e5-a907-7e88d0614fda" />

### 💻 Admin Schedule

<img width="1477" height="903" alt="image" src="https://github.com/user-attachments/assets/e699d0c9-ff8b-4c8f-ab4e-84b8e3a41748" />

Deployment Tips
---------------

-   Use environment-specific `.env` values and never commit secrets.

-   Enable caching in production: `php artisan config:cache && php artisan route:cache && php artisan view:cache`.

-   Ensure your server's Cron is set up to run Laravel's scheduler (required for the automatic completion/cancellation of past appointments).

Contributing
------------

If you'd like changes or additions, open an issue or submit a PR. Keep changes focused.

About
-----

*Note: This project was originally developed as a technical challenge for the DSIN recruitment process, and has since been open-sourced.*

License
-------

The Cabeleila App is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
