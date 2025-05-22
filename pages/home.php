<?php
// Мэдээллийн сангийн холболтыг шалгах
if (!$conn) {
    die("Database connection failed. Please check your configuration.");
}

// Хувийн мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM personal_info LIMIT 1");
$stmt->execute();
$personal = $stmt->fetch();

// Ур чадваруудыг авах
$stmt = $conn->prepare("SELECT * FROM skills ORDER BY level DESC LIMIT 6");
$stmt->execute();
$skills = $stmt->fetchAll();

// Сүүлийн боловсролын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM education ORDER BY end_date DESC LIMIT 1");
$stmt->execute();
$education = $stmt->fetch();

// Сүүлийн ажлын туршлагыг авах
$stmt = $conn->prepare("SELECT * FROM experience ORDER BY end_date DESC LIMIT 1");
$stmt->execute();
$experience = $stmt->fetch();
?>

<!-- Танилцуулга хэсэг -->
<section id="profile" class="profile-section">
    <div class="row">
        <div class="col-md-4 text-center">
            <?php if($personal && !empty($personal['photo'])): ?>
                <img src="<?php echo UPLOAD_URL . $personal['photo']; ?>" alt="<?php echo $personal['name']; ?>" class="profile-photo mb-3">
            <?php else: ?>
                <img src="assets/images/placeholder.png" alt="Profile" class="profile-photo mb-3">
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <h2 class="mb-2"><?php echo $personal ? $personal['name'] : 'Таны нэр'; ?></h2>
            <h4 class="text-muted mb-4"><?php echo $personal ? $personal['profession'] : 'Таны мэргэжил'; ?></h4>
            
            <div class="mb-4">
                <?php echo $personal ? $personal['bio'] : 'Энд танилцуулга текст байрлана.'; ?>
            </div>
            
            <div class="row">
                <?php if($personal && !empty($personal['email'])): ?>
                <div class="col-md-6 mb-3">
                    <strong><i class="fas fa-envelope me-2"></i> Имэйл:</strong>
                    <p><?php echo $personal['email']; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if($personal && !empty($personal['phone'])): ?>
                <div class="col-md-6 mb-3">
                    <strong><i class="fas fa-phone me-2"></i> Утас:</strong>
                    <p><?php echo $personal['phone']; ?></p>
                </div>
                <?php endif; ?>
                
                <?php if($personal && !empty($personal['address'])): ?>
                <div class="col-md-6 mb-3">
                    <strong><i class="fas fa-map-marker-alt me-2"></i> Хаяг:</strong>
                    <p><?php echo $personal['address']; ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Товч мэдээлэл -->
<section id="summary" class="mb-5">
    <div class="row">
        <!-- Сүүлийн боловсрол -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-graduation-cap me-2"></i> Сүүлийн боловсрол</h4>
                    <?php if($education): ?>
                        <h5 class="mt-3"><?php echo $education['institution']; ?></h5>
                        <h6 class="text-muted"><?php echo $education['degree']; ?> - <?php echo $education['field']; ?></h6>
                        <p class="text-muted">
                            <?php echo format_date($education['start_date']); ?> - 
                            <?php echo empty($education['end_date']) ? 'Одоог хүртэл' : format_date($education['end_date']); ?>
                        </p>
                        <p><?php echo truncate($education['description'], 150); ?></p>
                        <a href="index.php?page=education" class="btn btn-outline-primary mt-2">Бүх боловсрол</a>
                    <?php else: ?>
                        <p class="mt-3">Боловсролын мэдээлэл оруулаагүй байна.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Сүүлийн ажлын туршлага -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-briefcase me-2"></i> Сүүлийн ажлын туршлага</h4>
                    <?php if($experience): ?>
                        <h5 class="mt-3"><?php echo $experience['position']; ?></h5>
                        <h6 class="text-muted"><?php echo $experience['company']; ?></h6>
                        <p class="text-muted">
                            <?php echo format_date($experience['start_date']); ?> - 
                            <?php echo empty($experience['end_date']) ? 'Одоог хүртэл' : format_date($experience['end_date']); ?>
                        </p>
                        <p><?php echo truncate($experience['description'], 150); ?></p>
                        <a href="index.php?page=experience" class="btn btn-outline-primary mt-2">Бүх туршлага</a>
                    <?php else: ?>
                        <p class="mt-3">Ажлын туршлагын мэдээлэл оруулаагүй байна.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Ур чадварууд -->
<section id="home-skills" class="mb-5">
    <h3 class="mb-4">Онцлох ур чадварууд</h3>
    <div class="row">
        <?php if(count($skills) > 0): ?>
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
            <div class="col-12 mt-3">
                <a href="index.php?page=skills" class="btn btn-outline-primary">Бүх ур чадвар</a>
            </div>
        <?php else: ?>
            <div class="col-12">
                <p>Ур чадварын мэдээлэл оруулаагүй байна.</p>
            </div>
        <?php endif; ?>
    </div>
</section> 