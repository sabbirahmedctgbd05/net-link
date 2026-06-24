<?php
/**
 * ফাইল নাম: admin.php
 * কাজ: মূল অ্যাডমিন ড্যাশবোর্ড (মোবাইল অ্যাপ স্টাইল)
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
        'manage_links' => 'লিংক সেটিংস',
        'manage_users' => 'ইউজার সেটিংস',
        'settings' => 'প্রোফাইল সেটিংস',
        'logout' => 'লগ আউট',
        'admin_panel' => 'অ্যাডমিন প্যানেল',
        'features' => 'ম্যানেজমেন্ট মেনু'
    ],
    'en' => [
        'title' => 'Admin Dashboard',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'manage_links' => 'Link Settings',
        'manage_users' => 'User Settings',
        'settings' => 'Profile Settings',
        'logout' => 'Logout',
        'admin_panel' => 'Admin Panel',
        'features' => 'Management Menu'
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
    <!-- ফন্ট অসাম আইকন লাইব্রেরি -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { margin: 0; padding: 0; background: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        
        /* মোবাইল অ্যাপ স্টাইল টপ হেডার */
        .app-header { width: 100%; max-width: 450px; background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 30px 20px 40px; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); position: relative; text-align: center; }
        
        /* কোণার অ্যাকশন বাটনসমূহ */
        .header-actions { position: absolute; top: 15px; width: calc(100% - 40px); display: flex; justify-content: space-between; align-items: center; }
        .lang-btn { background: rgba(255, 255, 255, 0.2); color: white; padding: 4px 12px; border-radius: 20px; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); }
        .edit-setting-btn { background: rgba(255, 255, 255, 0.2); color: white; padding: 5px 10px; border-radius: 50%; text-decoration: none; font-size: 14px; border: 1px solid rgba(255,255,255,0.3); width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .edit-setting-btn:active { transform: scale(0.9); background: #ffffff; color: #1e3c72; }
        
        /* মাঝখানে গোল প্রোফাইল এরিয়া */
        .profile-container { display: flex; flex-direction: column; align-items: center; margin-top: 10px; }
        .profile-icon { width: 85px; height: 85px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 38px; color: #fff; border: 3px solid rgba(255, 255, 255, 0.4); box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 12px; }
        
        .admin-title { font-size: 12px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 2px; }
        .admin-name { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
        
        /* প্রোফাইলের ভেতরের তথ্য বিবরণী */
        .profile-details { background: rgba(255, 255, 255, 0.1); padding: 8px 15px; border-radius: 12px; font-size: 12px; display: flex; flex-direction: column; gap: 4px; border: 1px solid rgba(255,255,255,0.08); width: 85%; }
        .profile-details span { display: flex; align-items: center; gap: 8px; justify-content: center; opacity: 0.9; }
        
        /* নিচের কন্টেইনার */
        .container { width: 100%; max-width: 450px; padding: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #4a5568; margin: 15px 0 15px 5px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        /* কালারফুল ৪-কলাম গ্রিড মেনু লেআউট */
        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .menu-card { background: white; padding: 12px 5px; border-radius: 14px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #edf2f7; transition: transform 0.2s; min-height: 90px; }
        .menu-card:active { transform: scale(0.93); }
        
        /* ডায়নামিক আইকন রাউন্ড বক্স কালার */
        .card-icon { font-size: 16px; margin-bottom: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; }
        
        /* আইকনগুলোর জন্য আকর্ষণীয় কালার কোড */
        .menu-card:nth-child(4n+1) .card-icon { background: linear-gradient(135deg, #007bff, #00c6ff); }
        .menu-card:nth-child(4n+2) .card-icon { background: linear-gradient(135deg, #ff9900, #ff5500); }
        .menu-card:nth-child(4n+3) .card-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .menu-card:nth-child(4n+4) .card-icon { background: linear-gradient(135deg, #7f00ff, #e100ff); }
        
        .card-text { font-size: 10px; font-weight: 700; color: #4a5568; line-height: 1.2; word-break: break-word; }

        /* লগ আউট সেকশন */
        .logout-box { text-align: center; margin-top: 35px; width: 100%; }
        .logout-btn { display: inline-flex; align-items: center; gap: 8px; color: #e53e3e; text-decoration: none; font-weight: bold; font-size: 14px; padding: 10px 25px; background: #fff1f1; border-radius: 12px; border: 1px solid #fed7d7; transition: 0.2s; }
        .logout-btn:active { transform: scale(0.95); }
    </style>
</head>
<body>

    <!-- টপ হেডার অংশ -->
    <div class="app-header">
        <div class="header-actions">
            <!-- ভাষা পরিবর্তনের বাটন -->
            <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-btn">
                <i class="fa-solid fa-globe"></i> <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
            </a>
            <!-- ডান কোণায় গিয়ার সেটিং বাটন (প্রোফাইল সেটিংসের জন্য এটিই থাকবে) -->
            <a href="admin_settings.php" class="edit-setting-btn" title="<?php echo $text[$lang]['settings']; ?>">
                <i class="fa-solid fa-gear"></i>
            </a>
        </div>
        
        <!-- মাঝখানে গোল প্রোফাইল ইনফো -->
        <div class="profile-container">
            <div class="profile-icon">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div class="admin-title"><?php echo $text[$lang]['admin_panel']; ?></div>
            <div class="admin-name"><?php echo htmlspecialchars($admin['full_name'] ?? 'Sabbir Ahmed'); ?></div>
            
            <!-- অ্যাডমিনের বাকি তথ্য আইকনের নিচে ছিমছামভাবে -->
            <div class="profile-details">
                <span><i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($admin['email'] ?? ''); ?></span>
                <span><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($admin['phone'] ?? ''); ?></span>
            </div>
        </div>
    </div>

    <!-- মূল বডি কন্টেইনার -->
    <div class="container">
        
        <!-- শিরোনাম -->
        <div class="section-title">
            <i class="fa-solid fa-sliders" style="color: #1e3c72;"></i> 
            <?php echo $text[$lang]['features']; ?>
        </div>

        <!-- ৪-কলামের ডায়নামিক রেডি গ্রিড লেআউট -->
        <div class="menu-grid">
            
            <!-- ১. লিংক সেটিংস ম্যানেজমেন্ট কার্ড -->
            <a href="index_settings.php" class="menu-card">
                <div class="card-icon"><i class="fa-solid fa-link"></i></div>
                <div class="card-text"><?php echo $text[$lang]['manage_links']; ?></div>
            </a>
            
            <!-- ২. ইউজার ম্যানেজমেন্ট বাটন কার্ড -->
            <a href="user_settings.php" class="menu-card">
                <div class="card-icon"><i class="fa-solid fa-users-gear"></i></div>
                <div class="card-text"><?php echo $text[$lang]['manage_users']; ?></div>
            </a>
            
        </div>

        <!-- লগ আউট বাটন -->
        <div class="logout-box">
            <a href="admin_logout.php" class="logout-btn">
                <i class="fa-solid fa-power-off"></i> <?php echo $text[$lang]['logout']; ?>
            </a>
        </div>

    </div>

</body>
</html>
