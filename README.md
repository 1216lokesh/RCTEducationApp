# RCT Education Portal

A comprehensive, multi-language web application for dental RCT (Root Canal Treatment) education and patient management.

## 🚀 Features

- **Multi-Language Support**: English, Tamil, Hindi, Telugu
- **Patient Management**: Track appointment journey through 3 main appointments + follow-up
- **Interactive Education**: Custom education modules for each appointment type
- **Assessment & Quizzes**: Pre and post-appointment questionnaires and assessments
- **Digital Consent**: Secure digital consent forms with acceptance tracking
- **Admin Dashboard**: Comprehensive dentist/admin dashboard for patient management
- **Progress Tracking**: Track patient baseline vs post-treatment scores
- **Attendance Tracking**: Monitor patient attendance and punctuality
- **Data Export**: Export patient data and analytics
- **Responsive Design**: Mobile-friendly Bootstrap 5 interface

## 📋 System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache with mod_rewrite enabled
- **Browser**: Modern browser with JavaScript enabled

## 🔧 Installation & Setup

### 1. Database Setup

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database or import the schema:
   - Navigate to `config/database.sql`
   - Execute the SQL file to create all tables

```bash
mysql -u root -p < config/database.sql
```

### 2. Configuration

Edit `config/config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'rct_education');
```

### 3. Access the Application

Navigate to: `http://localhost/rct-education-web/`

### 4. Create Admin User (First Time)

Access the database and insert an admin user:

```sql
INSERT INTO users (
    role, email, password_hash, first_name, last_name, 
    phone, language, status, email_verified
) VALUES (
    'admin', 
    'admin@rct.com', 
    '$2y$10$...', -- Use bcrypt hash for password
    'Admin', 
    'User',
    '9000000000',
    'en',
    'active',
    TRUE
);
```

Or create via the register page and update role in database:

```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@rct.com';
```

## 📁 Project Structure

```
rct-education-web/
├── admin/                      # Admin/Dentist pages
│   ├── dashboard.php          # Main admin dashboard
│   ├── patients.php           # Patient list
│   ├── patient-detail.php     # Individual patient details
│   ├── scores.php             # Scores and assessments
│   ├── consent.php            # Consent tracking
│   ├── attendance.php         # Attendance records
│   └── export.php             # Data export
├── api/                        # API endpoints
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css  # Bootstrap framework
│   │   └── style.css          # Custom styles
│   ├── js/
│   │   ├── bootstrap.bundle.min.js
│   │   └── script.js          # Custom JavaScript
│   └── images/                # Image assets
├── auth/
│   ├── login.php              # User login
│   ├── register.php           # Patient registration
│   └── logout.php             # Logout handler
├── classes/
│   ├── Database.php           # Database operations
│   ├── Auth.php               # Authentication
│   └── Language.php           # Language handling
├── config/
│   ├── config.php             # Configuration settings
│   └── database.sql           # Database schema
├── includes/
│   ├── init.php               # Application initialization
│   └── db.php                 # Database connection
├── languages/
│   ├── en.php                 # English translations
│   ├── ta.php                 # Tamil translations
│   ├── hi.php                 # Hindi translations
│   └── te.php                 # Telugu translations
├── patient/
│   ├── dashboard.php          # Patient dashboard
│   ├── appointment-detail.php # Appointment details
│   ├── questionnaire.php      # Questionnaire page
│   ├── education.php          # Education content
│   ├── quiz.php               # Quiz/Assessment
│   ├── consent.php            # Digital consent
│   └── instructions.php       # Post-op instructions
├── uploads/                   # Uploaded files
├── views/
│   ├── header.php             # Header template
│   └── footer.php             # Footer template
├── index.php                  # Application entry point
└── README.md                  # This file
```

## 👥 User Roles

### Patient Role
- View personal appointments
- Complete pre/post questionnaires
- Access education content
- Take assessments and quizzes
- Sign digital consent forms
- View treatment progress

