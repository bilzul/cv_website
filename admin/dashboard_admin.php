<?php
// Хүснэгтүүдийн мэдээлэл авах
$tables = [
    'education' => 'Боловсрол',
    'experience' => 'Ажлын туршлага',
    'skills' => 'Ур чадвар',
    'projects' => 'Төслүүд',
    'contacts' => 'Холбоо барих'
];

$stats = [];

// Icon mapping function for statistics cards
function getIconForTable($table)
{
    $iconMap = [
        'education' => 'fas fa-graduation-cap fa-2x text-primary',
        'experience' => 'fas fa-briefcase fa-2x text-success',
        'skills' => 'fas fa-chart-bar fa-2x text-info',
        'projects' => 'fas fa-project-diagram fa-2x text-warning',
        'contacts' => 'fas fa-address-book fa-2x text-danger',
    ];

    return isset($iconMap[$table]) ? $iconMap[$table] : 'fas fa-folder fa-2x text-secondary';
}

foreach ($tables as $table => $label) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table");
    $stmt->execute();
    $result = $stmt->fetch();
    $stats[$table] = $result['count'];
}

// Сүүлийн нэмсэн мэдээллүүд
$recent_items = [];
$recent_limit = 5;

foreach ($tables as $table => $label) {
    $stmt = $conn->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT $recent_limit");
    $stmt->execute();
    $recent_items[$table] = $stmt->fetchAll();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 dashboard-title"><i class="fas fa-tachometer-alt me-2"></i> Хянах самбар</h2> <a href="index.php" class="btn btn-outline-primary back-to-site-btn">
        <i class="fas fa-home me-1"></i> Нүүр хуудас руу буцах
    </a>
</div>

<!-- Товч статистик -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="admin-card bg-primary text-white welcome-card">
            <h3 class="mb-3 text-white">Таны CV профайл</h3>
            <p>Админ хянах самбарт тавтай морил! Энэ хэсгээс та CV профайлын бүх мэдээллийг удирдах боломжтой.</p>
            <div class="dashboard-stats mb-3 mt-3">
                <div class="dashboard-stat-item">
                    <i class="fas fa-file-alt stat-icon-sm"></i>
                    <span>Нийт бүртгэл: <?php echo array_sum($stats); ?></span>
                </div>
                <div class="dashboard-stat-item">
                    <i class="fas fa-calendar-alt stat-icon-sm"></i>
                    <span>Өнөөдөр: <?php echo date('Y-m-d'); ?></span>
                </div>
                <div class="dashboard-stat-item">
                    <i class="fas fa-clock stat-icon-sm"></i>
                    <span>Сүүлд шинэчлэгдсэн: <?php echo date('Y-m-d H:i'); ?></span>
                </div>
                <div class="dashboard-stat-item">
                    <i class="fas fa-check-circle stat-icon-sm"></i>
                    <span>Системийн төлөв: <span class="badge bg-success">Идэвхтэй</span></span>
                </div>
            </div>
            <div class="mt-auto pt-3 text-center">
                <a href="index.php?page=admin-personal" class="btn btn-light btn-lg">
                    <i class="fas fa-user me-2"></i> Хувийн мэдээлэл засах
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row h-100">
            <?php foreach ($tables as $table => $label): ?>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="admin-card text-center h-100 stat-card">
                        <div class="stat-icon mb-2">
                            <i class="<?php echo getIconForTable($table); ?>"></i>
                        </div>
                        <h1 class="display-4 mb-2"><?php echo $stats[$table]; ?></h1>
                        <p class="mb-2"><?php echo $label; ?></p>
                        <a href="index.php?page=admin-<?php echo $table; ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> Харах
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Цэсний товчууд -->
<div class="row mb-5">
    <div class="col-12">
        <div class="admin-card quick-access-section">
            <div class="admin-header">
                <h4 class="mb-0">Шууд очих</h4>
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="index.php?page=admin-personal" class="quick-access-card">
                        <div class="icon-container">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="card-title">Хувийн мэдээлэл</div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="index.php?page=admin-education" class="quick-access-card">
                        <div class="icon-container">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="card-title">Боловсрол</div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="index.php?page=admin-experience" class="quick-access-card">
                        <div class="icon-container">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="card-title">Туршлага</div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="index.php?page=admin-skills" class="quick-access-card">
                        <div class="icon-container">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="card-title">Ур чадвар</div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="index.php?page=admin-projects" class="quick-access-card">
                        <div class="icon-container">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="card-title">Төслүүд</div>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4 col-6 mb-3">
                    <a href="index.php?page=admin-contact" class="quick-access-card">
                        <div class="icon-container">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div class="card-title">Холбоо барих</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Сүүлийн нэмсэн мэдээллүүд -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="admin-card">
            <div class="admin-header">
                <h4 class="mb-0">Сүүлийн нэмсэн боловсрол</h4>
                <a href="index.php?page=admin-education" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Шинээр нэмэх
                </a>
            </div>

            <?php if (count($recent_items['education']) > 0): ?>
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Сургууль</th>
                                <th>Зэрэг</th>
                                <th>Эхэлсэн</th>
                                <th>Дууссан</th>
                                <th>Үйлдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_items['education'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['institution']; ?></td>
                                    <td><?php echo $item['degree']; ?></td>
                                    <td><?php echo format_date($item['start_date']); ?></td>
                                    <td><?php echo empty($item['end_date']) ? 'Одоог хүртэл' : format_date($item['end_date']); ?></td>
                                    <td>
                                        <div class="admin-actions">
                                            <a href="index.php?page=admin-education&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=admin-education&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mt-3">Боловсролын мэдээлэл одоогоор байхгүй байна.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="admin-card">
            <div class="admin-header">
                <h4 class="mb-0">Сүүлийн нэмсэн ажлын туршлага</h4>
                <a href="index.php?page=admin-experience" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Шинээр нэмэх
                </a>
            </div>

            <?php if (count($recent_items['experience']) > 0): ?>
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Компани</th>
                                <th>Албан тушаал</th>
                                <th>Эхэлсэн</th>
                                <th>Дууссан</th>
                                <th>Үйлдэл</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_items['experience'] as $item): ?>
                                <tr>
                                    <td><?php echo $item['company']; ?></td>
                                    <td><?php echo $item['position']; ?></td>
                                    <td><?php echo format_date($item['start_date']); ?></td>
                                    <td><?php echo empty($item['end_date']) ? 'Одоог хүртэл' : format_date($item['end_date']); ?></td>
                                    <td>
                                        <div class="admin-actions">
                                            <a href="index.php?page=admin-experience&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=admin-experience&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mt-3">Ажлын туршлагын мэдээлэл одоогоор байхгүй байна.</p>
            <?php endif; ?>
        </div>
    </div>
</div>