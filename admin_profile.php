<?php
/**
 * ফাইল নাম: admin_profile.php
 * কাজ: অ্যাডমিন প্রোফাইল তথ্য এবং ড্যাশবোর্ডে ফিরে যাওয়ার বাটন
 */
session_start();
if(!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
include 'db.php';

// ল্যাঙ্গুয়েজ সিলেকশন
if(isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn';

// টেক্সট অ্যারে
$text = [
    'bn' => [
        'title' => 'অ্যাডমিন প্রোফাইল',
        'name' => 'নাম', 'email' => 'ইমেইল', 'phone' => 'ফোন',
        'manage_links' => 'লিংক ম্যানেজমেন্ট',
        'settings' => 'প্রোফাইল ও পিন সেটিংস',
        'back' => 'ড্যাশবোর্ডে ফিরে যান'
    ],
    'en' => [
        'title' => 'Admin Profile',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'manage_links' => 'Manage Links',
        'settings' => 'Profile & PIN Settings',
        'back' => 'Back to Dashboard'
    ]
];

// বর্তমান তথ্য ডাটাবেস থেকে নেওয়া
$admin = $conn->query("SELECT * FROM admin_pins LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <!-- ব্রাউজারের অটো-ট্রান্সলেট মেসেজ বন্ধ করার জন্য -->
    <meta name="google" content="notranslate">
    <title><?php echo $text[$lang]['title']; ?> - Sabbir-Net</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background: #f0f2f5; min-height: 100vh; display: flex; flex-direction: column; align-items: center; color: #2c3e50; }
        .lang-btn { text-align: center; margin-bottom: 20px; }
        .lang-link { background: #6c757d; color: white; padding: 5px 15px; border-radius: 20px; text-decoration: none; font-size: 12px; }
        .card { background: #ffffff; padding: 30px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); width: 100%; max-width: 400px; text-align: center; }
        .profile-info { text-align: left; background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        .profile-info p { margin: 5px 0; font-size: 14px; }
        .nav-btn { display: block; width: 100%; padding: 15px; margin: 10px 0; background: #fff; border: 1px solid #dee2e6; border-radius: 12px; text-decoration: none; color: #333; font-weight: bold; transition: 0.3s; }
        .nav-btn:hover { background: #f1f3f5; border-color: #007bff; }
        .back-btn { color: #007bff; text-decoration: none; font-weight: bold; margin-top: 20px; display: block; padding: 10px; border: 1px solid #007bff; border-radius: 12px; }
    </style>
</head>
<body>

    <div class="lang-btn">
        <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-link">
            <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
        </a>
    </div>

    <div class="card">
        <h3><?php echo $text[$lang]['title']; ?></h3>
        
        <div class="profile-info">
            <p><strong><?php echo $text[$lang]['name']; ?>:</strong> <?php echo htmlspecialchars($admin['full_name'] ?? ''); ?></p>
            <p><strong><?php echo $text[$lang]['email']; ?>:</strong> <?php echo htmlspecialchars($admin['email'] ?? ''); ?></p>
            <p><strong><?php echo $text[$lang]['phone']; ?>:</strong> <?php echo htmlspecialchars($admin['phone'] ?? ''); ?></p>
        </div>
        
        <a href="index_settings.php" class="nav-btn"><?php echo $text[$lang]['manage_links']; ?></a>
        <a href="admin_settings.php" class="nav-btn"><?php echo $text[$lang]['settings']; ?></a>
        
        <a href="admin.php" class="back-btn"><?php echo $text[$lang]['back']; ?></a>
    </div>

</body>
</html>
