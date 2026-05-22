# Cabeleila - Lightweight Salon Management

## Description

Cabeleila is a lightweight appointment and customer management system built exclusively for solo beauty professionals. It focuses on simplifying daily operations with an emphasis on extremely fast customer registration and login using only the customer's phone number.

## Table of Contents

- [Target Audience & Objective](https://www.google.com/search?q=%23target-audience--objective)

- [How it Works (Features)](https://www.google.com/search?q=%23how-it-works-features)

- [Business Rules](https://www.google.com/search?q=%23business-rules)

- [Installation Guide](https://www.google.com/search?q=%23installation-guide)

- [Screenshots](https://www.google.com/search?q=%23screenshots)

- [Contributing](https://www.google.com/search?q=%23contributing)

## Target Audience & Objective

- **Audience:** Solo beauticians and independent beauty professionals who need a no-fuss, centralized scheduling and client-management tool.

- **Objective:** Reduce friction for customer onboarding and appointment booking. Customers sign in or register with a single phone number, minimizing data entry for walk-ins and repeat clients.

## How it Works (Features)

- **Phone-first login/registration:** Customers are identified by phone number. Entering a phone number either finds an existing client or creates a new, minimal client record instantly.

- **Appointments & Services:** Create appointments linked to one or more services. Services are defined in the system and attached to appointments via a pivot table.

- **Client Autonomy:** Clients can easily schedule their visits, view their upcoming appointments, and make changes if needed (subject to time constraints).

- **Availability management:** The admin declares their availability windows, and the system automatically enforces scheduling rules against those specific hours.

- **Simple admin UI:** A single centralized dashboard to manage clients, services, availabilities, and appointments through a responsive Vue + Blade interface.

## Business Rules

1.  **Client identity by phone:** The phone number is the unique identifier for customers. If a provided phone number exists, the system logs the customer in; otherwise, it creates a new client profile with that phone number.

2.  **No password barrier for customers:** The customer flow is intentionally lightweight --- phone number only --- suitable for in-salon quick sign-ins.

3.  **Single-Admin Architecture:** The system is designed for a single professional. All appointments, services, and availabilities belong to one central schedule managed by the sole admin account.

4.  **Appointment constraints:**
    - Appointments must be scheduled within the admin's configured availability windows (availabilities table).

    - Appointments cannot overlap.

    - Each appointment may include multiple services.

5.  **Rescheduling & Cancellations:** Clients have the autonomy to view, modify, or cancel their upcoming appointments, but **only up to 48 hours (2 days) before** the scheduled time. After this window, changes must be made directly with the administrator.

6.  **Automated Status Updates:** Past appointments are automatically processed by the system and marked as "completed" or "canceled" once their scheduled time has passed, keeping the dashboard clean and up-to-date.

7.  **Data integrity:** Deleting a service or client cascades or is prevented according to the database policy (review migration files in database/migrations to adjust).

## Installation Guide

### Prerequisites

- PHP 8.1+

- Composer

- Node.js 18+ and npm

- PostgreSQL (Database)

### Local setup (development)

1.  Clone the repo and enter the project folder:

Bash

```
git clone https://github.com/DaviJat/cabeleila-app.git cabeleila-app && cd cabeleila-app

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

1.  Configure .env: Set up your database connection and admin contact info. _Note: This project does not use external mailing or WhatsApp APIs by default._

Snippet de código

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=leila_db_example
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Admin contact number for customer support / manual changes
WHATSAPP_ADMIN_NUMBER="(11) 99999-9999"

```

1.  Migrate and seed the database:

Bash

```
php artisan migrate --seed

```

_(If you prefer a fresh database reset during development, use: php artisan migrate:fresh --seed)_

1.  Run the application:

Bash

```
php artisan serve --host=127.0.0.1 --port=8000

```

Open http://127.0.0.1:8000 in your browser.

### Production build

Bash

```
composer install --optimize-autoloader --no-dev
npm ci
npm run build
php artisan migrate --force

```

## Screenshots

### 📱 Client Flow

_Extremely fast authentication using only a phone number._

_Clients selecting services and available time slots._

### 💻 Admin Dashboard

_Overview of the day's appointments and statuses._

_Setting up working hours and service durations._

## Deployment Tips

- Use environment-specific .env values and never commit secrets.

- Enable caching in production: php artisan config:cache && php artisan route:cache && php artisan view:cache.

- Ensure your server's Cron is set up to run Laravel's scheduler (required for the automatic completion/cancellation of past appointments).

## Contributing

If you'd like changes or additions, open an issue or submit a PR. Keep changes focused.

## About

_Note: This project was originally developed as a technical challenge for the DSIN recruitment process, and has since been open-sourced._

## License

The Cabeleila App is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
