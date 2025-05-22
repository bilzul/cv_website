<?php
// Төслүүдийн мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM projects ORDER BY order_num ASC, end_date DESC");
$stmt->execute();
$projects_list = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4 pb-2 border-bottom"><i class="fas fa-project-diagram me-2"></i> Төслүүд</h2>
        
        <?php if(count($projects_list) > 0): ?>
            <div class="row">
                <?php foreach($projects_list as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="project-card h-100">
                            <?php if(!empty($project['image'])): ?>
                                <img src="<?php echo UPLOAD_URL . $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="project-image w-100">
                            <?php endif; ?>
                            <div class="project-info">
                                <h4><?php echo $project['title']; ?></h4>
                                
                                <?php if(!empty($project['start_date'])): ?>
                                <p class="text-muted">
                                    <?php echo format_date($project['start_date']); ?> - 
                                    <?php echo empty($project['end_date']) ? 'Одоог хүртэл' : format_date($project['end_date']); ?>
                                </p>
                                <?php endif; ?>
                                
                                <p><?php echo $project['description']; ?></p>
                                
                                <?php if(!empty($project['technologies'])): ?>
                                <div class="project-technologies">
                                    <?php foreach(explode(',', $project['technologies']) as $tech): ?>
                                        <span><?php echo trim($tech); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($project['url'])): ?>
                                <div class="mt-3">
                                    <a href="<?php echo $project['url']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i> Төсөл үзэх
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p class="mb-0">Төслийн мэдээлэл оруулаагүй байна.</p>
            </div>
        <?php endif; ?>
    </div>
</div>