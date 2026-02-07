# Complete System Setup Checklist

## 📋 Pre-Setup Requirements

- [ ] XAMPP/WAMP/LAMP is installed and running
- [ ] MySQL server is accessible
- [ ] PHP is version 7.2 or higher
- [ ] You can access `localhost/portfolio/` in your browser

## 🚀 Setup Steps (In Order)

### Step 1: Create Database
**Location**: phpMyAdmin → SQL tab, or MySQL console

```sql
CREATE DATABASE portfolio_admin CHARACTER SET utf8 COLLATE utf8_general_ci;
```

- [ ] Database created successfully

### Step 2: Configure Credentials
**File**: `config.php` (lines 7-10)

Edit these values to match your MySQL setup:
```php
$servername = "localhost";
$username = "root";              // Change if needed
$password = "";                  // Your MySQL password
$database = "portfolio_admin";
```

- [ ] Updated credentials in config.php
- [ ] Saved file

### Step 3: Initialize Database
**Action**: Visit in browser

1. Open: `http://localhost/portfolio/setup.php`
2. You should see:
   - ✓ admin_users table created successfully
   - ✓ admin_sessions table created successfully
   - ✓ Default admin user created
   - Setup complete! You can now delete this setup.php file.

- [ ] Saw success messages for both tables
- [ ] Default user created (admin@portfolio.com / admin123)

### Step 4: Delete setup.php (IMPORTANT!)

**Security**: Do NOT leave setup.php on your server

```bash
# Delete setup.php from portfolio folder
rm setup.php
```

Or manually delete via file explorer/FTP

- [ ] setup.php deleted from portfolio folder

### Step 5: Test Login

1. Open: `http://localhost/portfolio/admin-login.html`
2. Enter credentials:
   - Email: `admin@portfolio.com`
   - Password: `admin123`
3. Click "Sign In"

**Expected Result**: Redirects to admin dashboard

- [ ] Login works with demo credentials
- [ ] Admin dashboard loads successfully

## ✅ Post-Setup

### Security: Change Default Password

1. Generate bcrypt hash using this PHP:
```php
<?php
echo password_hash('your_new_password_here', PASSWORD_BCRYPT, ['cost' => 12]);
?>
```

2. Copy the hash output (starts with `$2y$`)

3. Update database:
```sql
UPDATE admin_users 
SET password = '[paste_hash_here]' 
WHERE email = 'admin@portfolio.com';
```

- [ ] Default password changed
- [ ] New password works in login

### Verify All Features

- [ ] Login page displays correctly
- [ ] Password can be shown/hidden
- [ ] "Remember me" checkbox works
- [ ] Demo credentials section shows
- [ ] Error messages display if wrong password
- [ ] Session persists (no auto-logout on page refresh)

## 📁 File Structure After Setup

```
portfolio/
├── index.html              ✓ Main portfolio
├── admin-login.html        ✓ Login page (NEW)
├── admin.html              ✓ Dashboard
├── admin-welcome.html      ✓ Welcome guide
├── portfolio.html          ✓ Backup
├── config.php              ✓ Database config (NEW)
├── setup.php               ✗ DELETE THIS
├── DATABASE_SETUP.md       ✓ Setup docs (NEW)
├── AUTHENTICATION.md       ✓ Auth docs (NEW)
└── api/
    └── auth.php            ✓ API endpoint (NEW)
```

## 🔍 Troubleshooting

### ❌ "MySQL connection failed"
**Solution:**
- [ ] Check MySQL is running in XAMPP Control Panel
- [ ] Verify credentials in config.php
- [ ] Check database name is correct

### ❌ "Table doesn't exist" error
**Solution:**
- [ ] Re-visit setup.php
- [ ] Check for errors in setup output
- [ ] Verify database was created

### ❌ Login doesn't work
**Solution:**
- [ ] Check browser console (F12) for errors
- [ ] Verify api/auth.php exists
- [ ] Check PHP error logs
- [ ] Verify credentials in database

### ❌ setup.php still exists after setup
**Solution:**
- [ ] Manually delete file from portfolio folder
- [ ] Use FTP/file manager if needed
- [ ] **Important for security!**

## 📞 Support Documentation

- **Setup Issues**: See [DATABASE_SETUP.md](DATABASE_SETUP.md)
- **API Reference**: See [AUTHENTICATION.md](AUTHENTICATION.md)
- **Login Form**: Check [admin-login.html](admin-login.html)
- **API Code**: Check [api/auth.php](api/auth.php)

## 🎯 Next Steps

After successful setup:

1. **Test thoroughly**
   - Try login/logout multiple times
   - Test "Remember me" feature
   - Try wrong credentials

2. **Change default password**
   - Security requirement

3. **Customize as needed**
   - Adjust session timeout in config.php
   - Add more admin users
   - Customize login page styling

4. **Production Preparation**
   - Review [AUTHENTICATION.md](AUTHENTICATION.md) security recommendations
   - Set up HTTPS
   - Configure backups
   - Review file permissions

## 📊 Quick Validation

After setup, verify with these quick checks:

```bash
# Check if api/auth.php exists
ls -la api/auth.php

# Check if config.php exists  
ls -la config.php

# Verify setup.php is deleted
ls -la setup.php    # Should show "file not found"
```

---

**Estimated Setup Time**: 5-10 minutes  
**Difficulty Level**: ⭐ Easy  
**Status**: Ready for testing

---

**Questions?** Check the documentation files or review the setup.php output for specific error messages.
