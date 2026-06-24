<?php
ob_start();
session_start();
if(!isset($_SESSION['admin_logged_in'])) { header("Location: admin_login.php"); exit; }
include 'db.php';

// ল্যাঙ্গুয়েজ সিলেকশন
if (isset($_GET['lang'])) { $_SESSION['lang'] = $_GET['lang']; }
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'bn';

// টেক্সট ডিকশনারি
$text = [
    'bn' => [
        'title' => 'ইউজার সেটিংস',
        'btn_add' => 'নতুন ইউজার যোগ করুন',
        'edit' => 'এডিট',
        'del' => 'ডিলিট',
        'save' => 'আপডেট করুন',
        'msg_add' => 'ইউজার সফলভাবে তৈরি হয়েছে!',
        'msg_upd' => 'ইউজার তথ্য আপডেট হয়েছে!',
        'msg_del' => 'ইউজার ডিলিট করা হয়েছে!',
        'back' => 'হোম ড্যাশবোর্ড',
        'admin_panel' => 'অ্যাডমিন কন্ট্রোল',
        'current_users' => 'নিবন্ধিত ইউজার তালিকা',
        'th_name' => 'ইউজার আইডি',
        'th_phone' => 'মোবাইল',
        'th_pin' => 'পিন কোড',
        'th_action' => 'অ্যাকশন',
        'u_name' => 'ইউজারনেম / লগইন আইডি',
        'u_phone' => 'মোবাইল নাম্বার',
        'u_pin' => 'লগইন পিন কোড',
        'close' => 'বন্ধ করুন'
    ],
    'en' => [
        'title' => 'User Settings',
        'btn_add' => 'Add New User',
        'edit' => 'Edit',
        'del' => 'Delete',
        'save' => 'Update User',
        'msg_add' => 'User created successfully!',
        'msg_upd' => 'User updated successfully!',
        'msg_del' => 'User has been deleted!',
        'back' => 'Home Dashboard',
        'admin_panel' => 'Admin Control',
        'current_users' => 'Registered Users List',
        'th_name' => 'User ID',
        'th_phone' => 'Mobile',
        'th_pin' => 'PIN',
        'th_action' => 'Action',
        'u_name' => 'Username / Login ID',
        'u_phone' => 'Mobile Number',
        'u_pin' => 'Login PIN Code',
        'close' => 'Close'
    ]
];

$message = "";

