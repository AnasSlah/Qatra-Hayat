<?php
require_once 'db.php';

// مصفوفة ربط أسماء المراكز (لترجمة القيم القادمة من الداتا بيز)
$center_names = [
    'estak' => 'استاك المركزي (الخرطوم)',
    'omdurman' => 'مستشفى أم درمان',
    'bahri' => 'مستشفى بحري التعليمي',
    'moalem' => 'مستشفى المعلم'
];

// جلب الطلبات مع دمج ذكي للبيانات الصحية الأساسية وإضافة center_id
$stmt_donors = $pdo->query("
    SELECT d.id, u.full_name, u.primary_phone, 
           COALESCE(d.weight, u.weight) AS weight, 
           COALESCE(d.blood_pressure, u.blood_pressure) AS blood_pressure, 
           u.blood_type, d.donation_status, d.created_at, d.center_id 
    FROM donations d 
    LEFT JOIN users u ON d.user_id = u.id 
    ORDER BY d.created_at DESC
");
$all_donors = $stmt_donors->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .admin-home-grid { display: flex; flex-direction: column; gap: 25px; }
    .panel { background-color: #ffffff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f4f6f9; padding-bottom: 15px; color: #333333; }
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: right; font-size: 14px; }
    thead th { color: #777777; font-weight: 700; padding-bottom: 15px; border-bottom: 1px solid #eaeaea; white-space: nowrap; }
    tbody td { padding: 15px 10px 15px 0; border-bottom: 1px solid #eaeaea; color: #333333; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background-color: #fcfcfc; }
    .donor-info h4 { font-size: 14px; color: #333333; margin: 0; font-weight: 800; }
    .phone-text { color: #555; font-weight: 600; direction: ltr; display: inline-block; }
    .health-info { font-size: 12px; color: #666; line-height: 1.6; }
    .health-info span { font-weight: 700; color: #333; }
    .blood-badge-sm { background-color: #fbeef0; color: #df3e4d; padding: 6px 12px; border-radius: 6px; font-weight: 800; font-size: 13px; display: inline-block; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; white-space: nowrap; }
    .status-warning { background-color: #fff8e1; color: #f57f17; }
    .status-success { background-color: #e8f5e9; color: #2e7d32; }
    .status-danger  { background-color: #ffebee; color: #c62828; }
    .donor-actions { display: flex; gap: 8px; }
    .btn-accept, .btn-reject { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 12px; transition: transform 0.2s, opacity 0.2s; display: flex; align-items: center; gap: 5px; }
    .btn-accept:active, .btn-reject:active { transform: scale(0.95); }
    .btn-accept:hover, .btn-reject:hover { opacity: 0.85; }
    .btn-accept { background-color: #e8f5e9; color: #2e7d32; }
    .btn-reject { background-color: #ffebee; color: #c62828; }
    .center-text { font-weight: 700; color: #475569; font-size: 13px; display: flex; align-items: center; gap: 6px; }
</style>

<div class="admin-home-grid">
    <div class="panel">
        <h3 class="panel-title"><i class="fa-solid fa-list-check" style="color: #df3e4d;"></i> سجل طلبات التبرع الشامل</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>اسم المتبرع</th>
                        <th>رقم الهاتف</th>
                        <th>المؤشرات (وزن / ضغط)</th>
                        <th>الفصيلة</th>
                        <th>المركز المحدد</th> <th>تاريخ تقديم الطلب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($all_donors) > 0): ?>
                        <?php foreach ($all_donors as $donor): ?>
                            <tr class="donor-card">
                                <td class="donor-info"><h4><?= htmlspecialchars($donor['full_name'] ?? 'مستخدم غير معروف') ?></h4></td>
                                <td><span class="phone-text"><?= htmlspecialchars($donor['primary_phone'] ?? 'غير متوفر') ?></span></td>
                                <td>
                                    <div class="health-info">
                                        الوزن: <span><?= htmlspecialchars($donor['weight'] ?? '--') ?> كجم</span><br>
                                        الضغط: <span dir="ltr"><?= htmlspecialchars($donor['blood_pressure'] ?? '--') ?></span>
                                    </div>
                                </td>
                                <td><span class="blood-badge-sm blood-type-text"><?= htmlspecialchars($donor['blood_type'] ?? 'غير محدد') ?></span></td>
                                
                                <td>
                                    <span class="center-text">
                                        <i class="fa-solid fa-hospital" style="color: #94A3B8;"></i>
                                        <?php 
                                            $c_id = $donor['center_id'];
                                            echo htmlspecialchars($center_names[$c_id] ?? $c_id ?? 'غير محدد'); 
                                        ?>
                                    </span>
                                </td>

                                <td style="color: #777; font-size: 13px;"><i class="fa-regular fa-clock"></i> <?= date('Y/m/d - H:i', strtotime($donor['created_at'])) ?></td>
                                <td>
                                    <?php
                                        $status_text = ''; $status_class = '';
                                        if ($donor['donation_status'] === 'pending') { $status_text = 'قيد المراجعة'; $status_class = 'status-warning'; }
                                        elseif ($donor['donation_status'] === 'successful') { $status_text = 'مقبول'; $status_class = 'status-success'; }
                                        elseif ($donor['donation_status'] === 'rejected') { $status_text = 'مرفوض'; $status_class = 'status-danger'; }
                                    ?>
                                    <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                </td>
                                <td>
                                    <?php if ($donor['donation_status'] === 'pending'): ?>
                                        <div class="donor-actions">
                                            <button class="btn-reject" title="رفض الطلب" data-id="<?= $donor['id'] ?>"><i class="fa-solid fa-xmark"></i> رفض</button>
                                            <button class="btn-accept" title="تأكيد التبرع" data-id="<?= $donor['id'] ?>"><i class="fa-solid fa-check"></i> تأكيد</button>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #aaa; font-size: 12px; font-weight: 700;"><i class="fa-solid fa-lock"></i> تمت المراجعة</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align: center; color: #777; padding: 20px;">لا يوجد أي سجلات للتبرع حالياً.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-accept').forEach(button => {
    button.addEventListener('click', function() {
        const donationId = this.getAttribute('data-id');
        if(confirm('هل أنت متأكد من قبول الطلب والموافقة عليه؟')) {
            const formData = new FormData();
            formData.append('donation_id', donationId);
            fetch('process_accept.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'success') { alert('تم تأكيد وتحويل حالة الطلب بنجاح!'); window.location.reload(); }
                else { alert(data); }
            });
        }
    });
});

document.querySelectorAll('.btn-reject').forEach(button => {
    button.addEventListener('click', function() {
        const donationId = this.getAttribute('data-id');
        if(confirm('هل تريد فعلاً رفض هذا الطلب؟')) {
            const formData = new FormData();
            formData.append('donation_id', donationId);
            fetch('process_reject.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'success') { alert('تم رفض الطلب بنجاح.'); window.location.reload(); }
                else { alert(data); }
            });
        }
    });
});
</script>