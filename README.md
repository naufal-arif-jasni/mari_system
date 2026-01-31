# MARI - Malaysia Aid Registration Initiative

![MARI Logo](images/logo.png)

## 📋 Project Overview

**MARI (Malaysia Aid Registration Initiative)** is a comprehensive web-based system designed to streamline the application and distribution of disability aid across Malaysia. The platform serves as a bridge between Orang Kurang Upaya (OKU) individuals and available assistance from government agencies, NGOs, and charitable partners.

The name "Mari" means "come" in Bahasa Malaysia, reflecting our welcoming spirit and mission to invite everyone to come together in supporting the OKU community.

---

## ✨ Key Features

### For Applicants
- **User Registration & Authentication** - Secure account creation with encrypted passwords
- **Profile Management** - Update personal information, upload profile pictures
- **Application Submission** - Comprehensive disability aid application form with:
  - Personal & guardian information
  - Disability details and medical history
  - Functional impact assessment
  - Document uploads (medical reports, OKU card, IC copy, etc.)
  - Digital signature capability
- **Application Tracking** - View submission history and status updates
- **Admin Remarks** - Receive feedback from administrators

### For Administrators
- **Dashboard Analytics** - Real-time statistics and overview
- **Application Management** - View, edit, and process applications
- **Status Updates** - Change application status with remarks
- **Search & Filter** - Find applications by name, ID, MyKad, or status
- **Report Generation** - Create printable reports
- **Admin Account Management** - Add/remove administrator accounts
- **Activity Logging** - Track all system actions with IP addresses

---
---

## 📂 Project Structure

```
mari-system/
│
├── assets/
│   ├── css/              # Stylesheets
│   └── js/               # JavaScript files
│
├── images/               # System images and logos
│
├── uploads/              # User uploads
│   ├── documents/        # Application documents
│   └── profiles/         # Profile pictures
│
├── admin.php             # Application management
├── admin_dashboard.php   # Admin statistics
├── admin_profile.php     # Admin account management
├── application.php       # Application overview
├── application_form.php  # Application submission form
├── auth.php              # Authentication handler
├── db_conn.php           # Database connection
├── home.php              # User dashboard
├── index.php             # Landing page
├── login.php             # Login page
├── profile.php           # User profile view
├── register.php          # User registration
├── submit_application.php # Application processing
└── README.md             # This file
```

## 🗄️ Database Schema

### Main Tables

#### `users`
Stores user account information
- `user_id` (Primary Key)
- `username`
- `password` (hashed)
- `full_name`
- `ic_number`
- `email`
- `phone_number`
- `profile_picture`
- etc.

#### `application_history`
Main application records
- `application_id` (Primary Key)
- `user_id` (Foreign Key)
- `application_number`
- `status`
- `submission_date`
- `admin_remarks`

#### `applicant_details`
Detailed application information
- All applicant personal data
- Guardian information
- Disability details
- Document paths
- Declaration data

#### `admins`
Administrator accounts
- `admin_id` (Primary Key)
- `username`
- `password` (hashed)
- `full_name`

#### `status`
Status change history
- Tracks all status updates
- Records who made changes and when

#### `activity_log`
System activity logging
- All user and admin actions
- IP address tracking

---
## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Server**: Apache (XAMPP/WAMP/LAMP)

---

## 📦 System Requirements

### Minimum Requirements
- **Web Server**: Apache 2.4+
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 8.0 or higher
- **Disk Space**: 500MB minimum
- **RAM**: 2GB minimum

### Recommended Requirements
- **PHP**: Version 8.0+
- **MySQL**: Version 8.0+
- **Disk Space**: 2GB (for document storage)
- **RAM**: 4GB

---
### Github Repositery
https://github.com/naufal-arif-jasni/mari_system.git

## 🚀 Step-by-Step Login Instructions

-- ADMIN LOGIN --
### 1. Access the Login Page
Navigate to: `http://localhost/mari_system/login.php`

### 2. Select Admin Role
- In the "Login As" dropdown, select **"System Administrator"**

### 3. Enter Credentials
- **Username**: `masteradmin`
- **Password**: `admin123`

### 4. Click "Login to Account"

### 5. You will be redirected to Admin Dashboard
- URL: `http://localhost/mari_system/admin_dashboard.php`
---

## 🔐 Hardcoded Admin Implementation

The master admin account is hardcoded in the `auth.php` file for demonstration and testing purposes:

```php
// Location: auth.php (Lines 11-18)

if ($role_type === 'admin') {
    // --- HARDCODED MASTER ADMIN ---
    if ($username === 'masteradmin' && $password === 'admin123') {
        $_SESSION['user_id'] = 0;
        $_SESSION['username'] = 'MasterAdmin';
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    }
    // ... rest of code
}
```

**Note**: This hardcoded account bypasses database authentication and provides immediate admin access.

---

## 📊 What You Can Access as Admin

### 1. **Admin Dashboard** (`admin_dashboard.php`)
- View total applications statistics
- See approved, pending, and rejected counts
- Recent applications list
- Category distribution
- Quick action buttons

### 2. **Application Management** (`admin.php`)
- View all submitted applications
- Search by name, ID, or MyKad
- Filter by status (Pending, Under Review, Approved, Rejected)
- View detailed application information
- Edit application status
- Add admin remarks
- Delete applications
- Generate reports

### 3. **Admin Account Management** (`admin_profile.php`)
- Add new administrator accounts
- Remove existing admins
- View all admin users

---

## 🧪 Testing the System

### Creating Test User Account (Optional)

If you want to test the user side:

1. Go to: `http://localhost/mari-system/register.php`
2. Create a test account with any details
3. Login as user to submit test applications
4. Then login as admin to review them

