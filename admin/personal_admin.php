<?php
// Хувийн мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM personal_info LIMIT 1");
$stmt->execute();
$personal = $stmt->fetch();

// Мэдээллийг засах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $name = clean($_POST['name']);
    $profession = clean($_POST['profession']);
    $bio = $_POST['bio']; // WYSIWYG editor-оос ирсэн учир цэвэрлэхгүй
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $address = clean($_POST['address']);

    // Зургийн файл шалгах
    $photo = $personal['photo'] ?? '';
    if (!empty($_FILES['photo']['name'])) {
        $result = saveImage($_FILES['photo'], UPLOAD_PATH);
        if ($result['success']) {
            $photo = $result['file_name'];
        } else {
            admin_message('error', $result['message'], 'admin-personal');
        }
    }

    if ($personal) {
        // Мэдээллийг шинэчлэх
        $stmt = $conn->prepare("UPDATE personal_info SET name = :name, profession = :profession, bio = :bio, 
                              email = :email, phone = :phone, address = :address, photo = :photo WHERE id = :id");
        $stmt->bindParam(':id', $personal['id']);
    } else {
        // Шинээр мэдээлэл үүсгэх
        $stmt = $conn->prepare("INSERT INTO personal_info (name, profession, bio, email, phone, address, photo) 
                              VALUES (:name, :profession, :bio, :email, :phone, :address, :photo)");
    }

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':profession', $profession);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':photo', $photo);

    if ($stmt->execute()) {
        admin_message('success', 'Хувийн мэдээлэл амжилттай хадгалагдлаа!', 'admin-personal');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-personal');
    }
}
?>

<div class="admin-header mb-4">
    <h2><i class="fas fa-user me-2"></i> Хувийн мэдээлэл</h2>
    <a href="index.php?page=admin" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Буцах
    </a>
</div>

<div class="admin-card">
    <form action="index.php?page=admin-personal" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <!-- CSRF token -->
        <?php if (isset($_SESSION['csrf_token'])): ?>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Нэр</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $personal['name'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Нэрээ оруулна уу.</div>
                </div>

                <div class="mb-3">
                    <label for="profession" class="form-label">Мэргэжил</label>
                    <input type="text" class="form-control" id="profession" name="profession" value="<?php echo $personal['profession'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Мэргэжлээ оруулна уу.</div>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Танилцуулга</label>
                    <textarea class="form-control wysiwyg-editor" id="bio" name="bio" rows="6"><?php echo html_decode($personal['bio'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Имэйл</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $personal['email'] ?? ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Утас</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $personal['phone'] ?? ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Хаяг</label>
                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo $personal['address'] ?? ''; ?></textarea>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="photo" class="form-label">Профайл зураг</label>
                    <?php if ($personal && !empty($personal['photo'])): ?>
                        <div class="mb-3">
                            <img src="<?php echo UPLOAD_URL . $personal['photo']; ?>" alt="Current photo" class="img-thumbnail mb-2" style="max-height: 200px;">
                            <p class="text-muted small">Одоогийн зураг</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <div class="form-text">Хэрэв шинэ зураг сонгохгүй бол одоогийн зураг хэвээрээ үлдэнэ.</div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Хадгалах
                </button>
            </div>
        </div>
    </form>
</div>