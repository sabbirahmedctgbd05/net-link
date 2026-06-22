<?php
/**
 * ফাইল নাম: admin.php
 * কাজ: মূল অ্যাডমিন ড্যাশবোর্ড
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
        'title' => 'অ্যাডমিন ড্যাশবোর্ড',
        'name' => 'নাম', 'email' => 'ইমেইল', 'phone' => 'ফোন',
        'manage_links' => 'লিংক সেটিংস (Index)',
        'settings' => 'প্রোফাইল ও পিন সেটিংস',
        'logout' => 'লগ আউট'
    ],
    'en' => [
        'title' => 'Admin Dashboard',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'manage_links' => 'Link Settings (Index)',
        'settings' => 'Profile & PIN Settings',
        'logout' => 'Logout'
    ]
];

// ডাটাবেস থেকে অ্যাডমিনের তথ্য আনা
$admin = $conn->query("SELECT * FROM admin_pins LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="google" content="notranslate">
    <title><?php echo $text[$lang]['title']; ?> - Sabbir-Net</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background: #f0f2f5; min-height: 100vh; display: flex; flex-direction: column; align-items: center; color: #2c3e50; }
        .lang-btn { text-align: center; margin-bottom: 20px; }
        .lang-link { background: #6c757d; color: white; padding: 5px 15px; border-radius: 20px; text-decoration: none; font-size: 12px; }
        
        .card { background: #ffffff; padding: 30px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); width: 100%; max-width: 400px; text-align: center; }
        
        /* প্রোফাইল আইকন */
        .profile-icon { width: 90px; height: 90px; background: #eef2f3; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 45px; color: #007bff; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .profile-info { text-align: left; background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 25px; border-left: 4px solid #007bff; }
        .profile-info p { margin: 8px 0; font-size: 14px; font-weight: 500; }
        
        .nav-btn { display: block; width: 100%; padding: 15px; margin: 10px 0; background: #fff; border: 1px solid #dee2e6; border-radius: 12px; text-decoration: none; color: #333; font-weight: bold; transition: 0.3s; }
        .nav-btn:hover { background: #f1f3f5; border-color: #007bff; }
        
        .logout-btn { color: #ff4757; text-decoration: none; font-weight: bold; margin-top: 25px; display: block; }
    </style>
</head>
<body>

    <div class="lang-btn">
        <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-link">
            <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
        </a>
    </div>

    <div class="card">
        <!-- আইকন -->
        <div class="profile-icon">👤</div>
        
        <h3><?php echo $text[$lang]['title']; ?></h3>
        
        <!-- অ্যাডমিনের তথ্য -->
        <div class="profile-info">
            <p><strong><?php echo $text[$lang]['name']; ?>:</strong> <?php echo htmlspecialchars($admin['full_name'] ?? 'Admin'); ?></p>
            <p><strong><?php echo $text[$lang]['email']; ?>:</strong> <?php echo htmlspecialchars($admin['email'] ?? 'N/A'); ?></p>
            <p><strong><?php echo $text[$lang]['phone']; ?>:</strong> <?php echo htmlspecialchars($admin['phone'] ?? 'N/A'); ?></p>
        </div>
        
        <!-- বাটনসমূহ -->
        <a href="index_settings.php" class="nav-btn"><?php echo $text[$lang]['manage_links']; ?></a>
        <a href="admin_settings.php" class="nav-btn"><?php echo $text[$lang]['settings']; ?></a>
        
        <!-- লগ আউট -->
        <a href="admin_logout.php" class="logout-btn"><?php echo $text[$lang]['logout']; ?></a>
    </div>

</body>
</html>
