<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_id'])) {
    $donation_id = (int)$_POST['donation_id'];
    
    try {
        // 1. جلب الـ user_id المرتبط بطلب التبرع الحالي
        $stmtDonation = $pdo->prepare("SELECT user_id FROM donations WHERE id = ? LIMIT 1");
        $stmtDonation->execute([$donation_id]);
        $donation = $stmtDonation->fetch(PDO::FETCH_ASSOC);

        if ($donation) {
            $user_id = $donation['user_id'];

            // 2. جلب البيانات الصحية الحالية للمستخدِم من جدول users
            $stmtUser = $pdo->prepare("SELECT weight, blood_pressure FROM users WHERE id = ? LIMIT 1");
            $stmtUser->execute([$user_id]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            // تعيين القراءات لتوثيقها في نفس السجل
            $weight = !empty($user['weight']) ? $user['weight'] : 70;
            $blood_pressure = !empty($user['blood_pressure']) ? $user['blood_pressure'] : '120/80';
            $hemoglobin = '14'; // النسبة المبدئية الافتراضية لحين السحب
            $donation_date = date('Y-m-d'); // تاريخ اليوم كحجز للموعد المعتمد
            $center_id = 'omdurman'; // المركز الافتراضي الافتراضي

            // 3. تحديث نفس السجل الموحد بكافة البيانات وتغيير الحالة إلى مقبول
            $stmtUpdate = $pdo->prepare("
                UPDATE donations 
                SET donation_status = 'successful',
                    weight = ?,
                    blood_pressure = ?,
                    hemoglobin = ?,
                    donation_date = ?,
                    center_id = ?
                WHERE id = ?
            ");
            
            if ($stmtUpdate->execute([$weight, $blood_pressure, $hemoglobin, $donation_date, $center_id, $donation_id])) {
                echo "success";
            } else {
                echo "حدث خطأ أثناء تحديث البيانات.";
            }
        } else {
            echo "عذراً، لم يتم العثور على طلب التبرع.";
        }

    } catch (PDOException $e) {
        echo "خطأ في قاعدة البيانات: " . $e->getMessage();
    }
} else {
    echo "طلب غير صالح.";
}
?>