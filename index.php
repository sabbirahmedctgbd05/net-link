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

// সেশন থেকে লগইন থাকা ইউজারের আইডি নেওয়া
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; 

// প্রোফাইল তথ্য এবং পিন কোড আপডেট লজিক
$msg = '';
$msg_type = '';

if(isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $current_pin = mysqli_real_escape_string($conn, $_POST['current_pin']);
    $new_pin = mysqli_real_escape_string($conn, $_POST['new_pin']);
    
    // প্রথমে ডাটাবেজ থেকে বর্তমান পিন চেক করা (স্ক্রিনশট অনুযায়ী কলামের নাম pin_code)
    $check_query = $conn->query("SELECT * FROM users WHERE id = '$user_id' AND pin_code = '$current_pin'");
    
    if($check_query && $check_query->num_rows > 0) {
        // বর্তমান পিন মিললে প্রোফাইল তথ্য এবং নতুন পিন একসঙ্গে আপডেট হবে
        // আপনার ডাটাবেজের টেবিল অনুযায়ী কলামের নাম (username, email, phone, pin_code) নিশ্চিত করে নেবেন
        $update_sql = "UPDATE users SET 
                        username = '$username', 
                        email = '$email', 
                        phone = '$phone'";
                        
        // ইউজার যদি নতুন পিন কোড ইনপুট দেয়, তবেই পিন কোড আপডেট হবে
        if(!empty($new_pin)) {
            $update_sql .= ", pin_code = '$new_pin'";
        }
        
        $update_sql .= " WHERE id = '$user_id'";
        
        if($conn->query($update_sql)) {
            $msg = ($lang == 'bn') ? 'প্রোফাইল ও পিন সফলভাবে আপডেট হয়েছে!' : 'Profile & PIN updated successfully!';
            $msg_type = 'success';
        } else {
            $msg = ($lang == 'bn') ? 'আপডেট করা সম্ভব হয়নি। আবার চেষ্টা করুন।' : 'Update failed. Please try again.';
            $msg_type = 'error';
        }
    } else {
        $msg = ($lang == 'bn') ? 'বর্তমান পিনটি সঠিক নয়!' : 'Current PIN is incorrect!';
        $msg_type = 'error';
    }
}

// ফরম পূরণের সুবিধার্থে ডাটাবেজ থেকে ইউজারের বর্তমান তথ্য তুলে আনা
$user_data = [];
$user_res = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
if($user_res && $user_res->num_rows > 0) {
    $user_data = $user_res->fetch_assoc();
}

// ডাটাবেজে তথ্য না থাকলে বা কলামের নাম ভিন্ন হলে এরর এড়াতে ডিফল্ট ভ্যালু সেট করা
$current_username = isset($user_data['username']) ? $user_data['username'] : 'Sabbir Ahmed';
$current_email    = isset($user_data['email']) ? $user_data['email'] : 'sabbir@example.com';
$current_phone    = isset($user_data['phone']) ? $user_data['phone'] : '01700000000';

