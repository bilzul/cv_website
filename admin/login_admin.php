<?php
// Session эхлүүлэх
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Нэвтрэх хүсэлт шалгах
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Login forms often skip CSRF verification on the first login since there's no session yet
    // But we'll add a check if a session already exists
    if (isset($_SESSION['csrf_token']) && function_exists('verify_csrf_token')) {
        verify_csrf_token();
    }

    $username = clean($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        login_error("Хэрэглэгчийн нэр болон нууц үгээ оруулна уу.");
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();

            // Check if password is using old MD5 format
            if (strlen($user['password']) == 32) {
                // Still using old MD5 - verify and update to secure format
                if (md5($password) === $user['password']) {
                    // Password matches, update to new secure format
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE admin SET password = :password WHERE id = :id");
                    $update_stmt->bindParam(':password', $new_hash);
                    $update_stmt->bindParam(':id', $user['id']);
                    $update_stmt->execute();                    // Login successful
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];

                    // Create a new CSRF token for the session
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    header("Location: index.php?page=admin");
                    exit;
                } else {
                    login_error("Буруу нууц үг.");
                }
            } else {
                // Using new secure format
                if (password_verify($password, $user['password'])) {                    // Нэвтрэлт амжилттай
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];

                    // Create a new CSRF token for the session
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    // Add CSRF token for better security
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header("Location: index.php?page=admin");
                    exit;
                } else {
                    login_error("Буруу нууц үг.");
                }
            }
        } else {
            login_error("Хэрэглэгчийн нэр олдсонгүй.");
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Админ нэвтрэх</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['login_error']; ?>
                        <?php unset($_SESSION['login_error']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?page=admin" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Хэрэглэгчийн нэр</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">
                            Хэрэглэгчийн нэр оруулна уу.
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Нууц үг</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">
                            Нууц үг оруулна уу.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Нэвтрэх</button>
                </form>
            </div>
        </div>
    </div>
</div>