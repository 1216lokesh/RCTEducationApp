# RCT Education Portal - Development Guide

A comprehensive guide for developers to extend and maintain the application.

## 🏗️ Architecture Overview

### MVC-Style Organization

```
Views (HTML Templates)
    ↓
Controllers (Page Logic)
    ↓
Classes (Business Logic)
    ↓
Database (MySQL)
```

## 📦 Core Classes

### 1. Database Class (`classes/Database.php`)

Handles all database operations with methods:

```php
// Connection
$db = new Database();

// Fetch operations
$row = $db->fetchOne("SELECT * FROM users WHERE id = 1");
$rows = $db->fetchAll("SELECT * FROM users");

// CRUD operations
$db->insert('users', ['email' => 'test@example.com', 'password_hash' => '...']);
$db->update('users', ['status' => 'active'], "id = 1");
$db->delete('users', "id = 1");

// Execute prepared statements
$result = $db->executePrepared("SELECT * FROM users WHERE email = ?", [$email], "s");
```

### 2. Auth Class (`classes/Auth.php`)

Handles authentication and authorization:

```php
// Login
if ($auth->login($email, $password, $rememberMe = false)) {
    // Success
}

// Registration
$result = $auth->register([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'secure_password',
    'role' => 'patient'
]);

// Session management
if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    if ($auth->hasRole('admin')) {
        // Admin-specific code
    }
}

// Access control
Auth::requireLogin();  // Redirect if not logged in
Auth::requireRole('admin');  // Redirect if not admin
```

### 3. Language Class (`classes/Language.php`)

Manages multi-language support:

```php
// Get translated string
$text = Language::get('welcome');  // Returns translated text
$text = __('welcome');  // Helper function

// Set language
Language::set('ta');  // Switch to Tamil

// Get current language
$current = Language::current();  // Returns 'en', 'ta', 'hi', 'te'

// Get all strings
$allStrings = Language::all();
```

## 🔄 Request Flow

1. **User Request** → index.php or specific page
2. **Initialize** → includes/init.php loads configuration, database, auth
3. **Authenticate** → Check if user is logged in and has required role
4. **Process** → Page-specific logic, database queries
5. **Render** → Include header.php, page content, footer.php
6. **Respond** → HTML sent to browser

## 📝 Creating a New Admin Page

### Step 1: Create the PHP file

```php
<?php
require_once __DIR__ . '/../../includes/init.php';

// Require admin role
Auth::requireRole('admin');

// Your page logic here
$data = $db->fetchAll("SELECT * FROM users WHERE role = 'patient'");
?>

<?php include __DIR__ . '/../../views/header.php'; ?>

<!-- Your page HTML here -->

<?php include __DIR__ . '/../../views/footer.php'; ?>
```

### Step 2: Add menu item

Edit `views/header.php` and add link to navbar

### Step 3: Create corresponding translation strings

Add to all language files in `/languages/`:
```php
'page_title' => 'Translated Title',
```

## 📝 Creating a New Patient Page

```php
<?php
require_once __DIR__ . '/../../includes/init.php';

// Require patient role
Auth::requireRole('patient');

$user = $auth->getCurrentUser();

// Your logic here
?>

<?php include __DIR__ . '/../../views/header.php'; ?>

<!-- Your page HTML -->

<?php include __DIR__ . '/../../views/footer.php'; ?>
```

## 🗄️ Working with Database

### Insert Data

```php
$result = $db->insert('appointments', [
    'patient_id' => $userId,
    'dentist_id' => $dentistId,
    'appointment_type' => 'diagnosis',
    'scheduled_date' => '2024-06-15 10:30:00',
    'status' => 'scheduled'
]);

if ($result['success']) {
    $appointmentId = $result['id'];
}
```

### Fetch Data

```php
// Single row
$user = $db->fetchOne("SELECT * FROM users WHERE id = 1");

// All rows
$users = $db->fetchAll("SELECT * FROM users WHERE role = 'patient'");

// With WHERE condition
$appointments = $db->fetchAll(
    "SELECT * FROM appointments WHERE patient_id = {$userId} ORDER BY scheduled_date DESC"
);
```

### Update Data

```php
$result = $db->update('users', [
    'first_name' => 'New Name',
    'language' => 'ta'
], "id = {$userId}");

if ($result['success']) {
    echo "Updated {$result['affected']} rows";
}
```

### Delete Data

```php
$result = $db->delete('appointments', "id = {$appointmentId}");

if ($result['success']) {
    echo "Deleted {$result['affected']} rows";
}
```

## 🌐 Multi-Language Implementation

