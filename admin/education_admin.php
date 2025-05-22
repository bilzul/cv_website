<?php
// Үйлдлийг шалгах
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Бичлэг устгах
if ($action === 'delete' && $id) {
    // Verify CSRF token if it's a GET request with actions
    if (
        isset($_GET['csrf_token']) && isset($_SESSION['csrf_token']) &&
        $_GET['csrf_token'] === $_SESSION['csrf_token']
    ) {

        $stmt = $conn->prepare("DELETE FROM education WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            // Regenerate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            admin_message('success', 'Боловсролын мэдээлэл амжилттай устгагдлаа!', 'admin-education');
        } else {
            admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-education');
        }
    } else {
        admin_message('error', 'CSRF token is invalid or missing', 'admin-education');
    }
}

// Бичлэг нэмэх эсвэл засах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $institution = clean($_POST['institution']);
    $degree = clean($_POST['degree']);
    $field = clean($_POST['field']);
    $start_date = clean($_POST['start_date']);
    $end_date = clean($_POST['end_date']);
    $description = $_POST['description']; // WYSIWYG editor-оос ирсэн учир цэвэрлэхгүй
    $order_num = (int)$_POST['order_num'];

    if (empty($institution) || empty($degree) || empty($field) || empty($start_date)) {
        admin_message('error', 'Сургуулийн нэр, зэрэг, чиглэл, эхэлсэн огноо талбаруудыг заавал бөглөнө үү!', 'admin-education');
    }

    if ($action === 'edit' && $id) {
        // Мэдээллийг шинэчлэх
        $stmt = $conn->prepare("UPDATE education SET institution = :institution, degree = :degree, field = :field, 
                           start_date = :start_date, end_date = :end_date, description = :description, order_num = :order_num 
                           WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        // Шинээр мэдээлэл үүсгэх
        $stmt = $conn->prepare("INSERT INTO education (institution, degree, field, start_date, end_date, description, order_num) 
                           VALUES (:institution, :degree, :field, :start_date, :end_date, :description, :order_num)");
    }

    $stmt->bindParam(':institution', $institution);
    $stmt->bindParam(':degree', $degree);
    $stmt->bindParam(':field', $field);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':order_num', $order_num);

    if ($stmt->execute()) {
        admin_message('success', 'Боловсролын мэдээлэл амжилттай хадгалагдлаа!', 'admin-education');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-education');
    }
}

// Засах үйлдлийн бол мэдээллийг авах
$education = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM education WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $education = $stmt->fetch();

    if (!$education) {
        admin_message('error', 'Боловсролын мэдээлэл олдсонгүй!', 'admin-education');
    }
}

// Хуудаслалт
$current_page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$records_per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $records_per_page;

// Эрэмбэлэлт
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'start_date';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'desc';

$valid_sort_fields = ['institution', 'degree', 'field', 'start_date', 'end_date', 'order_num'];
if (!in_array($sort_field, $valid_sort_fields)) {
    $sort_field = 'start_date';
}

$valid_sort_orders = ['asc', 'desc'];
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'desc';
}

// Боловсролын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM education ORDER BY $sort_field $sort_order LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$education_list = $stmt->fetchAll();

// Нийт бичлэгийн тоог авах
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM education");
$stmt->execute();
$row = $stmt->fetch();
$total_records = $row['count'];
?>

