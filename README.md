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
git clone https://github.com/NBinte/school-attendance-system.git
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

(Optional)  
```bash
php artisan db:seed
```

---

## Running the Backend
```bash
php artisan serve
```

Backend URL:
```
http://127.0.0.1:8000
```

---

## Redis Setup

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

### LOGIN API  
POST → `/api/login`

#### Headers
```
Content-Type: application/json
Accept: application/json
```

#### Body
```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

#### Successful Response
```json
{
  "token": "your_token_here",
  "user": {
    "id": 1,
    "email": "admin@example.com"
  }
}
```

Use token for protected routes:
```
Authorization: Bearer <token>
```

---

# API Endpoints

## Students

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/students | List students |
| POST | /api/students | Create student |
| GET | /api/students/{id} | Show student |

---

# Test API Using Insomnia/Postman

---

## 1. LIST STUDENTS  
GET → `/api/students`

### Headers
```
Authorization: Bearer <token>
Accept: application/json
```

### Example Response
```json
[
  {
    "id": 1,
    "name": "Test Student",
    "student_id": "S-001",
    "class": "10",
    "section": "A"
  }
]
```

---

## 2. CREATE STUDENT  
POST → `/api/students`

### Headers
```
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

### Body
```json
{
  "name": "Test Student",
  "student_id": "S-001",
  "class": "10",
  "section": "A"
}
```

---

## 3. SHOW STUDENT  
GET → `/api/students/1`

### Headers
```
Authorization: Bearer <token>
Accept: application/json
```

---

# Attendance APIs

## 4. RECORD ATTENDANCE (BULK)  
POST → `/api/attendance/bulk`

### Headers
```
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

### Body
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

## 5. MONTHLY REPORT  
GET → `/api/attendance/monthly?month=2025-02&class=10`

### Headers
```
Authorization: Bearer <token>
Accept: application/json
```

### Response Example
```json
[
  {
    "student": { "name": "Test Student", "student_id": "S-001" },
    "date": "2025-02-15",
    "status": "present",
    "note": null
  }
]
```

---

# Attendance Report Command (CLI)

```bash
php artisan attendance:generate-report {month} {class}
```

Example:
```bash
php artisan attendance:generate-report 2025-02 10
```

---

# Events & Listeners

- **AttendanceRecorded** event  
- **UpdateAttendanceStats** listener  
  → Updates Redis cache

---

# AI Workflow Documentation
```
AI_WORKFLOW.md
```

---

# Author
Nuzhat Binte Islam  
School Attendance Backend – Laravel

---

# Done
Backend is fully ready for deployment, testing, or integration with the frontend.