<?php
// تأكد من بدء الجلسة إذا لم تكن مبدوءة بالفعل
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'qatra_hayat';
$username = 'root'; 
$password = ''; 

// 🔴 معالجة طلب الـ AJAX من الجافاسكربت لإرسال الرسالة 🔴
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message_ajax'])) {
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // جلب معرف المستخدم إذا كان مسجل دخوله
        $user_id = $_SESSION['user_id'] ?? null;
        
        // تنظيف وتجهيز البيانات
        $sender_name = trim($_POST['sender_name'] ?? '');
        $contact_info = trim($_POST['contact_info'] ?? '');
        $subject = $_POST['subject'] ?? 'general';
        $message = trim($_POST['message'] ?? '');

        // التحقق من أن الحقول غير فارغة
        if (empty($sender_name) || empty($contact_info) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'يرجى تعبئة جميع الحقول المطلوبة.']);
            exit;
        }

        // إدخال البيانات في جدول الرسائل الجديد
        $query = "INSERT INTO contact_messages (user_id, sender_name, contact_info, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id, $sender_name, $contact_info, $subject, $message]);

        // إرجاع رسالة نجاح
        echo json_encode(['status' => 'success']);
        exit;

    } catch(PDOException $e) {
        // في حال وجود خطأ في قاعدة البيانات
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ في قاعدة البيانات: ' . $e->getMessage()]);
        exit;
    }
}
?>

