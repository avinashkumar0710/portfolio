# MySQL Database Setup Guide

## Quick Start

### Step 1: Create the Database
Run this SQL command in phpMyAdmin or MySQL command line:

```sql
CREATE DATABASE portfolio_admin CHARACTER SET utf8 COLLATE utf8_general_ci;
```

### Step 2: Configure Database Connection
Edit [config.php](config.php) and update these lines:

```php
$servername = "localhost";  // Your MySQL host
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password
$database = "portfolio_admin";
```

### Step 3: Initialize Database Tables
Open your browser and visit: `http://localhost/portfolio/setup.php`

This will:
- Create `admin_users` table
- Create `admin_sessions` table
- Insert default admin user (admin@portfolio.com / admin123)

Then **delete setup.php** for security.

### Step 4: Test Login
1. Go to [admin-login.html](admin-login.html)
2. Enter credentials:
   - Email: `admin@portfolio.com`
   - Password: `admin123`
3. Click "Sign In"

---

## Database Structure

### admin_users Table
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| email | VARCHAR(255) | Unique email address |
| password | VARCHAR(255) | Bcrypt hashed password |
| name | VARCHAR(255) | User's full name |
| created_at | TIMESTAMP | Account creation time |
| updated_at | TIMESTAMP | Last update time |

### admin_sessions Table
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key, auto-increment |
| session_id | VARCHAR(255) | Unique session identifier |
| user_id | INT | Foreign key to admin_users |
| ip_address | VARCHAR(45) | Client IP address |
| user_agent | TEXT | Client browser/device info |
| created_at | TIMESTAMP | Session creation time |
| expires_at | TIMESTAMP | Session expiration time |

---

## Security Features

✓ **Bcrypt Password Hashing** - Passwords use PHP's `password_hash()` with bcrypt algorithm  
✓ **Session Management** - Server-side sessions prevent session hijacking  
✓ **IP Address Tracking** - Sessions bound to IP address for extra security  
✓ **User Agent Tracking** - Detects unauthorized device changes  
✓ **Session Expiration** - Sessions expire after 1 hour of inactivity  
✓ **One Session Per User** - New login automatically logs out previous session  
✓ **CSRF Protection Headers** - Security headers prevent common attacks  

---

## File Structure

```
portfolio/
├── index.html                 # Main portfolio page
├── admin-login.html          # Login page (frontend)
├── admin.html                # Admin dashboard
├── config.php                # Database configuration
├── setup.php                 # Database initialization (DELETE after use)
├── portfolio.html            # (backup or original)
├── admin-welcome.html        # Welcome guide
└── api/
    └── auth.php              # Authentication API endpoint
```

---

## API Endpoints

### POST /api/auth.php (Login)
```javascript
// Request
{
  action: 'login',
  email: 'admin@portfolio.com',
  password: 'admin123'
}

// Success Response (200 OK)
{
  success: true,
  message: 'Login successful',
  user: {
    id: 1,
    email: 'admin@portfolio.com',
    name: 'Admin'
  }
}

// Error Response
{
  success: false,
  message: 'Invalid password'
}
```

### POST /api/auth.php (Logout)
```javascript
// Request
{
  action: 'logout'
}

// Response
{
  success: true,
  message: 'Logged out successfully'
}
```

### GET /api/auth.php (Check Authentication)
```javascript
// Request
?action=check_auth

// Authenticated Response
{
  success: true,
  authenticated: true,
  user: {
    id: 1,
    email: 'admin@portfolio.com',
    name: 'Admin'
  }
}

// Not Authenticated
{
  success: false,
  authenticated: false,
  message: 'Not authenticated'
}
```

---

## Troubleshooting

### "Database connection failed"
- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database `portfolio_admin` exists

### "Table doesn't exist"
- Visit `setup.php` again to create tables
- Check MySQL user has CREATE TABLE permissions

### "Login fails but credentials are correct"
- Check bcrypt hash in database: `SELECT * FROM admin_users;`
- Verify password was hashed correctly in setup.php

### "Sessions not persisting"
- Check `php.ini` for session configuration
- Verify session directory is writable
- Check browser cookies are enabled

---

## Changing Default Password

```sql
-- Hash the new password with bcrypt first
-- Use: password_hash('newpassword', PASSWORD_BCRYPT)
-- Get the hash and update:

UPDATE admin_users 
SET password = '$2y$12$...[your_bcrypt_hash_here]...' 
WHERE email = 'admin@portfolio.com';
```

---

## Adding More Admin Users

```sql
-- Replace with actual bcrypt hash
INSERT INTO admin_users (email, password, name) 
VALUES ('newadmin@portfolio.com', '[bcrypt_hash]', 'New Admin');
```

Or via admin dashboard once it's updated to support user management.

---

## Production Checklist

- [ ] Delete `setup.php` after initialization
- [ ] Use strong passwords (12+ characters, mixed case, numbers, symbols)
- [ ] Configure HTTPS/SSL for all connections
- [ ] Set appropriate file permissions (600 for config.php)
- [ ] Configure firewall to restrict database access
- [ ] Set up regular database backups
- [ ] Enable PHP error logging (disable display_errors)
- [ ] Configure secure session cookies (HttpOnly, Secure flags)
- [ ] Implement rate limiting on login endpoint
- [ ] Add 2-factor authentication (optional enhancement)

---

## Next Steps

1. ✓ Set up database and tables
2. ✓ Test login functionality  
3. Integrate admin.html with backend for data persistence
4. Add user profile management
5. Implement password change functionality
6. Add activity logging and audit trail

---

Last Updated: 2024
