<?php
// Үйлдлийг шалгах
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Бичлэг устгах
if ($action === 'delete' && $id) {
    // Verify CSRF token for GET requests - add ?csrf_token=token to the URL
    if (isset($_GET['csrf_token']) && function_exists('verify_csrf_token')) {
        if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            admin_message('error', 'Аюулгүй байдлын шалгалт амжилтгүй боллоо!', 'admin-experience');
        }
    }

    $stmt = $conn->prepare("DELETE FROM experience WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        admin_message('success', 'Ажлын туршлагын мэдээлэл амжилттай устгагдлаа!', 'admin-experience');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-experience');
    }
}

// Бичлэг нэмэх эсвэл засах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $company = clean($_POST['company']);
    $position = clean($_POST['position']);
    $start_date = clean($_POST['start_date']);
    $end_date = clean($_POST['end_date']);
    $description = $_POST['description']; // WYSIWYG editor-оос ирсэн учир цэвэрлэхгүй
    $order_num = (int)$_POST['order_num'];

    if (empty($company) || empty($position) || empty($start_date)) {
        admin_message('error', 'Компанийн нэр, албан тушаал, эхэлсэн огноо талбаруудыг заавал бөглөнө үү!', 'admin-experience');
    }

    if ($action === 'edit' && $id) {
        // Мэдээллийг шинэчлэх
        $stmt = $conn->prepare("UPDATE experience SET company = :company, position = :position, 
                           start_date = :start_date, end_date = :end_date, description = :description, order_num = :order_num 
                           WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        // Шинээр мэдээлэл үүсгэх
        $stmt = $conn->prepare("INSERT INTO experience (company, position, start_date, end_date, description, order_num) 
                           VALUES (:company, :position, :start_date, :end_date, :description, :order_num)");
    }

    $stmt->bindParam(':company', $company);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':order_num', $order_num);

    if ($stmt->execute()) {
        admin_message('success', 'Ажлын туршлагын мэдээлэл амжилттай хадгалагдлаа!', 'admin-experience');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-experience');
    }
}

// Засах үйлдлийн бол мэдээллийг авах
$experience = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM experience WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $experience = $stmt->fetch();

    if (!$experience) {
        admin_message('error', 'Ажлын туршлагын мэдээлэл олдсонгүй!', 'admin-experience');
    }
}

// Хуудаслалт
$current_page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$records_per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $records_per_page;

// Эрэмбэлэлт
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'start_date';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'desc';

$valid_sort_fields = ['company', 'position', 'start_date', 'end_date', 'order_num'];
if (!in_array($sort_field, $valid_sort_fields)) {
    $sort_field = 'start_date';
}

$valid_sort_orders = ['asc', 'desc'];
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'desc';
}

// Ажлын туршлагын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM experience ORDER BY $sort_field $sort_order LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$experience_list = $stmt->fetchAll();

// Нийт бичлэгийн тоог авах
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM experience");
$stmt->execute();
$row = $stmt->fetch();
$total_records = $row['count'];
?>

<div class="admin-header mb-4">
    <h2><?php echo $action === 'edit' || $action === 'add' ? ($action === 'edit' ? '<i class="fas fa-edit me-2"></i> Ажлын туршлага засах' : '<i class="fas fa-plus me-2"></i> Ажлын туршлага нэмэх') : '<i class="fas fa-briefcase me-2"></i> Ажлын туршлага'; ?></h2>

    <?php if ($action === 'list'): ?>
        <a href="index.php?page=admin-experience&action=add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Шинээр нэмэх
        </a>
    <?php else: ?>
        <a href="index.php?page=admin-experience" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Жагсаалт руу буцах
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
    <!-- Ажлын туршлагын жагсаалт -->
    <div class="admin-card">
        <?php if (count($experience_list) > 0): ?>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th><a href="index.php?page=admin-experience&sort=company&order=<?php echo $sort_field === 'company' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="company" data-order="<?php echo $sort_field === 'company' ? $sort_order : 'asc'; ?>">Компани <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-experience&sort=position&order=<?php echo $sort_field === 'position' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="position" data-order="<?php echo $sort_field === 'position' ? $sort_order : 'asc'; ?>">Албан тушаал <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-experience&sort=start_date&order=<?php echo $sort_field === 'start_date' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="start_date" data-order="<?php echo $sort_field === 'start_date' ? $sort_order : 'asc'; ?>">Эхэлсэн <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-experience&sort=end_date&order=<?php echo $sort_field === 'end_date' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="end_date" data-order="<?php echo $sort_field === 'end_date' ? $sort_order : 'asc'; ?>">Дууссан <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-experience&sort=order_num&order=<?php echo $sort_field === 'order_num' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="order_num" data-order="<?php echo $sort_field === 'order_num' ? $sort_order : 'asc'; ?>">Дараалал <i class="fas fa-sort"></i></a></th>
                            <th>Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experience_list as $item): ?>
                            <tr>
                                <td><?php echo $item['company']; ?></td>
                                <td><?php echo $item['position']; ?></td>
                                <td><?php echo format_date($item['start_date']); ?></td>
                                <td><?php echo empty($item['end_date']) ? 'Одоог хүртэл' : format_date($item['end_date']); ?></td>
                                <td><?php echo $item['order_num']; ?></td>
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

            <?php pagination($total_records, $records_per_page, $current_page, 'admin-experience'); ?>

        <?php else: ?>
            <p class="text-muted">Ажлын туршлагын мэдээлэл одоогоор байхгүй байна.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Ажлын туршлага нэмэх/засах форм -->
    <div class="admin-card">
        <form action="index.php?page=admin-experience<?php echo $action === 'edit' ? '&action=edit&id=' . $id : ''; ?>" method="post" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="company" class="form-label">Компанийн нэр</label>
                    <input type="text" class="form-control" id="company" name="company" value="<?php echo $experience['company'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Компанийн нэрийг оруулна уу.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="position" class="form-label">Албан тушаал</label>
                    <input type="text" class="form-control" id="position" name="position" value="<?php echo $experience['position'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Албан тушаал оруулна уу.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label">Эхэлсэн огноо</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $experience['start_date'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Эхэлсэн огноог оруулна уу.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label">Дууссан огноо</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $experience['end_date'] ?? ''; ?>">
                    <div class="form-text">Хоосон үлдээвэл "Одоог хүртэл" гэж харагдана.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="order_num" class="form-label">Дараалал</label>
                    <input type="number" class="form-control" id="order_num" name="order_num" value="<?php echo $experience['order_num'] ?? '0'; ?>" min="0">
                    <div class="form-text">Дэлгэцэнд харагдах дараалал (0-ээс эхэлнэ)</div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Тайлбар</label>
                    <textarea class="form-control wysiwyg-editor" id="description" name="description" rows="5"><?php echo html_decode($experience['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Хадгалах
                </button>
                <a href="index.php?page=admin-experience" class="btn btn-outline-secondary ms-2">Цуцлах</a>
            </div>
        </form>
    </div>
<?php endif; ?>