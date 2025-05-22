<?php
// Үйлдлийг шалгах
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Бичлэг устгах
if ($action === 'delete' && $id) {
    // Verify CSRF token for GET requests - add ?csrf_token=token to the URL
    if (isset($_GET['csrf_token']) && function_exists('verify_csrf_token')) {
        if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            admin_message('error', 'Аюулгүй байдлын шалгалт амжилтгүй боллоо!', 'admin-projects');
        }
    }

    $stmt = $conn->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        admin_message('success', 'Төслийн мэдээлэл амжилттай устгагдлаа!', 'admin-projects');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-projects');
    }
}

// Бичлэг нэмэх эсвэл засах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $title = clean($_POST['title']);
    $description = $_POST['description']; // WYSIWYG editor-оос ирсэн учир цэвэрлэхгүй
    $technologies = clean($_POST['technologies']);
    $url = clean($_POST['url']);
    $start_date = clean($_POST['start_date']);
    $end_date = clean($_POST['end_date']);
    $order_num = (int)$_POST['order_num'];

    // Зургийн файл шалгах
    $image = '';
    if ($action === 'edit' && $id) {
        $stmt = $conn->prepare("SELECT image FROM projects WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        $image = $row['image'] ?? '';
    }

    if (empty($title) || empty($description) || empty($technologies) || empty($url) || empty($start_date)) {
        admin_message('error', 'Төслийн нэр, тайлбар, технологи, URL, эхэлсэн огноо талбаруудыг заавал бөглөнө үү!', 'admin-projects');
    }

    if ($action === 'edit' && $id) {
        // Мэдээллийг шинэчлэх
        $stmt = $conn->prepare("UPDATE projects SET title = :title, description = :description, technologies = :technologies, url = :url, start_date = :start_date, end_date = :end_date, order_num = :order_num, image = :image WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        // Шинээр мэдээлэл үүсгэх
        $stmt = $conn->prepare("INSERT INTO projects (title, description, technologies, url, start_date, end_date, order_num, image) VALUES (:title, :description, :technologies, :url, :start_date, :end_date, :order_num, :image)");
    }

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':technologies', $technologies);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':order_num', $order_num);
    $stmt->bindParam(':image', $image);

    if ($stmt->execute()) {
        admin_message('success', 'Төслийн мэдээлэл амжилттай хадгалагдлаа!', 'admin-projects');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-projects');
    }
}

// Засах үйлдлийн бол мэдээллийг авах
$project = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $project = $stmt->fetch();

    if (!$project) {
        admin_message('error', 'Төслийн мэдээлэл олдсонгүй!', 'admin-projects');
    }
}

// Хуудаслалт
$current_page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$records_per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $records_per_page;

// Эрэмбэлэлт
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'start_date';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'desc';

$valid_sort_fields = ['title', 'start_date', 'end_date', 'order_num'];
if (!in_array($sort_field, $valid_sort_fields)) {
    $sort_field = 'start_date';
}

$valid_sort_orders = ['asc', 'desc'];
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'desc';
}

// Төслийн мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM projects ORDER BY $sort_field $sort_order LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$project_list = $stmt->fetchAll();

// Нийт бичлэгийн тоог авах
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM projects");
$stmt->execute();
$row = $stmt->fetch();
$total_records = $row['count'];
?>

<div class="admin-header mb-4">
    <h2><?php echo $action === 'edit' || $action === 'add' ? ($action === 'edit' ? '<i class="fas fa-edit me-2"></i> Төслийн засах' : '<i class="fas fa-plus me-2"></i> Төслийн нэмэх') : '<i class="fas fa-project-diagram me-2"></i> Төслийн мэдээлэл'; ?></h2>

    <?php if ($action === 'list'): ?>
        <a href="index.php?page=admin-projects&action=add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Шинээр нэмэх
        </a>
    <?php else: ?>
        <a href="index.php?page=admin-projects" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Жагсаалт руу буцах
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
    <!-- Төслийн жагсаалт -->
    <div class="admin-card">
        <?php if (count($project_list) > 0): ?>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th><a href="index.php?page=admin-projects&sort=title&order=<?php echo $sort_field === 'title' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="title" data-order="<?php echo $sort_field === 'title' ? $sort_order : 'asc'; ?>">Төслийн нэр <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-projects&sort=start_date&order=<?php echo $sort_field === 'start_date' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="start_date" data-order="<?php echo $sort_field === 'start_date' ? $sort_order : 'asc'; ?>">Эхэлсэн <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-projects&sort=end_date&order=<?php echo $sort_field === 'end_date' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="end_date" data-order="<?php echo $sort_field === 'end_date' ? $sort_order : 'asc'; ?>">Дууссан <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-projects&sort=order_num&order=<?php echo $sort_field === 'order_num' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="order_num" data-order="<?php echo $sort_field === 'order_num' ? $sort_order : 'asc'; ?>">Дараалал <i class="fas fa-sort"></i></a></th>
                            <th>Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($project_list as $item): ?>
                            <tr>
                                <td><?php echo $item['title']; ?></td>
                                <td><?php echo format_date($item['start_date']); ?></td>
                                <td><?php echo empty($item['end_date']) ? 'Одоог хүртэл' : format_date($item['end_date']); ?></td>
                                <td><?php echo $item['order_num']; ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="index.php?page=admin-projects&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?page=admin-projects&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php pagination($total_records, $records_per_page, $current_page, 'admin-projects'); ?>

        <?php else: ?>
            <p class="text-muted">Төслийн мэдээлэл одоогоор байхгүй байна.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Төслийн нэмэх/засах форм -->
    <div class="admin-card">
        <form action="index.php?page=admin-projects<?php echo $action === 'edit' ? '&action=edit&id=' . $id : ''; ?>" method="post" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Төслийн нэр</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $project['title'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Төслийн нэрийг оруулна уу.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="url" class="form-label">URL</label>
                    <input type="url" class="form-control" id="url" name="url" value="<?php echo $project['url'] ?? ''; ?>" required>
                    <div class="invalid-feedback">URL оруулна уу.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label">Эхэлсэн огноо</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $project['start_date'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Эхэлсэн огноог оруулна уу.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label">Дууссан огноо</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $project['end_date'] ?? ''; ?>">
                    <div class="form-text">Хоосон үлдээвэл "Одоог хүртэл" гэж харагдана.</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="order_num" class="form-label">Дараалал</label>
                    <input type="number" class="form-control" id="order_num" name="order_num" value="<?php echo $project['order_num'] ?? '0'; ?>" min="0">
                    <div class="form-text">Дэлгэцэнд харагдах дараалал (0-ээс эхэлнэ)</div>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Тайлбар</label>
                    <textarea class="form-control wysiwyg-editor" id="description" name="description" rows="5"><?php echo html_decode($project['description'] ?? ''); ?></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="technologies" class="form-label">Технологи</label>
                    <input type="text" class="form-control" id="technologies" name="technologies" value="<?php echo $project['technologies'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Технологи оруулна уу.</div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Хадгалах
                </button>
                <a href="index.php?page=admin-projects" class="btn btn-outline-secondary ms-2">Цуцлах</a>
            </div>
        </form>
    </div>
<?php endif; ?>