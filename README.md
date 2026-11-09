# 🍔 Foodo — Full-Stack Food Ordering & Restaurant Platform

<p align="center">
  <img src="images/logo.svg" alt="Foodo Logo" width="160" />
</p>

<p align="center">
  <strong>Production-ready Full-Stack Food Ordering and Delivery Management Platform built with PHP, MySQL, and modern JavaScript.</strong>
  <br />
  <em>Complete customer experience, real-time cart engine, Razorpay online payments, SMS OTP verification, and comprehensive administrative portal.</em>
</p>

<p align="center">
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-blue.svg" alt="License: MIT"></a>
  <img src="https://img.shields.io/badge/PHP-8.x%20%7C%20MySQL-purple.svg" alt="PHP & MySQL"></a>
  <img src="https://img.shields.io/badge/Payments-Razorpay%20API-blue" alt="Razorpay"></a>
  <img src="https://img.shields.io/badge/SMS-Fast2SMS%20API-orange" alt="Fast2SMS"></a>
  <img src="https://img.shields.io/badge/Security-Prepared%20Statements%20%7C%20CSRF%20%7C%20Rate%20Limit-green" alt="Security"></a>
</p>

---

## 🌟 Overview

**Foodo** is a full-featured, secure, and responsive food ordering web application designed for restaurants, cafes, and multi-cuisine kitchens. It streamlines online menu browsing, real-time cart calculations, checkout workflows, payment collection, and kitchen dispatch tracking.

---

## ✨ Key Features

### 1. 🛍️ Customer Storefront & Menu
- **Interactive Menu Catalog**: 18+ categorized food sections with veg/non-veg tags, ratings, and price variations.
- **Dynamic Real-Time Cart**: Add, update, and remove items with instant subtotal and tax re-calculation.
- **Wishlist & Favorites**: Save preferred dishes across sessions.
- **Live Order Tracking**: Visual progress timeline tracking kitchen preparation, dispatch, and delivery stages.

### 2. 💳 Payment & Notification Integrations
- **Dual Payment Channels**: Seamless support for Cash on Delivery (COD) and Online Payments powered by **Razorpay API**.
- **SMS OTP Verification**: Instant mobile OTP verification via **Fast2SMS API** for account security and order authorization.
- **Automated Email Invoices**: HTML receipt dispatch via **PHPMailer** and Gmail SMTP.

### 3. 🛡️ Enterprise Defensive Security
- **SQL Injection Defense**: Strict use of MySQLi prepared statements across all database queries.
- **CSRF Token Validation**: Cryptographic token matching protecting all form submissions and API endpoints.
- **Adaptive Rate Limiting**: Built-in IP rate limiter guarding against brute-force login and OTP spamming attacks.
- **Cryptographic Password Hashing**: Passwords stored securely using `password_hash()` with modern bcrypt algorithms.

### 4. 📊 Administrative Control Center
- **Order Pipeline Management**: Update order lifecycle states (Pending, Preparing, Out for Delivery, Delivered, Cancelled).
- **Menu & Category CRUD**: Add, edit, or disable food items with high-resolution image uploads.
- **Customer Messages & Inquiries**: View and respond to user feedback submitted through the contact gateway.
- **Revenue Analytics**: Daily, weekly, and monthly order summaries.

---

## 📁 Project Architecture

```plaintext
food-ordering/
├── admin/                  # Administrative Management Portal
│   ├── categories.php      # Food Category Manager
│   ├── dashboard.php       # Operational Analytics & KPIs
│   ├── foods.php           # Dish Inventory & Pricing
│   ├── orders.php          # Live Order Status Dispatch
│   └── login.php           # Admin Authentication Guard
│
├── includes/               # Core Application Infrastructure
│   ├── config.example.php  # Environment Configuration Template
│   ├── db_connect.php      # Central MySQL Connection Engine
│   ├── csrf.php            # CSRF Token Generator & Validator
│   ├── rate_limit.php      # IP-Based Brute-Force Barrier
│   ├── notify.php          # Multi-channel Notification Pipeline (SMS/Email)
│   └── PHPMailer/          # Enterprise SMTP Mailer Library
│
├── pages/                  # Public Customer Views
│   ├── menu.php            # Interactive Category Filter & Dish Cards
│   ├── cart.php            # Real-Time Cart Manager
│   ├── checkout.php        # Address & Payment Selection
│   ├── track-order.php     # Visual Delivery Tracking Stepper
│   ├── login.php           # User Sign-In with Rate Limiting
│   └── register.php        # User Onboarding with Validation
│
├── css/                    # Typography, Components & Responsive Design
├── js/                     # AJAX Cart, Modal Controllers & Validations
├── database.sql            # Master Database Schema & Sample Data
├── LICENSE                 # MIT License (karthikjr)
└── README.md               # Engineering Documentation
```

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (with PHP 8.0+ and MySQL / MariaDB)

---

### Installation Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Karthikjr48/foodo.git
   ```
   *Move the folder into your web root directory (e.g. `C:\xampp\htdocs\food-ordering`).*

2. **Import the Database:**
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Create a database named `tastybite_db`.
   - Click the **Import** tab, choose `database.sql`, and click **Go**.

3. **Configure Environment Secrets:**
   Copy `includes/config.example.php` to `includes/config.local.php`:
   ```bash
   cp includes/config.example.php includes/config.local.php
   ```
   Open `includes/config.local.php` and configure:
   ```php
   // Gmail SMTP (App Password)
   define('MAIL_USERNAME', 'your_email@gmail.com');
   define('MAIL_PASSWORD', 'your_16_digit_app_password');

   // Fast2SMS API Key
   define('FAST2SMS_API_KEY', 'your_fast2sms_api_key');

   // Razorpay Credentials
   define('RAZORPAY_KEY_ID', 'rzp_test_your_key_id');
   define('RAZORPAY_KEY_SECRET', 'your_razorpay_secret');
   ```

4. **Launch Application:**
   Start Apache and MySQL in XAMPP, then open:
   - **Customer Portal**: `http://localhost/food-ordering`
   - **Admin Portal**: `http://localhost/food-ordering/admin/login.php`

---

## 📜 License

This project is licensed under the [MIT License](LICENSE) &copy; 2026 **karthikjr**.

---

## 👨‍💻 Author

**karthikjr**
- GitHub: [@Karthikjr48](https://github.com/Karthikjr48)
- Email: [karthikjr11@outlook.com](mailto:karthikjr11@outlook.com)
