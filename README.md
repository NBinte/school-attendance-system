# School Attendance System – Backend (Laravel)

## Overview
This is the backend API for the School Attendance System.  
Built using **Laravel 12**, **MySQL**, **Redis**, and **Sanctum authentication**.

This backend exposes APIs for:
- Student management
- Attendance recording
- Monthly attendance reporting
- Token-based authentication (Sanctum)

---

## Requirements
- PHP 8.2+
- Composer
- MySQL 8+
- Redis (Docker recommended)
- Laravel 12.x

---

## Project Setup

### 1. Clone Repository
```bash
git clone https://github.com/your-username/school-attendance-system.git
cd school-attendance-system
```

---

## Install Dependencies
```bash
composer install
```

---

## Environment Setup

### 1. Copy example environment
```bash
cp .env.example .env
```

### 2. Update Database Credentials
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_attendance
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### 3. Redis Configuration
```
CACHE_STORE=redis
CACHE_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
```

### 4. Generate App Key
```bash
php artisan key:generate
```

---

## Database Setup

### Create Database
```sql
CREATE DATABASE school_attendance;
```

### Run Migrations
```bash
php artisan migrate
```

(Optional) Add seeders if included:
```bash
php artisan db:seed
```

---

## Running the Backend

### Start Laravel
```bash
php artisan serve
```

Backend runs at:
```
http://127.0.0.1:8000
```

---

## Redis Setup

The system uses Redis for caching attendance statistics.

### Start Redis Using Docker
```bash
docker run -d --name redis-server -p 6379:6379 redis:latest
```

### Test Redis
```bash
docker exec -it redis-server redis-cli ping
```

Expected:
```
PONG
```

---

## Authentication (Sanctum)

This project uses **Laravel Sanctum**.

### Test Login
POST → `/api/login`

**Credentials (example):**
```
email: admin@example.com
password: password123
```

Response includes:
- token
- user object

Use the token for all authenticated calls:
```
Authorization: Bearer <token>
```

---

## API Endpoints

### Students
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/students | List students |
| POST | /api/students | Create student |
| GET | /api/students/{id} | Show student |

### Attendance
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/attendance/bulk | Submit daily attendance |
| GET | /api/attendance/monthly | Monthly report |

---

## Attendance Report Command

A custom Artisan command is included:

```bash
php artisan attendance:generate-report {month} {class}
```

Example:
```bash
php artisan attendance:generate-report 2025-02 10
```

This generates a monthly attendance summary for the selected class.

---

## Events & Listeners

This project uses Laravel’s event system for attendance tracking:

- **AttendanceRecorded** event  
- **UpdateAttendanceStats** listener  
  - Updates Redis cache with daily attendance summary

These run automatically after bulk attendance submission.

---

## Test API Using Insomnia/Postman

### Example: Record Attendance  
POST → `/api/attendance/bulk`
```json
{
  "date": "2025-02-15",
  "class": "10",
  "section": "A",
  "attendance": [
    { "student_id": 1, "status": "present" },
    { "student_id": 2, "status": "absent", "note": "Sick" }
  ]
}
```

---

## AI Workflow Documentation

AI-assisted development details, prompts, and explanation are included in:

```
AI_WORKFLOW.md
```

---

## Author
Nuzhat Binte Islam  
School Attendance Backend – Laravel

---

## Done
Backend is fully ready for deployment, testing, or integration with the frontend.