**Sample Test User**:
```
Full Name: Test User
IC Number: 991234567890
Email: testuser@example.com
Phone: 0123456789
Username: testuser
Password: test123
```

---

## 📁 Key Admin Features to Test

### ✅ View Applications
1. Login as admin
2. Click "Applications" in sidebar
3. See all submitted applications in card format

### ✅ Search & Filter
1. Use search box to find by name/IC/ID
2. Use status filter dropdown
3. Click "Apply" to filter results

### ✅ View Application Details
1. Click "View" button on any application card
2. Modal popup shows complete application details
3. View all sections: Personal, Guardian, Disability, Documents

### ✅ Update Status
1. Click "Edit" button on application
2. Change status (Pending → Under Review → Approved/Rejected)
3. Add admin remarks
4. Click "Update Status"

### ✅ Generate Report
1. Click "Generate Report" button
2. View printable summary of all applications
3. Click "Download Report (PDF)" or "Print" button

### ✅ Delete Application
1. Click "Delete" button on application
2. Confirm deletion
3. Application and all related data removed

---

## 🗂️ Database Access (For Verification)

### phpMyAdmin Access
```
URL: http://localhost/phpmyadmin
Database Name: mari_system
```

### Key Tables to Check:
- `users` - User accounts
- `admins` - Admin accounts (database admins only)
- `application_history` - Main application records
- `applicant_details` - Detailed application data
- `status` - Status change history
- `activity_log` - All system activities

---

## 🎯 Sample Grading Checklist

### System Functionality
- ✅ User registration works
- ✅ User login authentication
- ✅ Admin login (hardcoded master account)
- ✅ Application form submission
- ✅ File uploads (documents)
- ✅ Admin can view applications
- ✅ Admin can update status
- ✅ Admin can add remarks
- ✅ Search and filter functionality
- ✅ Report generation
- ✅ Activity logging
- ✅ Profile management

### Database Design
- ✅ Normalized database structure
- ✅ Proper relationships (Foreign Keys)
- ✅ Transaction management
- ✅ Data integrity

### Security
- ✅ Password hashing
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (htmlspecialchars)
- ✅ Session management
- ✅ Role-based access control

### User Interface
- ✅ Responsive design
- ✅ Intuitive navigation
- ✅ Professional styling
- ✅ Form validation
- ✅ Modal popups

---

## 🔍 Admin Panel Tour

### Navigation Menu (Sidebar)
![sidebar](images\image.png)

### Dashboard Statistics Cards
![statistic cards](images\image-1.png)

---

## 📸 Visual Guide

### Login Screen
![admin login screen](images\image-3.png)


### Admin Dashboard View
![Dashboard View](images\image-2.png)

---

## ⚠️ Important Notes for Evaluation

1. **Hardcoded Admin**: The master admin account (`masteradmin` / `admin123`) is intentionally hardcoded for easy testing and demonstration purposes.

2. **Database Admins**: Additional admin accounts can be created via the "Manage Admins" page and are stored in the database.

3. **No Data Yet**: If the system is freshly installed, there will be no applications. You can:
   - Create a user account and submit test applications
   - Or I can provide sample SQL data to populate the database

4. **File Permissions**: Ensure `uploads/` folder has write permissions for document uploads to work.

5. **Session Management**: The system uses PHP sessions to maintain login state. Clear browser cookies if you encounter login issues.

---

## 🆘 Troubleshooting

### Cannot Login
**Problem**: "Invalid Admin Credentials" error  
**Solution**: 
- Ensure you selected "System Administrator" from dropdown
- Check username: `masteradmin` (all lowercase, no spaces)
- Check password: `admin123` (all lowercase)

### Page Not Found
**Problem**: 404 error  
**Solution**: 
- Verify project is in correct directory (htdocs/www)
- Check URL: `http://localhost/mari_system/`
- Ensure Apache service is running

### Database Connection Error
**Problem**: "Connection failed" message  
**Solution**: 
- Check MySQL service is running
- Verify database `mari_system` exists
- Check credentials in `db_conn.php`

---

## 📞 Contact for Issues

If you encounter any issues during evaluation:

**Developer Contact**:
- Naufal Arif (project Leader)
- muhammadnaufalarif2211@gmail.com
- 010-909 4907

---

## 🎓 For Academic Assessment

### Project Highlights
1. **Full-Stack Web Application** - PHP, MySQL, HTML, CSS, JavaScript
2. **Secure Authentication** - Password hashing, session management
3. **CRUD Operations** - Create, Read, Update, Delete functionality
4. **File Upload System** - Document management
5. **Role-Based Access** - User vs Admin privileges
6. **Responsive Design** - Works on desktop and mobile
7. **Database Normalization** - Properly structured schema
8. **Transaction Management** - Data integrity maintained
9. **Activity Logging** - Audit trail of all actions
10. **Report Generation** - Printable summaries

### Technical Implementation
- **MVC-Like Structure** - Separation of concerns
- **Prepared Statements** - SQL injection prevention
- **Input Sanitization** - XSS prevention
- **Error Handling** - Try-catch blocks for transactions
- **Code Comments** - Well-documented code

---

**Last Updated**: January 2026  
**System Version**: 1.0.0  
**Purpose**: Academic Project Submission

---

## ✅ Quick Start Checklist

- [ ] XAMPP/WAMP running
- [ ] Database `mari_system` created and imported
- [ ] Project in htdocs/www folder
- [ ] Navigate to `http://localhost/mari_system/`
- [ ] Go to login page
- [ ] Select "System Administrator"
- [ ] Enter: `masteradmin` / `admin123`
- [ ] Click Login
- [ ] Explore admin dashboard!

---

**Happy Testing! 🚀**