// ইউজার যোগ করার লজিক
if(isset($_POST['add_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password_pin = $conn->real_escape_string($_POST['password_pin']);
    
    $conn->query("INSERT INTO users (username, phone, password_pin) VALUES ('$username', '$phone', '$password_pin')");
    $message = "<div class='alert alert-success'><i class='fa-regular fa-circle-check'></i> {$text[$lang]['msg_add']}</div>";
}

// ইউজার আপডেট করার লজিক
if(isset($_POST['update_user'])) {
    $id = (int)$_POST['id'];
    $username = $conn->real_escape_string($_POST['username']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password_pin = $conn->real_escape_string($_POST['password_pin']);
    
    $conn->query("UPDATE users SET username='$username', phone='$phone', password_pin='$password_pin' WHERE id=$id");
    $message = "<div class='alert alert-success'><i class='fa-regular fa-circle-check'></i> {$text[$lang]['msg_upd']}</div>";
}

// ইউজার ডিলিট করার লজিক
if(isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id=$id");
    $message = "<div class='alert alert-error'><i class='fa-solid fa-user-minus'></i> {$text[$lang]['msg_del']}</div>";
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="google" content="notranslate">
    <title><?php echo $text[$lang]['title']; ?> - Sabbir-Net</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Roboto, sans-serif; }
        body { margin: 0; padding: 0; background: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        
        .app-header { width: 100%; max-width: 450px; background: linear-gradient(135deg, #1e3c72, #2a5298); color: white; padding: 30px 20px 35px; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); position: relative; text-align: center; }
        .header-actions { position: absolute; top: 15px; left: 20px; }
        .lang-btn { background: rgba(255, 255, 255, 0.2); color: white; padding: 4px 12px; border-radius: 20px; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); }
        
        .profile-container { display: flex; flex-direction: column; align-items: center; margin-top: 10px; }
        .profile-icon { width: 85px; height: 85px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 38px; color: #fff; border: 3px solid rgba(255, 255, 255, 0.4); box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 12px; }
        .admin-title { font-size: 12px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 2px; }
        .admin-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }

        .container { width: 100%; max-width: 450px; padding: 20px; }
        .card { background: white; width: 100%; border-radius: 18px; padding: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #edf2f7; margin-bottom: 15px; }
        
        .toggle-menu-btn { width: 100%; background: linear-gradient(135deg, #11998e, #38ef7d); color: white; border: none; padding: 14px; border-radius: 14px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 10px rgba(56,239,125,0.2); transition: 0.2s; margin-bottom: 15px; }
        .toggle-menu-btn:active { transform: scale(0.97); }

        .section-title { font-size: 13px; font-weight: bold; color: #4a5568; margin: 5px 0 12px 5px; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .add-form-container { display: none; background: white; border-radius: 18px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); border: 1px solid #edf2f7; margin-bottom: 25px; animation: slideDown 0.25s ease; }
        .form-group { margin-bottom: 12px; text-align: left; }
        .form-control { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 14px; outline: none; background: #f7fafc; transition: 0.2s; }
        .form-control:focus { border-color: #2a5298; background: #fff; }
        
        .submit-btn { width: 100%; background: #007bff; color: white; border: none; padding: 13px; border-radius: 10px; font-size: 14px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.2s; }
        .submit-btn:active { transform: scale(0.97); }
        
        /* রেসপন্সিভ অ্যাপ টেবিল স্টাইল */
        .table-responsive { width: 100%; overflow-x: auto; max-height: 420px; overflow-y: auto; border-radius: 12px; border: 1px solid #e2e8f0; }
        .table-responsive::-webkit-scrollbar { width: 4px; height: 4px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }

        .user-table { width: 100%; border-collapse: collapse; text-align: left; background: white; font-size: 13px; }
        .user-table th { background: #f7fafc; color: #4a5568; font-weight: 700; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; font-size: 12px; text-transform: uppercase; }
        .user-table td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; color: #2d3748; vertical-align: middle; }
        .user-table tr:last-child td { border-bottom: none; }
        
        .user-id-td { font-weight: bold; color: #1e3c72; display: flex; align-items: center; gap: 6px; }
        .pin-badge { background: #e6fffa; color: #234e52; padding: 2px 6px; border-radius: 6px; font-family: monospace; font-weight: bold; font-size: 12px; border: 1px solid #b2f5ea; }
        
        /* অ্যাকশন বাটনসমূহ */
        .action-actions { display: flex; gap: 6px; }
        .btn-table-action { border: none; padding: 6px 8px; border-radius: 6px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-weight: bold; }
        .btn-table-edit { background: #fff8e6; color: #b7791f; border: 1px solid #fefcbf; }
        .btn-table-del { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }

        /* ইনলাইন এডিট প্যানেল রো (Row) এর ডিজাইন */
        .edit-row-td { background: #f7fafc !important; padding: 0 !important; }
        .edit-form-wrapper { display: none; padding: 15px; border-top: 1px dashed #cbd5e0; border-bottom: 1px dashed #cbd5e0; animation: slideDown 0.2s ease; }

        @keyframes slideDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
        
        .alert { padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 15px; text-align: center; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 5px; }
        .menu-card { background: white; padding: 12px 5px; border-radius: 14px; text-decoration: none; color: #333; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #edf2f7; transition: transform 0.2s; min-height: 90px; }
        .menu-card:active { transform: scale(0.93); }
        .card-icon { font-size: 16px; margin-bottom: 6px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; background: linear-gradient(135deg, #7f00ff, #e100ff); }
        .card-text { font-size: 10px; font-weight: 700; color: #4a5568; line-height: 1.2; }
    </style>
</head>
<body>

    <div class="app-header">
        <div class="header-actions">
            <a href="?lang=<?php echo ($lang == 'bn') ? 'en' : 'bn'; ?>" class="lang-btn">
                <i class="fa-solid fa-globe"></i> <?php echo ($lang == 'bn') ? 'English' : 'বাংলা'; ?>
            </a>
        </div>
        <div class="profile-container">
            <div class="profile-icon">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div class="admin-title"><?php echo $text[$lang]['admin_panel']; ?></div>
            <div class="admin-name"><?php echo $text[$lang]['title']; ?></div>
        </div>
    </div>

    <div class="container">
        
        <?php if(!empty($message)) { echo $message; } ?>

        <button class="toggle-menu-btn" onclick="toggleAddForm()">
            <i class="fa-solid fa-user-plus"></i> <?php echo $text[$lang]['btn_add']; ?>
        </button>

        <div id="addUserForm" class="add-form-container">
            <div class="section-title">
                <i class="fa-solid fa-square-plus" style="color: #11998e;"></i>
                <?php echo $text[$lang]['btn_add']; ?>
            </div>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="<?php echo $text[$lang]['u_name']; ?>" required>
                </div>
                <div class="form-group">
                    <input type="text" name="phone" class="form-control" placeholder="<?php echo $text[$lang]['u_phone']; ?>" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password_pin" class="form-control" placeholder="<?php echo $text[$lang]['u_pin']; ?>" required>
                </div>
                <button type="submit" name="add_user" class="submit-btn" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                    <i class="fa-solid fa-check"></i> <?php echo $text[$lang]['btn_add']; ?>
                </button>
            </form>
        </div>

        <div class="card">
            <div class="section-title">
                <i class="fa-solid fa-table-list" style="color: #007bff;"></i>
                <?php echo $text[$lang]['current_users']; ?>
            </div>
            
            <div class="table-responsive">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th><?php echo $text[$lang]['th_name']; ?></th>
                            <th><?php echo $text[$lang]['th_phone']; ?></th>
                            <th><?php echo $text[$lang]['th_pin']; ?></th>
                            <th style="text-align: center;"><?php echo $text[$lang]['th_action']; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM users ORDER BY id DESC");
                        if($res && $res->num_rows > 0) {
                            while($row = $res->fetch_assoc()) {
                                echo "<tr>
                                    <td class='user-id-td'><i class='fa-regular fa-user' style='color:#a0aec0;'></i> " . htmlspecialchars($row['username']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td><span class='pin-badge'>" . htmlspecialchars($row['password_pin']) . "</span></td>
                                    <td>
                                        <div class='action-actions'>
                                            <button class='btn-table-action btn-table-edit' onclick='toggleEditForm({$row['id']})'>
                                                <i class='fa-regular fa-pen-to-square'></i>
                                            </button>
                                            <a href='?delete_user={$row['id']}' class='btn-table-action btn-table-del' onclick='return confirm(\"Delete this user?\")'>
                                                <i class='fa-regular fa-trash-can'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr id='editRow{$row['id']}' class='edit-row-td' style='display:none;'>
                                    <td colspan='4'>
                                        <div class='edit-form-wrapper'>
                                            <form method='POST'>
                                                <input type='hidden' name='id' value='{$row['id']}'>
                                                <div class='form-group'>
                                                    <input type='text' name='username' class='form-control' value='" . htmlspecialchars($row['username']) . "' required>
                                                </div>
                                                <div class='form-group'>
                                                    <input type='text' name='phone' class='form-control' value='" . htmlspecialchars($row['phone']) . "' required>
                                                </div>
                                                <div class='form-group'>
                                                    <input type='text' name='password_pin' class='form-control' value='" . htmlspecialchars($row['password_pin']) . "' required>
                                                </div>
                                                <div style='display:flex; gap:8px;'>
                                                    <button type='submit' name='update_user' class='submit-btn' style='padding:9px; font-size:13px;'>
                                                        <i class='fa-solid fa-circle-check'></i> {$text[$lang]['save']}
                                                    </button>
                                                    <button type='button' class='submit-btn' style='padding:9px; font-size:13px; background:#6c757d;' onclick='toggleEditForm({$row['id']})'>
                                                        {$text[$lang]['close']}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; color:gray; padding:20px;'>No users registered yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="menu-grid">
            <a href="admin.php" class="menu-card" style="grid-column: span 4;">
                <div class="card-icon"><i class="fa-solid fa-house-user"></i></div>
                <div class="card-text"><?php echo $text[$lang]['back']; ?></div>
            </a>
        </div>

    </div>

    <script>
        function toggleAddForm() {
            const form = document.getElementById('addUserForm');
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
        }
        
        function toggleEditForm(id) {
            const row = document.getElementById('editRow' + id);
            const wrapper = row.querySelector('.edit-form-wrapper');
            if (row.style.display === 'table-row') {
                row.style.display = 'none';
                wrapper.style.display = 'none';
            } else {
                row.style.display = 'table-row';
                wrapper.style.display = 'block';
            }
        }
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
