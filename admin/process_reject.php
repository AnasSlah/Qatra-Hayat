<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_id'])) {
    $donation_id = (int)$_POST['donation_id'];
    
    try {
        // تحديث حالة التبرع إلى مرفوض
        $stmt = $pdo->prepare("UPDATE donations SET donation_status = 'rejected' WHERE id = ?");
        
        if ($stmt->execute([$donation_id])) {
            // إرجاع كلمة success لتفعيل الـ JavaScript بنجاح
            echo "success";
        } else {
            echo "حدث خطأ أثناء تحديث حالة الطلب.";
        }
    } catch (PDOException $e) {
        echo "خطأ في قاعدة البيانات: " . $e->getMessage();
    }
} else {
    echo "طلب غير صالح.";
}
?>