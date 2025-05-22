<?php
// Enable output buffering
ob_start();

// Error handling - Enable in development, disable in production
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session with secure settings - ready for Chrome's third-party cookie phase-out
session_start([
    'cookie_httponly' => true,      // Prevent JavaScript access to session cookie
    'cookie_secure' => isset($_SERVER['HTTPS']), // Use secure cookies when HTTPS is used
    'cookie_samesite' => 'Strict',  // Enhanced protection against CSRF attacks
    'cookie_lifetime' => 3600,      // Session cookie expires after 1 hour (in seconds)
    'use_strict_mode' => true       // Reject uninitialized session IDs
]);

// Set security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// Абсолют замыг тодорхойлох
define('ROOT_PATH', __DIR__);

// Тохиргооны файлуудыг оруулах
include_once ROOT_PATH . "/config/config.php";
include_once ROOT_PATH . "/config/database.php";
include_once ROOT_PATH . "/includes/functions.php";

// Чиглүүлэгч - URL-д байгаа хүсэлтийг боловсруулах
$request = $_GET['page'] ?? 'home';

// Мэдээллийн санг холбох
$db = new Database();
$conn = $db->getConnection();

// Get personal info for header
$stmt = $conn->prepare("SELECT name FROM personal_info LIMIT 1");
$stmt->execute();
$personal = $stmt->fetch();

// Хэрэглэгч нэвтэрснийг шалгах
$loggedIn = isset($_SESSION['admin_id']);

// Хуудасны толгойн хэсэг
include_once ROOT_PATH . "/includes/header.php";

// Үндсэн контентийг ачаалах
switch ($request) {
    case 'home':
        include_once ROOT_PATH . "/pages/home.php";
        break;
    case 'education':
        include_once ROOT_PATH . "/pages/education.php";
        break;
    case 'experience':
        include_once ROOT_PATH . "/pages/experience.php";
        break;
    case 'skills':
        include_once ROOT_PATH . "/pages/skills.php";
        break;
    case 'projects':
        include_once ROOT_PATH . "/pages/projects.php";
        break;
    case 'contact':
        include_once ROOT_PATH . "/pages/contact.php";
        break;
    // Админ хэсэг
    case 'admin':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/dashboard_admin.php";
        } else {
            include_once ROOT_PATH . "/admin/login_admin.php";
        }
        break;
    case 'admin-personal':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/personal_admin.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'admin-education':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/education_admin.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'admin-experience':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/experience_admin.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'admin-skills':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/skills_admin.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'admin-projects':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/projects_admin.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'admin-contact':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/contact_admin.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'documentation':
        if ($loggedIn) {
            include_once ROOT_PATH . "/admin/documentation.php";
        } else {
            header("Location: index.php?page=admin");
        }
        break;
    case 'logout':
        session_destroy();
        header("Location: index.php");
        break;
    default:
        include_once ROOT_PATH . "/pages/404.php";
}

// Хуудасны хөлний хэсэг
include_once ROOT_PATH . "/includes/footer.php";
