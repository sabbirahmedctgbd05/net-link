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
        'title' => 'প্রোফাইল সেটিংস', 
        'name' => 'পুরো নাম', 
        'email' => 'ইমেইল এড্রেস', 
        'phone' => 'মোবাইল নাম্বার', 
        'old_pin' => 'বর্তমান পিন কোড (যাচাইয়ের জন্য)', 
        'new_pin' => 'নতুন পিন কোড (পরিবর্তন করতে চাইলে)', 
        'btn' => 'তথ্য সংরক্ষণ করুন', 
        'success' => 'প্রোফাইল ও পিন সফলভাবে আপডেট হয়েছে!',
        'invalid_pin' => 'বর্তমান পিনটি সঠিক নয়!',
        'back' => 'হোম ড্যাশবোর্ড',
        'profile' => 'অ্যাডমিন প্রোফাইল'
    ],
    'en' => [
        'title' => 'Profile Settings', 
        'name' => 'Full Name', 
        'email' => 'Email Address', 
        'phone' => 'Mobile Number', 
        'old_pin' => 'Current PIN Code (For Verification)', 
        'new_pin' => 'New PIN Code (Optional)', 
        'btn' => 'Save Changes', 
        'success' => 'Profile & PIN updated successfully!',
        'invalid_pin' => 'Current PIN is incorrect!',
        'back' => 'Home Dashboard',
        'profile' => 'Admin Profile'
    ]
];

$message = "";

// বর্তমান তথ্য আনা
$admin = $conn->query("SELECT * FROM admin_pins LIMIT 1")->fetch_assoc();

// আপডেট লজিক
if (isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $old_pin = $_POST['old_pin'];
    $new_pin = !empty($_POST['new_pin']) ? $_POST['new_pin'] : $old_pin;

    // বর্তমান পিনটি ডাটাবেজের পিনের সাথে মিলছে কিনা তা যাচাই করা হচ্ছে
    if ($admin && $old_pin === $admin['pin_code']) {
        $stmt = $conn->prepare("UPDATE admin_pins SET full_name=?, email=?, phone=?, pin_code=? WHERE pin_code=?");
        $stmt->bind_param("sssss", $full_name, $email, $phone, $new_pin, $old_pin);
        
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'><i class='fa-regular fa-circle-check'></i> {$text[$lang]['success']}</div>";
            // আপডেটের পর নতুন ডাটা রিফ্রেশ করা হচ্ছে
            $admin = $conn->query("SELECT * FROM admin_pins LIMIT 1")->fetch_assoc();
        }
    } else {
        $message = "<div class='alert alert-error'><i class='fa-solid fa-triangle-exclamation'></i> {$text[$lang]['invalid_pin']}</div>";
    }
}
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

        /* নিচের কন্টেইনার ও ফর্ম কার্ড */
        .container { width: 100%; max-width: 450px; padding: 20px; }
        .card { background: white; width: 100%; border-radius: 18px; padding: 22px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #edf2f7; }
        
        .form-group { margin-bottom: 16px; text-align: left; }
        .form-group label { display: block; font-size: 12px; color: #718096; margin-bottom: 6px; font-weight: 600; }
        .form-control { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f7fafc; transition: 0.2s; }
        .form-control:focus { border-color: #2a5298; background: #fff; }
        
        /* বাটন ও নোটিফিকেশন অ্যালার্ট */
        .submit-btn { width: 100%; background: #007bff; color: white; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .submit-btn:active { transform: scale(0.97); }
        
        .alert { padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* ব্যাক গ্রিড মেনু */
        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 20px; }
        .menu-card { background: white; padding: 12px 5px; border-radius: 14px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #edf2f7; transition: transform 0.2s; min-height: 90px; }
        .menu-card:active { transform: scale(0.93); }
        
        .card-icon { font-size: 16px; margin-bottom: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; background: linear-gradient(135deg, #7f00ff, #e100ff); }
        .card-text { font-size: 10px; font-weight: 700; color: #4a5568; line-height: 1.2; }
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
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div class="admin-title"><?php echo $text[$lang]['title']; ?></div>
            <div class="admin-name"><?php echo htmlspecialchars($admin['full_name'] ?? 'Sabbir Ahmed'); ?></div>
        </div>
    </div>

    <!-- মূল বডি কন্টেইনার -->
    <div class="container">
        
        <div class="card">
            <!-- মেথড মেসেজ (সাকসেস/এরর) -->
            <?php echo $message; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="fa-regular fa-user"></i> <?php echo $text[$lang]['name']; ?></label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($admin['full_name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fa-regular fa-envelope"></i> <?php echo $text[$lang]['email']; ?></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fa-solid fa-phone"></i> <?php echo $text[$lang]['phone']; ?></label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" required>
                </div>
                
                <hr style="border: 0; border-top: 1px dashed #e2e8f0; margin: 20px 0;">
                
                <div class="form-group">
                    <label style="color: #e53e3e;"><i class="fa-solid fa-shield"></i> <?php echo $text[$lang]['old_pin']; ?></label>
                    <input type="password" name="old_pin" class="form-control" placeholder="••••" required>
                </div>
                
                <div class="form-group">
                    <label style="color: #38ef7d;"><i class="fa-solid fa-key"></i> <?php echo $text[$lang]['new_pin']; ?></label>
                    <input type="password" name="new_pin" class="form-control" placeholder="••••">
                </div>
                
                <button type="submit" name="update_profile" class="submit-btn">
                    <i class="fa-solid fa-floppy-disk"></i> <?php echo $text[$lang]['btn']; ?>
                </button>
            </form>
        </div>

        <!-- ফিরে যাওয়ার জন্য নিচের ডাইনামিক গ্রিড বাটন -->
        <div class="menu-grid">
            <a href="admin.php" class="menu-card" style="grid-column: span 4;">
                <div class="card-icon"><i class="fa-solid fa-house-user"></i></div>
                <div class="card-text"><?php echo $text[$lang]['back']; ?></div>
            </a>
        </div>

    </div>

</body>
</html>
<?php 
ob_end_flush(); 
?>
