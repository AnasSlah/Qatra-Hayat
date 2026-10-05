<?php
session_start(); // لازم نبدأ الجلسة
$current_user_id = $_SESSION['user_id'] ?? 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $dbname = 'qatra_hayat';
    $username = 'root'; 
    $password = ''; 
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // إدخال البيانات في الجدول الموحد (donations) مع تعيين الحالة إلى 'pending'
        $stmt = $pdo->prepare("INSERT INTO donations (user_id, donation_status, weight, blood_pressure, hemoglobin, donation_date, center_id, health_status) VALUES (?, 'pending', ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $current_user_id,
            $_POST['weight'],
            $_POST['blood_pressure'],
            $_POST['hemoglobin'],
            $_POST['donation_date'],
            $_POST['center_id'],
            $_POST['health_status']
        ]);

        echo "success";
    } catch(PDOException $e) {
        echo "error: " . $e->getMessage();
    }
    exit; 
}
?>
<style>
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.6); display: flex; justify-content: center;
        align-items: center; z-index: 9999; animation: fadeIn 0.3s ease;
    }
    .modal-box {
        background-color: #FFFFFF; width: 90%; max-width: 550px; border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; transform: translateY(-20px);
        animation: slideDown 0.3s ease forwards; font-family: 'Cairo', sans-serif; direction: rtl;
    }
    .modal-header { background-color: #F8FAFC; padding: 20px 25px; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 18px; color: #2B2D42; display: flex; align-items: center; gap: 10px; }
    .modal-close-btn { background: none; border: none; font-size: 20px; color: #8D99AE; cursor: pointer; transition: color 0.2s; }
    .modal-close-btn:hover { color: #E63946; }
    .modal-body { padding: 25px; max-height: 70vh; overflow-y: auto; }
    .form-row { display: flex; gap: 15px; margin-bottom: 18px; }
    .form-group { flex: 1; margin-bottom: 18px; }
    .form-row .form-group { margin-bottom: 0; }
    .form-group label { display: block; font-size: 14px; font-weight: 700; color: #2B2D42; margin-bottom: 8px; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid #E2E8F0; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 14px; color: #2B2D42; background-color: #F8FAFC; transition: all 0.3s ease; box-sizing: border-box; }
    .form-control:focus { background-color: #FFFFFF; border-color: #2A9D8F; outline: none; box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.1); }
    .radio-group { display: flex; gap: 30px; margin-top: 10px; padding: 5px 0; }
    .radio-label { display: flex; align-items: center; gap: 8px; font-size: 15px; font-weight: 600; color: #2B2D42; cursor: pointer; }
    .radio-label input[type="radio"] { accent-color: #2A9D8F; width: 18px; height: 18px; cursor: pointer; }
    .modal-footer { padding: 20px 25px; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; gap: 15px; background-color: #F8FAFC; }
    .btn-modal-cancel { background-color: white; border: 1px solid #CBD5E1; color: #2B2D42; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; font-family: 'Cairo', sans-serif; }
    .btn-modal-cancel:hover { background-color: #F1F5F9; }
    .btn-modal-confirm { background-color: #2A9D8F; border: none; color: white; padding: 10px 25px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.3s; font-family: 'Cairo', sans-serif; }
    .btn-modal-confirm:hover { background-color: #1A7D72; }
    .modal-body::-webkit-scrollbar { width: 6px; }
    .modal-body::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 8px; }
    .modal-body::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 8px; }
    .modal-body::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    @keyframes pulse-heart { 0% { transform: scale(1); } 50% { transform: scale(1.15); } 100% { transform: scale(1); } }
    .pulse-heart { animation: pulse-heart 1.5s infinite ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideDown { to { transform: translateY(0); } }
    @media (max-width: 600px) { .form-row { flex-direction: column; gap: 18px; } }
</style>

<div class="modal-overlay" onclick="closePopup()">
    <div class="modal-box" onclick="event.stopPropagation()" id="modalContentBox">
        
        <div class="modal-header">
            <h3><i class="fa-solid fa-heart-pulse" style="color: #E63946;"></i> بيانات حجز موعد التبرع</h3>
            <button class="modal-close-btn" onclick="closePopup()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="modal-body">
            <form action="#" method="POST" id="vitalsForm">
                <div class="form-row">
                    <div class="form-group"><label for="weight">الوزن الحالي (كجم)</label><input type="number" name="weight" id="weight" class="form-control" placeholder="مثال: 75" required></div>
                    <div class="form-group"><label for="bloodPressure">ضغط الدم</label><input type="text" name="blood_pressure" id="bloodPressure" class="form-control" placeholder="مثال: 120/80" dir="ltr" style="text-align: right;" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label for="hemoglobin">مستوى الهيموجلوبين</label><input type="text" name="hemoglobin" id="hemoglobin" class="form-control" placeholder="اختياري (مثال: 14.5)" dir="ltr" style="text-align: right;"></div>
                    <div class="form-group"><label for="donationDate">تاريخ التبرع المطلوب</label><input type="date" name="donation_date" id="donationDate" class="form-control" required></div>
                </div>
                <div class="form-group">
                    <label for="preferredCenter">المراكز المتاحة للتبرع</label>
                    <select name="center_id" id="preferredCenter" class="form-control" required>
                        <option value="" disabled selected>اختر المركز الأقرب إليك</option>
                        <option value="estak">استاك المركزي (الخرطوم)</option>
                        <option value="omdurman">مستشفى أم درمان</option>
                        <option value="bahri">مستشفى بحري التعليمي</option>
                        <option value="moalem">مستشفى المعلم</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>هل تعاني من أي أمراض مزمنة أو تتناول أدوية؟</label>
                    <div class="radio-group">
                        <label class="radio-label"><input type="radio" name="health_status" value="yes" required> نعم</label>
                        <label class="radio-label"><input type="radio" name="health_status" value="no" required> لا</label>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" onclick="closePopup()">إلغاء</button>
            <button type="button" class="btn-modal-confirm" onclick="
                var form = document.getElementById('vitalsForm');
                if (!form.reportValidity()) return; 

                var formData = new FormData(form);
                var btn = this;
                btn.innerHTML = 'جاري الإرسال... <i class=\'fa-solid fa-circle-notch fa-spin\'></i>';
                btn.disabled = true;

                fetch('medical_popup.php', { method: 'POST', body: formData })
                .then(function(response) { return response.text(); })
                .then(function(data) {
                    if (data.trim() === 'success') {
                        var successHtml = '<div style=\'padding: 50px 20px; text-align: center;\'>';
                        successHtml += '<i class=\'fa-solid fa-heart pulse-heart\' style=\'color: #E63946; font-size: 60px; margin-bottom: 25px; display: inline-block;\'></i>';
                        successHtml += '<h3 style=\'color: #2B2D42; font-size: 24px; font-weight: 800; margin-bottom: 10px;\'>تم تقديم الطلب</h3>';
                        successHtml += '<p style=\'color: #8D99AE; font-size: 16px; font-weight: 600; margin-bottom: 30px;\'>انتظر القبول ❤️</p>';
                        successHtml += '<button onclick=\'closePopup(); window.lockDonationUI && window.lockDonationUI();\' class=\'btn-modal-confirm\' style=\'padding: 12px 35px; font-size: 16px;\'>حسناً</button>';
                        successHtml += '</div>';
                        
                        document.getElementById('modalContentBox').innerHTML = successHtml;
                    } else {
                        alert('حدث خطأ في قاعدة البيانات: ' + data);
                        btn.innerHTML = 'تأكيد حجز الموعد';
                        btn.disabled = false;
                    }
                }).catch(function(error) { 
                    alert('حدث خطأ في الاتصال.'); 
                    btn.innerHTML = 'تأكيد حجز الموعد'; 
                    btn.disabled = false; 
                });
            ">تأكيد حجز الموعد</button>
        </div>
    </div>
</div>