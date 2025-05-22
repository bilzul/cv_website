<?php
// Detect if using HTTPS
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Main site configuration for InfinityFree hosting
define('SITE_NAME', 'CV - Миний танилцуулга');

// Update with your InfinityFree domain - auto-detect protocol
define('SITE_URL', $protocol . 'bilcv.infinityfreeapp.com'); // Replace with your actual domain
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('ADMIN_EMAIL', 'ubilguun@gmail.com'); // Update with your email

// Page settings
define('ITEMS_PER_PAGE', 10);

// Assets URL
define('ASSETS_URL', SITE_URL . '/assets');

// Error reporting - Consider turning off for production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Enable error logging instead
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/error_log.txt');

// Time zone
date_default_timezone_set('Asia/Ulaanbaatar');
