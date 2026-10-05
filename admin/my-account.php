<?php
require_once 'db.php';

// ================= [ 1. معالجة طلب تعديل بيانات الحساب ] ================= //
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_profile') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    $sql = "UPDATE admins SET name = ?, email = ?, phone = ?";
    $params = [$name, $email, $phone];

    if (!empty($new_password)) {
        $sql .= ", password = ?";
        $params[] = $new_password; 
    }

    $sql .= " WHERE id = 1"; 

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo "تم حفظ بيانات الحساب بنجاح!";
    } catch (PDOException $e) {
        echo "حدث خطأ: " . $e->getMessage();
    }
    exit;
}
// ======================================================================== //

// 2. جلب بيانات مدير النظام 
$stmt_admin = $pdo->query("SELECT * FROM admins LIMIT 1");
$admin = $stmt_admin->fetch();

$admin_name = $admin ? $admin['name'] : 'أدمن غير معروف';
$admin_email = $admin ? $admin['email'] : 'لا يوجد بريد';
$admin_phone = ($admin && isset($admin['phone'])) ? $admin['phone'] : '';

$first_char = mb_substr($admin_name, 0, 1, 'UTF-8');

// 3. جلب إحصائيات لوحة المشرف
$stmt_pending = $pdo->query("SELECT COUNT(*) FROM donations WHERE donation_status = 'pending'");
$pending_count = $stmt_pending->fetchColumn();

$stmt_users = $pdo->query("SELECT COUNT(*) FROM users");
$users_count = $stmt_users->fetchColumn();

// 4. جلب الرسائل الواردة من جدول contact_messages الحقيقي
$messages = [];
try {
    $stmt_msg = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 10");
    $messages = $stmt_msg->fetchAll();
} catch (PDOException $e) {
    // في حالة وجود مشكلة في الجدول
}
?>

