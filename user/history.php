<?php
session_start();
$current_user_id = $_SESSION['user_id'] ?? 1;

require_once '../db.php'; 

$records = [];
$total_success = 0;

// مصفوفة ربط أسماء المراكز (لترجمة القيم القادمة من الفورم)
$center_names = [
    'estak' => 'استاك المركزي (الخرطوم)',
    'omdurman' => 'مستشفى أم درمان',
    'bahri' => 'مستشفى بحري التعليمي',
    'moalem' => 'مستشفى المعلم'
];

try {
    // جلب البيانات من جدول donations الموحد
    $stmt = $pdo->prepare("
        SELECT * FROM donations 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$current_user_id]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // حساب عدد التبرعات الناجحة أو المكتملة
    foreach ($records as $rec) {
        if (in_array($rec['donation_status'], ['successful', 'completed'])) {
            $total_success++;
        }
    }

} catch(PDOException $e) {
    $records = [];
    $total_success = 0;
}

// كل تبرع ينقذ 3 أرواح
$lives_saved = $total_success * 3;
?>

<style>
    .page-header-title { font-size: 22px; color: var(--text-main); margin-bottom: 25px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .summary-cards { display: flex; gap: 20px; margin-bottom: 30px; }
    .summary-card { flex: 1; background-color: var(--card-bg); padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); text-align: center; border-bottom: 3px solid var(--banner-green); }
    .summary-card:first-child { border-bottom: 3px solid var(--primary-red); }
    .summary-card h4 { color: var(--text-muted); font-size: 15px; margin-bottom: 10px; font-weight: 600; }
    .summary-card .number { font-size: 32px; font-weight: 800; color: var(--text-main); }
    .summary-card .number.green { color: var(--banner-green); }
    
    .archive-container { background-color: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); padding: 25px; overflow-x: auto; }
    .archive-header { font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f0f0f0; }
    .archive-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .archive-table th, .archive-table td { padding: 18px 15px; text-align: right; border-bottom: 1px solid #f9f9f9; }
    .archive-table th { color: var(--text-muted); font-weight: 700; font-size: 14px; background-color: #FAFAFA; }
    
    .date-type .date { font-weight: 700; color: var(--text-main); display: block; margin-bottom: 3px; font-size: 15px; direction: ltr; text-align: right;}
    .date-type .type { font-size: 12px; color: var(--text-muted); }
    
    .vitals-list { list-style: none; padding: 0; margin: 0; font-size: 13px; color: var(--text-muted); }
    .vitals-list li { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    
    .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
    .status-completed { background-color: #DCFCE7; color: #15803D; }
    .status-approved { background-color: #e8f5e9; color: #2e7d32; }
    .status-pending { background-color: #FEF3C7; color: #D97706; }
    .status-rejected { background-color: #FDE8E8; color: #E63946; }
</style>

<h2 class="page-header-title">
    <i class="fa-solid fa-notes-medical" style="color: var(--primary-red);"></i>
    سجل التبرعات والأرشيف الطبي
</h2>

<div class="summary-cards">
    <div class="summary-card">
        <h4>إجمالي التبرعات الناجحة</h4>
        <div class="number"><?php echo $total_success; ?></div>
    </div>
    <div class="summary-card">
        <h4>أرواح أنقذتها</h4>
        <div class="number green"><?php echo $lives_saved; ?></div>
    </div>
</div>

<div class="archive-container">
    <div class="archive-header">العمليات السابقة والطلبات الحالية (الأحدث أولاً):</div>
    <table class="archive-table">
        <thead>
            <tr>
                <th>التاريخ والنوع</th>
                <th>المركز / المستشفى</th>
                <th>البيانات الحيوية (Vitals)</th>
                <th>حالة الطلب</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($records)): ?>
                <tr><td colspan="4" style="text-align:center; color: #777;">لا توجد سجلات تبرع أو طلبات حالية.</td></tr>
            <?php else: ?>
                <?php foreach($records as $rec): ?>
                <tr>
                    <td class="date-type">
                        <span class="date"><?php echo date('Y-m-d', strtotime($rec['donation_date'] ?? $rec['created_at'])); ?></span>
                        <span class="type">تبرع بالدم</span>
                    </td>
                    <td style="font-weight: 600; color: var(--text-main);">
                        <?php 
                            // تحويل كود المركز إلى الاسم العربي باستخدام المصفوفة
                            $c_id = $rec['center_id'];
                            echo htmlspecialchars($center_names[$c_id] ?? $c_id ?? 'غير محدد'); 
                        ?>
                    </td>
                    <td>
                        <ul class="vitals-list">
                            <li><i class="fa-solid fa-droplet" style="color: #df3e4d;"></i> هيموجلوبين: <?php echo !empty($rec['hemoglobin']) ? htmlspecialchars($rec['hemoglobin']) : '--'; ?></li>
                            <li><i class="fa-solid fa-heart-pulse" style="color: #2a9d8f;"></i> الضغط: <span dir="ltr"><?php echo !empty($rec['blood_pressure']) ? htmlspecialchars($rec['blood_pressure']) : '--'; ?></span></li>
                            <li><i class="fa-solid fa-weight-scale" style="color: #1565c0;"></i> الوزن: <?php echo !empty($rec['weight']) ? htmlspecialchars($rec['weight']) : '--'; ?> كجم</li>
                        </ul>
                    </td>
                    <td>
                        <?php if($rec['donation_status'] == 'pending'): ?>
                            <span class="status-badge status-pending">
                                <i class="fa-solid fa-clock"></i> قيد المراجعة
                            </span>
                        <?php elseif($rec['donation_status'] == 'rejected'): ?>
                            <span class="status-badge status-rejected">
                                <i class="fa-solid fa-circle-xmark"></i> تم الرفض
                            </span>
                        <?php elseif($rec['donation_status'] == 'successful'): ?>
                            <span class="status-badge status-approved">
                                <i class="fa-solid fa-check"></i> طلب مقبول
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-completed">
                                <i class="fa-solid fa-check-double"></i> تمت العملية
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>