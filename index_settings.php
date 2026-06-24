<?php
ob_start();
session_start();
if(!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
include 'db.php';

// ল্যাঙ্গুয়েজ সিলেকশন
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn';

// টেক্সট অ্যারে
$text = [
    'bn' => [
        'title' => 'লিংক সেটিংস', 
        'btn_add' => 'নতুন বাটন যোগ করুন', 
        'edit' => 'এডিট', 
        'del' => 'ডিলিট', 
        'save' => 'তথ্য আপডেট করুন', 
        'msg_add' => 'বাটন সফলভাবে যোগ হয়েছে!', 
        'msg_upd' => 'বাটন সফলভাবে আপডেট হয়েছে!', 
        'msg_del' => 'বাটনটি ডিলিট করা হয়েছে!', 
        'back' => 'হোম ড্যাশবোর্ড',
        'admin_panel' => 'অ্যাডমিন প্যানেল',
        'current_links' => 'বর্তমানে সচল লিংকসমূহ',
        'placeholder_bn' => 'বাটনের বাংলা নাম',
        'placeholder_en' => 'Button English Name',
        'placeholder_url' => 'লিংক ইউআরএল (https://...)',
        'close' => 'বন্ধ করুন'
    ],
    'en' => [
        'title' => 'Link Settings', 
        'btn_add' => 'Add New Button', 
        'edit' => 'Edit', 
        'del' => 'Delete', 
        'save' => 'Save Changes', 
        'msg_add' => 'Button added successfully!', 
        'msg_upd' => 'Button updated successfully!', 
        'msg_del' => 'Button has been deleted!', 
        'back' => 'Home Dashboard',
        'admin_panel' => 'Admin Panel',
        'current_links' => 'Currently Active Links',
        'placeholder_bn' => 'Button Bangla Name',
        'placeholder_en' => 'Button English Name',
        'placeholder_url' => 'Link URL (https://...)',
        'close' => 'Close'
    ]
];

$message = "";

// লিংক আপডেট করা
if(isset($_POST['update_link'])) {
    $id = (int)$_POST['id'];
    $name_bn = $conn->real_escape_string($_POST['name_bn']);
    $name_en = $conn->real_escape_string($_POST['name_en']);
    $url = $conn->real_escape_string($_POST['url']);
    $conn->query("UPDATE isp_links SET name_bn='$name_bn', name_en='$name_en', url='$url' WHERE id=$id");
    $message = "<div class='alert alert-success'><i class='fa-regular fa-circle-check'></i> {$text[$lang]['msg_upd']}</div>";
}

// লিংক যোগ করা
if(isset($_POST['add_link'])) {
    $name_bn = $conn->real_escape_string($_POST['name_bn']);
    $name_en = $conn->real_escape_string($_POST['name_en']);
    $url = $conn->real_escape_string($_POST['url']);
    $conn->query("INSERT INTO isp_links (name_bn, name_en, url) VALUES ('$name_bn', '$name_en', '$url')");
    $message = "<div class='alert alert-success'><i class='fa-regular fa-circle-check'></i> {$text[$lang]['msg_add']}</div>";
}

// ডিলিট করা
if(isset($_GET['delete'])) {
    $conn->query("DELETE FROM isp_links WHERE id=".(int)$_GET['delete']);
    $message = "<div class='alert alert-error'><i class='fa-solid fa-trash-can'></i> {$text[$lang]['msg_del']}</div>";
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
        .card { background: white; width: 100%; border-radius: 18px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #edf2f7; margin-bottom: 20px; }
        
        .section-title { font-size: 13px; font-weight: bold; color: #4a5568; margin: 5px 0 15px 0; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .form-group { margin-bottom: 12px; text-align: left; }
        .form-control { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f7fafc; transition: 0.2s; }
        .form-control:focus { border-color: #2a5298; background: #fff; }
        
        /* বাটনসমূহ */
        .submit-btn { width: 100%; background: #007bff; color: white; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .submit-btn:active { transform: scale(0.97); }
        
        /* লিংক লিস্ট সেকশন */
        .link-list { max-height: 380px; overflow-y: auto; padding-right: 5px; }
        /* কাস্টম স্ক্রলবার */
        .link-list::-webkit-scrollbar { width: 4px; }
        .link-list::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        
        .link-item { border-bottom: 1px solid #edf2f7; padding: 14px 0; position: relative; }
        .link-item:last-child { border-bottom: none; padding-bottom: 0; }
        .link-title { font-size: 14px; font-weight: bold; color: #2d3748; margin-bottom: 2px; display: flex; align-items: center; gap: 6px; }
        .link-url { font-size: 11px; color: #718096; word-break: break-all; display: block; margin-bottom: 8px; }
        
        .action-row { display: flex; gap: 10px; }
        .btn-action { padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; border: none; cursor: pointer; }
        .btn-edit { background: #fff8e6; color: #dd9000; border: 1px solid #ffe8b3; }
        .btn-delete { background: #fff1f1; color: #e53e3e; border: 1px solid #fed7d7; }
        
        /* ইনলাইন এডিট ফর্ম */
        .edit-form { display: none; background: #f7fafc; padding: 15px; border-radius: 12px; margin-top: 10px; border: 1px solid #e2e8f0; animation: slideDown 0.2s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        
        /* অ্যালার্ট নোটিফিকেশন */
        .alert { padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* ব্যাক টু ড্যাশবোর্ড গ্রিড বাটন */
        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 5px; }
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
                <i class="fa-solid fa-link"></i>
            </div>
            <div class="admin-title"><?php echo $text[$lang]['admin_panel']; ?></div>
            <div class="admin-name"><?php echo $text[$lang]['title']; ?></div>
        </div>
    </div>

    <!-- মূল বডি কন্টেইনার -->
    <div class="container">
        
        <!-- মেথড মেসেজ (সাকসেস/এরর) -->
        <?php if(!empty($message)) { echo $message; } ?>

        <!-- লিংক যোগ করার ফর্ম কার্ড -->
        <div class="card">
            <div class="section-title">
                <i class="fa-solid fa-circle-plus" style="color: #007bff;"></i>
                <?php echo $text[$lang]['btn_add']; ?>
            </div>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="name_bn" class="form-control" placeholder="<?php echo $text[$lang]['placeholder_bn']; ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" name="name_en" class="form-control" placeholder="<?php echo $text[$lang]['placeholder_en']; ?>" required>
                </div>
                <div class="form-group">
                    <input type="url" name="url" class="form-control" placeholder="<?php echo $text[$lang]['placeholder_url']; ?>" required>
                </div>
                <button type="submit" name="add_link" class="submit-btn">
                    <i class="fa-solid fa-plus"></i> <?php echo $text[$lang]['btn_add']; ?>
                </button>
            </form>
        </div>

        <!-- বর্তমান সক্রিয় লিংকগুলোর লিস্ট কার্ড -->
        <div class="card">
            <div class="section-title">
                <i class="fa-solid fa-list-check" style="color: #11998e;"></i>
                <?php echo $text[$lang]['current_links']; ?>
            </div>
            
            <div class="link-list">
                <?php
                $res = $conn->query("SELECT * FROM isp_links ORDER BY id DESC");
                if($res && $res->num_rows > 0) {
                    while($row = $res->fetch_assoc()) {
                        $display_name = ($lang == 'bn') ? $row['name_bn'] : $row['name_en'];
                        $icon = (!empty($row['icon'])) ? $row['icon'] : 'fa-solid fa-network-wired';
                        
                        echo "<div class='link-item'>
                            <div class='link-title'><i class='{$icon}' style='color:#2a5298; font-size:12px;'></i> " . htmlspecialchars($display_name) . "</div>
                            <span class='link-url'>" . htmlspecialchars($row['url']) . "</span>
                            
                            <div class='action-row'>
                                <button class='btn-action btn-edit' onclick='toggleEditForm({$row['id']})'>
                                    <i class='fa-regular fa-pen-to-square'></i> {$text[$lang]['edit']}
                                </button>
                                <a href='?delete={$row['id']}' class='btn-action btn-delete' onclick='return confirm(\"Are you sure?\")'>
                                    <i class='fa-regular fa-trash-can'></i> {$text[$lang]['del']}
                                </a>
                            </div>
                            
                            <!-- ইনলাইন স্লাইড ডাউন এডিট ফর্ম -->
                            <div id='edit{$row['id']}' class='edit-form'>
                                <form method='POST'>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <div class='form-group'>
                                        <input type='text' name='name_bn' class='form-control' value='" . htmlspecialchars($row['name_bn']) . "' required>
                                    </div>
                                    <div class='form-group'>
                                        <input type='text' name='name_en' class='form-control' value='" . htmlspecialchars($row['name_en']) . "' required>
                                    </div>
                                    <div class='form-group'>
                                        <input type='url' name='url' class='form-control' value='" . htmlspecialchars($row['url']) . "' required>
                                    </div>
                                    <div style='display:flex; gap:8px;'>
                                        <button type='submit' name='update_link' class='submit-btn' style='padding:9px; font-size:13px;'>
                                            <i class='fa-solid fa-circle-check'></i> {$text[$lang]['save']}
                                        </button>
                                        <button type='button' class='submit-btn' style='padding:9px; font-size:13px; background:#6c757d;' onclick='toggleEditForm({$row['id']})'>
                                            {$text[$lang]['close']}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<p style='text-align:center; color:gray; font-size:13px; padding:10px 0;'>No links found.</p>";
                }
                ?>
            </div>
        </div>

        <!-- ড্যাশবোর্ডে ফিরে যাওয়ার ৩ নম্বর কার্ড লেআউট -->
        <div class="menu-grid">
            <a href="admin.php" class="menu-card" style="grid-column: span 4;">
                <div class="card-icon"><i class="fa-solid fa-house-user"></i></div>
                <div class="card-text"><?php echo $text[$lang]['back']; ?></div>
            </a>
        </div>

    </div>

    <!-- জাভাস্ক্রিপ্ট কন্ট্রোলার -->
    <script>
        function toggleEditForm(id) {
            const form = document.getElementById('edit' + id);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                form.style.display = 'block';
            }
        }
    </script>
</body>
</html>
<?php 
ob_end_flush(); 
?>
