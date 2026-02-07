# Contact Messages Troubleshooting Guide

## Issue: Data saves to database but not showing in admin Contact Messages tab

---

## 🔍 **Quick Diagnostics**

### Step 1: Check if data is being saved
1. Visit: `http://localhost/portfolio/test-contacts.php`
2. Look for:
   - ✓ "Contacts table EXISTS" - means data is saving
   - Total messages count - should show your submissions
   - Recent Messages table - should list all submissions

### Step 2: Check admin access
1. Make sure you're **logged in** to the admin dashboard
2. Session must be active (login required for viewing messages)

### Step 3: Check browser console
1. Open browser DevTools (F12)
2. Go to **Console** tab
3. Click "Contact Messages" in admin
4. Check for any error messages
5. Look for "Contact Messages Response:" log to see API response

---

## ✅ **Step-by-Step Fix**

### **If messages are saving but not showing:**

1. **Verify Login Session**
   ```
   - Make sure you're logged into admin dashboard
   - You should see your username in top-right corner
   - Session is required to view messages (security feature)
   ```

2. **Refresh the Page**
   ```
   - Press Ctrl+Shift+R (hard refresh)
   - This clears cache and reloads everything
   ```

3. **Clear Browser Cookies**
   ```
   - Settings → Privacy → Cookies
   - Delete portfolio cookies
   - Login again to admin
   - Click "Contact Messages"
   ```

4. **Check Admin Permissions**
   - Ensure your user_id is set in the session
   - This happens automatically when you login
   - If not working, logout and login again

---

## 🧪 **Testing the API Directly**

### Test if data saves (POST):
```bash
# Using curl or similar
POST /portfolio/api/contact.php
firstName=John
lastName=Doe
email=john@example.com
subject=Test
message=Test message
```

### Test if messages retrieve (GET):
```bash
# Must be logged in (requires session)
GET /portfolio/api/contact.php
(with cookies/session)
```

---

## 📋 **Files to Check**

| File | What to Check |
|------|---|
| `api/contact.php` | Message saving/retrieval logic |
| `admin.html` | Display of messages in dashboard |
| `test-contacts.php` | Visual verification of stored data |
| Browser Console | API errors and responses |

---

## 🐛 **Common Issues & Solutions**

### **Issue 1: "No messages yet" always shows**
**Solution:**
- Check test-contacts.php to verify data exists
- If data doesn't exist, submit a test contact form first
- Verify database connection in config.php

### **Issue 2: Authorization error in console**
**Solution:**
- This means session isn't active
- Login to admin dashboard first
- Session timeout - login again

### **Issue 3: Can see in test-contacts.php but not in admin**
**Solution:**
- Browser console shows the issue
- Usually a JavaScript error
- Check F12 → Console for errors
- May need to refresh page (Ctrl+Shift+R)

### **Issue 4: Admin panel shows "Error loading messages"**
**Solution:**
- Check browser console (F12)
- Verify you're logged in
- Check test-contacts.php first
- Clear cookies and login again

---

## 🔐 **Security Notes**

The message viewing requires authentication because:
- Only logged-in admins can view submissions
- Session validates user permissions
- If you're not logged in, you get 401 Unauthorized
- This is intentional security feature

---

## 📞 **Debug Checklist**

Before reporting an issue, verify:

- [ ] Data appears in `test-contacts.php`
- [ ] Logged into admin dashboard
- [ ] Browser console has no errors (F12)
- [ ] Page refreshed with Ctrl+Shift+R
- [ ] Cookies not blocking session
- [ ] Database credentials correct in config.php
- [ ] Contacts table created successfully
- [ ] API endpoint responding (check Network tab in F12)

---

## 🚀 **Quick Verification**

1. **Submit a test contact** (on portfolio)
   - Go to Contact section
   - Fill and submit form
   - Should see success message

2. **Check test page** (`test-contacts.php`)
   - Visit: http://localhost/portfolio/test-contacts.php
   - Should show your message in table

3. **View in admin** 
   - Login to admin dashboard
   - Click "Contact Messages"
   - Should see your message in list

If it appears in test page but not admin, it's a session/permission issue.

---

## 📝 **Log Output Format**

When checking browser console, you should see:
```javascript
Contact Messages Response: {
    success: true,
    data: [
        {
            id: 1,
            first_name: "John",
            last_name: "Doe",
            email: "john@example.com",
            subject: "Test",
            message: "Test message",
            created_at: "2026-02-06 10:30:00",
            status: "new"
        }
    ],
    count: 1
}
```

If you see this, messages are loading correctly!

---

## 💾 **Database Query to Check Manually**

If you have phpMyAdmin access:
```sql
SELECT * FROM contacts ORDER BY created_at DESC;
```

This shows all stored messages directly.

---

**Still having issues?** Check the browser console (F12) for the exact error message and verify test-contacts.php shows your data.
