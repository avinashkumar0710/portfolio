# 📁 Data Storage Location

## Where Your Portfolio Data is Saved

### **Primary Storage**
```
📂 Project Root
 └── 📂 data/
     └── 📄 portfolio-data.json  ← ALL YOUR DATA IS SAVED HERE
```

**Full Path:** `e:\xampp\htdocs\portfolio\data\portfolio-data.json`

---

## What Gets Saved

The `portfolio-data.json` file stores all your portfolio information:

### **1. About Section**
- Name
- Role/Title
- Location
- Bio
- Image URL

### **2. Projects**
- Project titles
- Descriptions
- Categories (web, mobile, data)
- Technologies used

### **3. Skills**
- Skill names
- Categories (Languages, Frameworks, Databases, Tools)
- Proficiency levels (0-100%)

### **4. Experience**
- Job titles
- Company names
- Duration
- Description

### **5. Testimonials**
- Testimonial text
- Author name
- Company/Position

### **6. Certificates** ⭐ *NEW*
- Certificate titles
- Issuing organizations
- Certificate links
- Issue dates

### **7. Contact Information**
- Email address
- Phone number
- LinkedIn URL
- GitHub URL
- Twitter URL
- Location

### **8. Custom Sections**
- Any custom sections you create

---

## How Saving Works

### **Admin Panel → Save All Button**
```
Admin Panel (index.html)
       ↓
   Save All Button
       ↓
   saveAllChanges() function
       ↓
   saveData() function
       ↓
   POST to: api/portfolio-data.php
       ↓
   Writes to: data/portfolio-data.json
       ↓
   ✅ Data Saved!
```

### **Automatic Save (When Adding Items)**
```
Add Project/Certificate/Skill
       ↓
   saveItem() function
       ↓
   saveData() function
       ↓
   POST to: api/portfolio-data.php
       ↓
   Writes to: data/portfolio-data.json
```

---

## File Permissions

⚠️ **Important:** The `data/` directory needs write permissions for PHP to save files.

**Required permissions:**
- Directory: `755` (read, write, execute)
- File: `644` (read, write for owner; read-only for others)

If saving doesn't work:
1. Right-click `data/` folder → Properties
2. Check "Allow full control" for your user
3. Apply and OK

---

## How to Verify Data is Being Saved

### **Method 1: Check Browser Console**
1. Open Admin Panel
2. Press `F12` to open Developer Tools
3. Go to **Console** tab
4. Click "Save All" button
5. Look for messages:
   - ✅ `"Data successfully saved to: /data/portfolio-data.json"`
   - ✅ `"API Response: {success: true, message: '...'}"` 

### **Method 2: Check the File Directly**
1. Open: `e:\xampp\htdocs\portfolio\data\portfolio-data.json`
2. Your changes should appear in JSON format
3. Check the file's "Modified" timestamp

### **Method 3: Check Network Activity**
1. Open Admin Panel
2. Press `F12` → Network tab
3. Click "Save All"
4. Look for `portfolio-data.php` request
5. Should show Status: `200` (successful)

---

## Troubleshooting Save Issues

### ❌ If Save Shows Error:

**1. Check if `data/` directory exists**
```
Path: e:\xampp\htdocs\portfolio\data\
```

**2. Check PHP error log**
- XAMPP Control Panel → Logs → Apache

**3. Check file permissions**
- Right-click `data/` folder
- Properties → Security → Edit

**4. Open Browser Console**
- Press F12
- Check Console tab for detailed error messages

**5. Clear browser cache**
- Ctrl + Shift + Delete
- Clear cache and try again

---

## Real-Time Data Flow

```
🌐 Portfolio Website
    ↓ (loads from)
📄 api/portfolio-data.php (GET)
    ↓ (reads)
📂 data/portfolio-data.json ← YOUR DATA
    ↓ (displays)
✨ Beautiful Portfolio Page

---

✏️ Admin Panel
    ↓ (saves to)
📄 api/portfolio-data.php (POST)
    ↓ (writes to)
📂 data/portfolio-data.json ← UPDATES HERE
    ↓ (next load)
🌐 Portfolio reflects changes
```

---

## Backup Your Data

To backup your portfolio data:

**1. Copy the file**
```
From: e:\xampp\htdocs\portfolio\data\portfolio-data.json
To:   Your backup location (e.g., Desktop, Cloud Storage)
```

**2. Or export from Admin Panel**
- View the portfolio-data.json file in text editor
- Copy all content
- Save to backup location

---

## Summary

| Item | Location |
|------|----------|
| **Data Storage** | `data/portfolio-data.json` |
| **API Endpoint** | `api/portfolio-data.php` |
| **Admin Panel** | `admin.html` |
| **Portfolio Page** | `index.html` |
| **Save Function** | `saveData()` in admin.html |
| **Auto-Save** | On each item creation |

✅ **All your portfolio data is safely stored in JSON format and can be viewed anytime!**
