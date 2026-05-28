# SUNY University Management System

A full-stack PHP web application for managing school data, featuring Role-Based Access Control (RBAC), secure authentication, and dynamic CRUD management for 46 tables.

## 1. System Requirements
- **PHP:** 8.0 or higher
- **Database:** MySQL/MariaDB
- **Extensions:** PDO

## 2. Setup Instructions

### 2.1 Configuration
1. Rename the `.env.example` file to `.env` (or create a new `.env` file).
2. Configure your database credentials in the `.env` file:
   ```env
   DB_HOST=localhost
   DB_NAME=your_database_name
   DB_USER=your_database_user
   DB_PASS=your_database_password
   ```

### 2.2 Database Import
**CRITICAL:** The database must be imported in the exact order below to satisfy foreign key constraints:
1. `00_DROP_TABLES.sql` (Cleans existing database)
2. `01_Admin.sql` through `43_User.sql` (Table definitions and data)
3. `44_Indexes.sql` (Creates performance indexes)
4. `45_Constraints.sql` (Applies foreign key relationships)

*Note: For large files (e.g., `Attendance.sql`), split them into smaller chunks if your hosting provider has a packet size limit.*

## 3. Project Structure
- `/public`: Public-facing website pages.
- `/portal`: Role-based portals (Admin, Faculty, Student, StatStaff).
- `/src/controllers`: Backend logic and CRUD operations.
- `/src/models`: Data models.
- `/config`: Database connection configuration.
- `/includes`: Reusable UI components (header/footer).

## 4. Security
- **RBAC:** Access is restricted based on user role (Admin, Faculty, Student, StatStaff).
- **Password Security:** Passwords must be stored using `password_hash()` and verified via `password_verify()`.
- **Sanitization:** All CRUD inputs are trimmed and handled via PDO prepared statements to prevent SQL Injection.
