<?php
/* File: db.php - Database Configuration */

// রেন্ডারের Environment Variables ব্যবহার করে কানেকশন
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

// কানেকশন তৈরি
$conn = new mysqli($host, $user, $pass, $db);

// কানেকশন এরর হ্যান্ডলিং
if ($conn->connect_error) {
    // এরর লগ করা এবং ইউজারকে সাধারণ মেসেজ দেখানো
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error. Please try again later.");
}

// ইউনিকোড সাপোর্ট নিশ্চিত করা
$conn->set_charset("utf8mb4");

// ডেট টাইমজোন সেট করা
date_default_timezone_set('Asia/Dhaka');