### Admin/Dentist Role
- View all patients
- Create and manage appointments
- View patient scores and progress
- Track patient attendance
- View consent status
- Export patient data
- Add education content

## 🗺️ Appointment Types

### Appointment 1: Diagnosis
- Baseline Questionnaire 1
- Education Screen 1
- Post Quiz 1
- Counselling Screen
- Digital Consent

### Appointment 2: Root Canal Procedure
- Baseline Questionnaire 2
- Education Screen 2
- Post Quiz 2
- Post-Operative Instructions

### Appointment 3: Crown / Final Restoration
- Baseline Questionnaire 3
- Education Screen 3
- Post Quiz 3

### Follow-Up Visit
- Baseline Questionnaire 4
- Reinforcement Education
- Final Assessment

## 🌐 Multi-Language Support

The application supports 4 languages:

- **en** - English
- **ta** - Tamil (தமிழ்)
- **hi** - Hindi (हिंदी)
- **te** - Telugu (తెలుగు)

Language selection is available:
- At login/registration
- In user profile
- In navigation menu (dropdown)

Language files are stored in `/languages/` directory.

## 🔐 Security Features

- **Password Hashing**: BCrypt with PHP's password_hash()
- **Session Management**: Secure PHP sessions with timeout
- **SQL Injection Prevention**: Prepared statements and input escaping
- **CSRF Protection**: Session-based token validation
- **Remember Me**: Optional persistent login with secure tokens
- **Role-Based Access Control**: User roles determine page access
- **Email Verification**: Email verification for accounts

## 💾 Database Schema

The application uses 12 main tables:

1. **users** - User accounts (patients, admins, dentists)
2. **appointments** - Patient appointments
3. **questionnaires** - Questionnaire templates
4. **questions** - Individual questions
5. **question_options** - Multiple choice options
6. **user_answers** - Patient responses
7. **education_content** - Educational materials
8. **post_operative_instructions** - Post-op care instructions
9. **digital_consent** - Consent forms and acceptance
10. **quiz_results** - Assessment scores
11. **counseling_sessions** - Counseling notes
12. **attendance** - Attendance tracking
13. **audit_logs** - System audit trail

## 📊 API Endpoints

(To be implemented)

```
POST   /api/appointments/create
GET    /api/appointments/{id}
POST   /api/questionnaires/submit
GET    /api/scores/{patient_id}
POST   /api/consent/sign
GET    /api/patient/{id}/progress
POST   /api/export/data
```

## 🎨 Customization

### Adding a New Language

1. Create file: `languages/xx.php` (xx = language code)
2. Copy structure from `languages/en.php`
3. Translate all strings
4. Update `SUPPORTED_LANGUAGES` in `config/config.php`

### Custom Styling

Edit `assets/css/style.css` for custom styles.

## 📝 Usage Examples

### Login
```
URL: /auth/login.php
Email: patient@example.com (or admin@rct.com)
Password: User's password
```

### Patient Dashboard
```
URL: /patient/dashboard.php
Shows: Appointments, progress, assessment scores
```

### Admin Dashboard
```
URL: /admin/dashboard.php
Shows: Patient list, statistics, management tools
```

## 🐛 Troubleshooting

### Database Connection Error
- Check database credentials in `config/config.php`
- Ensure MySQL server is running
- Verify database and tables exist

### Session Errors
- Clear browser cookies
- Check PHP session settings
- Ensure `/uploads` directory is writable

### Language Not Changing
- Clear browser cache
- Check language files exist in `/languages/`
- Verify language code in URL query parameter

## 📧 Support & Contact

For issues and feature requests, please contact the development team.

## 📄 License

This project is licensed under [Your License Here]

## 🙏 Acknowledgments

- Bootstrap 5 for responsive framework
- Font Awesome for icons
- Contributors and testers

---

**Last Updated**: 2026-06-10
**Version**: 1.0.0 - Beta
