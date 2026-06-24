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
        'manage_links' => 'লিংক সেটিংস',
        'settings' => 'প্রোফাইল সেটিংস',
        'back' => 'হোম ড্যাশবোর্ড',
        'profile_info' => 'বিস্তারিত প্রোফাইল তথ্য'
    ],
    'en' => [
        'title' => 'Admin Profile',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'manage_links' => 'Link Settings',
        'settings' => 'Profile Settings',
        'back' => 'Home Dashboard',
        'profile_info' => 'Detailed Profile Info'
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
    <meta name="google" content="notranslate">
    <title><?php echo $text[$lang]['title']; ?> - Sabbir-Net</title>
    <!-- ফন্ট অসাম আইকন লাইব্রেরি -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { margin: 0; padding: 0; background: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        
        /* মোবাইল অ্যাপ স্টাইল টপ হেডার */
        .app-header { width: 100%; max-width: 450px; background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 30px 20px 35px; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); position: relative; text-align: center; }
        
        /* কোণার ল্যাঙ্গুয়েজ বাটন */
        .header-actions { position: absolute; top: 15px; left: 20px; }
        .lang-btn { background: rgba(255, 255, 255, 0.2); color: white; padding: 4px 12px; border-radius: 20px; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); }
        
        /* মাঝখানে গোল প্রোফাইল এরিয়া */
        .profile-container { display: flex; flex-direction: column; align-items: center; margin-top: 10px; }
        .profile-icon { width: 85px; height: 85px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 38px; color: #fff; border: 3px solid rgba(255, 255, 255, 0.4); box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 12px; }
        
        .admin-title { font-size: 12px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 2px; }
        .admin-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }

        /* নিচের কন্টেইনার */
        .container { width: 100%; max-width: 450px; padding: 20px; }
        
        /* প্রোফাইলের বিস্তারিত তথ্য বক্স */
        .info-box { background: white; padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #edf2f7; margin-bottom: 25px; }
        .info-title { font-size: 14px; font-weight: bold; color: #4a5568; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .info-item { display: flex; align-items: center; gap: 15px; padding: 12px 0; border-bottom: 1px dashed #e2e8f0; }
        .info-item:last-child { border-bottom: none; padding-bottom: 0; }
        .info-item i { font-size: 16px; color: #2a5298; width: 24px; text-align: center; }
        .info-label { font-size: 12px; color: #718096; font-weight: 600; min-width: 60px; }
        .info-value { font-size: 14px; color: #2c3e50; font-weight: 500; word-break: break-all; }

        /* কালারফুল ৪-কলাম গ্রিড মেনু লেআউট */
        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .menu-card { background: white; padding: 12px 5px; border-radius: 14px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #edf2f7; transition: transform 0.2s; min-height: 90px; }
        .menu-card:active { transform: scale(0.93); }
        
        /* ডায়নামিক আইকন রাউন্ড বক্স কালার */
        .card-icon { font-size: 16px; margin-bottom: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; }
        
        /* আইকনগুলোর জন্য গ্রেডিয়েন্ট কম্বিনেশন */
        .menu-card:nth-child(4n+1) .card-icon { background: linear-gradient(135deg, #007bff, #00c6ff); }
        .menu-card:nth-child(4n+2) .card-icon { background: linear-gradient(135deg, #ff9900, #ff5500); }
        .menu-card:nth-child(4n+3) .card-icon { background: linear-gradient(135deg, #e53e3e, #ff5f6d); }
        .menu-card:nth-child(4n+4) .card-icon { background: linear-gradient(135deg, #7f00ff, #e100ff); }
        
        .card-text { font-size: 10px; font-weight: 700; color: #4a5568; line-height: 1.2; word-break: break-word; }
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
        </div>
        
        <!-- মাঝখানে গোল প্রোফাইল ইনফো -->
        <div class="profile-container">
            <div class="profile-icon">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <div class="admin-title"><?php echo $text[$lang]['title']; ?></div>
            <div class="admin-name"><?php echo htmlspecialchars($admin['full_name'] ?? 'Sabbir Ahmed'); ?></div>
        </div>
    </div>

    <!-- মূল বডি কন্টেইনার -->
    <div class="container">
        
        <!-- প্রোফাইল কার্ডের ভেতরের তথ্য বিবরণী -->
        <div class="info-box">
            <div class="info-title">
                <i class="fa-solid fa-circle-info" style="color: #2a5298;"></i>
                <?php echo $text[$lang]['profile_info']; ?>
            </div>
            
            <div class="info-item">
                <i class="fa-regular fa-user"></i>
                <div class="info-label"><?php echo $text[$lang]['name']; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($admin['full_name'] ?? 'Sabbir Ahmed'); ?></div>
            </div>
            
            <div class="info-item">
                <i class="fa-regular fa-envelope"></i>
                <div class="info-label"><?php echo $text[$lang]['email']; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($admin['email'] ?? 'sabbirahmedctgbd05@gmail.com'); ?></div>
            </div>
            
            <div class="info-item">
                <i class="fa-solid fa-phone"></i>
                <div class="info-label"><?php echo $text[$lang]['phone']; ?></div>
                <div class="info-value"><?php echo htmlspecialchars($admin['phone'] ?? '01832663636'); ?></div>
            </div>
        </div>

        <!-- ৪-কলামের ডায়নামিক রেডি গ্রিড মেনু লেআউট -->
        <div class="menu-grid">
            
            <!-- ১. লিংক সেটিংস ম্যানেজমেন্ট কার্ড -->
            <a href="index_settings.php" class="menu-card">
                <div class="card-icon"><i class="fa-solid fa-link"></i></div>
                <div class="card-text"><?php echo $text[$lang]['manage_links']; ?></div>
            </a>
            
            <!-- ২. প্রোফাইল সেটিংস কার্ড -->
            <a href="admin_settings.php" class="menu-card">
                <div class="card-icon"><i class="fa-solid fa-user-gear"></i></div>
                <div class="card-text"><?php echo $text[$lang]['settings']; ?></div>
            </a>
            
            <!-- ৩. ড্যাশবোর্ডে ফিরে যাওয়ার কার্ড (Back to Dashboard) -->
            <a href="admin.php" class="menu-card">
                <div class="card-icon"><i class="fa-solid fa-house-user"></i></div>
                <div class="card-text"><?php echo $text[$lang]['back']; ?></div>
            </a>
            
        </div>

    </div>

</body>
</html>
