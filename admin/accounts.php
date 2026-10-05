<?php
require_once 'db.php';

// سحب بيانات المستخدمين (المتبرعين) من قاعدة البيانات مع إضافة رقم الهاتف والعنوان
$stmt = $pdo->query("SELECT id, full_name, blood_type, primary_phone, address, is_eligible FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<style>
    .accounts-page-container {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .panel {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .panel-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        border-bottom: 2px solid #f4f6f9;
        padding-bottom: 15px;
    }

    .panel-title {
        font-size: 18px;
        font-weight: 700;
        color: #333333;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .search-box {
        display: flex;
        align-items: center;
        background-color: #f9fafb;
        border: 1px solid #eaeaea;
        border-radius: 8px;
        padding: 5px 15px;
        width: 300px;
    }

    .search-box i {
        color: #777777;
        margin-left: 10px;
    }

    .search-box input {
        border: none;
        background: transparent;
        padding: 8px 0;
        width: 100%;
        font-family: inherit;
        outline: none;
        font-size: 14px;
        color: #333333;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        text-align: right;
        font-size: 14px;
    }

    thead th {
        color: #777777;
        font-weight: 700;
        padding-bottom: 15px;
        border-bottom: 1px solid #eaeaea;
    }

    tbody td {
        padding: 16px 0;
        border-bottom: 1px solid #eaeaea;
        color: #333333;
        vertical-align: middle;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-sm {
        width: 36px;
        height: 36px;
        background-color: #fbeef0;
        color: #df3e4d;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        font-weight: bold;
    }

    .user-cell-info h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #333333;
    }

    .user-cell-info p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #777777;
    }

    .blood-badge-sm {
        background-color: #fbeef0;
        color: #df3e4d;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 13px;
        display: inline-block;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }

    .status-active { background-color: #e8f5e9; color: #2e7d32; }
    .status-inactive { background-color: #ffebee; color: #c62828; }

    .table-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-action-sm {
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        font-family: inherit;
        color: #ffffff;
        transition: opacity 0.2s;
    }

    .btn-action-sm:hover { opacity: 0.85; }
    .btn-view { background-color: #2a9d8f; }
    .btn-edit { background-color: #1565c0; }
    .btn-delete { background-color: #df3e4d; }

    .empty-data {
        color: #aaa;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .panel-header-flex { flex-direction: column; align-items: flex-start; gap: 15px; }
        .search-box { width: 100%; }
        .table-actions { flex-direction: column; align-items: stretch; }
        .btn-action-sm { justify-content: center; }
    }
</style>

<div class="accounts-page-container">

    <div class="panel">
        <div class="panel-header-flex">
            <h3 class="panel-title">
                <i class="fa-solid fa-users" style="color: #df3e4d;"></i> 
                إدارة حسابات المتبرعين
            </h3>
            
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="ابحث بالاسم، فصيلة الدم...">
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>المتبرع</th>
                        <th>فصيلة الدم</th>
                        <th>رقم الهاتف</th>
                        <th>محل الإقامة</th>
                        <th>حالة الحساب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <?php 
                                $first_char = mb_substr($user['full_name'], 0, 1, 'UTF-8');
                                
                                $status_class = $user['is_eligible'] ? 'status-active' : 'status-inactive';
                                $status_text = $user['is_eligible'] ? 'نشط / مؤهل' : 'غير مؤهل / محظور';
                                
                                $bg_colors = ['#fbeef0', '#e3f2fd', '#e8f5e9', '#fff8e1', '#f3e5f5'];
                                $text_colors = ['#df3e4d', '#1565c0', '#2e7d32', '#f57f17', '#7b1fa2'];
                                $color_index = hexdec(substr(md5($user['full_name']), 0, 1)) % 5;
                            ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-sm" style="background-color: <?= $bg_colors[$color_index] ?>; color: <?= $text_colors[$color_index] ?>;">
                                            <?= htmlspecialchars($first_char) ?>
                                        </div>
                                        <div class="user-cell-info">
                                            <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                                            <p>QH-<?= 100000 + $user['id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="blood-badge-sm"><?= htmlspecialchars($user['blood_type']) ?></span></td>
                                
                                <td dir="ltr" style="text-align: right; font-weight: 500;">
                                    <?= !empty($user['primary_phone']) ? htmlspecialchars($user['primary_phone']) : '<span class="empty-data">(غير متوفر)</span>' ?>
                                </td>
                                
                                <td style="font-weight: 500;">
                                    <?= !empty($user['address']) ? htmlspecialchars($user['address']) : '<span class="empty-data">(غير متوفر)</span>' ?>
                                </td>
                                
                                <td><span class="status-badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="btn-action-sm btn-view">
                                            <i class="fa-regular fa-file-lines"></i> عرض الملف
                                        </button>
                                        
                                        <button class="btn-action-sm btn-edit" data-id="<?= $user['id'] ?>">
                                            <i class="fa-solid fa-pen"></i> تعديل
                                        </button>
                                        
                                        <button class="btn-action-sm btn-delete" data-id="<?= $user['id'] ?>">
                                            <i class="fa-solid fa-trash"></i> حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #777; padding: 20px;">لا يوجد متبرعين مسجلين في النظام حالياً.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>