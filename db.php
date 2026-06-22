
<?php
// ডাটাবেস কনফিগারেশন
$host = 'sql107.infinityfree.com';
$db   = 'if0_42220690_sabbir_net';
$user = 'if0_42220690';
$pass = '31663636';

// মাইএসকিউএলআই (MySQLi) কানেকশন তৈরি
$conn = new mysqli($host, $user, $pass, $db);

// কানেকশন এরর চেক করা
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// সারা পৃথিবীর ভাষা সাপোর্ট করার জন্য UTF-8MB4 সেট করা
// এটি বাংলাসহ সকল ভাষার অক্ষর ঠিক রাখার জন্য বাধ্যতামূলক
$conn->set_charset("utf8mb4");

// পিএইচপি এনকোডিং নিশ্চিত করা
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");
?>