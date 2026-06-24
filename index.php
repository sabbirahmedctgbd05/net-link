<?php
// ১. হেডার এরর প্রতিরোধের জন্য আউটপুট বাফারিং চালু করা হলো
ob_start();

// ২. সেশন সফলভাবে শুরু করা হলো
session_start();

// ৩. সেশনে কোনো ডেটা রাখতে চাইলে (উদাহরণস্বরূপ):
// $_SESSION['user'] = "JohnDoe";

?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>আমার ওয়েবসাইট</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .success-box {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="success-box">
        <strong>সফল হয়েছে!</strong> সেশন সফলভাবে চালু হয়েছে এবং কোনো এরর নেই।
    </div>

    </body>
</html>
