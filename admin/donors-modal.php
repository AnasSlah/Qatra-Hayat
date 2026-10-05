<?php
require_once 'db.php';

// سحب قائمة عشوائية للمتبرعين المؤهلين كأمثلة للمرشحين (يمكن لاحقاً ربطها بالـ ID الخاص بالحالة عبر AJAX)
$stmt = $pdo->query("SELECT id, full_name FROM users WHERE is_eligible = 1 ORDER BY RAND() LIMIT 5");
$candidate_donors = $stmt->fetchAll();
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

    /* تكبير العرض ليناسب قائمة المتبرعين */
    .donors-modal-box {
        background-color: #ffffff;
        width: 100%;
        max-width: 600px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        overflow: hidden;
        font-family: 'Tajawal', sans-serif;
    }

    .modal-overlay.active .donors-modal-box {
        transform: translateY(0);
    }

    /* هيدر بلون مختلف (Teal) لتمييزه عن بوب أب الرفض */
    .donors-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #e0f2f1; /* لون أخضر فاتح */
        border-bottom: 1px solid #b2dfdb;
    }

    .donors-modal-header h3 {
        color: #00796b;
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .close-donors-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #00796b;
        cursor: pointer;
        line-height: 1;
        transition: transform 0.2s;
    }
    
    .close-donors-modal:hover {
        transform: scale(1.1);
    }

    .modal-body {
        padding: 20px;
        direction: rtl;
        max-height: 400px;
        overflow-y: auto; /* إضافة سكرول لو عدد المتبرعين كبير */
    }

    /* شريط ملخص الحالة أعلى القائمة */
    .case-summary-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f4f6f9;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #eaeaea;
    }

    .case-summary-bar .hospital-name {
        font-weight: 700;
        color: #333333;
        font-size: 15px;
    }

    /* تصميم كروت المتبرعين المصغرة داخل البوب أب */
    .mini-donor-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        border: 1px solid #eaeaea;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background 0.2s;
    }

    .mini-donor-card:hover {
        background-color: #fcfcfc;
        border-color: #2a9d8f;
    }

    .mini-donor-info h4 {
        font-size: 14px;
        color: #333333;
        margin-bottom: 4px;
        font-weight: 700;
    }

    .mini-donor-info p {
        font-size: 12px;
        color: #777777;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-notify {
        background-color: #2a9d8f;
        color: #ffffff;
        border: none;
        padding: 7px 12px;
        border-radius: 4px;
        font-weight: 700;
        font-size: 12px;
        cursor: pointer;
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .btn-notify:hover {
        background-color: #21867a;
    }
    
    .btn-notify.sent {
        background-color: #e0e0e0;
        color: #777777;
        cursor: not-allowed;
    }
</style>

<div class="modal-overlay" id="donorsModal">
    <div class="donors-modal-box">
        <div class="donors-modal-header">
            <h3><i class="fa-solid fa-users-viewfinder"></i> المتبرعين المرشحين للحالة</h3>
            <button class="close-donors-modal" id="closeDonorsModalBtn">&times;</button>
        </div>
        
        <div class="modal-body">
            
            <div class="case-summary-bar">
                <span class="hospital-name" id="modalCaseHospital">اسم المستشفى</span>
                <span class="status-badge" id="modalCaseBlood" style="background-color: #fbeef0; color: #df3e4d; font-size: 14px; font-weight: 800;">فصيلة الدم</span>
            </div>
            
            <?php if (count($candidate_donors) > 0): ?>
                <?php foreach ($candidate_donors as $index => $donor): ?>
                    <div class="mini-donor-card">
                        <div class="mini-donor-info">
                            <h4><?= htmlspecialchars($donor['full_name']) ?></h4>
                            <p><i class="fa-solid fa-location-dot" style="color:#2a9d8f;"></i> (موقع افتراضي) • آخر تبرع: (غير محدد)</p>
                        </div>
                        <?php if ($index % 3 == 0): // مجرد محاكاة عشان نخلي زرار فيهم "تم الإشعار" ?>
                            <button class="btn-notify sent"><i class="fa-solid fa-check"></i> تم الإشعار</button>
                        <?php else: ?>
                            <button class="btn-notify" onclick="this.classList.add('sent'); this.innerHTML='<i class=\'fa-solid fa-check\'></i> تم الإشعار';"><i class="fa-solid fa-bell"></i> إرسال إشعار</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #777; padding: 20px;">لا يوجد متبرعين مؤهلين متاحين حالياً في النظام.</p>
            <?php endif; ?>

        </div>
    </div>
</div>