<?php
require_once 'db.php';

// 1. استقبال البيانات وتحديث الداتا بيز بناءً على الـ ID فقط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    // لو الـ ID بصفر، نوقف العملية ونطبع المشكلة
    if ($user_id === 0) {
        echo "خطأ: لم يتم استلام ID المتبرع! تأكد من إضافة data-id لزر التعديل في جدول المتبرعين.";
        exit;
    }

    $full_name    = trim($_POST['full_name'] ?? '');
    $national_id  = trim($_POST['national_id'] ?? '');
    $birth_date   = trim($_POST['birth_date'] ?? '');
    $blood_type   = trim($_POST['blood_type'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $chronic      = trim($_POST['chronic_diseases'] ?? '');
    $status       = isset($_POST['account_status']) ? (int)$_POST['account_status'] : 1;

    // 2. تحويل النصوص الفارغة إلى NULL لمنع تعارض الحقول الفريدة (Unique) مثل الإيميل والرقم الوطني
    $national_id = ($national_id === '') ? null : $national_id;
    $birth_date  = ($birth_date === '') ? null : $birth_date;
    $phone       = ($phone === '') ? null : $phone;
    $email       = ($email === '') ? null : $email;

    try {
        // 3. التحديث باستخدام الـ ID حصراً (الأسلم والأدق)
        $stmt = $pdo->prepare("
            UPDATE users 
            SET full_name = ?, national_id = ?, birth_date = ?, blood_type = ?, primary_phone = ?, email = ?, address = ?, chronic_diseases = ?, is_eligible = ? 
            WHERE id = ?
        ");
        
        $stmt->execute([$full_name, $national_id, $birth_date, $blood_type, $phone, $email, $address, $chronic, $status, $user_id]);
        
        echo "تم حفظ التعديلات بنجاح!";
    } catch (PDOException $e) {
        echo "عذراً، حدث خطأ في قاعدة البيانات:\n" . $e->getMessage();
    }
    exit;
}
?>

<style>
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .edit-modal-box { background-color: #ffffff; width: 100%; max-width: 550px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); transform: translateY(-20px); transition: transform 0.3s ease; overflow: hidden; font-family: 'Tajawal', sans-serif; }
    .modal-overlay.active .edit-modal-box { transform: translateY(0); }
    .edit-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background-color: #e3f2fd; border-bottom: 1px solid #bbdefb; }
    .edit-modal-header h3 { color: #1565c0; font-size: 16px; margin: 0; display: flex; align-items: center; gap: 8px; }
    .close-edit-modal { background: none; border: none; font-size: 24px; color: #1565c0; cursor: pointer; line-height: 1; transition: transform 0.2s; }
    .close-edit-modal:hover { transform: scale(1.1); }
    .modal-body { padding: 20px; direction: rtl; max-height: 450px; overflow-y: auto; }
    .edit-inputs-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .input-group { display: flex; flex-direction: column; gap: 6px; }
    .input-group.full-width { grid-column: span 2; }
    .input-group label { font-size: 13px; font-weight: 700; color: #333333; }
    .input-group input, .input-group select, .input-group textarea { padding: 10px; border: 1px solid #eaeaea; border-radius: 6px; font-family: inherit; outline: none; font-size: 14px; background-color: #f9fafb; color: #333333; width: 100%; font-weight: 500; }
    .input-group input:focus, .input-group select:focus, .input-group textarea:focus { border-color: #2a9d8f; background-color: #ffffff; }
    .input-group input[readonly] { background-color: #f4f6f9; color: #777777; cursor: not-allowed; }
    .modal-footer { padding: 15px 20px; border-top: 1px solid #eaeaea; display: flex; justify-content: flex-end; gap: 10px; background-color: #f9fafb; }
    .btn-cancel { background-color: #ffffff; color: #777777; border: 1px solid #eaeaea; padding: 9px 18px; border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 14px; transition: background 0.2s; }
    .btn-cancel:hover { background-color: #f4f6f9; }
    .btn-confirm-save { background-color: #2a9d8f; color: #ffffff; border: none; padding: 9px 18px; border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 14px; transition: background 0.2s; }
    .btn-confirm-save:hover { background-color: #21867a; }
</style>

<div class="modal-overlay" id="editAccountModal">
    <div class="edit-modal-box">
        <div class="edit-modal-header">
            <h3><i class="fa-solid fa-user-pen"></i> تعديل بيانات حساب المتبرع</h3>
            <button class="close-edit-modal" id="closeEditModalBtn">&times;</button>
        </div>
        
        <div class="modal-body">
            <div id="editAccountForm" class="edit-inputs-grid">
                
                <input type="hidden" id="editUserIdInput" value="">
                
                <div class="input-group">
                    <label>رقم العضوية (ثابت)</label>
                    <input type="text" id="editMembershipId" readonly>
                </div>
                <div class="input-group">
                    <label>الرقم الوطني</label>
                    <input type="text" id="editNationalId">
                </div>

                <div class="input-group full-width">
                    <label>الاسم الكامل</label>
                    <input type="text" id="editFullName" placeholder="أدخل الاسم الرباعي">
                </div>

                <div class="input-group">
                    <label>تاريخ الميلاد</label>
                    <input type="date" id="editBirthDate">
                </div>
                <div class="input-group">
                    <label>فصيلة الدم</label>
                    <select id="editBloodType">
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>رقم الهاتف</label>
                    <input type="text" id="editPhone" placeholder="09xxxxxxx">
                </div>
                <div class="input-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" id="editEmail" placeholder="example@email.com" dir="ltr">
                </div>

                <div class="input-group full-width">
                    <label>محل الإقامة (العنوان)</label>
                    <input type="text" id="editAddress" placeholder="المدينة، الحي، الشارع...">
                </div>

                <div class="input-group full-width">
                    <label>أمراض مزمنة أو أدوية (إن وجد)</label>
                    <textarea id="editChronicDiseases" rows="2" placeholder="اكتب تفاصيل الحالة الطبية هنا..."></textarea>
                </div>

                <div class="input-group full-width">
                    <label>حالة الحساب</label>
                    <select id="editAccountStatus">
                        <option value="1">نشط / مؤهل للتبرع</option>
                        <option value="0">غير مؤهل / محظور</option>
                    </select>
                </div>

            </div>
        </div>
        
        <div class="modal-footer">
            <button class="btn-cancel" id="cancelEditBtn">إلغاء</button>
            <button class="btn-confirm-save" id="confirmSaveEditBtn">حفظ التعديلات</button>
        </div>
    </div>
</div>