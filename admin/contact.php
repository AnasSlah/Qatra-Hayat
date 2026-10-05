<?php
// إعداد الرابط الافتراضي للواتساب (تم إضافة رابط القناة الخاص بك)
$whatsapp_channel_link = "https://whatsapp.com/channel/0029Vb8O7UA3bbV1zMyIKP0o";
?>

<style>
    .contact-page-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 75vh;
    }

    .qr-card {
        background-color: #ffffff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        text-align: center;
        max-width: 420px;
        width: 100%;
        border-top: 6px solid #25D366;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .qr-card i.fa-whatsapp {
        color: #25D366;
        font-size: 55px;
        margin-bottom: 15px;
    }

    .qr-card h3 {
        font-size: 22px;
        font-weight: 800;
        color: #333333;
        margin-bottom: 10px;
    }

    .qr-card p {
        font-size: 14px;
        color: #777777;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .qr-image-container {
        background-color: #f9fafb;
        padding: 20px;
        border-radius: 12px;
        border: 2px dashed #eaeaea;
        display: inline-block;
        margin-bottom: 30px;
        transition: transform 0.3s;
    }

    .qr-image-container:hover {
        transform: scale(1.05);
        border-color: #25D366;
    }

    .qr-image-container img {
        width: 200px;
        height: 200px;
        object-fit: contain;
    }

    .btn-whatsapp {
        background-color: #25D366;
        color: #ffffff;
        border: none;
        padding: 14px 25px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 15px;
        cursor: pointer;
        font-family: inherit;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        transition: background 0.2s, transform 0.2s;
        text-decoration: none;
    }

    .btn-whatsapp:hover {
        background-color: #1ebe57;
        transform: translateY(-2px);
    }
</style>

<div class="contact-page-container">
    <div class="qr-card">
        <i class="fa-brands fa-whatsapp"></i>
        
        <h3>قناة التواصل الرسمية</h3>
        <p>امسح رمز الاستجابة السريعة (QR Code) بكاميرا هاتفك للانضمام إلى قناة "قطرة حياة" على الواتساب لمتابعة الحالات الطارئة.</p>
        
        <div class="qr-image-container">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($whatsapp_channel_link) ?>" alt="QR Code">
        </div>

        <a href="<?= htmlspecialchars($whatsapp_channel_link) ?>" target="_blank" class="btn-whatsapp">
            <i class="fa-brands fa-whatsapp"></i> الذهاب للقناة 
        </a>
    </div>
</div>