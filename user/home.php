<?php
session_start();
$current_user_id = $_SESSION['user_id'] ?? 1;

// إعدادات الاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'qatra_hayat';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. جلب عدد المتبرعين الكلي من النظام
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmtUsers->fetchColumn() ?: 1245;

    // 2. جلب الأبطال الذين تم قبول تبرعهم أو أكملوه بالفعل من جدول donations
    $stmtHeroes = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM donations WHERE donation_status IN ('successful', 'completed')");
    $total_heroes = $stmtHeroes->fetchColumn() ?: 830;

    // 3. التحقق هل المستخدم الحالي لديه طلب تبرع قيد المراجعة؟
    $stmtCheckPending = $pdo->prepare("SELECT COUNT(*) FROM donations WHERE user_id = ? AND donation_status = 'pending'");
    $stmtCheckPending->execute([$current_user_id]);
    $has_pending_appointment = $stmtCheckPending->fetchColumn() > 0;

    // 4. التحقق من فترة النقاهة الإلزامية (3 أشهر بعد آخر تبرع ناجح)
    $stmtCheckRecent = $pdo->prepare("
        SELECT donation_date 
        FROM donations 
        WHERE user_id = ? AND donation_status IN ('successful', 'completed') 
        ORDER BY donation_date DESC LIMIT 1
    ");
    $stmtCheckRecent->execute([$current_user_id]);
    $last_donation = $stmtCheckRecent->fetchColumn();

    $is_locked_3_months = false;
    $days_remaining = 0;

    if ($last_donation) {
        $last_date = strtotime($last_donation);
        $eligible_date = strtotime('+3 months', $last_date);
        $today = time();
        
        if ($today < $eligible_date) {
            $is_locked_3_months = true;
            $days_remaining = ceil(($eligible_date - $today) / 86400); // تحويل الفرق لأيام
        }
    }

    // 5. جلب طلبات التبرع الحالية النشطة المطلوبة من المستشفيات
    $requests = $pdo->query("
        SELECT dr.blood_type, c.name as center_name, dr.case_description, dr.priority 
        FROM donation_requests dr 
        JOIN centers c ON dr.center_id = c.id 
        WHERE dr.status = 'active'
        ORDER BY FIELD(dr.priority, 'critical', 'warning', 'stable', 'normal')
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $total_users = 1245; $total_heroes = 830; 
    $has_pending_appointment = false; $is_locked_3_months = false; 
    $requests = [];
}
?>

<style>
    /* البانر الأخضر/الأحمر المدمج */
    .status-banner { border-radius: 12px; padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; color: white; margin-bottom: 30px; transition: all 0.3s ease; }
    .banner-green { background-color: var(--banner-green); box-shadow: 0 4px 15px rgba(42, 157, 143, 0.2); }
    .banner-red { background-color: var(--primary-red); box-shadow: 0 4px 15px rgba(230, 57, 70, 0.2); }
    .banner-text h2 { font-size: 22px; margin-bottom: 5px; display: flex; align-items: center; gap: 10px; }
    .banner-text p { font-size: 14px; opacity: 0.9; }
    .btn-book { background-color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: transform 0.2s; }
    .btn-green-text { color: var(--banner-green); }
    .btn-red-text { color: var(--primary-red); }
    .btn-book:hover { transform: scale(1.05); }
    .btn-book:disabled { cursor: not-allowed; opacity: 0.8; transform: none; }

    /* الكروت الإحصائية */
    .section-title { font-size: 18px; color: var(--text-main); margin-bottom: 15px; font-weight: 700; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background-color: var(--card-bg); padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: flex-start; gap: 15px; transition: transform 0.3s ease; border-right: 3px solid var(--primary-red); }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-icon { width: 45px; height: 45px; border-radius: 10px; background-color: rgba(230, 57, 70, 0.1); color: var(--primary-red); display: flex; justify-content: center; align-items: center; font-size: 20px; }
    .stat-details { text-align: right; width: 100%; }
    .stat-details h3 { font-size: 13px; color: var(--text-muted); margin-bottom: 2px; font-weight: 600; }
    .stat-details p { font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0; }

    /* جدول الطلبات */
    .recent-requests { background-color: var(--card-bg); padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .requests-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .requests-table th, .requests-table td { padding: 15px; text-align: right; border-bottom: 1px solid #eee; }
    .requests-table th { color: var(--text-muted); font-weight: 600; font-size: 14px; }
    .blood-type { background-color: rgba(43, 45, 66, 0.05); color: var(--text-main); padding: 5px 10px; border-radius: 4px; font-weight: 800; font-size: 16px; direction: ltr; display: inline-block; }
    .status { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .status.critical { background-color: var(--critical-bg); color: var(--critical-text); }
    .status.warning { background-color: var(--warning-bg); color: var(--warning-text); }
    .status.stable { background-color: var(--stable-bg); color: var(--stable-text); }
    .btn-action { background-color: var(--bg-color); border: none; color: var(--primary-red); padding: 8px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: 0.3s; }
    .btn-action:hover:not(:disabled) { background-color: var(--primary-red); color: white; }
    .btn-action:disabled { background-color: #CBD5E1; color: white; cursor: not-allowed; }

    @media (max-width: 768px) {
        .status-banner { flex-direction: column; text-align: center; gap: 15px; }
        .requests-table { display: block; overflow-x: auto; white-space: nowrap; }
    }
</style>

<section class="status-banner <?php echo ($has_pending_appointment || $is_locked_3_months) ? 'banner-red' : 'banner-green'; ?>" id="statusBanner">
    <div class="banner-text">
        <?php if($is_locked_3_months): ?>
            <h2>فترة نقاهة إلزامية <i class="fa-solid fa-shield-heart"></i></h2>
            <p>حفاظاً على صحتك، لا يمكنك التبرع بالدم إلا بعد مرور 3 أشهر. (متبقي <?php echo $days_remaining; ?> يوم).</p>
        <?php elseif($has_pending_appointment): ?>
            <h2>في انتظار مراجعة طلبك <i class="fa-solid fa-clock"></i></h2>
            <p>تم تسجيل طلب التبرع بنجاح، وهو قيد المراجعة من قبل الإدارة حالياً.</p>
        <?php else: ?>
            <h2>أنت مؤهل للتبرع <i class="fa-solid fa-circle-check"></i></h2>
            <p>نظامك الصحي ممتاز وجاهز لإنقاذ الأرواح اليوم.</p>
        <?php endif; ?>
    </div>
    <div id="bannerActionArea">
        <?php if($is_locked_3_months): ?>
            <button class="btn-book btn-red-text" disabled>غير متاح حالياً</button>
        <?php elseif($has_pending_appointment): ?>
            <button class="btn-book btn-red-text" onclick="document.querySelectorAll('.nav-links a')[1].click()">سجل طلباتي <i class="fa-solid fa-clipboard-list"></i></button>
        <?php else: ?>
            <button class="btn-book btn-green-text" onclick="window.handleDonationAction()">حجز موعد سريع</button>
        <?php endif; ?>
    </div>
</section>

<h3 class="section-title">إحصائيات النظام:</h3>
<section class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-details">
            <h3>عدد المتبرعين في النظام</h3>
            <p><?php echo number_format($total_users); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-hand-holding-medical"></i></div>
        <div class="stat-details">
            <h3>الأبطال الذين تبرعوا</h3>
            <p><?php echo number_format($total_heroes); ?></p>
        </div>
    </div>
    <div class="stat-card" style="cursor: pointer; justify-content: flex-start; gap: 15px; padding: 15px 25px; position: relative;" onclick="try{document.getElementById('systemDate').showPicker()}catch(e){}">
        <div class="stat-icon" style="background-color: #FFF1F2; color: #E63946; width: 45px; height: 45px; border-radius: 10px; display: flex; justify-content: center; align-items: center; font-size: 19px; margin: 0; flex-shrink: 0;">
            <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div class="stat-details" style="text-align: right; width: auto; margin: 0;">
            <h3 style="color: #8D99AE; font-size: 13px; font-weight: 600; margin-bottom: 5px;">التاريخ</h3>
            <div style="display: flex; align-items: center; gap: 8px; justify-content: flex-start;">
                <span style="direction: ltr; color: #2B2D42; font-weight: 800; font-size: 17px;"><?php echo date('Y / m / d'); ?></span>
                <i class="fa-regular fa-calendar" style="color: #64748B; font-size: 15px;"></i>
            </div>
        </div>
        <input type="date" id="systemDate" value="<?php echo date('Y-m-d'); ?>" style="position: absolute; opacity: 0; width: 0; height: 0; pointer-events: none; border: none; padding: 0;">
    </div>
</section>

<section class="recent-requests">
    <h3 class="section-title" style="margin-bottom: 5px;">طلبات التبرع العاجلة في المستشفيات</h3>
    <table class="requests-table">
        <thead>
            <tr>
                <th>الفصيلة المطلوبة</th>
                <th>المستشفى / المركز</th>
                <th>وصف الحالة</th>
                <th>درجة الأولوية</th>
                <th>الإجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $req): 
                    $priorityClass = ''; $priorityText = ''; $textColor = '';
                    switch ($req['priority']) {
                        case 'critical': $priorityClass = 'critical'; $priorityText = 'خطيرة جداً'; $textColor = 'var(--critical-text)'; break;
                        case 'warning': $priorityClass = 'warning'; $priorityText = 'متوسطة'; $textColor = 'var(--warning-text)'; break;
                        case 'stable': $priorityClass = 'stable'; $priorityText = 'مستقرة'; $textColor = 'var(--stable-text)'; break;
                        case 'normal': $priorityClass = 'normal'; $priorityText = 'عادية'; $textColor = 'var(--normal-text)'; break;
                    }
                ?>
                <tr>
                    <td><span class="blood-type" style="color: <?php echo $textColor; ?>;"><?php echo htmlspecialchars($req['blood_type']); ?></span></td>
                    <td><?php echo htmlspecialchars($req['center_name']); ?></td>
                    <td style="color: var(--text-muted); font-size: 13px;"><?php echo htmlspecialchars($req['case_description']); ?></td>
                    <td><span class="status <?php echo $priorityClass; ?>"><?php echo $priorityText; ?></span></td>
                    <td>
                        <?php if($is_locked_3_months): ?>
                            <button class="btn-action" disabled title="متبقي <?php echo $days_remaining; ?> يوم">غير مؤهل للآن</button>
                        <?php elseif($has_pending_appointment): ?>
                            <button class="btn-action" disabled>لديك طلب معلق</button>
                        <?php else: ?>
                            <button class="btn-action" onclick="window.handleDonationAction()">تبرع الآن</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: var(--text-muted);">لا توجد طلبات تبرع عاجلة في الوقت الحالي.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<img src="dummy_image" style="display:none;" onerror="
    window.handleDonationAction = function() {
        openPopup('medical_popup.php');
    };
    window.lockDonationUI = function() {
        document.querySelector('.nav-links li.active a').click();
    };
">