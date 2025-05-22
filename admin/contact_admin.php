<?php
// Үйлдлийг шалгах
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// Бичлэг устгах
if ($action === 'delete' && $id) {
    // Verify CSRF token for GET requests - add ?csrf_token=token to the URL
    if (isset($_GET['csrf_token']) && function_exists('verify_csrf_token')) {
        if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            admin_message('error', 'Аюулгүй байдлын шалгалт амжилтгүй боллоо!', 'admin-contact');
        }
    }

    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = :id");
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        admin_message('success', 'Холбоо барих мэдээлэл амжилттай устгагдлаа!', 'admin-contact');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-contact');
    }
}

// Бичлэг нэмэх эсвэл засах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $name = clean($_POST['name']);
    $value = clean($_POST['value']);
    $icon = clean($_POST['icon']);
    $url = clean($_POST['url']);
    $order_num = (int)$_POST['order_num'];

    if (empty($name) || empty($value)) {
        admin_message('error', 'Нэр болон утга заавал оруулах шаардлагатай!', 'admin-contact');
    }

    if ($action === 'edit' && $id) {
        // Мэдээллийг шинэчлэх
        $stmt = $conn->prepare("UPDATE contacts SET name = :name, value = :value, icon = :icon, url = :url, order_num = :order_num WHERE id = :id");
        $stmt->bindParam(':id', $id);
    } else {
        // Шинээр мэдээлэл үүсгэх
        $stmt = $conn->prepare("INSERT INTO contacts (name, value, icon, url, order_num) VALUES (:name, :value, :icon, :url, :order_num)");
    }

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':value', $value);
    $stmt->bindParam(':icon', $icon);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':order_num', $order_num);

    if ($stmt->execute()) {
        admin_message('success', 'Холбоо барих мэдээлэл амжилттай хадгалагдлаа!', 'admin-contact');
    } else {
        admin_message('error', 'Алдаа гарлаа: ' . $stmt->errorInfo()[2], 'admin-contact');
    }
}

// Засах үйлдлийн бол мэдээллийг авах
$contact = null;
if ($action === 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $contact = $stmt->fetch();

    if (!$contact) {
        admin_message('error', 'Холбоо барих мэдээлэл олдсонгүй!', 'admin-contact');
    }
}

// Хуудаслалт
$current_page = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
$records_per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $records_per_page;

// Эрэмбэлэлт
$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'order_num';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$valid_sort_fields = ['name', 'value', 'order_num'];
if (!in_array($sort_field, $valid_sort_fields)) {
    $sort_field = 'order_num';
}

$valid_sort_orders = ['asc', 'desc'];
if (!in_array($sort_order, $valid_sort_orders)) {
    $sort_order = 'asc';
}

// Холбоо барих мэдээллийг авах
$stmt = $conn->prepare("SELECT * FROM contacts ORDER BY $sort_field $sort_order LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$contact_list = $stmt->fetchAll();

// Нийт бичлэгийн тоог авах
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM contacts");
$stmt->execute();
$row = $stmt->fetch();
$total_records = $row['count'];
?>

<div class="admin-header mb-4">
    <h2><?php echo $action === 'edit' || $action === 'add' ? ($action === 'edit' ? '<i class="fas fa-edit me-2"></i> Холбоо барих засах' : '<i class="fas fa-plus me-2"></i> Холбоо барих нэмэх') : '<i class="fas fa-address-card me-2"></i> Холбоо барих'; ?></h2>

    <?php if ($action === 'list'): ?>
        <a href="index.php?page=admin-contact&action=add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Шинээр нэмэх
        </a>
    <?php else: ?>
        <a href="index.php?page=admin-contact" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Жагсаалт руу буцах
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
    <!-- Холбоо барих жагсаалт -->
    <div class="admin-card">
        <?php if (count($contact_list) > 0): ?>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th><a href="index.php?page=admin-contact&sort=name&order=<?php echo $sort_field === 'name' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="name" data-order="<?php echo $sort_field === 'name' ? $sort_order : 'asc'; ?>">Нэр <i class="fas fa-sort"></i></a></th>
                            <th><a href="index.php?page=admin-contact&sort=value&order=<?php echo $sort_field === 'value' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="value" data-order="<?php echo $sort_field === 'value' ? $sort_order : 'asc'; ?>">Утга <i class="fas fa-sort"></i></a></th>
                            <th>Айкон</th>
                            <th><a href="index.php?page=admin-contact&sort=order_num&order=<?php echo $sort_field === 'order_num' && $sort_order === 'asc' ? 'desc' : 'asc'; ?>" class="sort-btn" data-sort="order_num" data-order="<?php echo $sort_field === 'order_num' ? $sort_order : 'asc'; ?>">Дараалал <i class="fas fa-sort"></i></a></th>
                            <th>Үйлдэл</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contact_list as $item): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['value']; ?></td>
                                <td>
                                    <?php if (!empty($item['icon'])): ?>
                                        <i class="<?php echo $item['icon']; ?>"></i> <?php echo $item['icon']; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Хоосон</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['order_num']; ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="index.php?page=admin-contact&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?page=admin-contact&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php pagination($total_records, $records_per_page, $current_page, 'admin-contact'); ?>

        <?php else: ?>
            <p class="text-muted">Холбоо барих мэдээлэл одоогоор байхгүй байна.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <!-- Холбоо барих нэмэх/засах форм -->
    <div class="admin-card">
        <form action="index.php?page=admin-contact<?php echo $action === 'edit' ? '&action=edit&id=' . $id : ''; ?>" method="post" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Нэр</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $contact['name'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Нэрийг оруулна уу.</div>
                    <div class="form-text">Жишээ: Имэйл, Утас, LinkedIn, GitHub гэх мэт</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="value" class="form-label">Утга</label>
                    <input type="text" class="form-control" id="value" name="value" value="<?php echo $contact['value'] ?? ''; ?>" required>
                    <div class="invalid-feedback">Утгыг оруулна уу.</div>
                    <div class="form-text">Жишээ: your.email@example.com, +976 99887766 гэх мэт</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="icon" class="form-label">Font Awesome Айкон</label>
                    <input type="text" class="form-control" id="icon" name="icon" value="<?php echo $contact['icon'] ?? ''; ?>">
                    <div class="form-text">Жишээ: fas fa-envelope, fas fa-phone гэх мэт. <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a> сайтаас айконыг сонгоно уу.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="url" class="form-label">URL (заавал биш)</label>
                    <input type="url" class="form-control" id="url" name="url" value="<?php echo $contact['url'] ?? ''; ?>">
                    <div class="form-text">Холбоос хэрэгтэй бол оруулна уу. Жишээ: https://linkedin.com/in/yourprofile</div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="order_num" class="form-label">Дараалал</label>
                    <input type="number" class="form-control" id="order_num" name="order_num" value="<?php echo $contact['order_num'] ?? '0'; ?>" min="0">
                    <div class="form-text">Дэлгэцэнд харагдах дараалал (0-ээс эхэлнэ)</div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Хадгалах
                </button>
                <a href="index.php?page=admin-contact" class="btn btn-outline-secondary ms-2">Цуцлах</a>
            </div>
        </form>
    </div>
<?php endif; ?>