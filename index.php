<?php
session_start();
require_once 'db.php';

// توجيه ذكي: لو مسجل دخول يروح لملفه فوراً
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/index.php");
    exit;
} elseif (isset($_SESSION['user_id'])) {
    header("Location: user/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'يرجى إدخال البريد الإلكتروني وكلمة المرور.';
    } else {
        // 1. فحص جدول الأدمن
        $stmtAdmin = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
        $stmtAdmin->execute([$email]);
        $admin = $stmtAdmin->fetch();

        if ($admin && $password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['role'] = 'admin';
            header("Location: admin/index.php"); // توجيه لمجلد الأدمن
            exit;
        }

        // 2. فحص جدول المستخدمين/المتبرعين
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmtUser->execute([$email]);
        $user = $stmtUser->fetch();

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = 'user';
            header("Location: user/index.php"); // توجيه لمجلد المستخدم
            exit;
        }

        $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة!';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - قطرة حياة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; border-top: 5px solid #df3e4d; }
        .logo { font-size: 30px; font-weight: 800; color: #333; margin-bottom: 5px; }
        .logo span { color: #df3e4d; }
        .subtitle { color: #777; font-size: 14px; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; text-align: right; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #eaeaea; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box; transition: border 0.3s; }
        .form-group input:focus { border-color: #df3e4d; }
        .btn-login { width: 100%; padding: 12px; background-color: #df3e4d; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.3s; }
        .btn-login:hover { background-color: #c93543; }
        .error-msg { background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 700; margin-bottom: 20px; }
        .register-link { margin-top: 20px; font-size: 14px; color: #777; }
        .register-link a { color: #1565c0; text-decoration: none; font-weight: 700; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo"><i class="fa-solid fa-droplet" style="color: #df3e4d;"></i> قطرة <span>حياة</span></div>
    <div class="subtitle">سجل دخولك للمتابعة إلى لوحة التحكم</div>

    <?php if(!empty($error)): ?>
        <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" placeholder="أدخل بريدك الإلكتروني" required dir="ltr" style="text-align: right;">
        </div>
        <div class="form-group">
            <label>كلمة المرور</label>
            <input type="password" name="password" placeholder="أدخل كلمة المرور" required dir="ltr" style="text-align: right;">
        </div>
        <button type="submit" class="btn-login">تسجيل الدخول</button>
    </form>

    <div class="register-link">
        ليس لديك حساب متبرع؟ <a href="register.php">سجل كمتبرع جديد</a>
    </div>
</div>

</body>
</html>