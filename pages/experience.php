<?php
// Ажлын туршлагын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM experience ORDER BY order_num ASC, start_date DESC");
$stmt->execute();
$experience_list = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4 pb-2 border-bottom"><i class="fas fa-briefcase me-2"></i> Ажлын туршлага</h2>
        
        <?php if(count($experience_list) > 0): ?>
            <div class="timeline">
                <?php foreach($experience_list as $experience): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <?php echo format_date($experience['start_date']); ?> - 
                            <?php echo empty($experience['end_date']) ? 'Одоог хүртэл' : format_date($experience['end_date']); ?>
                        </div>
                        <h4 class="timeline-title"><?php echo $experience['position']; ?></h4>
                        <h5 class="timeline-subtitle"><?php echo $experience['company']; ?></h5>
                        <div class="timeline-content">
                            <?php echo $experience['description']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Ажлын туршлагын мэдээлэл оруулаагүй байна.</p>
            </div>
        <?php endif; ?>
    </div>
</div>