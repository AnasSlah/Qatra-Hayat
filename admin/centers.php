<?php
require_once 'db.php';

// ================= [ معالجة طلبات الإضافة والتعديل والحذف ] ================= //
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // 1. معالجة الإضافة الجديدة
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? ''); 
        $type = trim($_POST['type'] ?? 'hospital'); 
        $phone = trim($_POST['phone'] ?? '');
        $status = trim($_POST['status'] ?? 'مفتوح');
        
        $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? trim($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? trim($_POST['longitude']) : null;
        
        if (empty($name)) {
            echo "خطأ: يرجى كتابة اسم المركز أولاً!";
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO centers (name, description, type, phone, status, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $desc, $type, $phone, $status, $latitude, $longitude]);
            echo "تم إضافة المركز الجديد بنجاح!";
        } catch (PDOException $e) {
            echo "حدث خطأ: " . $e->getMessage();
        }
        exit;
    }

    // 2. معالجة التعديل
    if ($_POST['action'] === 'edit') {
        $id = (int)$_POST['center_id'];
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? ''); 
        $type = trim($_POST['type'] ?? 'hospital'); 
        $phone = trim($_POST['phone'] ?? '');
        $status = trim($_POST['status'] ?? 'مفتوح');
        
        $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? trim($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? trim($_POST['longitude']) : null;
        
        try {
            $stmt = $pdo->prepare("UPDATE centers SET name = ?, description = ?, type = ?, phone = ?, status = ?, latitude = ?, longitude = ? WHERE id = ?");
            $stmt->execute([$name, $desc, $type, $phone, $status, $latitude, $longitude, $id]);
            echo "تم حفظ بيانات المركز بنجاح!";
        } catch (PDOException $e) {
            echo "حدث خطأ: " . $e->getMessage();
        }
        exit;
    }

    // 3. معالجة الحذف
    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['center_id'];
        try {
            $stmt_cascade = $pdo->prepare("DELETE FROM donation_requests WHERE center_id = ?");
            $stmt_cascade->execute([$id]);
            
            $stmt = $pdo->prepare("DELETE FROM centers WHERE id = ?");
            $stmt->execute([$id]);
            echo "تم حذف المركز بنجاح!";
        } catch (PDOException $e) {
            echo "حدث خطأ: " . $e->getMessage();
        }
        exit; 
    }
}
// ============================================================================== //

// سحب كافة بيانات المراكز
$stmt = $pdo->query("SELECT id, name, description, type, phone, status, latitude, longitude, created_at FROM centers ORDER BY created_at DESC");
$centersList = $stmt->fetchAll();
?>

