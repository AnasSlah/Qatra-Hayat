<?php
// واجهة تأكيد رفض طلب التبرع واختيار السبب
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

    /* كلاس active عشان نظهر البوب أب لما نضغط على الزرار */
    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-box {
        background-color: #ffffff;
        width: 100%;
        max-width: 450px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        overflow: hidden;
        font-family: 'Tajawal', sans-serif;
    }

    .modal-overlay.active .modal-box {
        transform: translateY(0);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #ffebee;
        border-bottom: 1px solid #ffcdd2;
    }

    .modal-header h3 {
        color: #c62828;
        font-size: 16px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #c62828;
        cursor: pointer;
        line-height: 1;
        transition: transform 0.2s;
    }
    
    .close-modal:hover {
        transform: scale(1.1);
    }

    .modal-body {
        padding: 20px;
        direction: rtl;
    }

    .donor-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f4f6f9;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 700;
        color: #333333;
    }

    .modal-body .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .modal-body label {
        font-size: 14px;
        font-weight: 700;
        color: #333333;
    }

    .modal-body select {
        padding: 12px;
        border: 1px solid #eaeaea;
        border-radius: 6px;
        font-family: inherit;
        outline: none;
        font-size: 14px;
        background-color: #ffffff;
        width: 100%;
        cursor: pointer;
    }

    .modal-body select:focus {
        border-color: #df3e4d;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #eaeaea;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background-color: #f9fafb;
    }

    .btn-cancel {
        background-color: #ffffff;
        color: #777777;
        border: 1px solid #eaeaea;
        padding: 9px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
        font-size: 14px;
        transition: background 0.2s;
    }

    .btn-cancel:hover {
        background-color: #f4f6f9;
    }

    .btn-confirm-reject {
        background-color: #df3e4d;
        color: #ffffff;
        border: none;
        padding: 9px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        font-weight: 700;
        font-size: 14px;
        transition: background 0.2s;
    }

    .btn-confirm-reject:hover {
        background-color: #c93543;
    }
</style>

<div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-triangle-exclamation"></i> تأكيد رفض طلب التبرع</h3>
            <button class="close-modal" id="closeModalBtn">&times;</button>
        </div>
        
        <div class="modal-body">
            
            <input type="hidden" id="rejectDonationIdInput" value="">

            <div class="donor-summary">
                <span id="modalDonorName">اسم المتبرع</span>
                <span class="blood-badge" id="modalDonorBlood" style="background-color: #fbeef0; color: #df3e4d; padding: 4px 10px; border-radius: 6px;">فصيلة الدم</span>
            </div>
            
            <div class="form-group">
                <label>يرجى تحديد سبب الرفض الإداري أو الطبي:</label>
                <select id="rejectReasonSelect">
                    <option value="" disabled selected>-- اختر السبب من القائمة --</option>
                    <option value="hemoglobin">مستوى الهيموجلوبين أقل من الحد المسموح</option>
                    <option value="pressure">معدل ضغط الدم غير منتظم</option>
                    <option value="weight">الوزن أقل من الحد المسموح (50 كجم)</option>
                    <option value="time">لم تنقضِ المدة الكافية منذ آخر تبرع (3 أشهر)</option>
                    <option value="medical">تاريخ مرضي أو تناول أدوية تمنع التبرع حالياً</option>
                    <option value="other">أسباب إدارية أو طبية أخرى</option>
                </select>
            </div>
        </div>
        
        <div class="modal-footer">
            <button class="btn-cancel" id="cancelRejectBtn">تراجع</button>
            <button class="btn-confirm-reject" id="confirmRejectBtn">تأكيد الرفض</button>
        </div>
    </div>
</div>