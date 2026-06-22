<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
include 'db.php';

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn';

// টেক্সট অ্যারে
$text = [
    'bn' => ['title' => 'ইন্ডেক্স সেটিং', 'btn_add' => 'বাটন যোগ করুন', 'edit' => 'এডিট', 'del' => 'ডিলিট', 'save' => 'আপডেট করুন', 'msg_add' => '✅ যোগ হয়েছে!', 'msg_upd' => '✅ আপডেট হয়েছে!', 'msg_del' => '❌ ডিলিট হয়েছে!', 'back' => 'ড্যাশবোর্ডে ফিরে যান'],
    'en' => ['title' => 'Index Settings', 'btn_add' => 'Add Button', 'edit' => 'Edit', 'del' => 'Delete', 'save' => 'Update', 'msg_add' => '✅ Added!', 'msg_upd' => '✅ Updated!', 'msg_del' => '❌ Deleted!', 'back' => 'Back to Dashboard']
];

$message = "";

// লিংক আপডেট করা
if(isset($_POST['update_link'])) {
    $id = (int)$_POST['id'];
    $name_bn = $conn->real_escape_string($_POST['name_bn']);
    $name_en = $conn->real_escape_string($_POST['name_en']);
    $url = $conn->real_escape_string($_POST['url']);
    $conn->query("UPDATE isp_links SET name_bn='$name_bn', name_en='$name_en', url='$url' WHERE id=$id");
    $message = "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:10px; text-align:center; margin-bottom:10px;'>{$text[$lang]['msg_upd']}</div>";
}

// লিংক যোগ করা
if(isset($_POST['add_link'])) {
    $conn->query("INSERT INTO isp_links (name_bn, name_en, url) VALUES ('".$conn->real_escape_string($_POST['name_bn'])."', '".$conn->real_escape_string($_POST['name_en'])."', '".$conn->real_escape_string($_POST['url'])."')");
    $message = "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:10px; text-align:center;'>{$text[$lang]['msg_add']}</div>";
}

// ডিলিট করা
if(isset($_GET['delete'])) {
    $conn->query("DELETE FROM isp_links WHERE id=".(int)$_GET['delete']);
    $message = "<div style='background:#f8d7da; color:#721c24; padding:10px; border-radius:10px; text-align:center;'>{$text[$lang]['msg_del']}</div>";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ব্রাউজারের অটো-ট্রান্সলেট বন্ধ করার জন্য -->
    <meta name="google" content="notranslate">
    <title>Index Settings</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 15px; }
        .card { background: #fff; padding: 15px; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        input { width: 100%; padding: 12px; margin: 5px 0; border: 1px solid #ddd; border-radius: 10px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 10px; cursor: pointer; }
        .edit-form { display: none; background: #f9f9f9; padding: 10px; border-radius: 10px; margin-top: 5px; }
        .btn-edit { background: #ffc107; padding: 5px 10px; border-radius: 5px; color: black; text-decoration: none; font-size: 12px; cursor: pointer; border: none; }
        .back-btn { display: block; text-align: center; margin-top: 20px; padding: 12px; background: #6c757d; color: white; text-decoration: none; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h3><?php echo $text[$lang]['title']; ?></h3>
        <?php echo $message; ?>
        <form method="POST">
            <input type="text" name="name_bn" placeholder="বাংলা নাম" required>
            <input type="text" name="name_en" placeholder="English Name" required>
            <input type="url" name="url" placeholder="https://..." required>
            <button type="submit" name="add_link"><?php echo $text[$lang]['btn_add']; ?></button>
        </form>
    </div>

    <div class="card" style="max-height: 400px; overflow-y: auto;">
        <?php
        $res = $conn->query("SELECT * FROM isp_links ORDER BY id DESC");
        while($row = $res->fetch_assoc()) {
            echo "<div style='border-bottom:1px solid #eee; padding:10px 0;'>
                <strong>".($lang=='bn'?$row['name_bn']:$row['name_en'])."</strong><br>
                <small>{$row['url']}</small><br>
                <button class='btn-edit' onclick='document.getElementById(\"edit{$row['id']}\").style.display=\"block\"'>{$text[$lang]['edit']}</button>
                <a href='?delete={$row['id']}' style='color:red; font-size:12px; margin-left:10px; text-decoration:none;'>{$text[$lang]['del']}</a>
                
                <div id='edit{$row['id']}' class='edit-form'>
                    <form method='POST'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='text' name='name_bn' value='{$row['name_bn']}'>
                        <input type='text' name='name_en' value='{$row['name_en']}'>
                        <input type='url' name='url' value='{$row['url']}'>
                        <button type='submit' name='update_link'>{$text[$lang]['save']}</button>
                    </form>
                </div>
            </div>";
        }
        ?>
    </div>
    <a href="admin.php" class="back-btn"><?php echo $text[$lang]['back']; ?></a>
</body>
</html>