<style>
    /* ترويسة الصفحة */
    .page-header-title {
        font-size: 22px;
        color: var(--text-main);
        margin-bottom: 25px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* تخطيط الصفحة (نموذج المراسلة + الواتساب) */
    .contact-layout {
        display: flex;
        gap: 25px;
        align-items: stretch;
    }

    /* قسم نموذج المراسلة */
    .contact-form-section {
        flex: 2;
        background-color: var(--card-bg);
        border-radius: 12px;
        padding: 35px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .section-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* تنسيق حقول الإدخال */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 14px;
        color: var(--text-main);
        background-color: #F8FAFC;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-control::placeholder {
        color: #94A3B8;
    }

    .form-control:focus {
        background-color: #FFFFFF;
        border-color: var(--primary-red);
        outline: none;
        box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .btn-submit {
        width: 100%;
        background-color: var(--primary-red);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        margin-top: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .btn-submit:hover:not(:disabled) {
        background-color: var(--dark-red);
        transform: translateY(-2px);
    }
    
    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* قسم قناة الواتساب */
    .whatsapp-section {
        flex: 1;
        background-color: #F0FDF4; /* خلفية خضراء فاتحة جداً */
        border-top: 5px solid #22C55E;
        border-radius: 12px;
        padding: 35px 25px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .wa-icon {
        font-size: 45px;
        color: #22C55E;
        margin-bottom: 15px;
    }

    .whatsapp-section h3 {
        color: #166534;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .whatsapp-section p {
        color: #15803D;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .qr-code-box {
        width: 130px;
        height: 130px;
        background-color: white;
        border: 2px dashed #86EFAC;
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 25px;
        padding: 8px; /* إضافة حواف داخلية لكي لا يلتصق الكود بالإطار */
        box-sizing: border-box;
    }

    .qr-code-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
    }

    .btn-whatsapp {
        background-color: #22C55E;
        color: white;
        text-decoration: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-whatsapp:hover {
        background-color: #16A34A;
        transform: scale(1.05);
    }

    /* تصميم متجاوب للموبايل */
    @media (max-width: 992px) {
        .contact-layout {
            flex-direction: column;
        }
        .contact-form-section, .whatsapp-section {
            width: 100%;
        }
    }
</style>

<h2 class="page-header-title">
    <i class="fa-solid fa-headset" style="color: var(--primary-red);"></i>
    تواصل معنا
</h2>

<div class="contact-layout">
    
    <div class="contact-form-section">
        <h3 class="section-title">أرسل لنا رسالة</h3>
        
        <form id="contact-form" onsubmit="submitContactForm(event)">
            <div class="form-group">
                <label for="fullName">الاسم الكامل</label>
                <input type="text" id="fullName" name="sender_name" class="form-control" placeholder="أدخل اسمك الكريم" required>
            </div>

            <div class="form-group">
                <label for="contactInfo">البريد الإلكتروني أو الهاتف</label>
                <input type="text" id="contactInfo" name="contact_info" class="form-control" placeholder="للتواصل معك" dir="auto" required>
            </div>

            <div class="form-group">
                <label for="subject">موضوع الرسالة</label>
                <select id="subject" name="subject" class="form-control" required>
                    <option value="general">استفسار عام</option>
                    <option value="technical">مشكلة تقنية في الحساب</option>
                    <option value="urgent">حالة تبرع عاجلة</option>
                    <option value="suggestion">اقتراح تطوير</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">رسالتك</label>
                <textarea id="message" name="message" class="form-control" placeholder="اكتب تفاصيل رسالتك هنا..." required></textarea>
            </div>

            <button type="submit" class="btn-submit">إرسال الرسالة <i class="fa-regular fa-paper-plane"></i></button>
        </form>
    </div>

    <div class="whatsapp-section">
        <i class="fa-brands fa-whatsapp wa-icon"></i>
        <h3>قناة الواتساب الرسمية</h3>
        <p>انضم الآن لمتابعة أحدث أخبار المنصة والحالات العاجلة للتبرع في منطقتك الجغرافية.</p>
        
        <div class="qr-code-box">
            <!-- تم استخدام API لتوليد QR Code ديناميكي من الرابط -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://whatsapp.com/channel/0029Vb8O7UA3bbV1zMyIKP0o" alt="QR Code لمجموعة الواتساب">
        </div>

        <!-- تم وضع الرابط هنا ليفتح في نافذة جديدة عند الضغط عليه -->
        <a href="https://whatsapp.com/channel/0029Vb8O7UA3bbV1zMyIKP0o" target="_blank" class="btn-whatsapp">
            انضم للقناة الآن
        </a>
    </div>

</div>

<!-- تضمين مكتبة SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function submitContactForm(event) {
    event.preventDefault(); // منع المتصفح من إعادة تحميل الصفحة
    
    let form = document.getElementById('contact-form');
    let formData = new FormData(form);
    
    // إضافة متغير للـ PHP لمعرفة أن هذا طلب إرسال رسالة
    formData.append('send_message_ajax', '1');

    // تغيير حالة الزر ليعطي إحساساً بالتحميل
    let submitBtn = form.querySelector('.btn-submit');
    let originalBtnHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = 'جاري الإرسال... <i class="fa-solid fa-spinner fa-spin"></i>';
    submitBtn.disabled = true;

    // إرسال البيانات باستخدام Fetch API
    fetch('contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('مشكلة في الاتصال بالسيرفر');
        return response.text(); 
    })
    .then(textData => {
        try {
            let data = JSON.parse(textData);
            
            if (data.status === 'success') {
                // إظهار تنبيه النجاح بالنص المطلوب
                Swal.fire({
                    icon: 'success',
                    title: 'تم الإرسال بنجاح',
                    text: 'تم ارسال رسالتك بنجاح شكرا علي المقترح',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#16A34A'
                });
                // تفريغ النموذج بعد النجاح
                form.reset();
            } else {
                // إظهار تنبيه الخطأ من السيرفر
                Swal.fire({
                    icon: 'error',
                    title: 'حدث خطأ',
                    text: data.message || 'لم نتمكن من إرسال رسالتك.',
                    confirmButtonText: 'إغلاق',
                    confirmButtonColor: '#DC2626'
                });
            }
        } catch(e) {
            console.error("خطأ في قراءة الـ JSON:", e, textData);
            Swal.fire({
                icon: 'error',
                title: 'خطأ داخلي',
                text: 'يرجى التحقق من الخادم وقاعدة البيانات.',
                confirmButtonText: 'إغلاق',
                confirmButtonColor: '#DC2626'
            });
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        Swal.fire({
            icon: 'warning',
            title: 'فشل الاتصال',
            text: 'تأكد من اتصالك بالإنترنت وأن الخادم يعمل.',
            confirmButtonText: 'إغلاق',
            confirmButtonColor: '#D97706'
        });
    })
    .finally(() => {
        // إعادة الزر لحالته الأصلية في جميع الحالات
        submitBtn.innerHTML = originalBtnHtml;
        submitBtn.disabled = false;
    });
}
</script>