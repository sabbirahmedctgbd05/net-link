<?php
// সেশন শুরু করা
session_start();

// অ্যাডমিন সেশনটি মুছে ফেলা
unset($_SESSION['admin_logged_in']);

// সেশনটি পুরোপুরি ধ্বংস করে দেওয়া (সব ধরনের সেশন ডাটা মুছে ফেলা)
session_destroy();

// অ্যাডমিনকে আবার অ্যাডমিন লগইন পেজে পাঠিয়ে দেওয়া
header("Location: admin_login.php");
exit;
?>
