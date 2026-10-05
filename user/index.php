<?php
session_start();

// ---------------------------------------------------------
// [مؤقت] محاكاة تسجيل الدخول (امسح هذا البلوك لاحقاً عندما تعتمد على login.php)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
}
// ---------------------------------------------------------

// حماية الصفحة: طرد أي شخص غير مسجل دخول
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // تم التعديل للرجوع لصفحة اللوجن في المسار الرئيسي
    exit();
}

// إعدادات الاتصال بقاعدة البيانات (يفضل استدعاء ملف db.php)
$host = 'localhost';
$dbname = 'qatra_hayat';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جلب الصورة الشخصية مع الاسم
    $stmt = $pdo->prepare("SELECT full_name, profile_photo FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $profile_photo = null;
    if ($user) {
        $name_parts = explode(' ', trim($user['full_name']));
        $first_name = $name_parts[0];
        $avatar_char = mb_substr($first_name, 0, 1, "UTF-8");
        $profile_photo = $user['profile_photo'] ?? null;
    } else {
        $first_name = "ضيف";
        $avatar_char = "?";
    }

} catch(PDOException $e) {
    $first_name = "مستخدم";
    $avatar_char = "-";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المتبرع | قطرة حياة</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-red: #E63946;
            --dark-red: #D62828;
            --bg-color: #F4F7F6;
            --card-bg: #FFFFFF;
            --text-main: #2B2D42;
            --text-muted: #8D99AE;
            --banner-green: #2A9D8F;
            --critical-bg: #FDE8E8; --critical-text: #E63946;
            --warning-bg: #FEF4C3;  --warning-text: #B45309;
            --stable-bg: #E0F2FE;   --stable-text: #0284C7;
            --normal-bg: #DCFCE7;   --normal-text: #16A34A;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); }
        .dashboard-container { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
        .sidebar { background-color: var(--card-bg); box-shadow: -2px 0 10px rgba(0,0,0,0.05); padding: 20px; display: flex; flex-direction: column; justify-content: space-between; }
        .logo { display: flex; align-items: center; gap: 10px; color: var(--primary-red); font-size: 24px; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #eee; font-weight: 800; }
        .nav-links { list-style: none; }
        .nav-links li { margin-bottom: 15px; }
        .nav-links a { text-decoration: none; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 8px; transition: all 0.3s ease; cursor: pointer; }
        .nav-links a:hover, .nav-links li.active a { background-color: var(--primary-red); color: white; }
        
        /* تنسيق خاص لزر تسجيل الخروج */
        .logout-link a { color: var(--primary-red); margin-top: 30px; border-top: 1px solid #eee; border-radius: 0; padding-top: 20px; }
        .logout-link a:hover { background-color: #fff; color: var(--dark-red); opacity: 0.8; }

        .main-content { padding: 30px; display: flex; flex-direction: column; height: 100vh; overflow-y: auto; }
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background-color: var(--card-bg); padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); flex-shrink: 0; }
        .profile-info { display: flex; align-items: center; gap: 15px; font-weight: 600; color: var(--text-main); }
        .avatar-text { width: 45px; height: 45px; border-radius: 50%; background-color: var(--primary-red); color: white; display: flex; justify-content: center; align-items: center; font-size: 18px; font-weight: 700; overflow: hidden; }
        .notifications { position: relative; cursor: pointer; font-size: 22px; color: #6B7280; }
        .notifications .badge { position: absolute; top: -6px; right: -6px; background-color: var(--primary-red); color: white; font-size: 11px; font-weight: 700; width: 18px; height: 18px; display: flex; justify-content: center; align-items: center; border-radius: 50%; }
        .page-content { flex-grow: 1; animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div>
                <div class="logo">
                    <i class="fa-solid fa-droplet"></i>
                    <span>قطرة حياة</span>
                </div>
                <ul class="nav-links">
                    <li class="active"><a href="javascript:void(0)" onclick="loadPage('home.php', this)"><i class="fa-solid fa-house-chimney"></i> الرئيسية</a></li>
                    <li><a href="javascript:void(0)" onclick="loadPage('history.php', this)"><i class="fa-regular fa-clipboard"></i> السجل</a></li>
                    <li><a href="javascript:void(0)" onclick="loadPage('centers.php', this)"><i class="fa-solid fa-location-dot"></i> المراكز</a></li>
                    <li><a href="javascript:void(0)" onclick="loadPage('profile.php', this)"><i class="fa-solid fa-user"></i> حسابي</a></li>
                    <li><a href="javascript:void(0)" onclick="loadPage('contact.php', this)"><i class="fa-solid fa-phone"></i> تواصل</a></li>
                    
                    <li class="logout-link"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج</a></li>
                </ul>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <div class="profile-info">
                    <div class="avatar-text" id="top-header-avatar">
                        <?php if (!empty($profile_photo) && file_exists('uploads/' . $profile_photo)): ?>
                            <img src="uploads/<?php echo htmlspecialchars($profile_photo); ?>" alt="صورة المستخدم" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo htmlspecialchars($avatar_char); ?>
                        <?php endif; ?>
                    </div>
                    <span id="top-header-name">مرحباً، <?php echo htmlspecialchars($first_name); ?> 👋</span>
                </div>
                
                <!-- التعديل هنا: إضافة زر الخروج بجانب الإشعارات -->
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div class="notifications">
                        <i class="fa-solid fa-bell"></i>
                        <span class="badge">1</span>
                    </div>
                    
                    <a href="logout.php" style="color: var(--primary-red); text-decoration: none; font-size: 20px; transition: 0.3s;" title="تسجيل الخروج" onmouseover="this.style.color='var(--dark-red)'" onmouseout="this.style.color='var(--primary-red)'">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </header>

            <div class="page-content" id="page-content">
                <div style="text-align: center; margin-top: 50px; color: var(--text-muted);">
                    <i class="fa-solid fa-circle-notch fa-spin fa-2x"></i>
                    <p style="margin-top: 10px;">جاري تحميل البيانات...</p>
                </div>
            </div>
        </main>
    </div>

    <div id="popup-area"></div>

    <script>
        function loadPage(pageUrl, element) {
            const contentDiv = document.getElementById('page-content');
            
            fetch(pageUrl)
                .then(response => {
                    if (!response.ok) throw new Error('حدث خطأ في جلب الصفحة');
                    return response.text();
                })
                .then(html => {
                    contentDiv.innerHTML = html;
                    
                    const scripts = contentDiv.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                    
                    contentDiv.style.animation = 'none';
                    contentDiv.offsetHeight; 
                    contentDiv.style.animation = null; 
                    
                    if(element) {
                        document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
                        element.parentElement.classList.add('active');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    contentDiv.innerHTML = `
                        <div style="text-align:center; margin-top:50px; color: var(--primary-red);">
                            <i class="fa-solid fa-triangle-exclamation fa-3x"></i>
                            <h3 style="margin-top:15px;">عذراً، لم نتمكن من تحميل الصفحة.</h3>
                        </div>`;
                });
        }

        // ================= [ إضافة دوال التحكم بالنافذة المنبثقة ] ================= //
        function openPopup(url) {
            const popupArea = document.getElementById('popup-area');
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    popupArea.innerHTML = html;
                    
                    // تشغيل أي أكواد جافاسكريبت داخل النافذة
                    const scripts = popupArea.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                })
                .catch(error => console.error('خطأ في تحميل النافذة:', error));
        }

        function closePopup() {
            document.getElementById('popup-area').innerHTML = '';
        }
        // ========================================================================= //

        document.addEventListener('DOMContentLoaded', () => {
            const activeLink = document.querySelector('.nav-links li.active a');
            loadPage('home.php', activeLink); 
        });
    </script>

</body>
</html>