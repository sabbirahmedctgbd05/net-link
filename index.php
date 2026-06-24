<?php
ob_start(); // হেডার এরর প্রতিরোধের জন্য আউটপুট বাফারিং
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

// পিন/পাসওয়ার্ড পরিবর্তনের লজিক (প্রসেসিং)
$msg = '';
if(isset($_POST['change_pin'])) {
    $current_pin = mysqli_real_escape_with_str($conn, $_POST['current_pin']); // আপনার ডিবি স্ট্রাকচার অনুযায়ী কুয়েরি লিখবেন
    $new_pin = mysqli_real_escape_with_str($conn, $_POST['new_pin']);
    
    // এখানে আপনার ইউজারের টেবিল অনুযায়ী পাসওয়ার্ড/পিন আপডেট কুয়েরি হবে
    // $uid = $_SESSION['user_id'];
    // $conn->query("UPDATE users SET pin='$new_pin' WHERE id='$uid'");
    $msg = ($lang == 'bn') ? 'পিন সফলভাবে পরিবর্তিত হয়েছে!' : 'PIN changed successfully!';
}

// টেক্সট অ্যারে
$text = [
    'bn' => [
        'title' => 'সাব্বির নেট পোর্টাল', 
        'logout' => 'লগ আউট', 
        'no_link' => 'বর্তমানে কোনো লিংক নেই',
        'settings' => 'পিন পরিবর্তন করুন',
        'current_pin' => 'বর্তমান পিন',
        'new_pin' => 'নতুন পিন',
        'save' => 'সংরক্ষণ করুন',
        'admin' => 'গ্রাহক প্রোফাইল'
    ],
    'en' => [
        'title' => 'Sabbir-Net Portal', 
        'logout' => 'Logout', 
        'no_link' => 'No links available',
        'settings' => 'Change PIN Settings',
        'current_pin' => 'Current PIN',
        'new_pin' => 'New PIN',
        'save' => 'Save Changes',
        'admin' => 'User Profile'
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
        * { box-sizing: border-box; font-family: 'Segoe UI', Roboto, Arial, sans-serif; }
        body { margin: 0; padding: 0; background: #f4f6f9; min-height: 100vh; padding-bottom: 30px; }
        
        /* প্রোফাইল হেডার স্টাইল */
        .top-profile-bar { background: linear-gradient(135deg, #007bff, #00c6ff); color: white; padding: 25px 20px; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; }
        .profile-container { display: flex; align-items: center; gap: 15px; }
        .avatar-box { width: 60px; height: 60px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; color: #007bff; border: 3px solid rgba(255,255,255,0.4); }
        .profile-info .role { font-size: 12px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; }
        .profile-info .name { font-size: 20px; font-weight: bold; margin-top: 2px; }
        
        /* ভাষা পরিবর্তনের বাটন */
        .lang-btn { position: absolute; top: 20px; right: 20px; background: rgba(255, 255, 255, 0.2); color: white; padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 12px; font-weight: bold; backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.3); }
        
        .main-container { padding: 20px; max-width: 500px; margin: 0 auto; }
        .portal-title { font-size: 18px; font-weight: 700; color: #444; margin: 15px 0; text-align: left; display: flex; align-items: center; gap: 8px; }
        
        /* মোবাইল অ্যাপের মত কালারফুল বাটন গ্রিড */
        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 25px; }
        .menu-card { background: white; padding: 20px 15px; border-radius: 18px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.04); transition: transform 0.2s; position: relative; overflow: hidden; border: 1px solid #edf2f7; }
        .menu-card:active { transform: scale(0.95); }
        
        /* আইকন ও চমৎকার কালার প্যালেট (অটোমেটিক কালার চক্রের জন্য CSS এনথ-চাইল্ড) */
        .card-icon { font-size: 28px; margin-bottom: 12px; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; }
        
        .menu-card:nth-child(4n+1) .card-icon { background: linear-gradient(135deg, #ff416c, #ff4b2b); }
        .menu-card:nth-child(4n+2) .card-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .menu-card:nth-child(4n+3) .card-icon { background: linear-gradient(135deg, #ff9900, #ff5500); }
        .menu-card:nth-child(4n+4) .card-icon { background: linear-gradient(135deg, #7f00ff, #e100ff); }
        
        .card-text { font-size: 14px; font-weight: 600; color: #4a5568; }

        /* পিন কোড সেটিংস বক্স */
        .settings-box { background: white; padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); margin-top: 20px; border: 1px solid #edf2f7; }
        .settings-title { font-size: 15px; font-weight: bold; color: #4a5568; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 12px; color: #718096; margin-bottom: 5px; font-weight: 600; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f7fafc; }
        .form-control:focus { border-color: #007bff; background: #fff; }
        .submit-btn { width: 100%; background: #4a5568; color: white; border: none; padding: 11px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        .submit-btn:active { background: #2d3748; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 10px; font-size: 13px; margin-bottom: 15px; text-align: center; }

        /* লগআউট বাটন */
        .logout-container { text-align: center; margin-top: 30px; }
        .logout-btn { display: inline-flex; align-items: center; gap: 8px; color: #e53e3e; text-decoration: none; font-weight: bold; font-size: 15px; padding: 10px 20px; background: #fff1f1; border-radius: 12px; border: 1px solid #fed7d7; }
    </style>
</head>
<body>

    <div class="top-profile-bar">
        <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-btn">
            <i class="fa-solid fa-earth-americas"></i> <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
        </a>
        <div class="profile-container">
            <div class="avatar-box">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div class="profile-info">
                <div class="role"><?php echo $text[$lang]['admin']; ?></div>
                <div class="name">Sabbir Ahmed</div> </div>
        </div>
    </div>

    <div class="main-container">
        
        <div class="portal-title">
            <i class="fa-solid fa-layer-group" style="color: #007bff;"></i> 
            <?php echo $text[$lang]['title']; ?>
        </div>

        <div class="menu-grid">
            <?php
            $res = $conn->query("SELECT * FROM isp_links ORDER BY id DESC");
            if($res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $display_name = ($lang == 'bn') ? $row['name_bn'] : $row['name_en'];
                    
                    // ডেটাবেজ থেকে যদি আইকন নাম সেট করা না থাকে, তবে ডিফল্ট আইকন সেট হবে
                    $icon = (!empty($row['icon'])) ? $row['icon'] : 'fa-solid fa-link';
                    
                    echo '<a href="'.$row['url'].'" class="menu-card" target="_blank">';
                    echo '  <div class="card-icon"><i class="'.$icon.'"></i></div>';
                    echo '  <div class="card-text">'.$display_name.'</div>';
                    echo '</a>';
                }
            } else {
                echo '<p style="grid-column: span 2; text-align:center; color:gray; padding:20px;">'.$text[$lang]['no_link'].'</p>';
            }
            ?>
        </div>

        <div class="settings-box">
            <div class="settings-title">
                <i class="fa-solid fa-key" style="color: #ff9900;"></i> 
                <?php echo $text[$lang]['settings']; ?>
            </div>
            
            <?php if(!empty($msg)) { echo '<div class="alert-success">'.$msg.'</div>'; } ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label><?php echo $text[$lang]['current_pin']; ?></label>
                    <input type="password" name="current_pin" class="form-control" placeholder="••••" required>
                </div>
                <div class="form-group">
                    <label><?php echo $text[$lang]['new_pin']; ?></label>
                    <input type="password" name="new_pin" class="form-control" placeholder="••••" required>
                </div>
                <button type="submit" name="change_pin" class="submit-btn">
                    <i class="fa-solid fa-circle-check"></i> <?php echo $text[$lang]['save']; ?>
                </button>
            </form>
        </div>

        <div class="logout-container">
            <a href="logout.php" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> <?php echo $text[$lang]['logout']; ?>
            </a>
        </div>

    </div>

</body>
</html>
<?php 
ob_end_flush(); // বাফার শেষ করে পেজ রেন্ডার করা হলো
?>
