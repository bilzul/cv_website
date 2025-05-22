<?php
// Ур чадварын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM skills ORDER BY category, level DESC");
$stmt->execute();
$skills_list = $stmt->fetchAll();

// Ур чадварыг категориор бүлэглэх
$skills_by_category = [];
foreach ($skills_list as $skill) {
    $category = !empty($skill['category']) ? $skill['category'] : 'Бусад';
    if (!isset($skills_by_category[$category])) {
        $skills_by_category[$category] = [];
    }
    $skills_by_category[$category][] = $skill;
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4 pb-2 border-bottom"><i class="fas fa-chart-bar me-2"></i> Ур чадварууд</h2>
        
        <?php if(count($skills_list) > 0): ?>
            <?php foreach($skills_by_category as $category => $skills): ?>
                <h3 class="mb-3 mt-4"><?php echo $category; ?></h3>
                <div class="row">
                    <?php foreach($skills as $skill): ?>
                        <div class="col-md-6 skill-item">
                            <div class="d-flex justify-content-between mb-1">
                                <span><strong><?php echo $skill['name']; ?></strong></span>
                                <span><?php echo $skill['level']; ?>%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['level']; ?>%" 
                                    aria-valuenow="<?php echo $skill['level']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Ур чадварын мэдээлэл оруулаагүй байна.</p>
            </div>
        <?php endif; ?>
    </div>
</div>