<?php
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

// টেক্সট অ্যারে
$text = [
    'bn' => ['title' => 'সাব্বির নেট পোর্টাল', 'logout' => 'লগ আউট', 'no_link' => 'বর্তমানে কোনো লিংক নেই'],
    'en' => ['title' => 'Sabbir-Net Portal', 'logout' => 'Logout', 'no_link' => 'No links available']
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
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background: #f0f2f5; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        .lang-btn { background: #6c757d; color: white; padding: 5px 15px; border-radius: 20px; text-decoration: none; font-size: 12px; margin-bottom: 20px; }
        .header { margin-bottom: 20px; font-weight: bold; color: #333; font-size: 20px; }
        .btn-container { width: 100%; max-width: 400px; display: grid; gap: 15px; }
        .btn { background: white; padding: 20px; border-radius: 15px; text-decoration: none; color: #333; font-weight: 600; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #007bff; transition: 0.3s; }
        .btn:active { transform: scale(0.98); background: #f8f9fa; }
        .logout-btn { margin-top: 30px; color: #ff4757; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

    <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-btn">
        <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
    </a>

    <div class="header"><?php echo $text[$lang]['title']; ?></div>

    <div class="btn-container">
        <?php
        $res = $conn->query("SELECT * FROM isp_links ORDER BY id DESC");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $display_name = ($lang == 'bn') ? $row['name_bn'] : $row['name_en'];
                echo '<a href="'.htmlspecialchars($row['url']).'" class="btn" target="_blank">'.htmlspecialchars($display_name).'</a>';
            }
        } else {
            echo '<p style="text-align:center; color:gray;">'.$text[$lang]['no_link'].'</p>';
        }
        ?>
    </div>

    <a href="logout.php" class="logout-btn"><?php echo $text[$lang]['logout']; ?></a>

</body>
</html>
