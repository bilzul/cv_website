<?php
// Detect if using HTTPS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Сайтын үндсэн тохиргоо
define('SITE_NAME', 'CV - Миний танилцуулга');
define('SITE_URL', $protocol . 'localhost/cv'); // XAMPP local URL
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/cv/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('ADMIN_EMAIL', 'admin@example.com');

// Хуудсын үзүүлэлтүүд
define('ITEMS_PER_PAGE', 10);

// CSS, Javascript болон бусад ассет файлууд
define('ASSETS_URL', SITE_URL . '/assets');

// Алдааны тайлан - Development горимд асаалттай байх
ini_set('display_errors', 1); // Production горимд 0 болгох
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Цагийн бүс
date_default_timezone_set('Asia/Ulaanbaatar');
