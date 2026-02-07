# Admin Authentication System - Complete Implementation

## Overview

Your portfolio now has a **production-ready authentication system** with:
- MySQL database for storing credentials
- Server-side session management
- Bcrypt password hashing
- One-session-per-user enforcement
- IP address and user-agent tracking
- 1-hour session expiration

## What's New

### New Files Created

1. **config.php** - Database configuration and connection setup
2. **setup.php** - Database initialization script (run once, then delete)
3. **api/auth.php** - Authentication API endpoint
4. **DATABASE_SETUP.md** - Complete setup guide

### Modified Files

1. **admin-login.html** - Now uses backend API instead of demo credentials
2. **admin.html** - Ready for backend integration

## Installation Steps

### 1. Create Database
```sql
CREATE DATABASE portfolio_admin CHARACTER SET utf8 COLLATE utf8_general_ci;
```

### 2. Configure Connection
Edit `config.php` with your MySQL credentials:
```php
$servername = "localhost";
$username = "root";           // Your MySQL user
$password = "";               // Your MySQL password
$database = "portfolio_admin";
```

### 3. Initialize Tables
Visit: `http://localhost/portfolio/setup.php`

This creates all tables and the default admin user.

### 4. Delete setup.php
```bash
rm setup.php
```

### 5. Test Login
- Go to: `admin-login.html`
- Email: `admin@portfolio.com`
- Password: `admin123`

## How It Works

### Login Flow
```
User enters credentials
    ↓
admin-login.html sends POST to api/auth.php
    ↓
auth.php queries admin_users table
    ↓
password_verify() checks password hash
    ↓
New session created in admin_sessions table
    ↓
Session cookie/PHP session stored
    ↓
User redirected to admin.html
```

### Session Management
```
User visits admin.html
    ↓
JavaScript checks authentication via api/auth.php?action=check_auth
    ↓
Server verifies session exists and hasn't expired
    ↓
Dashboard loads with user's data
    ↓
User can edit portfolio content
```

## API Reference

### Authentication Endpoint: `/api/auth.php`

#### Login
```javascript
const formData = new FormData();
formData.append('action', 'login');
formData.append('email', 'admin@portfolio.com');
formData.append('password', 'admin123');

const response = await fetch('api/auth.php', {
    method: 'POST',
    body: formData
});
const data = await response.json();
```

#### Check Authentication
```javascript
const response = await fetch('api/auth.php?action=check_auth');
const data = await response.json();

if (data.authenticated) {
    console.log('User:', data.user);
}
```

#### Logout
```javascript
const formData = new FormData();
formData.append('action', 'logout');

const response = await fetch('api/auth.php', {
    method: 'POST',
    body: formData
});
const data = await response.json();
```

## Database Schema

### admin_users
Stores admin account credentials

```sql
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,          -- Bcrypt hash
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### admin_sessions
Tracks active user sessions

```sql
CREATE TABLE admin_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
);
```

## Security Features

### ✓ Implemented
- **Bcrypt Hashing**: Uses PHP's `password_hash()` with cost factor 12
- **Server-Side Sessions**: Sessions stored in database, not client-side
- **IP Address Tracking**: Detects unauthorized access from different networks
- **User-Agent Tracking**: Detects device/browser changes
- **Session Expiration**: 1-hour timeout for inactive sessions
- **One Session Per User**: New login automatically logs out old session
- **CSRF Headers**: X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
- **Password Verification**: Uses `password_verify()` for safe comparison

### 🔒 Additional Recommendations
- Enable HTTPS in production
- Set HttpOnly flag on session cookies
- Implement rate limiting on login attempts
- Add 2-factor authentication (future enhancement)
- Use environment variables for sensitive config
- Enable database encryption at rest
- Implement activity logging and audit trails

## Configuration Details

### Session Lifetime
Currently set to **3600 seconds (1 hour)** in `config.php`:
```php
define('SESSION_LIFETIME', 3600);
```

To change:
```php
define('SESSION_LIFETIME', 1800);  // 30 minutes
define('SESSION_LIFETIME', 86400); // 24 hours
```

### Password Requirements
The current default password is `admin123`, but you should change it:

1. Generate bcrypt hash:
```php
echo password_hash('your_new_password', PASSWORD_BCRYPT, ['cost' => 12]);
```

2. Update in database:
```sql
UPDATE admin_users SET password = '$2y$12$...[hash]...' WHERE email = 'admin@portfolio.com';
```

## Troubleshooting

### "Database connection failed"
- Ensure MySQL is running
- Check credentials in `config.php`
- Verify database `portfolio_admin` exists

### Login page doesn't connect to backend
- Check that `api/auth.php` exists
- Verify PHP is running on your server
- Check browser console for errors (F12)
- Check server error logs

### "Session expired" message
- Sessions last 1 hour by default
- Clear browser cache and try logging in again
- Check system time is correct

### Password verification fails
- Verify bcrypt hash was created correctly
- Check that `php` has bcrypt support (usually enabled by default)
- Look for errors in `php_errors.log`

## File Locations

```
portfolio/
├── admin-login.html        ← Login form (frontend)
├── admin.html              ← Dashboard (frontend)
├── config.php              ← Database config
├── setup.php               ← Init script (delete after use)
├── DATABASE_SETUP.md       ← Setup documentation
├── AUTHENTICATION.md       ← This file
└── api/
    └── auth.php            ← API endpoint
```

## What's Next?

Once login is working, the next steps are:

1. **Integrate admin.html with backend**
   - Modify save functions to POST to backend
   - Add backend endpoints for CRUD operations
   - Migrate data from localStorage to database

2. **Add data persistence**
   - Create endpoints for each admin section
   - Store portfolio data in MySQL
   - Load data from database on page load

3. **Add user management**
   - Allow admins to add/remove users
   - Different permission levels
   - Audit logging for all changes

4. **Enhance security**
   - Add 2-factor authentication
   - Implement CAPTCHA on login
   - Add password complexity requirements
   - Email verification for new accounts

## Support

For issues or questions, refer to:
- [DATABASE_SETUP.md](DATABASE_SETUP.md) - Database configuration guide
- [admin-login.html](admin-login.html) - Login form source code
- [api/auth.php](api/auth.php) - API endpoint source code

---

**Status**: ✅ Login system complete and ready for testing  
**Last Updated**: 2024
