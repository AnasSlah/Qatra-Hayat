<?php
// واجهة عرض الملف الشامل للمتبرع
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

    /* بوب أب عريض جداً عشان يستوعب الملف الشامل */
    .profile-modal-box {
        background-color: #f4f6f9; /* نفس خلفية النظام */
        width: 100%;
        max-width: 950px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transform: translateY(-20px);
        transition: transform 0.3s ease;
        overflow: hidden;
        font-family: 'Tajawal', sans-serif;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }

    .modal-overlay.active .profile-modal-box {
        transform: translateY(0);
    }

    .profile-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        background-color: #ffffff;
        border-bottom: 1px solid #eaeaea;
    }

    .profile-modal-header h3 {
        color: #333333;
        font-size: 18px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .close-profile-modal {
        background: none;
        border: none;
        font-size: 24px;
        color: #df3e4d;
        cursor: pointer;
        line-height: 1;
        transition: transform 0.2s;
    }
    
    .close-profile-modal:hover { transform: scale(1.1); }

    .profile-modal-body {
        padding: 25px;
        direction: rtl;
        overflow-y: auto;
    }

    /* --- نفس تنسيقات الملف الشامل اللي عملناها --- */
    .profile-page-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
        align-items: start;
    }

    .profile-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 30px 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        text-align: center;
        border-top: 5px solid #df3e4d;
        position: relative;
    }

    .status-badge-top {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: #fbeef0;
        color: #df3e4d;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .avatar-circle {
        width: 80px;
        height: 80px;
        background-color: #e3f2fd;
        color: #1565c0;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 35px;
        margin: 25px auto 15px;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .profile-card h3 { font-size: 18px; color: #333333; margin-bottom: 5px; }
    .profile-card p { font-size: 13px; color: #777777; margin-bottom: 20px; }

    .blood-type-display {
        background-color: #fbeef0;
        color: #df3e4d;
        font-size: 18px;
        font-weight: 800;
        padding: 10px;
        border-radius: 8px;
    }

    .form-panel {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .form-section { margin-bottom: 25px; }
    .form-section:last-child { margin-bottom: 0; }

    .form-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #1565c0;
        margin-bottom: 15px;
        border-right: 4px solid #1565c0;
        padding-right: 10px;
    }

    .form-section-title.medical { color: #df3e4d; border-right-color: #df3e4d; }

    .inputs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .input-group { display: flex; flex-direction: column; gap: 6px; }
    .input-group.full-width { grid-column: span 2; }
    
    .input-group label { font-size: 12px; font-weight: 700; color: #777777; }
    
    .input-group input {
        padding: 10px;
        border: 1px solid #eaeaea;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        background-color: #f9fafb;
        color: #333333;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .profile-page-grid { grid-template-columns: 1fr; }
        .profile-card { order: -1; }
    }
</style>

<div class="modal-overlay" id="profileModal">
    <div class="profile-modal-box">
        
        <div class="profile-modal-header">
            <h3><i class="fa-regular fa-address-card"></i> الملف الشخصي الشامل</h3>
            <button class="close-profile-modal" id="closeProfileModalBtn">&times;</button>
        </div>

        <div class="profile-modal-body">
            <div class="profile-page-grid">
                
                <div class="form-panel">
                    
                    <input type="hidden" id="viewUserIdInput" value="">

                    <div class="form-section">
                        <h3 class="form-section-title">البيانات الأساسية</h3>
                        <div class="inputs-grid">
                            <div class="input-group full-width">
                                <label>الاسم الكامل</label>
                                <input type="text" id="viewFullName" readonly>
                            </div>
                            <div class="input-group">
                                <label>الرقم الوطني</label>
                                <input type="text" value="11223344556" readonly>
                            </div>
                            <div class="input-group">
                                <label>تاريخ الميلاد</label>
                                <input type="text" value="2001-12-08" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">معلومات التواصل</h3>
                        <div class="inputs-grid">
                            <div class="input-group">
                                <label>رقم الهاتف الأساسي</label>
                                <input type="text" id="viewPhone" dir="ltr" style="text-align: right;" readonly>
                            </div>
                            <div class="input-group">
                                <label>البريد الإلكتروني</label>
                                <input type="email" value="user@example.com" dir="ltr" style="text-align: right;" readonly>
                            </div>
                            <div class="input-group full-width">
                                <label>محل الإقامة</label>
                                <input type="text" id="viewAddress" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title medical">الملف الطبي (حيوي)</h3>
                        <div class="inputs-grid">
                            <div class="input-group">
                                <label>الوزن (كجم)</label>
                                <input type="text" value="65" readonly>
                            </div>
                            <div class="input-group">
                                <label>معدل ضغط الدم</label>
                                <input type="text" value="120/80" dir="ltr" style="text-align: right;" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <span class="status-badge-top" id="viewStatusBadge"><i class="fa-solid fa-shield-heart"></i> نشط</span>
                    <div class="avatar-circle"><i class="fa-solid fa-user"></i></div>
                    <h3 id="viewCardName">اسم المتبرع</h3>
                    <p id="viewCardId">رقم العضوية</p>
                    <div class="blood-type-display" id="viewCardBlood">O+</div>
                </div>

            </div>
        </div>
    </div>
</div>