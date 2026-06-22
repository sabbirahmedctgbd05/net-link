<?php
/* File: test_db.php - Database Connection Test */

// রেন্ডারের এনভায়রনমেন্ট ভেরিয়েবল থেকে তথ্য নেওয়া
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

echo "<h2>ডাটাবেস কানেকশন টেস্ট</h2>";
echo "Host: " . htmlspecialchars($host) . "<br>";
echo "User: " . htmlspecialchars($user) . "<br>";
echo "Database: " . htmlspecialchars($db) . "<br><br>";

// কানেকশন তৈরি (Error suppression @ ব্যবহার করা হয়েছে)
$conn = @new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<b style='color:red;'>ফেইল! কানেকশন করা সম্ভব হয়নি।</b><br>";
    echo "এরর মেসেজ: " . htmlspecialchars($conn->connect_error);
} else {
    echo "<b style='color:green;'>সাকসেস! ডাটাবেস সফলভাবে কানেক্ট হয়েছে।</b><br><br>";
    
    // ইউনিকোড সাপোর্ট নিশ্চিত করা
    $conn->set_charset("utf8mb4");

    // টেবিলগুলো দেখাচ্ছে কি না তা যাচাই করা
    $query = $conn->query("SHOW TABLES");
    echo "<b>ডাটাবেসের টেবিল তালিকা:</b><br>";
    if ($query) {
        if ($query->num_rows > 0) {
            while($row = $query->fetch_array()) {
                echo "- " . htmlspecialchars($row[0]) . "<br>";
            }
        } else {
            echo "ডাটাবেসে কোনো টেবিল পাওয়া যায়নি।";
        }
    } else {
        echo "টেবিল তালিকা ফেচ করতে সমস্যা হয়েছে: " . htmlspecialchars($conn->error);
    }
    $conn->close();
}
?>