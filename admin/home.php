<?php
require_once 'db.php';

// ================= [ معالجة نداءات الاستغاثة (إضافة، تعديل، حذف) ] ================= //
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // 1. إضافة نداء جديد
    if ($_POST['action'] === 'add_request') {
        $center_id = (int)$_POST['center_id'];
        $blood_type = trim($_POST['blood_type']);
        $priority = trim($_POST['priority']);
        $case_description = trim($_POST['case_description']);
        
        if(empty($center_id) || empty($blood_type) || empty($case_description)) {
            echo "خطأ: يرجى إكمال جميع الحقول المطلوبة."; exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO donation_requests (center_id, blood_type, priority, case_description, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt->execute([$center_id, $blood_type, $priority, $case_description]);
            echo "تم نشر نداء الاستغاثة بنجاح!";
        } catch (PDOException $e) {
            echo "حدث خطأ: " . $e->getMessage();
        }
        exit;
    }

    // 2. تعديل النداء
    if ($_POST['action'] === 'edit_request') {
        $id = (int)$_POST['request_id'];
        $center_id = (int)$_POST['center_id'];
        $blood_type = trim($_POST['blood_type']);
        $priority = trim($_POST['priority']);
        $case_description = trim($_POST['case_description']);
        
        try {
            $stmt = $pdo->prepare("UPDATE donation_requests SET center_id = ?, blood_type = ?, priority = ?, case_description = ? WHERE id = ?");
            $stmt->execute([$center_id, $blood_type, $priority, $case_description, $id]);
            echo "تم حفظ التعديلات بنجاح!";
        } catch (PDOException $e) {
            echo "حدث خطأ: " . $e->getMessage();
        }
        exit;
    }

    // 3. حذف النداء
    if ($_POST['action'] === 'delete_request') {
        $id = (int)$_POST['request_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM donation_requests WHERE id = ?");
            $stmt->execute([$id]);
            echo "تم حذف النداء بنجاح!";
        } catch (PDOException $e) {
            echo "حدث خطأ: " . $e->getMessage();
        }
        exit;
    }
}
// ========================================================================================= //

// جلب قائمة المراكز لاستخدامها في القوائم المنسدلة
$stmt_centers = $pdo->query("SELECT id, name FROM centers ORDER BY name ASC");
$centersList = $stmt_centers->fetchAll();

