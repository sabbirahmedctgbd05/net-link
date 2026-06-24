<?php
ob_start(); // লাইন ১: হেডার এরর স্থায়ীভাবে দূর করার জন্য আউটপুট বাফারিং
session_start();

// ইউজার লগইন না থাকলে তাকে লগইন পেজে পাঠিয়ে দিবে
if(!isset($_SESSION['user_logged_in'])) { 
    header("Location: login.php"); 
    exit; 
}
include 'db.php';

// ল্যাঙ্গুয়েজ সিলেকশন হ্যান্ডেল করা
if(isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn';

// টেক্সট অ্যারে (আপনার আগেরটাই রাখা হয়েছে, শুধু সেটিংসের টেক্সট যোগ করা হয়েছে)
$text = [
    'bn' => [
        'title' => 'সাব্বির নেট পোর্টাল', 
        'logout' => 'লগ আউট', 
        'no_link' => 'বর্তমানে কোনো লিংক নেই',
        'settings' => 'পিন পরিবর্তন',
        'profile' => 'গ্রাহক প্রোফাইল'
    ],
    'en' => [
        'title' => 'Sabbir-Net Portal', 
        'logout' => 'Logout', 
        'no_link' => 'No links available',
        'settings' => 'Change PIN',
        'profile' => 'User Profile'
    ]
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Language" content="<?php echo $lang; ?>">
    <title>Sabbir-Net Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { margin: 0; padding: 0; background: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        
        /* মোবাইল অ্যাপ স্টাইল টপ হেডার ও প্রোফাইল */
        .app-header { width: 100%; max-width: 450px; background: linear-gradient(135deg, #007bff, #00c6ff); color: white; padding: 25px 20px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: relative; }
        .profile-section { display: flex; align-items: center; gap: 15px; }
        .avatar { width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; border: 2px solid rgba(255,255,255,0.4); }
        .profile-info .user-role { font-size: 11px; opacity: 0.8; text-transform: uppercase; }
        .profile-info .user-name { font-size: 18px; font-weight: bold; }
        
        /* ল্যাঙ্গুয়েজ বাটন */
        .lang-btn { position: absolute; top: 25px; right: 20px; background: rgba(255, 255, 255, 0.2); color: white; padding: 5px 12px; border-radius: 20px; text-decoration: none; font-size: 12px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); }
        
        /* মেইন কন্টেইনার */
        .container { width: 100%; max-width: 450px; padding: 20px; }
        .portal-title { font-size: 16px; font-weight: bold; color: #4a5568; margin: 10px 0 15px 5px; display: flex; align-items: center; gap: 8px; }
        
        /* মোবাইল সফটওয়্যারের মতো গ্রিড মেনু */
        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .menu-card { background: white; padding: 20px 15px; border-radius: 15px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #edf2f7; transition: transform 0.2s; }
        .menu-card:active { transform: scale(0.95); }
        
        /* আইকন কালার থিম */
        .card-icon { font-size: 24px; margin-bottom: 10px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; }
        .menu-card:nth-child(4n+1) .card-icon { background: linear-gradient(135deg, #ff416c, #ff4b2b); }
        .menu-card:nth-child(4n+2) .card-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .menu-card:nth-child(4n+3) .card-icon { background: linear-gradient(135deg, #ff9900, #ff5500); }
        .menu-card:nth-child(4n+4) .card-icon { background: linear-gradient(135deg, #7f00ff, #e100ff); }
        
        /* সেটিংস ও অন্য ফিক্সড বাটন স্টাইল */
        .settings-card { background: white; grid-column: span 2; padding: 15px 20px; border-radius: 15px; text-decoration: none; color: #4a5568; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #edf2f7; font-weight: 600; font-size: 14px; margin-top: 5px; }
        .settings-card .setting-icon { width: 35px; height: 35px; background: #edf2f7; color: #4a5568; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .settings-card .arrow-right { margin-left: auto; color: #a0aec0; }
        
        .card-text { font-size: 13px; font-weight: 600; color: #4a5568; }

        /* লগআউট বাটন */
        .logout-box { text-align: center; margin-top: 30px; width: 100%; }
        .logout-btn { display: inline-flex; align-items: center; gap: 8px; color: #e53e3e; text-decoration: none; font-weight: bold; font-size: 14px; padding: 10px 25px; background: #fff1f1; border-radius: 12px; border: 1px solid #fed7d7; }
    </style>
</head>
<body>

    <div class="app-header">
        <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-btn">
            <i class="fa-solid fa-globe"></i> <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
        </a>
        <div class="profile-section">
            <div class="avatar">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div class="profile-info">
                <div class="user-role"><?php echo $text[$lang]['profile']; ?></div>
                <div class="user-name">Sabbir Ahmed</div>
            </div>
        </div>
    </div>

    <div class="container">
        
        <div class="portal-title">
            <i class="fa-solid fa-grip" style="color: #007bff;"></i> 
            <?php echo $text[$lang]['title']; ?>
        </div>

        <div class="menu-grid">
            <?php
            $res = $conn->query("SELECT * FROM isp_links ORDER BY id DESC");
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $display_name = ($lang == 'bn') ? $row['name_bn'] : $row['name_en'];
                    
                    // ডাটাবেজে আইকন ফিল্ড থাকলে সেটা পাবে, না থাকলে ডিফল্ট গ্লোব আইকন শো করবে
                    $icon = (!empty($row['icon'])) ? $row['icon'] : 'fa-solid fa-network-wired';
                    
                    echo '<a href="'.$row['url'].'" class="menu-card" target="_blank">';
                    echo '  <div class="card-icon"><i class="'.$icon.'"></i></div>';
                    echo '  <div class="card-text">'.$display_name.'</div>';
                    echo '</a>';
                }
            } else {
                echo '<p style="grid-column: span 2; text-align:center; color:gray; padding:20px;">'.$text[$lang]['no_link'].'</p>';
            }
            ?>

            <a href="settings.php" class="settings-card">
                <div class="setting-icon"><i class="fa-solid fa-key"></i></div>
                <span><?php echo $text[$lang]['settings']; ?></span>
                <i class="fa-solid fa-chevron-right arrow-right"></i>
            </a>
        </div>

        <div class="logout-box">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-power-off"></i> <?php echo $text[$lang]['logout']; ?>
            </a>
        </div>

    </div>

</body>
</html>
<?php 
ob_end_flush(); // আউটপুট বাফার শেষ করা হলো
?>
