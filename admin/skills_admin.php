<?php
// Үйлдлийг шалгах
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Бичлэг устгах
if ($action === 'delete' && $id) {
    // Verify CSRF token for GET requests - add ?csrf_token=token to the URL
    if (isset($_GET['csrf_token']) && function_exists('verify_csrf_token')) {
        if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            admin_message('error', 'Аюулгүй байдлын шалгалт амжилтгүй боллоо!', 'admin-skills');
        }
    }

    $stmt = $conn->prepare("DELETE FROM skills WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        admin_message('success', 'Ур чадварын мэдээлэл амжилттай устгагдлаа!', 'admin-skills');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-skills');
    }
}

// Бичлэг нэмэх эсвэл засах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $name = clean($_POST['name']);
    $level = (int)$_POST['level'];
    $category = clean($_POST['category']);
    $order_num = (int)$_POST['order_num'];

    if (empty($name) || $level < 1 || $level > 100) {
        admin_message('error', 'Чадварын нэр заавал оруулж, түвшин нь 1-100 хооронд байх ёстой!', 'admin-skills');
    }

    if ($action === 'edit' && $id) {
        // Мэдээллийг шинэчлэх
        $stmt = $conn->prepare("UPDATE skills SET name = :name, level = :level, category = :category, order_num = :order_num WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        // Шинээр мэдээлэл үүсгэх
        $stmt = $conn->prepare("INSERT INTO skills (name, level, category, order_num) VALUES (:name, :level, :category, :order_num)");
    }

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':level', $level);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':order_num', $order_num);

    if ($stmt->execute()) {
        admin_message('success', 'Ур чадварын мэдээлэл амжилттай хадгалагдлаа!', 'admin-skills');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-skills');
    }
}

// Засах үйлдлийн бол мэдээллийг авах
$skill = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM skills WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $skill = $stmt->fetch();

    if (!$skill) {
        admin_message('error', 'Ур чадварын мэдээлэл олдсонгүй!', 'admin-skills');
    }
}

// Хуудаслалт
$current_page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$records_per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $records_per_page;

// Эрэмбэлэлт
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'level';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'desc';

$valid_sort_fields = ['name', 'level', 'category', 'order_num'];
if (!in_array($sort_field, $valid_sort_fields)) {
    $sort_field = 'level';
}

$valid_sort_orders = ['asc', 'desc'];
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'desc';
}

// Ур чадварын мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM skills ORDER BY $sort_field $sort_order LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$skills_list = $stmt->fetchAll();

// Нийт бичлэгийн тоог авах
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM skills");
$stmt->execute();
$row = $stmt->fetch();
$total_records = $row['count'];
?>

<div class="admin-header mb-4">
    <h2><?php echo $action === 'edit' || $action === 'add' ? ($action === 'edit' ? '<i class="fas fa-edit me-2"></i> Ур чадвар засах' : '<i class="fas fa-plus me-2"></i> Ур чадвар нэмэх') : '<i class="fas fa-chart-bar me-2"></i> Ур чадварууд'; ?></h2>

    <?php if ($action === 'list'): ?>
        <a href="index.php?page=admin-skills&action=add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Шинээр нэмэх
        </a>
    <?php else: ?>
        <a href="index.php?page=admin-skills" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Жагсаалт руу буцах
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
    <!-- Ур чадварын жагсаалт -->
    <div class="admin-card">
        <?php if (count($skills_list) > 0): ?>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th><a href="index.php?page=admin-skills&sort=name&order=<?php echo $sort_field === 'name' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="name" data-order="<?php echo $sort_field === 'name' ? $sort_order : 'asc'; ?>">Нэр <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-skills&sort=level&order=<?php echo $sort_field === 'level' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="level" data-order="<?php echo $sort_field === 'level' ? $sort_order : 'asc'; ?>">Түвшин <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-skills&sort=category&order=<?php echo $sort_field === 'category' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="category" data-order="<?php echo $sort_field === 'category' ? $sort_order : 'asc'; ?>">Ангилал <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-skills&sort=order_num&order=<?php echo $sort_field === 'order_num' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="order_num" data-order="<?php echo $sort_field === 'order_num' ? $sort_order : 'asc'; ?>">Дараалал <i class="fas fa-sort"></i></a></th>
                            <th>Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($skills_list as $item): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $item['level']; ?>%"
                                            aria-valuenow="<?php echo $item['level']; ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo $item['level']; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $item['category']; ?></td>
                                <td><?php echo $item['order_num']; ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="index.php?page=admin-skills&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?page=admin-skills&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php pagination($total_records, $records_per_page, $current_page, 'admin-skills'); ?>

        <?php else: ?>
            <p class="text-muted">Ур чадварын мэдээлэл одоогоор байхгүй байна.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Ур чадвар нэмэх/засах форм -->
    <div class="admin-card">
        <form action="index.php?page=admin-skills<?php echo $action === 'edit' ? '&action=edit&id=' . $id : ''; ?>" method="post" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Ур чадварын нэр</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $skill['name'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Ур чадварын нэрийг оруулна уу.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Ангилал</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo $skill['category'] ?? ''; ?>">
                    <div class="form-text">Жишээ: Техникийн, Хэлний, Програмчлалын гэх мэт</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="level" class="form-label">Түвшин (1-100%)</label>
                    <input type="range" class="form-range" id="level" name="level" min="1" max="100" value="<?php echo $skill['level'] ?? '50'; ?>">
                    <div class="text-center">
                        <span id="level-display"><?php echo $skill['level'] ?? '50'; ?>%</span>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="order_num" class="form-label">Дараалал</label>
                    <input type="number" class="form-control" id="order_num" name="order_num" value="<?php echo $skill['order_num'] ?? '0'; ?>" min="0">
                    <div class="form-text">Дэлгэцэнд харагдах дараалал (0-ээс эхэлнэ)</div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Хадгалах
                </button>
                <a href="index.php?page=admin-skills" class="btn btn-outline-secondary ms-2">Цуцлах</a>
            </div>
        </form>
    </div>

    <script>
        // Range слайдерын утгыг харуулах
        document.addEventListener('DOMContentLoaded', function() {
            const range = document.getElementById('level');
            const display = document.getElementById('level-display');

            range.addEventListener('input', function() {
                display.textContent = this.value + '%';
            });
        });
    </script>
<?php endif; ?>