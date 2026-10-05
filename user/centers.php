<?php
// إعدادات الاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'qatra_hayat';
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // جلب جميع المراكز من الداتا بيز
    $centers = $pdo->query("SELECT * FROM centers")->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $centers = [];
}
?>

<style>
    /* ترويسة الصفحة */
    .page-header-title { font-size: 22px; color: var(--text-main, #333); margin-bottom: 25px; font-weight: 800; display: flex; align-items: center; gap: 10px; }

    /* حاوية الخريطة */
    .map-container { width: 100%; height: 350px; background-color: #E2E8F0; border-radius: 16px; overflow: hidden; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); position: relative; border: 4px solid white; }
    .map-container iframe { width: 100%; height: 100%; border: none; }

    /* شبكة الكروت */
    .centers-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }

    /* تصميم كارت المركز */
    .center-card { background-color: var(--card-bg, #fff); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); overflow: hidden; display: flex; flex-direction: column; border-top: 4px solid var(--banner-green, #10B981); transition: transform 0.2s;}
    .center-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.05);}
    .card-body { padding: 20px 15px; flex-grow: 1; text-align: center; }
    
    /* ألوان الحالة ديناميكياً */
    .center-status { font-size: 13px; font-weight: 700; margin-bottom: 10px; }
    .status-open { color: var(--banner-green, #10B981); }
    .status-closed { color: var(--primary-red, #EF4444); }
    
    .center-name { font-size: 18px; font-weight: 800; color: var(--text-main, #333); margin-bottom: 8px; }
    .center-distance { font-size: 14px; color: var(--text-muted, #64748b); display: flex; align-items: center; justify-content: center; gap: 5px; font-weight: 600; margin-bottom: 8px;}
    .center-phone { font-size: 13px; color: #64748b; font-weight: 600; direction: ltr; display: flex; align-items: center; justify-content: center; gap: 5px; margin-bottom: 10px; }
    
    /* تنسيق الوصف */
    .center-description { font-size: 13px; color: #94A3B8; line-height: 1.5; margin-bottom: 10px; font-weight: 500; background-color: #F8FAFC; padding: 8px; border-radius: 6px; }

    /* أزرار الكارت */
    .card-footer { display: flex; border-top: 1px dashed #E2E8F0; background-color: #F8FAFC; }
    .btn-action { flex: 1; padding: 12px 10px; text-align: center; font-weight: 800; font-size: 14px; text-decoration: none; cursor: pointer; border: none; font-family: inherit; transition: background 0.2s; }
    .btn-directions { color: #3B82F6; background: transparent; width: 100%; display: block; }
    .btn-directions:hover { background-color: #EFF6FF; }

    @media (max-width: 768px) { 
        .map-container { height: 250px; } 
    }
</style>

<h2 class="page-header-title">
    <i class="fa-solid fa-map-location-dot" style="color: var(--primary-red, #EF4444);"></i>
    خريطة مراكز التبرع
</h2>

<div class="map-container">
    <iframe src="https://maps.google.com/maps?q=Khartoum,Sudan&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen="" loading="lazy"></iframe>
</div>

<div class="centers-grid">
    <?php if(empty($centers)): ?>
        <p style="text-align: center; width: 100%;">لا توجد مراكز مسجلة حالياً في قاعدة البيانات.</p>
    <?php else: ?>
        <?php foreach($centers as $center): ?>
            <?php 
                $is_open = ($center['status'] === 'مفتوح');
                $card_border_color = $is_open ? '#10B981' : '#EF4444';
            ?>
            <div class="center-card" style="border-top-color: <?php echo $card_border_color; ?>;">
                <div class="card-body">
                    
                    <div class="center-status <?php echo $is_open ? 'status-open' : 'status-closed'; ?>">
                        <i class="fa-solid fa-circle" style="font-size: 9px; margin-left: 4px;"></i>
                        <?php echo htmlspecialchars($center['status']); ?>
                    </div>
                    
                    <h3 class="center-name"><?php echo htmlspecialchars($center['name']); ?></h3>
                    
                    <div class="center-distance">
                        <i class="fa-solid fa-hospital"></i> 
                        <?php echo $center['type'] === 'hospital' ? 'مستشفى تعليمي' : 'بنك دم مركزي'; ?>
                    </div>

                    <?php if(!empty($center['phone'])): ?>
                        <div class="center-phone">
                            <i class="fa-solid fa-phone" style="font-size: 11px;"></i> 
                            <?php echo htmlspecialchars($center['phone']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($center['description']) && $center['description'] !== 'NULL'): ?>
                        <div class="center-description">
                            <?php echo htmlspecialchars($center['description']); ?>
                        </div>
                    <?php endif; ?>

                </div>
                
                <div class="card-footer">
                    <?php if(!empty($center['latitude']) && !empty($center['longitude'])): ?>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $center['latitude']; ?>,<?php echo $center['longitude']; ?>" target="_blank" class="btn-action btn-directions">
                            <i class="fa-solid fa-location-arrow"></i> الاتجاهات
                        </a>
                    <?php else: ?>
                        <span style="padding: 12px; color: #94A3B8; font-size: 13px; text-align: center; width: 100%; font-weight: 600;">لا يتوفر موقع على الخريطة</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>