<div class="admin-header mb-4">
    <h2><?php echo $action === 'edit' || $action === 'add' ? ($action === 'edit' ? '<i class="fas fa-edit me-2"></i> Боловсрол засах' : '<i class="fas fa-plus me-2"></i> Боловсрол нэмэх') : '<i class="fas fa-graduation-cap me-2"></i> Боловсрол'; ?></h2>

    <?php if ($action === 'list'): ?>
        <a href="index.php?page=admin-education&action=add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Шинээр нэмэх
        </a>
    <?php else: ?>
        <a href="index.php?page=admin-education" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Жагсаалт руу буцах
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
    <!-- Боловсролын жагсаалт -->
    <div class="admin-card">
        <?php if (count($education_list) > 0): ?>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th><a href="index.php?page=admin-education&sort=institution&order=<?php echo $sort_field === 'institution' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="institution" data-order="<?php echo $sort_field === 'institution' ? $sort_order : 'asc'; ?>">Сургууль <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-education&sort=degree&order=<?php echo $sort_field === 'degree' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="degree" data-order="<?php echo $sort_field === 'degree' ? $sort_order : 'asc'; ?>">Зэрэг <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-education&sort=field&order=<?php echo $sort_field === 'field' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="field" data-order="<?php echo $sort_field === 'field' ? $sort_order : 'asc'; ?>">Чиглэл <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-education&sort=start_date&order=<?php echo $sort_field === 'start_date' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="start_date" data-order="<?php echo $sort_field === 'start_date' ? $sort_order : 'asc'; ?>">Эхэлсэн <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-education&sort=end_date&order=<?php echo $sort_field === 'end_date' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="end_date" data-order="<?php echo $sort_field === 'end_date' ? $sort_order : 'asc'; ?>">Дууссан <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-education&sort=order_num&order=<?php echo $sort_field === 'order_num' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="order_num" data-order="<?php echo $sort_field === 'order_num' ? $sort_order : 'asc'; ?>">Дараалал <i class="fas fa-sort"></i></a></th>
                            <th>Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($education_list as $item): ?>
                            <tr>
                                <td><?php echo $item['institution']; ?></td>
                                <td><?php echo $item['degree']; ?></td>
                                <td><?php echo $item['field']; ?></td>
                                <td><?php echo format_date($item['start_date']); ?></td>
                                <td><?php echo empty($item['end_date']) ? 'Одоог хүртэл' : format_date($item['end_date']); ?></td>
                                <td><?php echo $item['order_num']; ?></td>
                                <td>
                                    <div class="admin-actions"> <a href="index.php?page=admin-education&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?page=admin-education&action=delete&id=<?php echo $item['id']; ?>&csrf_token=<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>" class="btn btn-sm btn-danger delete-btn" data-confirm="Энэ боловсролын мэдээллийг устгахдаа итгэлтэй байна уу?">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php pagination($total_records, $records_per_page, $current_page, 'admin-education'); ?>

        <?php else: ?>
            <p class="text-muted">Боловсролын мэдээлэл одоогоор байхгүй байна.</p>
        <?php endif; ?>
    </div>
<?php else: ?> <!-- Боловсрол нэмэх/засах форм -->
    <div class="admin-card">
        <form action="index.php?page=admin-education<?php echo $action === 'edit' ? '&action=edit&id=' . $id : ''; ?>" method="post" class="needs-validation" novalidate>
            <!-- CSRF token -->
            <?php if (isset($_SESSION['csrf_token'])): ?>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="institution" class="form-label">Сургуулийн нэр</label>
                    <input type="text" class="form-control" id="institution" name="institution" value="<?php echo $education['institution'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Сургуулийн нэрийг оруулна уу.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="degree" class="form-label">Зэрэг</label>
                    <input type="text" class="form-control" id="degree" name="degree" value="<?php echo $education['degree'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Зэргийг оруулна уу.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="field" class="form-label">Чиглэл/Мэргэжил</label>
                    <input type="text" class="form-control" id="field" name="field" value="<?php echo $education['field'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Чиглэл эсвэл мэргэжлийг оруулна уу.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label">Эхэлсэн огноо</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $education['start_date'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Эхэлсэн огноог оруулна уу.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label">Дууссан огноо</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $education['end_date'] ?? ''; ?>">
                    <div class="form-text">Хоосон үлдээвэл "Одоог хүртэл" гэж харагдана.</div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Тайлбар</label>
                    <textarea class="form-control wysiwyg-editor" id="description" name="description" rows="5"><?php echo html_decode($education['description'] ?? ''); ?></textarea>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="order_num" class="form-label">Дараалал</label>
                    <input type="number" class="form-control" id="order_num" name="order_num" value="<?php echo $education['order_num'] ?? '0'; ?>" min="0">
                    <div class="form-text">Дэлгэцэнд харагдах дараалал (0-ээс эхэлнэ)</div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Хадгалах
                </button>
                <a href="index.php?page=admin-education" class="btn btn-outline-secondary ms-2">Цуцлах</a>
            </div>
        </form>
    </div>
<?php endif; ?>