// টেক্সট অ্যারে
$text = [
    'bn' => [
        'title' => 'সাব্বির নেট পোর্টাল', 
        'logout' => 'লগ আউট', 
        'no_link' => 'বর্তমানে কোনো লিংক নেই',
        'profile_settings' => 'প্রোফাইল ও পিন সেটিংস',
        'lbl_username' => 'ইউজার নেম',
        'lbl_email' => 'ইমেইল এড্রেস',
        'lbl_phone' => 'মোবাইল নাম্বার',
        'lbl_cur_pin' => 'বর্তমান পিন কোড (যাচাইয়ের জন্য)',
        'lbl_new_pin' => 'নতুন পিন কোড (পরিবর্তন করতে চাইলে)',
        'save' => 'তথ্য সংরক্ষণ করুন',
        'profile' => 'গ্রাহক প্রোফাইল'
    ],
    'en' => [
        'title' => 'Sabbir-Net Portal', 
        'logout' => 'Logout', 
        'no_link' => 'No links available',
        'profile_settings' => 'Profile & PIN Settings',
        'lbl_username' => 'Username',
        'lbl_email' => 'Email Address',
        'lbl_phone' => 'Mobile Number',
        'lbl_cur_pin' => 'Current PIN Code (For Verification)',
        'lbl_new_pin' => 'New PIN Code (Optional)',
        'save' => 'Save Changes',
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
        
        /* মোবাইল অ্যাপ স্টাইল টপ প্রোফাইল হেডার */
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
        
        /* মোবাইল সফটওয়্যার ভিত্তিক গ্রিড মেনু */
        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .menu-card { background: white; padding: 20px 15px; border-radius: 15px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #edf2f7; transition: transform 0.2s; }
        .menu-card:active { transform: scale(0.95); }
        
        /* কালার থিম */
        .card-icon { font-size: 24px; margin-bottom: 10px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; }
        .menu-card:nth-child(4n+1) .card-icon { background: linear-gradient(135deg, #ff416c, #ff4b2b); }
        .menu-card:nth-child(4n+2) .card-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .menu-card:nth-child(4n+3) .card-icon { background: linear-gradient(135deg, #ff9900, #ff5500); }
        .menu-card:nth-child(4n+4) .card-icon { background: linear-gradient(135deg, #7f00ff, #e100ff); }
        
        .card-text { font-size: 13px; font-weight: 600; color: #4a5568; }

        /* প্রোফাইল তথ্য ও পিন কোড সেটিংস বক্স */
        .settings-box { background: white; padding: 20px; border-radius: 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-top: 25px; border: 1px solid #edf2f7; }
        .settings-title { font-size: 15px; font-weight: bold; color: #4a5568; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; color: #718096; margin-bottom: 6px; font-weight: 600; }
        .form-control { width: 100%; padding: 11px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f7fafc; transition: 0.2s; }
        .form-control:focus { border-color: #007bff; background: #fff; }
        
        .submit-btn { width: 100%; background: #007bff; color: white; border: none; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .submit-btn:active { background: #0056b3; }
        
        /* নোটিফিকেশন অ্যালার্ট */
        .alert { padding: 11px; border-radius: 10px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* লগআউট বাটন */
        .logout-box { text-align: center; margin-top: 25px; width: 100%; }
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
                <div class="user-name"><?php echo htmlspecialchars($current_username); ?></div>
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
                    
                    // ডেটাবেজে আইকন ফিল্ড থাকলে সেটা পাবে, না থাকলে ডিফল্ট আইকন শো করবে
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
        </div>

        <div class="settings-box">
            <div class="settings-title">
                <i class="fa-solid fa-user-slider" style="color: #ff9900;"></i> 
                <?php echo $text[$lang]['profile_settings']; ?>
            </div>
            
            <?php if(!empty($msg)) { ?>
                <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
            <?php } ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label><i class="fa-regular fa-user"></i> <?php echo $text[$lang]['lbl_username']; ?></label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($current_username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fa-regular fa-envelope"></i> <?php echo $text[$lang]['lbl_email']; ?></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($current_email); ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fa-solid fa-phone-clip"></i> <?php echo $text[$lang]['lbl_phone']; ?></label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($current_phone); ?>" required>
                </div>
                
                <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 20px 0;">
                
                <div class="form-group">
                    <label style="color: #e53e3e;"><i class="fa-solid fa-shield"></i> <?php echo $text[$lang]['lbl_cur_pin']; ?></label>
                    <input type="password" name="current_pin" class="form-control" placeholder="••••" required>
                </div>
                
                <div class="form-group">
                    <label style="color: #38ef7d;"><i class="fa-solid fa-key"></i> <?php echo $text[$lang]['lbl_new_pin']; ?></label>
                    <input type="password" name="new_pin" class="form-control" placeholder="••••">
                </div>
                
                <button type="submit" name="update_profile" class="submit-btn">
                    <i class="fa-solid fa-floppy-disk"></i> <?php echo $text[$lang]['save']; ?>
                </button>
            </form>
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