// جلب نداءات الاستغاثة الحالية من قاعدة البيانات
$stmt_requests = $pdo->query("
    SELECT r.id, r.blood_type, r.priority, r.case_description, r.center_id, c.name AS center_name 
    FROM donation_requests r 
    JOIN centers c ON r.center_id = c.id 
    ORDER BY r.created_at DESC
");
$requestsList = $stmt_requests->fetchAll();

// جلب طلبات التبرع المعلقة (Pending)
$stmt_donations = $pdo->query("
    SELECT d.id AS donation_id, d.created_at, d.donation_status, 
           u.full_name, u.blood_type, u.weight, u.blood_pressure, u.chronic_diseases
    FROM donations d
    JOIN users u ON d.user_id = u.id
    WHERE d.donation_status = 'pending' 
    ORDER BY d.created_at DESC 
    LIMIT 5
");
$pendingDonations = $stmt_donations->fetchAll();
?>

<style>
    .admin-home-grid { display: flex; flex-direction: column; gap: 25px; }
    .panel { background-color: #ffffff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .panel-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 2px solid #f4f6f9; padding-bottom: 15px; color: #333333; }
    
    /* --- 1. قسم إشعارات طلبات التبرع --- */
    .donor-list { display: flex; flex-direction: column; gap: 20px; }
    .donor-card { display: flex; flex-direction: column; padding: 18px; border: 1px solid #eaeaea; border-radius: 10px; transition: border-color 0.3s, box-shadow 0.3s; background-color: #ffffff; }
    .donor-card:hover { border-color: #2a9d8f; box-shadow: 0 4px 15px rgba(42, 157, 143, 0.05); }
    .donor-header { margin-bottom: 15px; }
    .donor-info h4 { font-size: 16px; color: #333333; margin-bottom: 4px; font-weight: 800; margin-top: 0; }
    .donor-info p { font-size: 12px; color: #777777; margin: 0; }
    .health-details-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; background-color: #f9fafb; padding: 15px; border-radius: 8px; }
    .detail-item { display: flex; flex-direction: column; gap: 4px; }
    .detail-item span { font-size: 11px; color: #777777; font-weight: 500; }
    .detail-item strong { font-size: 13px; color: #333333; font-weight: 700; }
    .blood-type-text { color: #df3e4d !important; font-size: 16px !important; font-weight: 800 !important; }
    .status-ok { color: #2a9d8f !important; }
    .status-danger-text { color: #df3e4d !important; }
    .donor-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #eaeaea; }
    .btn-accept, .btn-reject { border: none; padding: 9px 18px; border-radius: 6px; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 13px; transition: background 0.2s; display: flex; align-items: center; gap: 6px; }
    .btn-accept { background-color: #e8f5e9; color: #2e7d32; }
    .btn-accept:hover { background-color: #d0edd3; }
    .btn-reject { background-color: #ffebee; color: #c62828; }
    .btn-reject:hover { background-color: #ffcdd2; }

    /* --- 2. فورم إضافة خبر / نداء استغاثة --- */
    .news-form { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px; background-color: #f4f6f9; padding: 15px; border-radius: 8px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group.full-width { grid-column: span 3; }
    .form-group label { font-size: 13px; font-weight: 700; color: #333333; }
    .form-group input, .form-group select, .form-group textarea { padding: 10px; border: 1px solid #eaeaea; border-radius: 6px; font-family: inherit; outline: none; font-size: 14px; background-color: #ffffff; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #df3e4d; }
    .btn-submit { background-color: #df3e4d; color: #ffffff; border: none; padding: 11px; border-radius: 6px; font-weight: 700; cursor: pointer; grid-column: span 3; font-family: inherit; display: flex; justify-content: center; align-items: center; gap: 8px; transition: background 0.2s; }
    .btn-submit:hover { background-color: #c93543; }

    /* --- 3. جدول الأخبار والاستغاثات الحالية --- */
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: right; font-size: 14px; }
    thead th { color: #777777; font-weight: 700; padding-bottom: 12px; border-bottom: 1px solid #eaeaea; }
    tbody td { padding: 14px 0; border-bottom: 1px solid #eaeaea; color: #333333; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    .status-badge { padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; display: inline-block; }
    .status-danger { background-color: #ffebee; color: #c62828; }
    .status-warning { background-color: #fff8e1; color: #f57f17; }
    .status-info { background-color: #e3f2fd; color: #1565c0; }
    .table-actions { display: flex; gap: 8px; align-items: center; }
    .btn-action-sm { border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; font-family: inherit; color: #ffffff; transition: opacity 0.2s; }
    .btn-action-sm:hover { opacity: 0.85; }
    .btn-donors { background-color: #2a9d8f; }
    .btn-edit { background-color: #1565c0; }
    .btn-delete { background-color: #df3e4d; }
    .btn-save { background-color: #2a9d8f; }
    .btn-cancel { background-color: #777777; }

    @media (max-width: 992px) { .health-details-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
        .news-form { grid-template-columns: 1fr; }
        .form-group.full-width, .btn-submit { grid-column: span 1; }
        .table-actions { flex-direction: column; align-items: stretch; }
        .btn-action-sm { justify-content: center; }
    }
    @media (max-width: 576px) { .health-details-grid { grid-template-columns: 1fr; } .donor-actions { justify-content: center; flex-direction: column; } }
</style>

<div class="admin-home-grid">
    
    <div class="panel">
        <h3 class="panel-title">
            <i class="fa-solid fa-file-medical" style="color: #df3e4d;"></i> 
            طلبات التبرع الواردة للمراجعة
        </h3>
        <div class="donor-list">
            <?php if (count($pendingDonations) > 0): ?>
                <?php foreach ($pendingDonations as $donor): ?>
                    <?php 
                        $chronic = trim($donor['chronic_diseases']);
                        $has_disease = (!empty($chronic) && !in_array(strtolower($chronic), ['لا', 'لا يوجد', 'none'])) ? true : false;
                        $disease_text = $has_disease ? htmlspecialchars($chronic) : "لا";
                        $disease_class = $has_disease ? "status-danger-text" : "status-ok";
                    ?>
                    <div class="donor-card">
                        <div class="donor-header">
                            <div class="donor-info">
                                <h4><?php echo htmlspecialchars($donor['full_name']); ?></h4>
                                <p>تاريخ تقديم الطلب: <?php echo date('Y/m/d H:i', strtotime($donor['created_at'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="health-details-grid">
                            <div class="detail-item">
                                <span>فصيلة الدم</span>
                                <strong class="blood-type-text"><?php echo htmlspecialchars($donor['blood_type']); ?></strong>
                            </div>
                            <div class="detail-item">
                                <span>الوزن الحالي (كجم)</span>
                                <strong><?php echo htmlspecialchars($donor['weight']); ?></strong>
                            </div>
                            <div class="detail-item">
                                <span>ضغط الدم</span>
                                <strong><?php echo htmlspecialchars($donor['blood_pressure']); ?></strong>
                            </div>
                            <div class="detail-item">
                                <span>مستوى الهيموجلوبين</span>
                                <strong>-</strong>
                            </div>
                            <div class="detail-item">
                                <span>تاريخ التبرع المطلوب</span>
                                <strong><?php echo date('Y/m/d', strtotime($donor['created_at'] . ' + 1 days')); ?></strong>
                            </div>
                            <div class="detail-item">
                                <span>المركز المتاح</span>
                                <strong>غير محدد بعد</strong>
                            </div>
                            <div class="detail-item">
                                <span>أمراض مزمنة أو أدوية</span>
                                <strong class="<?php echo $disease_class; ?>"><?php echo $disease_text; ?></strong>
                            </div>
                        </div>

                        <div class="donor-actions">
                            <button class="btn-reject" title="رفض الطلب" data-id="<?php echo $donor['donation_id']; ?>">
                                <i class="fa-solid fa-xmark"></i> رفض التبرع
                            </button>
                            <button class="btn-accept" title="تأكيد الموعد" data-id="<?php echo $donor['donation_id']; ?>">
                                <i class="fa-solid fa-check"></i> تأكيد التبرع
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #777; padding: 20px;">لا توجد طلبات تبرع معلقة حالياً.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel">
        <h3 class="panel-title">
            <i class="fa-solid fa-bullhorn" style="color: #2a9d8f;"></i> 
            إدارة أخبار ونداءات المستشفيات
        </h3>
        
        <form class="news-form" id="addRequestForm">
            <div class="form-group">
                <label>المستشفى / المركز</label>
                <select id="req_center_id" required>
                    <option value="" disabled selected>-- اختر المستشفى --</option>
                    <?php foreach($centersList as $center): ?>
                        <option value="<?= $center['id'] ?>"><?= htmlspecialchars($center['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>الفصيلة المطلوبة</label>
                <select id="req_blood_type" required>
                    <option value="O-">O-</option>
                    <option value="O+">O+</option>
                    <option value="A-">A-</option>
                    <option value="A+">A+</option>
                    <option value="B-">B-</option>
                    <option value="B+">B+</option>
                    <option value="AB-">AB-</option>
                    <option value="AB+">AB+</option>
                </select>
            </div>
            <div class="form-group">
                <label>درجة الخطورة</label>
                <select id="req_priority" required>
                    <option value="critical">خطيرة جداً</option>
                    <option value="warning">متوسطة</option>
                    <option value="stable">مستقرة</option>
                    <option value="normal">روتيني</option>
                </select>
            </div>
            <div class="form-group full-width">
                <label>وصف الحالة (الاحتياج والأسباب)</label>
                <textarea id="req_case_description" rows="2" placeholder="مثال: نقص حاد في الفصيلة لعملية جراحية مستعجلة..." required></textarea>
            </div>
            <button type="button" class="btn-submit" id="submitRequestBtn">
                نشر الخبر في النظام 
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">المستشفى</th>
                        <th style="width: 10%;">الفصيلة</th>
                        <th style="width: 30%;">وصف الحالة / الاحتياج</th>
                        <th style="width: 15%;">درجة الخطورة</th>
                        <th style="width: 20%;">إجراء</th>
                    </tr>
                </thead>
                <tbody id="requestsTableBody">
                    <?php if(count($requestsList) > 0): ?>
                        <?php foreach($requestsList as $req): ?>
                            <?php 
                                $priority_text = ''; $priority_class = ''; $blood_color = '';
                                
                                if($req['priority'] === 'critical') { $priority_text = 'خطيرة جداً'; $priority_class = 'status-danger'; $blood_color = '#df3e4d';}
                                elseif($req['priority'] === 'warning') { $priority_text = 'متوسطة'; $priority_class = 'status-warning'; $blood_color = '#f57f17'; }
                                elseif($req['priority'] === 'stable') { $priority_text = 'مستقرة'; $priority_class = 'status-info'; $blood_color = '#1565c0';}
                                else { $priority_text = 'روتيني'; $priority_class = 'status-info'; $blood_color = '#777';}
                            ?>
                            <tr data-id="<?= $req['id'] ?>" 
                                data-center="<?= $req['center_id'] ?>" 
                                data-blood="<?= htmlspecialchars($req['blood_type']) ?>" 
                                data-priority="<?= htmlspecialchars($req['priority']) ?>" 
                                data-desc="<?= htmlspecialchars($req['case_description']) ?>">
                                
                                <td class="cell-center" style="font-weight: 600;"><?= htmlspecialchars($req['center_name']) ?></td>
                                <td class="cell-blood" style="color: <?= $blood_color ?>; font-weight: 800;"><?= htmlspecialchars($req['blood_type']) ?></td>
                                <td class="cell-desc" style="color: #777777;"><?= htmlspecialchars($req['case_description']) ?></td>
                                <td class="cell-priority"><span class="status-badge <?= $priority_class ?>"><?= $priority_text ?></span></td>
                                <td class="cell-actions">
                                    <div class="table-actions">
                                        <button class="btn-action-sm btn-donors" title="عرض المتبرعين">
                                            <i class="fa-solid fa-users"></i>
                                        </button>
                                        <button class="btn-action-sm btn-edit req-edit-btn" title="تعديل النداء">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn-action-sm btn-delete req-delete-btn" title="حذف النداء">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #777; padding: 20px;">لا توجد نداءات استغاثة منشورة حالياً.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div id="centersOptionsData" style="display:none;">
                <?php foreach($centersList as $center): ?>
                    <option value="<?= $center['id'] ?>"><?= htmlspecialchars($center['name']) ?></option>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>

</div>