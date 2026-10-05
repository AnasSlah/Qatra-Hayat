<?php
ob_start();
session_start();
$current_user_id = $_SESSION['user_id'] ?? 1;

$host = 'localhost';
$dbname = 'qatra_hayat';
$username = 'root'; 
$password = ''; 

// مصفوفة الشهور لاستخدامها في التنسيق
$months = ['January'=>'يناير', 'February'=>'فبراير', 'March'=>'مارس', 'April'=>'أبريل', 'May'=>'مايو', 'June'=>'يونيو', 'July'=>'يوليو', 'August'=>'أغسطس', 'September'=>'سبتمبر', 'October'=>'أكتوبر', 'November'=>'نوفمبر', 'December'=>'ديسمبر'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 🔴 معالجة طلب الـ AJAX من الجافاسكربت 🔴
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile_ajax'])) {
        
        $photo_query_part = "";
        $new_photo_name = "";
        
        $params = [
            $_POST['full_name'] ?? '',
            $_POST['national_id'] ?? '',
            $_POST['primary_phone'] ?? '',
            $_POST['alternate_phone'] ?? '',
            $_POST['email'] ?? '',
            $_POST['address'] ?? '',
            !empty($_POST['weight']) ? $_POST['weight'] : 0, 
            $_POST['blood_pressure'] ?? '',
            $_POST['chronic_diseases'] ?? '',
            $_POST['birth_date'] ?? null,  // تمت الإضافة
            $_POST['blood_type'] ?? null   // تمت الإضافة
        ];

        // معالجة الصورة
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/'; 
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                $photo_query_part = ", profile_photo = ?";
                $params[] = $fileName;
                $new_photo_name = $fileName;
            }
        }

        $params[] = $current_user_id;

        $updateQuery = "
            UPDATE users 
            SET full_name = ?, national_id = ?, primary_phone = ?, alternate_phone = ?, 
                email = ?, address = ?, weight = ?, blood_pressure = ?, chronic_diseases = ?,
                birth_date = ?, blood_type = ?
                $photo_query_part
            WHERE id = ?
        ";
        
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute($params);

        // تجهيز تاريخ الميلاد المُنسق لإرجاعه في الـ JSON
        $formatted_dob_response = 'غير مسجل';
        $posted_date = $_POST['birth_date'] ?? '';
        if (!empty($posted_date)) {
            $birthDateObj = new DateTime($posted_date);
            $todayObj = new DateTime('today');
            $new_age = $birthDateObj->diff($todayObj)->y;
            $month_en = date('F', strtotime($posted_date));
            $month_ar = $months[$month_en] ?? $month_en;
            $formatted_dob_response = date('d', strtotime($posted_date)) . ' ' . $month_ar . ' ' . date('Y', strtotime($posted_date)) . " ($new_age سنة)";
        }

        ob_clean(); 
        echo json_encode([
            'status' => 'success', 
            'new_photo' => $new_photo_name,
            'formatted_dob' => $formatted_dob_response,
            'blood_type' => $_POST['blood_type'] ?? '-'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$current_user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("<div style='text-align:center; padding:50px; color:red;'>عذراً، لم يتم العثور على بيانات المستخدم.</div>");
    }

    // تجهيز تاريخ الميلاد عند تحميل الصفحة
    $formatted_birth_date_full = 'غير مسجل';
    $age = 0;
    if (!empty($user['birth_date'])) {
        $birthDate = new DateTime($user['birth_date']);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
        $month_en = date('F', strtotime($user['birth_date']));
        $month_ar = $months[$month_en] ?? $month_en;
        $formatted_birth_date_full = date('d', strtotime($user['birth_date'])) . ' ' . $month_ar . ' ' . date('Y', strtotime($user['birth_date'])) . " ($age سنة)";
    }

    $formatted_last_checkup = $user['last_checkup_date'] ? date('d ', strtotime($user['last_checkup_date'])) . ($months[date('F', strtotime($user['last_checkup_date']))] ?? '') . date(' Y', strtotime($user['last_checkup_date'])) : 'غير مسجل';

    $name_parts = explode(' ', trim($user['full_name']));
    $short_name = $name_parts[0] . ' ' . ($name_parts[1] ?? '');

} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #F8FAFC; margin: 0; padding: 20px; }
        .profile-layout { display: flex; gap: 30px; align-items: flex-start; max-width: 1200px; margin: auto; }
        .profile-details { flex: 1; }
        .profile-sidebar { flex: 0 0 320px; }
        .profile-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #E2E8F0; }
        .profile-header h2 { font-size: 22px; color: #1e293b; font-weight: 800; margin: 0; }
        .header-actions { display: flex; gap: 10px; }
        .btn { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.3s; border: none; }
        .btn-edit { background: white; border: 1px solid #CBD5E1; color: #1e293b; }
        .btn-edit:hover { background: #f8fafc; border-color: #3B82F6; color: #3B82F6; }
        .btn-save { background: #16A34A; color: white; }
        .btn-save:hover { background: #15803d; }
        .btn-cancel { background: #ef4444; color: white; }
        .btn-cancel:hover { background: #dc2626; }
        .info-section { background-color: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); padding: 25px; margin-bottom: 25px; }
        .section-title-wrapper { display: flex; align-items: center; margin-bottom: 20px; border-right: 4px solid #16A34A; padding-right: 10px; }
        .section-title-wrapper h3 { font-size: 16px; font-weight: 800; color: #1e293b; margin: 0; }
        .data-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed #F1F5F9; font-size: 15px; align-items: center; min-height: 45px; }
        .data-row:last-child { border-bottom: none; padding-bottom: 0; }
        .data-label { color: #64748b; font-weight: 600; flex: 0 0 30%; }
        .data-value-container { flex: 1; display: flex; align-items: center; justify-content: flex-end; width: 100%; }
        .view-mode { color: #1e293b; font-weight: 700; display: inline-block; text-align: left; }
        .edit-mode { display: none; width: 100%; max-width: 400px; padding: 8px 15px; font-size: 15px; font-family: inherit; font-weight: 600; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #f8fafc; outline: none; box-sizing: border-box; transition: all 0.3s ease; }
        .edit-mode:focus { border-color: #3B82F6; background-color: #fff; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .save-btns { display: none; gap: 10px; }
        #profile-form.editing .view-mode.editable { display: none; }
        #profile-form.editing .edit-mode { display: inline-block; }
        #profile-form.editing .btn-edit { display: none; }
        #profile-form.editing .save-btns { display: flex; }
        .user-card { background-color: #fff; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); padding: 30px 20px; text-align: center; position: relative; border-top: 4px solid #DC2626; }
        .verified-badge { position: absolute; top: 15px; left: 15px; background-color: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 5px; }
        .unverified-badge { position: absolute; top: 15px; left: 15px; background-color: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 5px; }
        .avatar-large { width: 100px; height: 100px; background-color: #E0F2FE; color: #0284C7; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 45px; margin: 0 auto 20px auto; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden; }
        .avatar-large img { width: 100%; height: 100%; object-fit: cover; }
        .photo-edit-container { display: none; margin-top: -10px; margin-bottom: 15px; text-align: center; width: 100%; }
        #profile-form.editing .photo-edit-container { display: block; }
        .btn-change-photo { cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #f8fafc; border: 1px dashed #CBD5E1; color: #3B82F6; padding: 8px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; width: 80%; margin: 0 auto; transition: all 0.3s; }
        .btn-change-photo:hover { background: #eff6ff; border-color: #3B82F6; }
        .user-card h3 { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 5px; }
        .member-id { color: #64748b; font-size: 13px; font-weight: 600; margin-bottom: 25px; }
        .blood-type-badge { background-color: #FEE2E2; color: #DC2626; font-size: 18px; font-weight: 800; padding: 10px 20px; border-radius: 12px; display: inline-block; direction: ltr; }
        @media (max-width: 992px) { .profile-layout { flex-direction: column-reverse; } .profile-sidebar, .profile-details { flex: none; width: 100%; } .data-row { flex-direction: column; gap: 5px; text-align: right; align-items: flex-start; } .data-value-container { width: 100%; justify-content: flex-start; } .edit-mode { width: 100%; max-width: none; } }
    </style>
</head>
<body>

    <form id="profile-form" class="profile-layout" enctype="multipart/form-data">
        <div class="profile-details">
            
            <div class="profile-header">
                <h2>الملف الشخصي الشامل</h2>
                <div class="header-actions">
                    <button type="button" class="btn btn-edit" onclick="document.getElementById('profile-form').classList.add('editing')">تعديل البيانات <i class="fa-solid fa-pen"></i></button>
                    
                    <div class="save-btns">
                        <button type="button" class="btn btn-save" onclick="saveProfileData()">حفظ <i class="fa-solid fa-check"></i></button>
                        <button type="button" class="btn btn-cancel" onclick="document.getElementById('profile-form').classList.remove('editing')">إلغاء <i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title-wrapper"><h3>البيانات الأساسية</h3></div>
                
                <div class="data-row">
                    <span class="data-label">الاسم الكامل</span>
                    <div class="data-value-container">
                        <span class="view-mode editable"><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></span>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" class="edit-mode">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">الرقم الوطني</span>
                    <div class="data-value-container">
                        <span class="view-mode editable"><?php echo htmlspecialchars($user['national_id'] ?? ''); ?></span>
                        <input type="text" name="national_id" value="<?php echo htmlspecialchars($user['national_id'] ?? ''); ?>" class="edit-mode" dir="ltr">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">تاريخ الميلاد</span>
                    <div class="data-value-container">
                        <span class="view-mode editable" id="view-dob"><?php echo $formatted_birth_date_full; ?></span>
                        <input type="date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date'] ?? ''); ?>" class="edit-mode">
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title-wrapper" style="border-color: #3B82F6;"><h3>معلومات التواصل</h3></div>
                
                <div class="data-row">
                    <span class="data-label">رقم الهاتف الأساسي</span>
                    <div class="data-value-container">
                        <span class="view-mode editable"><?php echo htmlspecialchars($user['primary_phone'] ?? ''); ?></span>
                        <input type="text" name="primary_phone" value="<?php echo htmlspecialchars($user['primary_phone'] ?? ''); ?>" class="edit-mode" dir="ltr">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">رقم هاتف بديل</span>
                    <div class="data-value-container">
                        <?php $alt_phone = $user['alternate_phone'] ?? ''; ?>
                        <span class="view-mode editable"><?php echo htmlspecialchars($alt_phone ?: 'لا يوجد'); ?></span>
                        <input type="text" name="alternate_phone" value="<?php echo htmlspecialchars($alt_phone); ?>" class="edit-mode" dir="ltr">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">البريد الإلكتروني</span>
                    <div class="data-value-container">
                        <span class="view-mode editable"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="edit-mode" dir="ltr">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">محل الإقامة</span>
                    <div class="data-value-container">
                        <?php $addr = $user['address'] ?? ''; ?>
                        <span class="view-mode editable"><?php echo htmlspecialchars($addr ?: 'غير مسجل'); ?></span>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($addr); ?>" class="edit-mode">
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="section-title-wrapper" style="border-color: #DC2626;"><h3>الملف الطبي (حيوي)</h3></div>

                <div class="data-row">
                    <span class="data-label">فصيلة الدم</span>
                    <div class="data-value-container">
                        <?php $b_type = $user['blood_type'] ?? '-'; ?>
                        <span class="view-mode editable" id="view-blood-type" dir="ltr"><?php echo htmlspecialchars($b_type); ?></span>
                        <select name="blood_type" class="edit-mode" dir="ltr">
                            <option value="-" <?php echo ($b_type == '-' || empty($b_type)) ? 'selected' : ''; ?>>غير مسجل</option>
                            <option value="A+" <?php echo ($b_type == 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo ($b_type == 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo ($b_type == 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo ($b_type == 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="O+" <?php echo ($b_type == 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo ($b_type == 'O-') ? 'selected' : ''; ?>>O-</option>
                            <option value="AB+" <?php echo ($b_type == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo ($b_type == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        </select>
                    </div>
                </div>
                
                <div class="data-row">
                    <span class="data-label">الوزن المسجل (كجم)</span>
                    <div class="data-value-container">
                        <span class="view-mode editable"><?php echo htmlspecialchars($user['weight'] ?? '0'); ?></span>
                        <input type="number" name="weight" value="<?php echo htmlspecialchars($user['weight'] ?? ''); ?>" class="edit-mode">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">متوسط الضغط</span>
                    <div class="data-value-container">
                        <?php $bp = $user['blood_pressure'] ?? ''; ?>
                        <span class="view-mode editable"><?php echo htmlspecialchars($bp ?: 'غير مسجل'); ?></span>
                        <input type="text" name="blood_pressure" value="<?php echo htmlspecialchars($bp); ?>" class="edit-mode" dir="ltr">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">الأمراض المزمنة</span>
                    <div class="data-value-container">
                        <?php $chronic = $user['chronic_diseases'] ?? ''; ?>
                        <span class="view-mode editable"><?php echo htmlspecialchars($chronic ?: 'لا يوجد'); ?></span>
                        <input type="text" name="chronic_diseases" value="<?php echo htmlspecialchars($chronic); ?>" class="edit-mode">
                    </div>
                </div>

                <div class="data-row">
                    <span class="data-label">تاريخ آخر فحص</span>
                    <div class="data-value-container">
                        <span class="view-mode"><?php echo $formatted_last_checkup; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-sidebar">
            <div class="user-card">
                <?php if (isset($user['is_eligible']) && $user['is_eligible']): ?>
                    <div class="verified-badge">متبرع موثق <i class="fa-solid fa-check-double"></i></div>
                <?php else: ?>
                    <div class="unverified-badge">غير موثق طبياً <i class="fa-solid fa-circle-exclamation"></i></div>
                <?php endif; ?>
                
                <div class="avatar-large">
                    <?php if (!empty($user['profile_photo']) && file_exists('uploads/' . $user['profile_photo'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="صورة شخصية">
                    <?php else: ?>
                        <i class="fa-solid fa-user"></i>
                    <?php endif; ?>
                </div>

                <div class="photo-edit-container">
                    <label for="profile_image" class="btn-change-photo" id="photo-label">
                        تغيير الصورة <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="document.getElementById('photo-label').innerHTML = 'تم الاختيار <i class=\'fa-solid fa-check\'></i>';">
                </div>

                <h3 id="sidebar-name"><?php echo htmlspecialchars($short_name); ?></h3>
                <div class="member-id">رقم العضوية: <?php echo htmlspecialchars($user['member_id'] ?? 'غير متوفر'); ?></div>
                <div class="blood-type-badge">فصيلة الدم: <strong id="sidebar-blood-type"><?php echo htmlspecialchars($user['blood_type'] ?? '-'); ?></strong></div>
            </div>
        </div>
    </form>

    <script>
    function saveProfileData() {
        let form = document.getElementById('profile-form');
        if (!form) return;

        let formData = new FormData(form);
        formData.append('save_profile_ajax', '1'); 

        fetch('profile.php', { 
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error("مشكلة في الاتصال بالخادم");
            return response.text(); 
        })
        .then(textData => {
            try {
                let data = JSON.parse(textData); 
                if(data.status === 'success') {
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'عملية ناجحة!',
                        text: 'تم حفظ بياناتك بنجاح',
                        confirmButtonColor: '#16A34A',
                        timer: 2500,
                        showConfirmButton: false
                    });

                    form.classList.remove('editing');

                    // تحديث كافة الحقول النصية والرقمية العادية
                    document.querySelectorAll('.data-row').forEach(row => {
                        let input = row.querySelector('.edit-mode');
                        let span = row.querySelector('.view-mode.editable');
                        if(input && span) {
                            // نتخطى تاريخ الميلاد لأنه يحتاج تنسيق خاص يرجع من السيرفر
                            if(input.name !== 'birth_date') {
                                let val = input.value.trim();
                                span.textContent = val || (input.type === 'number' ? '0' : 'غير مسجل');
                            }
                        }
                    });

                    // تحديث تاريخ الميلاد من الرد القادم من الـ PHP
                    if(data.formatted_dob) {
                        let dobSpan = document.getElementById('view-dob');
                        if(dobSpan) dobSpan.textContent = data.formatted_dob;
                    }

                    // تحديث شارة فصيلة الدم في الشريط الجانبي
                    if(data.blood_type) {
                        let sidebarBlood = document.getElementById('sidebar-blood-type');
                        if(sidebarBlood) sidebarBlood.textContent = data.blood_type;
                    }

                    // تحديث الاسم المختصر في الشريط الجانبي
                    let fullNameInput = document.querySelector('input[name="full_name"]');
                    if(fullNameInput) {
                        let fullNameVal = fullNameInput.value.trim();
                        let nameParts = fullNameVal.split(' ');
                        document.getElementById('sidebar-name').textContent = nameParts[0] + ' ' + (nameParts[1] || '');
                    }

                    // تحديث الهيدر العلوي في الـ Dashboard فوراً (إذا كان موجوداً)
                    let headerNameObj = document.getElementById('top-header-name');
                    let headerAvatarObj = document.getElementById('top-header-avatar');
                    
                    if (headerNameObj && fullNameInput) {
                        let newFirstName = fullNameInput.value.trim().split(' ')[0] || 'مستخدم';
                        headerNameObj.innerHTML = 'مرحباً، ' + newFirstName + ' 👋';
                        
                        if (headerAvatarObj && !data.new_photo) {
                            if(!headerAvatarObj.querySelector('img')) {
                               headerAvatarObj.textContent = newFirstName.charAt(0);
                            }
                        }
                    }

                    // تحديث الصورة في جميع الأماكن
                    if(data.new_photo) {
                        let avatar = document.querySelector('.avatar-large');
                        if(avatar) avatar.innerHTML = '<img src="uploads/' + data.new_photo + '" alt="صورة شخصية" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">';
                        
                        if (headerAvatarObj) {
                            headerAvatarObj.innerHTML = '<img src="uploads/' + data.new_photo + '" style="width: 100%; height: 100%; object-fit: cover;">';
                        }
                        
                        let photoLabel = document.getElementById('photo-label');
                        if(photoLabel) photoLabel.innerHTML = 'تغيير الصورة <i class="fa-solid fa-camera"></i>';
                    }
                }
            } catch(e) {
                console.error("خطأ:", e);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ داخلي!',
                    text: 'حدث خطأ! يرجى التأكد من تشغيل السيرفر المحلي بشكل سليم.',
                    confirmButtonColor: '#DC2626'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'warning',
                title: 'فشل الإرسال',
                text: "يرجى التحقق من اسم الملف 'profile.php' أو اتصالك بالإنترنت.",
                confirmButtonColor: '#D97706'
            });
        });
    }
    </script>
</body>
</html>