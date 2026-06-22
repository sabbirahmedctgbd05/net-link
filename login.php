<?php
session_start();
if(isset($_SESSION['user_logged_in'])) { 
    header("Location: index.php"); 
    exit; 
}
include 'db.php';

// ল্যাঙ্গুয়েজ সিলেকশন হ্যান্ডেল করা
if(isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn';

// টেক্সট অ্যারে
$text = [
    'bn' => ['title' => 'সিস্টেমে প্রবেশ', 'placeholder' => 'পিনকোড লিখুন', 'btn' => 'লগইন করুন', 'error' => '❌ পিনকোডটি ভুল!'],
    'en' => ['title' => 'Login System', 'placeholder' => 'Enter PIN Code', 'btn' => 'Login', 'error' => '❌ Invalid PIN Code!']
];

if(isset($_POST['submit_pin'])) {
    $entered_pin = $_POST['pin'];
    $stmt = $conn->prepare("SELECT * FROM user_pins WHERE pin_code = ?");
    $stmt->bind_param("s", $entered_pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $_SESSION['user_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = $text[$lang]['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Language" content="<?php echo $lang; ?>">
    <title>Sabbir-Net Login</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 20px; background: #f0f2f5; height: 100vh; display: flex; justify-content: center; align-items: center; }
        .lang-btn { position: absolute; top: 20px; right: 20px; background: #6c757d; color: white; padding: 5px 15px; border-radius: 20px; text-decoration: none; font-size: 12px; }
        .login-card { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; width: 100%; max-width: 350px; }
        h2 { color: #1a1a1a; margin-bottom: 25px; font-size: 1.5rem; }
        input { width: 100%; padding: 15px; margin-bottom: 20px; border: 2px solid #e1e8ed; border-radius: 12px; font-size: 18px; text-align: center; outline: none; background: #fafafa; }
        input:focus { border-color: #007bff; background: #fff; }
        button { width: 100%; padding: 15px; background: #007bff; color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .error { color: #d9534f; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-btn">
        <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
    </a>

    <div class="login-card">
        <h2><?php echo $text[$lang]['title']; ?></h2>
        <form method="POST">
            <input type="password" name="pin" placeholder="<?php echo $text[$lang]['placeholder']; ?>" required autofocus>
            <button type="submit" name="submit_pin"><?php echo $text[$lang]['btn']; ?></button>
        </form>
        <?php if(isset($error)) echo '<p class="error">'.$error.'</p>'; ?>
    </div>

</body>
</html>
