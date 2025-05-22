<?php
// Хувийн мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM personal_info LIMIT 1");
$stmt->execute();
$personal = $stmt->fetch();

// Холбоо барих мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM contacts ORDER BY order_num ASC");
$stmt->execute();
$contacts = $stmt->fetchAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4 pb-2 border-bottom"><i class="fas fa-address-book me-2"></i> Холбоо барих</h2>

        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="contact-info">
                    <h3 class="mb-4">Хувийн мэдээлэл</h3>

                    <?php if ($personal && !empty($personal['email'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5>Имэйл</h5>
                                <p class="mb-0"><a href="mailto:<?php echo htmlspecialchars($personal['email']); ?>"><?php echo htmlspecialchars($personal['email']); ?></a></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($personal && !empty($personal['phone'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5>Утас</h5>
                                <p class="mb-0"><a href="tel:<?php echo htmlspecialchars($personal['phone']); ?>"><?php echo htmlspecialchars($personal['phone']); ?></a></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($personal && !empty($personal['address'])): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5>Хаяг</h5>
                                <p class="mb-0"><?php echo htmlspecialchars($personal['address']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (count($contacts) > 0): ?>
                        <div class="social-links">
                            <?php foreach ($contacts as $contact): ?>
                                <a href="<?php echo htmlspecialchars($contact['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo htmlspecialchars($contact['platform']); ?>" aria-label="<?php echo htmlspecialchars($contact['platform']); ?>">
                                    <i class="<?php echo !empty($contact['icon']) ? htmlspecialchars($contact['icon']) : 'fas fa-link'; ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-4">
                <div class="contact-info">
                    <h3 class="mb-4">Холбоо барих мэдээлэл</h3>
                    <p>Хэрэв танд асуух зүйл, хамтран ажиллах санал байвал надтай доорх холбоо барих мэдээллээр холбогдоорой.</p>

                    <div class="alert alert-info mt-4">
                        <p class="mb-0"><i class="fas fa-info-circle me-2"></i> Имэйл бол надтай холбогдох хамгийн шуурхай арга юм.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>