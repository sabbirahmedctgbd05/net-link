<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// ল্যাঙ্গুয়েজ সিলেকশন
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'bn';

$text = [
    'bn' => [
        'title' => 'অ্যাডমিন প্রোফাইল সেটিংস', 
        'name' => 'পুরো নাম', 
        'email' => 'ইমেইল', 
        'phone' => 'ফোন নম্বর', 
        'old_pin' => 'বর্তমান পিন', 
        'new_pin' => 'নতুন পিন (খালি রাখুন যদি না বদলাতে চান)', 
        'btn' => 'সেভ করুন', 
        'success' => '✅ তথ্য আপডেট হয়েছে!',
        'back' => 'অ্যাডমিন প্রোফাইলে ফিরে যান'
    ],
    'en' => [
        'title' => 'Admin Profile Settings', 
        'name' => 'Full Name', 
        'email' => 'Email', 
        'phone' => 'Phone Number', 
        'old_pin' => 'Current PIN', 
        'new_pin' => 'New PIN (Leave blank to keep old)', 
        'btn' => 'Save Changes', 
        'success' => '✅ Profile updated!',
        'back' => 'Back to Admin Profile'
    ]
];

$message = "";

// আপডেট লজিক
if(isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $old_pin = $_POST['old_pin'];
    $new_pin = !empty($_POST['new_pin']) ? $_POST['new_pin'] : $old_pin;

    $stmt = $conn->prepare("UPDATE admin_pins SET full_name=?, email=?, phone=?, pin_code=? WHERE pin_code=?");
    $stmt->bind_param("sssss", $full_name, $email, $phone, $new_pin, $old_pin);
    
    if($stmt->execute() && $stmt->affected_rows > 0) {
        $message = "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:10px; margin-bottom:10px; text-align:center;'>{$text[$lang]['success']}</div>";
    }
}

// বর্তমান তথ্য আনা
$admin = $conn->query("SELECT * FROM admin_pins LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?php echo $text[$lang]['title']; ?></title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .card { background: #fff; padding: 25px; border-radius: 20px; width: 100%; max-width: 400px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        input { width: 100%; padding: 15px; margin: 8px 0; border: 2px solid #e1e8ed; border-radius: 12px; font-size: 16px; outline: none; }
        button { width: 100%; padding: 15px; background: #007bff; color: white; border: none; border-radius: 12px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .back-btn { margin-top: 20px; padding: 12px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h3><?php echo $text[$lang]['title']; ?></h3>
        <?php echo $message; ?>
        <form method="POST">
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>" placeholder="<?php echo $text[$lang]['name']; ?>">
            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" placeholder="<?php echo $text[$lang]['email']; ?>">
            <input type="text" name="phone" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" placeholder="<?php echo $text[$lang]['phone']; ?>">
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <input type="password" name="old_pin" placeholder="<?php echo $text[$lang]['old_pin']; ?>" required>
            <input type="password" name="new_pin" placeholder="<?php echo $text[$lang]['new_pin']; ?>">
            <button type="submit" name="update_profile"><?php echo $text[$lang]['btn']; ?></button>
        </form>
    </div>
    <a href="admin_profile.php" class="back-btn"><?php echo $text[$lang]['back']; ?></a>
</body>
</html>