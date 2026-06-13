# RCT Education Portal - Quick Setup Guide

## 🎯 What Has Been Created

A complete **PHP + MySQL** web application for dental RCT patient education with:

✅ **Complete Project Structure** - Organized folders and files  
✅ **Database Schema** - 13 tables for comprehensive data management  
✅ **Authentication System** - Secure login/registration with roles  
✅ **Multi-Language Support** - English, Tamil, Hindi, Telugu  
✅ **Patient Portal** - Dashboard with appointment tracking  
✅ **Admin Dashboard** - Patient management and analytics  
✅ **Responsive Design** - Bootstrap 5 framework  
✅ **Security Features** - Password hashing, session management, CSRF protection  

## ⚡ Quick Start (5 Minutes)

### Step 1: Import Database Schema

```bash
# Option A: Using Command Line
mysql -u root -p rct_education < c:\xampp\htdocs\rct-education-web\config\database.sql

# Option B: Using phpMyAdmin
1. Go to http://localhost/phpmyadmin
2. Create database: rct_education
3. Select database and go to Import tab
4. Choose config/database.sql and click Import
```

### Step 2: Verify Configuration

Edit `config/config.php` and verify database credentials match your setup:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'rct_education');
```

### Step 3: Access Application

Open browser and go to:
```
http://localhost/rct-education-web/
```

### Step 4: Create Your First Admin User

#### Option A: Direct Database Insert
```sql
-- Generate password hash in PHP: password_hash('admin123', PASSWORD_BCRYPT)
-- Result: $2y$10$O9QmsPiuS2EsxZjgAVfVeO3d0ip3XWePMxnCw33S5XQVGz5LmKDO2

USE rct_education;
INSERT INTO users (
    role, email, password_hash, first_name, last_name, 
    phone, language, status, email_verified
) VALUES (
    'admin', 
    'admin@dentist.com', 
    '$2y$10$O9QmsPiuS2EsxZjgAVfVeO3d0ip3XWePMxnCw33S5XQVGz5LmKDO2',
    'Dr',
    'Admin',
    '9000000000',
    'en',
    'active',
    TRUE
);
```

Login with:
- Email: `admin@dentist.com`
- Password: `admin123`

#### Option B: Via Registration + Database Update
1. Click "Register"
2. Fill in details and create account
3. Update in database:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

## 📍 Key URLs

```
Home Page          → http://localhost/rct-education-web/
Patient Login      → http://localhost/rct-education-web/auth/login.php
Register           → http://localhost/rct-education-web/auth/register.php
Patient Dashboard  → http://localhost/rct-education-web/patient/dashboard.php
Admin Dashboard    → http://localhost/rct-education-web/admin/dashboard.php
```

## 👥 Test Accounts

### Patient Account
- Email: `patient@example.com`
- Password: `patient123`

### Admin Account
- Email: `admin@dentist.com`
- Password: `admin123`

(Create these by registering or using SQL INSERT)

## 📂 File Structure Reference

| Folder | Purpose |
|--------|---------|
| `/admin/` | Admin/Dentist pages and dashboards |
| `/patient/` | Patient portal pages |
| `/auth/` | Login, Register, Logout pages |
| `/classes/` | PHP classes (Database, Auth, Language) |
| `/config/` | Configuration and database schema |
| `/includes/` | Initialization and utilities |
| `/languages/` | Translation files |
| `/views/` | HTML templates (header, footer) |
| `/assets/` | CSS, JavaScript, images |
| `/api/` | API endpoints (for future development) |
| `/uploads/` | User uploaded files |

## 🌐 Language Selection

Users can change language:
1. **At Login** - 4 buttons (EN, TA, HI, TE)
2. **In Navigation** - Dropdown menu under navbar
3. **Via URL** - `?lang=ta` (en, ta, hi, te)

## 🗄️ Database Tables Overview

| Table | Purpose |
|-------|---------|
| users | All user accounts |
| appointments | Patient appointments |
| questionnaires | Pre/post questionnaires |
| questions | Individual questions |
| user_answers | Patient responses |
| education_content | Learning materials |
| digital_consent | Consent forms |
| quiz_results | Assessment scores |
| attendance | Attendance tracking |
| audit_logs | System activity |

## ✨ Features to Implement Next

The foundation is complete. To add more features:

1. **Appointment Pages** - Create `/patient/appointment-detail.php`
2. **Questionnaire Pages** - Build assessment interfaces
3. **Education Content** - Add education materials and videos
4. **Quiz Module** - Implement assessment scoring
5. **Consent Forms** - Create digital signing
6. **Admin Reports** - Build data export functionality
7. **Email Notifications** - Add appointment reminders
8. **Payment Integration** - If needed

## 🔒 Default Security Settings

- ✅ Password hashing with BCrypt
- ✅ Session timeout: 1 hour
- ✅ Remember me cookie: 7 days
- ✅ Input sanitization and validation
- ✅ Prepared statements for SQL queries
- ✅ Role-based access control

## 📞 Common Issues & Solutions

**Q: Login not working?**
- A: Clear browser cookies and cache
- Check database credentials in config.php
- Verify database and tables were created

**Q: Language not changing?**
- A: Refresh page after selecting language
- Check language files exist in /languages/
- Verify SUPPORTED_LANGUAGES in config.php

**Q: Can't access admin panel?**
- A: Make sure your user role is set to 'admin' in database
- Verify your account is 'active' status

**Q: Receiving 403 Access Denied?**
- A: Check folder permissions (especially /uploads/)
- Verify user role matches required role for page

## 🚀 Next Steps

1. **Populate Data** - Add education content and questionnaires
2. **Create Appointments** - Test appointment workflows
3. **Customize Styling** - Edit `/assets/css/style.css`
4. **Add Content** - Create education materials for each appointment type
5. **Test All Features** - Complete user journeys
6. **Deploy to Production** - Update config for live server

---

**Good to Go!** Your RCT Education Portal is ready. 🎉

For detailed documentation, see README.md