<style>
    .admin-account-container { display: grid; grid-template-columns: 1fr 320px; gap: 25px; align-items: start; }
    .admin-card { background-color: #ffffff; border-radius: 12px; padding: 30px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); text-align: center; border-top: 5px solid #333333; position: relative; }
    .role-badge { position: absolute; top: 15px; right: 15px; background-color: #333333; color: #ffffff; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 5px; }
    .admin-avatar { width: 100px; height: 100px; background-color: #df3e4d; color: #ffffff; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 45px; font-weight: 800; margin: 30px auto 15px; border: 4px solid #fbeef0; box-shadow: 0 4px 15px rgba(223, 62, 77, 0.2); }
    .admin-card h3 { font-size: 20px; color: #333333; font-weight: 800; margin-bottom: 5px; }
    .admin-card p { font-size: 13px; color: #777777; margin-bottom: 20px; }
    .admin-stats { display: flex; justify-content: space-between; background-color: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 15px; }
    .stat-item { display: flex; flex-direction: column; gap: 5px; }
    .stat-item span { font-size: 11px; color: #777777; font-weight: 700; }
    .stat-item strong { font-size: 18px; color: #df3e4d; font-weight: 800; }

    .settings-panel { background-color: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 25px;}
    .settings-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #eaeaea; padding-bottom: 15px; }
    .settings-header h2 { font-size: 20px; color: #333333; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .action-buttons { display: flex; gap: 10px; }

    .btn-edit-profile { background-color: #1565c0; color: #ffffff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; cursor: pointer; font-family: inherit; display: flex; align-items: center; gap: 8px; transition: background 0.2s; font-size: 14px; }
    .btn-edit-profile:hover { background-color: #0d47a1; }
    .btn-cancel-edit { background-color: #f4f6f9; color: #777777; border: 1px solid #eaeaea; padding: 10px 20px; border-radius: 6px; font-weight: 700; cursor: pointer; font-family: inherit; display: none; align-items: center; gap: 8px; font-size: 14px; }
    .btn-cancel-edit:hover { background-color: #eaeaea; }
    .btn-save-profile { background-color: #2a9d8f; color: #ffffff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; cursor: pointer; font-family: inherit; display: none; align-items: center; gap: 8px; font-size: 14px; }
    .btn-save-profile:hover { background-color: #21867a; }

    .form-section { margin-bottom: 35px; }
    .form-section:last-child { margin-bottom: 0; }
    .form-section-title { font-size: 16px; font-weight: 700; color: #1565c0; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; border-right: 4px solid #1565c0; padding-right: 10px; }
    .form-section-title.security { color: #333333; border-right-color: #333333; }
    .inputs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .input-group { display: flex; flex-direction: column; gap: 8px; }
    .input-group.full-width { grid-column: span 2; }
    .input-group label { font-size: 13px; font-weight: 700; color: #777777; }

    .edit-mode { display: none; }
    .is-editing .view-mode { display: none; }
    .is-editing .edit-mode { display: block; width: 100%; }

    .display-text { padding: 12px; background-color: #f4f6f9; border-radius: 8px; font-size: 14px; color: #333333; font-weight: 700; border: 1px solid transparent; min-height: 42px; display: block; }
    .edit-profile-input { padding: 12px; border: 1px solid #2a9d8f; border-radius: 8px; font-family: inherit; outline: none; font-size: 14px; background-color: #ffffff; color: #333333; font-weight: 500; }

    /* تنسيق قسم الرسائل الواردة */
    .message-card { background-color: #f9fafb; border: 1px solid #eaeaea; border-radius: 8px; padding: 15px; margin-bottom: 15px; transition: transform 0.2s; }
    .message-card:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
    .message-card.unread { border-right: 4px solid #df3e4d; background-color: #fff; }
    .msg-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #eaeaea; padding-bottom: 10px; }
    .msg-sender { font-weight: 800; color: #333333; font-size: 14px; display: flex; align-items: center; gap: 8px;}
    .msg-date { font-size: 11px; color: #777777; }
    .msg-body { font-size: 13px; color: #555; line-height: 1.6; }
    .msg-footer { margin-top: 12px; display: flex; gap: 15px; align-items: center; }
    .msg-phone { font-size: 12px; color: #1565c0; font-weight: 700;}
    .msg-subject { font-size: 11px; color: #777; background: #eaeaea; padding: 3px 8px; border-radius: 15px; font-weight: 600;}

    @media (max-width: 992px) {
        .admin-account-container { grid-template-columns: 1fr; }
        .admin-card { order: -1; }
    }
</style>

<div class="admin-account-container">
    
    <div>
        <div class="settings-panel" id="adminSettingsPanel">
            <div class="settings-header">
                <h2><i class="fa-solid fa-user-gear" style="color: #1565c0;"></i> إعدادات حسابك</h2>
                <div class="action-buttons">
                    <button class="btn-edit-profile" id="startEditProfileBtn">
                        <i class="fa-solid fa-pen"></i> تعديل البيانات
                    </button>
                    <button class="btn-cancel-edit" id="cancelEditProfileBtn">
                        <i class="fa-solid fa-xmark"></i> إلغاء
                    </button>
                    <button class="btn-save-profile" id="saveProfileBtn">
                        <i class="fa-solid fa-check"></i> حفظ 
                    </button>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">البيانات الأساسية للتواصل</h3>
                <div class="inputs-grid">
                    <div class="input-group full-width">
                        <label>اسم المشرف</label>
                        <div class="field-container">
                            <span class="display-text view-mode"><?= htmlspecialchars($admin_name) ?></span>
                            <input type="text" class="edit-profile-input edit-mode" id="adminNameInput" value="<?= htmlspecialchars($admin_name) ?>">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>البريد الإلكتروني</label>
                        <div class="field-container">
                            <span class="display-text view-mode" dir="ltr" style="text-align: right;"><?= htmlspecialchars($admin_email) ?></span>
                            <input type="email" class="edit-profile-input edit-mode" id="adminEmailInput" value="<?= htmlspecialchars($admin_email) ?>" dir="ltr" style="text-align: right;">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>رقم الهاتف</label>
                        <div class="field-container">
                            <span class="display-text view-mode" dir="ltr" style="text-align: right;"><?= !empty($admin_phone) ? htmlspecialchars($admin_phone) : '(غير متوفر)' ?></span>
                            <input type="text" class="edit-profile-input edit-mode" id="adminPhoneInput" value="<?= htmlspecialchars($admin_phone) ?>" placeholder="أدخل رقم الهاتف" dir="ltr" style="text-align: right;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title security">الأمان وكلمة المرور</h3>
                <div class="inputs-grid">
                    <div class="input-group full-width">
                        <label>كلمة المرور الحالية (مطلوبة فقط للتأكيد)</label>
                        <div class="field-container">
                            <span class="display-text view-mode">••••••••</span>
                            <input type="password" class="edit-profile-input edit-mode" id="currentPasswordInput" placeholder="أدخل كلمة المرور الحالية">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>كلمة المرور الجديدة</label>
                        <div class="field-container">
                            <span class="display-text view-mode">••••••••</span>
                            <input type="password" class="edit-profile-input edit-mode" id="newPasswordInput" placeholder="أدخل كلمة مرور جديدة (اتركه فارغاً إن لم ترد التغيير)">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>تأكيد كلمة المرور الجديدة</label>
                        <div class="field-container">
                            <span class="display-text view-mode">••••••••</span>
                            <input type="password" class="edit-profile-input edit-mode" id="confirmPasswordInput" placeholder="تأكيد كلمة المرور">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-panel">
            <div class="settings-header" style="margin-bottom: 20px;">
                <h2><i class="fa-solid fa-envelope" style="color: #df3e4d;"></i> الرسائل الواردة</h2>
            </div>
            
            <div class="messages-container">
                <?php if (count($messages) > 0): ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php $isUnread = (isset($msg['status']) && $msg['status'] === 'unread'); ?>
                        
                        <div class="message-card <?= $isUnread ? 'unread' : '' ?>">
                            <div class="msg-header">
                                <div class="msg-sender">
                                    <i class="fa-regular fa-circle-user" style="color: #df3e4d;"></i>
                                    <?= htmlspecialchars($msg['sender_name'] ?? 'مجهول') ?>
                                    
                                    <?php if($isUnread): ?>
                                        <span style="background: #df3e4d; color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-right: 5px;">جديدة</span>
                                    <?php endif; ?>
                                </div>
                                <div class="msg-date">
                                    <?= isset($msg['created_at']) ? date('Y-m-d H:i', strtotime($msg['created_at'])) : '' ?>
                                </div>
                            </div>
                            
                            <div class="msg-body">
                                <?= nl2br(htmlspecialchars($msg['message'] ?? 'لا يوجد نص رسالة')) ?>
                            </div>
                            
                            <div class="msg-footer">
                                <?php if(!empty($msg['contact_info'])): ?>
                                    <span class="msg-phone"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($msg['contact_info']) ?></span>
                                <?php endif; ?>
                                
                                <?php if(!empty($msg['subject'])): ?>
                                    <span class="msg-subject"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($msg['subject']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #777; padding: 20px 0;">لا توجد رسائل جديدة حالياً.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <span class="role-badge"><i class="fa-solid fa-star"></i> مدير النظام</span>
        <div class="admin-avatar" id="adminAvatarChar"><?= htmlspecialchars($first_char) ?></div>
        <h3 id="adminNameCard"><?= htmlspecialchars($admin_name) ?></h3>
        <p>مرحباً بك في لوحة تحكم قطرة حياة</p>
        
        <div class="admin-stats">
            <div class="stat-item">
                <span>طلبات مراجعة</span>
                <strong><?= number_format($pending_count) ?></strong>
            </div>
            <div class="stat-item">
                <span> عدد المتبرعين</span>
                <strong style="color: #2a9d8f;"><?= number_format($users_count) ?></strong>
            </div>
        </div>
    </div>

</div>