<style>
    .centers-page-container { display: flex; flex-direction: column; gap: 25px; }
    .admin-table-panel { background-color: #ffffff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .panel-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f4f6f9; padding-bottom: 15px; }
    .panel-title { font-size: 18px; font-weight: 700; color: #333333; display: flex; align-items: center; gap: 10px; margin: 0; }
    .header-actions { display: flex; gap: 15px; align-items: center; }
    .table-container { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; text-align: right; font-size: 13px; }
    thead th { color: #777777; font-weight: 700; padding-bottom: 12px; border-bottom: 1px solid #eaeaea; white-space: nowrap; }
    tbody td { padding: 14px 10px 14px 0; border-bottom: 1px solid #eaeaea; color: #333333; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    .admin-actions { display: flex; gap: 8px; }
    .btn-edit, .btn-delete, .btn-save, .btn-cancel { border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer; color: #ffffff; display: flex; align-items: center; gap: 5px; font-family: inherit; transition: opacity 0.2s; }
    .btn-edit:hover, .btn-delete:hover, .btn-save:hover, .btn-cancel:hover { opacity: 0.85; }
    .btn-edit { background-color: #1565c0; }
    .btn-delete { background-color: #df3e4d; }
    .btn-save { background-color: #2a9d8f; }
    .btn-cancel { background-color: #777777; }
    .edit-input { width: 100%; padding: 6px 8px; border: 1px solid #2a9d8f; border-radius: 6px; font-family: inherit; font-size: 12px; outline: none; background-color: #f9fafb; box-sizing: border-box; }
    .edit-select { width: 100%; padding: 6px; border: 1px solid #2a9d8f; border-radius: 6px; font-family: inherit; font-size: 12px; outline: none; background-color: #f9fafb; box-sizing: border-box; }
    .edit-input:focus, .edit-select:focus { background-color: #ffffff; }
    .empty-data { color: #aaa; font-style: italic; }
    .search-box { display: flex; align-items: center; background-color: #f9fafb; border: 1px solid #eaeaea; border-radius: 8px; padding: 5px 15px; width: 250px; }
    .search-box input { border: none; background: transparent; padding: 8px 0; width: 100%; outline: none; font-size: 13px; font-family: inherit; margin-right: 10px;}
</style>

<div class="centers-page-container">
    <div class="admin-table-panel">
        <div class="panel-header-flex">
            <h3 class="panel-title">
                <i class="fa-solid fa-sliders" style="color: #1565c0;"></i> 
                لوحة تحكم وإدارة المراكز
            </h3>
            
            <div class="header-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass" style="color:#777;"></i>
                    <input type="text" placeholder="ابحث بالاسم...">
                </div>
                <button id="addNewCenterBtn" class="btn-save" style="padding: 10px 15px; font-size: 13px;">
                    <i class="fa-solid fa-plus"></i> إضافة مستشفى / بنك دم
                </button>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 15%;">اسم المركز</th>
                        <th style="width: 15%;">الوصف</th>
                        <th style="width: 8%;">النوع</th>
                        <th style="width: 10%;">رقم الهاتف</th>
                        <th style="width: 12%;">تاريخ الإنشاء</th>
                        <th style="width: 9%;">خط العرض (Lat)</th>
                        <th style="width: 9%;">خط الطول (Long)</th>
                        <th style="width: 7%;">الحالة</th>
                        <th style="width: 10%;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($centersList) > 0): ?>
                        <?php foreach ($centersList as $center): ?>
                            <?php 
                                $desc = !empty($center['description']) ? htmlspecialchars($center['description']) : '<span class="empty-data">(لا يوجد)</span>';
                                $type_text = ($center['type'] === 'hospital') ? 'مستشفى' : 'بنك دم';
                                $phone = !empty($center['phone']) ? htmlspecialchars($center['phone']) : '<span class="empty-data">(غير متوفر)</span>';
                                $created_at = date('Y-m-d H:i', strtotime($center['created_at']));
                                
                                $lat = !empty($center['latitude']) ? htmlspecialchars($center['latitude']) : '<span class="empty-data">(غير متوفر)</span>';
                                $long = !empty($center['longitude']) ? htmlspecialchars($center['longitude']) : '<span class="empty-data">(غير متوفر)</span>';

                                $status = !empty($center['status']) ? htmlspecialchars($center['status']) : 'مفتوح';
                                $status_color = ($status === 'مفتوح') ? '#2a9d8f' : '#c62828';
                            ?>
                            <tr data-id="<?= $center['id'] ?>" 
                                data-name="<?= htmlspecialchars($center['name']) ?>" 
                                data-description="<?= htmlspecialchars($center['description'] ?? '') ?>" 
                                data-type="<?= htmlspecialchars($center['type']) ?>" 
                                data-phone="<?= htmlspecialchars($center['phone'] ?? '') ?>" 
                                data-latitude="<?= htmlspecialchars($center['latitude'] ?? '') ?>" 
                                data-longitude="<?= htmlspecialchars($center['longitude'] ?? '') ?>" 
                                data-status="<?= htmlspecialchars($center['status'] ?? 'مفتوح') ?>">
                                
                                <td style="color: #777; font-weight: bold;"><?= $center['id'] ?></td>
                                <td class="cell-name" style="font-weight: 800;"><?= htmlspecialchars($center['name']) ?></td>
                                <td class="cell-desc" style="color: #666;"><?= $desc ?></td>
                                <td class="cell-type"><?= $type_text ?></td>
                                <td class="cell-phone" dir="ltr" style="text-align: right; font-weight: 500;"><?= $phone ?></td>
                                <td dir="ltr" style="text-align: right; color: #777; font-size: 11px;"><?= $created_at ?></td>
                                
                                <td class="cell-lat" dir="ltr" style="text-align: right; font-weight: 500;"><?= $lat ?></td>
                                <td class="cell-long" dir="ltr" style="text-align: right; font-weight: 500;"><?= $long ?></td>
                                
                                <td class="cell-status"><span style="color: <?= $status_color ?>; font-weight: bold;"><?= $status ?></span></td>
                                <td>
                                    <div class="admin-actions">
                                        <button class="btn-edit center-edit-btn" title="تعديل">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn-delete center-delete-btn" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: #777; padding: 20px;">لا توجد مراكز مسجلة في النظام حالياً.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>