### Add New Language Strings

1. Edit all files in `/languages/`:

```php
// languages/en.php
'new_key' => 'English text',

// languages/ta.php
'new_key' => 'தமிழ் உரை',

// languages/hi.php
'new_key' => 'हिंदी पाठ',

// languages/te.php
'new_key' => 'తెలుగు పాఠం',
```

2. Use in templates:

```php
<?php echo __('new_key'); ?>
<!-- or -->
<?php echo Language::get('new_key'); ?>
```

## 🎨 Frontend Best Practices

### Use Bootstrap Classes

```html
<!-- Buttons -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>

<!-- Cards -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5><?php echo __('title'); ?></h5>
    </div>
    <div class="card-body">
        Content here
    </div>
</div>

<!-- Alerts -->
<div class="alert alert-info alert-dismissible fade show">
    <i class="fas fa-info-circle"></i> Message
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<!-- Tables -->
<table class="table table-hover">
    <thead class="table-light">
        <tr>
            <th>Header</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Data</td>
        </tr>
    </tbody>
</table>
```

### Forms with Validation

```html
<form method="POST" action="" novalidate>
    <div class="mb-3">
        <label for="email" class="form-label"><?php echo __('email'); ?></label>
        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
               id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        <?php if (isset($errors['email'])): ?>
        <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
        <?php endif; ?>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> <?php echo __('submit'); ?>
    </button>
</form>
```

## 🔒 Security Checklist

- [ ] Use `htmlspecialchars()` when outputting user data
- [ ] Use prepared statements or `$db->escape()` for SQL queries
- [ ] Validate input before using in database
- [ ] Check user role/auth before sensitive operations
- [ ] Hash passwords with `password_hash()`
- [ ] Validate email addresses with `filter_var()`
- [ ] Use HTTPS in production
- [ ] Implement CSRF tokens for forms
- [ ] Regular security audits

## 📊 Adding New Appointment Type

1. **Update Database Schema**: Add to questionnaires and education_content tables

2. **Create Questionnaire**: Insert records in database

3. **Create Education Content**: Add learning materials

4. **Create Page**: New PHP page for the appointment flow

5. **Update Language Files**: Add translation strings

6. **Add to Patient Dashboard**: Link in appointment list

## 🧪 Testing

### Manual Testing Checklist

- [ ] Register new patient account
- [ ] Login with patient account
- [ ] Change language and verify translation
- [ ] View patient dashboard
- [ ] Login with admin account
- [ ] Access admin dashboard
- [ ] View patient list
- [ ] Check all navigation links
- [ ] Test form submissions
- [ ] Verify error messages

### Test User Accounts

```sql
-- Patient
INSERT INTO users (role, email, password_hash, first_name, last_name, status) 
VALUES ('patient', 'patient@test.com', '$2y$10$...', 'Test', 'Patient', 'active');

-- Admin
INSERT INTO users (role, email, password_hash, first_name, last_name, status) 
VALUES ('admin', 'admin@test.com', '$2y$10$...', 'Test', 'Admin', 'active');
```

## 📈 Performance Optimization

1. **Database Indexing**: Indexes on frequently queried columns
2. **Query Optimization**: Use SELECT specific columns, not SELECT *
3. **Caching**: Cache user data in session
4. **Lazy Loading**: Load data only when needed
5. **Asset Minification**: Minify CSS and JavaScript
6. **Image Optimization**: Compress images before upload

## 🚀 Deployment Checklist

- [ ] Update config.php for production database
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Enable HTTPS/SSL
- [ ] Set strong database passwords
- [ ] Disable error reporting on production
- [ ] Set up regular backups
- [ ] Configure email for notifications
- [ ] Test all functionality on production
- [ ] Monitor error logs
- [ ] Set up monitoring and alerts

## 📚 Common Code Patterns

### Check and Fetch User Data

```php
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/auth/login.php');
    exit;
}
$user = $auth->getCurrentUser();
```

### List with Pagination

```php
$limit = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$items = $db->fetchAll(
    "SELECT * FROM items LIMIT $limit OFFSET $offset"
);
```

### Handle Form Submission

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (empty($name)) {
        $errors['name'] = __('field_required');
    }
    
    if (empty($errors)) {
        $result = $db->insert('table_name', ['name' => $name]);
        if ($result['success']) {
            header('Location: ' . APP_URL . '/page.php');
            exit;
        }
    }
}
```

## 🔗 Useful Resources

- [PHP Documentation](https://www.php.net/manual/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Font Awesome Icons](https://fontawesome.com/icons/)

---

**Happy Developing!** 🚀
