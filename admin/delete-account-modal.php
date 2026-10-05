<?php
require_once 'db.php';

// معالجة طلب الحذف القادم من الجافاسكريبت
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];

    if ($user_id === 0) {
        echo "خطأ: لم يتم التعرف على الحساب المطلوب حذفه!";
        exit;
    }

    try {
        // 1. حذف أي طلبات تبرع مرتبطة بهذا المستخدم أولاً (لتجنب خطأ الـ Foreign Key)
        $pdo->exec("DELETE FROM donations WHERE user_id = $user_id");
        
        // 2. حذف حساب المستخدم نفسه
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        if ($stmt->rowCount() > 0) {
            echo "تم حذف الحساب وجميع البيانات المرتبطة به بنجاح!";
        } else {
            echo "لم يتم العثور على الحساب، ربما تم حذفه مسبقاً.";
        }
    } catch (PDOException $e) {
        echo "عذراً، حدث خطأ في قاعدة البيانات:\n" . $e->getMessage();
    }
    exit; // إيقاف التنفيذ هنا حتى لا يعيد طباعة واجهة الـ HTML
}
?>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .delete-modal-box {
        background-color: #ffffff;
        width: 100%;
        max-width: 400px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        overflow: hidden;
        font-family: 'Tajawal', sans-serif;
        text-align: center;
    }

    .modal-overlay.active .delete-modal-box {
        transform: translateY(0);
    }

    .delete-modal-header {
        background-color: #ffebee;
        padding: 20px;
        color: #c62828;
        font-size: 40px;
    }

    .delete-modal-body {
        padding: 25px 20px;
        color: #333333;
    }

    .delete-modal-body h3 {
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 800;
    }

    .delete-modal-body p {
        font-size: 14px;
        color: #777777;
        margin-bottom: 5px;
        line-height: 1.5;
    }

    #deleteAccountNameDisplay {
        font-weight: 800;
        color: #c62828;
        font-size: 16px;
    }

    .delete-modal-footer {
        padding: 15px 20px 20px;
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .btn-disagree {
        background-color: #f4f6f9;
        color: #777777;
        border: 1px solid #eaeaea;
        padding: 10px 25px;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
        transition: background 0.2s;
    }

    .btn-disagree:hover { background-color: #eaeaea; }

    .btn-agree {
        background-color: #df3e4d;
        color: #ffffff;
        border: none;
        padding: 10px 25px;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
        transition: background 0.2s;
    }

    .btn-agree:hover { background-color: #c93543; }
</style>

<div class="modal-overlay" id="deleteAccountModal">
    <div class="delete-modal-box">
        
        <div class="delete-modal-header">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>

        <div class="delete-modal-body">
            <h3>تأكيد حذف الحساب</h3>
            <p>هل أنت متأكد من حذف الحساب الخاص بـ:</p>
            <p id="deleteAccountNameDisplay">اسم المتبرع</p>
            
            <input type="hidden" id="deleteAccountIdInput" value="">
            
            <p style="margin-top: 10px; font-size: 12px; color: #c62828;">هذا الإجراء لا يمكن التراجع عنه.</p>
        </div>
        
        <div class="delete-modal-footer">
            <button class="btn-disagree" id="cancelDeleteAccountBtn">لا أوافق</button>
            <button class="btn-agree" id="confirmDeleteAccountBtn">موافق</button>
        </div>
    </div>
</div>