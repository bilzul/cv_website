<?php
// Боловсролын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM education ORDER BY order_num ASC, start_date DESC");
$stmt->execute();
$education_list = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4 pb-2 border-bottom"><i class="fas fa-graduation-cap me-2"></i> Боловсрол</h2>
        
        <?php if(count($education_list) > 0): ?>
            <div class="timeline">
                <?php foreach($education_list as $education): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <?php echo format_date($education['start_date']); ?> - 
                            <?php echo empty($education['end_date']) ? 'Одоог хүртэл' : format_date($education['end_date']); ?>
                        </div>
                        <h4 class="timeline-title"><?php echo $education['institution']; ?></h4>
                        <h5 class="timeline-subtitle"><?php echo $education['degree']; ?> - <?php echo $education['field']; ?></h5>
                        <div class="timeline-content">
                            <?php echo $education['description']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Боловсролын мэдээлэл оруулаагүй байна.</p>
            </div>
        <?php endif; ?>
    </div>
</div> 