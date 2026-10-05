<?php
session_start();
require_once 'db.php';

// توجيه في حال كان مسجل دخول
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/index.php");
    exit;
} elseif (isset($_SESSION['user_id'])) {
    header("Location: user/index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. البيانات الإجبارية
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $primary_phone = trim($_POST['phone'] ?? ''); 
    $blood_type = trim($_POST['blood_type'] ?? '');
    
    // 2. البيانات الاختيارية (تحويل الفارغ إلى NULL)
    $national_id = !empty(trim($_POST['national_id'])) ? trim($_POST['national_id']) : null;
    $birth_date = !empty(trim($_POST['birth_date'])) ? trim($_POST['birth_date']) : null;
    $alternate_phone = !empty(trim($_POST['alt_phone'])) ? trim($_POST['alt_phone']) : null; 
    $address = !empty(trim($_POST['address'])) ? trim($_POST['address']) : null;
    $weight = !empty(trim($_POST['weight'])) ? (int)$_POST['weight'] : null;
    $blood_pressure = !empty(trim($_POST['blood_pressure'])) ? trim($_POST['blood_pressure']) : null;
    $chronic_diseases = !empty(trim($_POST['chronic_diseases'])) ? trim($_POST['chronic_diseases']) : null;

    if (empty($full_name) || empty($email) || empty($password) || empty($blood_type) || empty($primary_phone)) {
        $error = 'يرجى إكمال جميع الحقول الإجبارية (الاسم، البريد، كلمة المرور، الهاتف، والفصيلة).';
    } else {
        // التحقق المبدئي من أن البريد غير مسجل مسبقاً
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        
        if ($stmtCheck->rowCount() > 0) {
            $error = 'البريد الإلكتروني مسجل لدينا بالفعل!';
        } else {
            try {
                // إدراج المتبرع مع استخدام الأسماء الحقيقية للأعمدة
                $sql = "INSERT INTO users (full_name, national_id, birth_date, email, password, primary_phone, alternate_phone, address, blood_type, weight, blood_pressure, chronic_diseases, is_eligible) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $full_name, $national_id, $birth_date, $email, 
                    $password, $primary_phone, $alternate_phone, $address, 
                    $blood_type, $weight, $blood_pressure, $chronic_diseases
                ]);
                
                // توليد رقم العضوية (member_id) بشكل احترافي
                $last_id = $pdo->lastInsertId();
                $member_id = sprintf("DON-%03d", $last_id);
                $pdo->prepare("UPDATE users SET member_id = ? WHERE id = ?")->execute([$member_id, $last_id]);
                
                $success = 'تم إنشاء الحساب بنجاح! جاري تحويلك لتسجيل الدخول...';
                echo "<script>setTimeout(function(){ window.location.href = 'index.php'; }, 2000);</script>";
            } catch (PDOException $e) {
                // التقاط أخطاء تكرار البيانات الفريدة (SQLSTATE 23000 أو رمز الخطأ 1062)
                if ($e->getCode() == 23000 || (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062)) {
                    // فحص الحقل المتسبب في التكرار بدقة لتقديم رسالة واضحة للمستخدم
                    if (strpos($e->getMessage(), 'national_id') !== false) {
                        $error = "عذراً، الرقم الوطني الذي أدخلته مسجل مسبقاً بحساب آخر في النظام.";
                    } elseif (strpos($e->getMessage(), 'email') !== false) {
                        $error = "عذراً، البريد الإلكتروني الذي أدخلته مسجل مسبقاً لدينا بالفعل.";
                    } else {
                        $error = "عذراً، بعض البيانات المدخلة مسجلة مسبقاً في النظام.";
                    }
                } else {
                    // في حال حدوث أي خطأ بنيوي آخر في قاعدة البيانات
                    $error = "حدث خطأ غير متوقع أثناء معالجة الطلب، يرجى المحاولة لاحقاً.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب متبرع - قطرة حياة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 20px 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .register-container { background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 800px; border-top: 5px solid #2a9d8f; }
        .logo { font-size: 26px; font-weight: 800; color: #333; margin-bottom: 5px; text-align: center;}
        .logo span { color: #df3e4d; }
        .subtitle { color: #777; font-size: 14px; margin-bottom: 30px; text-align: center; }
        
        .section-title { font-size: 16px; font-weight: 800; color: #1565c0; margin-top: 25px; margin-bottom: 15px; padding-right: 10px; border-right: 4px solid #1565c0; display: flex; align-items: center; gap: 8px;}
        .section-title.medical { color: #df3e4d; border-right-color: #df3e4d; }
        .section-title.contact { color: #2a9d8f; border-right-color: #2a9d8f; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 10px; text-align: right; }
        .form-group.full { grid-column: span 2; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 8px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #eaeaea; border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box; font-family: inherit; transition: border 0.3s; }
        .form-group input:focus, .form-group select:focus { border-color: #2a9d8f; }
        
        .btn-register { width: 100%; padding: 14px; background-color: #2a9d8f; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 800; cursor: pointer; transition: background 0.3s; margin-top: 20px; font-family: inherit; }
        .btn-register:hover { background-color: #21867a; }
        
        .error-msg { background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 700; margin-bottom: 20px; text-align: center; }
        .success-msg { background-color: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 700; margin-bottom: 20px; text-align: center; }
        
        .login-link { margin-top: 20px; font-size: 14px; color: #777; text-align: center; }
        .login-link a { color: #1565c0; text-decoration: none; font-weight: 700; }
        .login-link a:hover { text-decoration: underline; }
        
        @media(max-width: 768px) { .form-row { grid-template-columns: 1fr; } .form-group.full { grid-column: span 1; } }
    </style>
</head>
<body>

<div class="register-container">
    <div class="logo"><i class="fa-solid fa-heart-pulse" style="color: #2a9d8f;"></i> حساب <span>متبرع</span> جديد</div>
    <div class="subtitle">انضم إلينا وأنقذ حياة الآخرين</div>

    <?php if(!empty($error)): ?>
        <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>
    <?php if(!empty($success)): ?>
        <div class="success-msg"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        
        <div class="section-title"><i class="fa-solid fa-id-card"></i> البيانات الأساسية</div>
        <div class="form-row">
            <div class="form-group">
                <label>الاسم الكامل <span style="color:red">*</span></label>
                <input type="text" name="full_name" placeholder="أدخل اسمك بالكامل" required>
            </div>
            <div class="form-group">
                <label>الرقم الوطني</label>
                <input type="text" name="national_id" placeholder="أدخل الرقم الوطني">
            </div>
            <div class="form-group">
                <label>تاريخ الميلاد</label>
                <input type="date" name="birth_date">
            </div>
            <div class="form-group">
                <label>البريد الإلكتروني <span style="color:red">*</span></label>
                <input type="email" name="email" placeholder="example@mail.com" required dir="ltr" style="text-align: right;">
            </div>
            <div class="form-group full">
                <label>كلمة المرور <span style="color:red">*</span></label>
                <input type="password" name="password" placeholder="أدخل كلمة مرور قوية" required dir="ltr" style="text-align: right;">
            </div>
        </div>

        <div class="section-title contact"><i class="fa-solid fa-address-book"></i> معلومات التواصل</div>
        <div class="form-row">
            <div class="form-group">
                <label>رقم الهاتف الأساسي <span style="color:red">*</span></label>
                <input type="text" name="phone" placeholder="أدخل رقم الهاتف" required dir="ltr" style="text-align: right;">
            </div>
            <div class="form-group">
                <label>رقم هاتف بديل</label>
                <input type="text" name="alt_phone" placeholder="أدخل رقماً إضافياً" dir="ltr" style="text-align: right;">
            </div>
            <div class="form-group full">
                <label>محل الإقامة</label>
                <input type="text" name="address" placeholder="المدينة، المنطقة، الشارع...">
            </div>
        </div>

        <div class="section-title medical"><i class="fa-solid fa-notes-medical"></i> الملف الطبي (حيوي)</div>
        <div class="form-row">
            <div class="form-group">
                <label>فصيلة الدم <span style="color:red">*</span></label>
                <select name="blood_type" required>
                    <option value="" disabled selected>-- اختر --</option>
                    <option value="O-">O-</option>
                    <option value="O+">O+</option>
                    <option value="A-">A-</option>
                    <option value="A+">A+</option>
                    <option value="B-">B-</option>
                    <option value="B+">B+</option>
                    <option value="AB-">AB-</option>
                    <option value="AB+">AB+</option>
                </select>
            </div>
            <div class="form-group">
                <label>الوزن المسجل (كجم)</label>
                <input type="number" name="weight" placeholder="مثال: 75">
            </div>
            <div class="form-group">
                <label>متوسط الضغط</label>
                <input type="text" name="blood_pressure" placeholder="مثال: 120/80" dir="ltr" style="text-align: right;">
            </div>
            <div class="form-group">
                <label>الأمراض المزمنة (إن وجدت)</label>
                <input type="text" name="chronic_diseases" placeholder="لا يوجد">
            </div>
        </div>

        <button type="submit" class="btn-register">إنشاء الحساب والتسجيل</button>
    </form>

    <div class="login-link">
        لديك حساب بالفعل؟ <a href="index.php">تسجيل الدخول من هنا</a>
    </div>
</div>

</body>
</html>