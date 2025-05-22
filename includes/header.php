<?php
// Хэрэв output буфер идэвхгүй бол идэвхжүүлэх
if (ob_get_level() == 0) ob_start();
?>
<!DOCTYPE html>
<html lang="mn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Миний CV - Программ хангамжийн инженерийн танилцуулга">
    <meta name="keywords" content="CV, биографи, боловсрол, туршлага, ур чадвар, программист">
    <meta name="author" content="<?php echo isset($personal['name']) ? htmlspecialchars($personal['name']) : 'CV Owner'; ?>">
    <meta name="theme-color" content="#4e6cff">
    <title><?php echo SITE_NAME; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo ASSETS_URL; ?>/images/favicon.svg" type="image/svg+xml">
    <link rel="shortcut icon" href="<?php echo ASSETS_URL; ?>/images/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?php echo ASSETS_URL; ?>/images/favicon.svg"> <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer"> <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"> <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo get_asset_with_integrity(ASSETS_URL . '/css/style.css'); ?>">

    <!-- Fallback for CSS loading errors -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if main stylesheet loaded correctly
            var styleSheets = Array.from(document.styleSheets);
            var mainStyleLoaded = styleSheets.some(function(sheet) {
                try {
                    return sheet.href && sheet.href.includes('style.css') && sheet.cssRules.length > 0;
                } catch (e) {
                    // If we can't access cssRules, the sheet may have failed to load or it's from a different origin
                    return false;
                }
            });

            if (!mainStyleLoaded) {
                console.warn('Main stylesheet failed to load. Loading fallback CSS...');
                var fallbackLink = document.createElement('link');
                fallbackLink.rel = 'stylesheet';
                fallbackLink.href = '<?php echo ASSETS_URL; ?>/css/fallback.css';
                document.head.appendChild(fallbackLink);
            }
        });
    </script>
    <!-- Security Headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com; script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; img-src 'self' data: https://source.unsplash.com http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com; connect-src 'self' http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com; frame-src 'self' http://gc.kis.v2.scr.kaspersky-labs.com ws://gc.kis.v2.scr.kaspersky-labs.com;">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <!-- X-Frame-Options header is already set in index.php -->
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="Permissions-Policy" content="geolocation=(), camera=(), microphone=()">
</head>

<body>
    <!-- Header Section -->
    <header class="header-main">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="logo-container">
                    <h1 class="site-logo mb-0">
                        <a href="index.php" class="logo-link">
                            <span class="logo-text">CV</span>
                            <span class="logo-dot"></span>
                        </a>
                    </h1>
                </div>
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <div class="admin-controls">
                        <a href="index.php?page=admin" class="btn btn-primary me-2"><i class="fas fa-tachometer-alt me-1"></i> Хянах самбар</a>
                        <a href="index.php?page=logout" class="btn btn-outline-dark"><i class="fas fa-sign-out-alt me-1"></i> Гарах</a>
                    </div>
                <?php else: ?>
                    <div>
                        <a href="index.php?page=admin" class="btn btn-outline-primary"><i class="fas fa-user me-1"></i> Админ нэвтрэх</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark main-nav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $request == 'home' ? 'active' : ''; ?>" href="index.php?page=home">
                            <i class="fas fa-home nav-icon"></i> Нүүр
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $request == 'education' ? 'active' : ''; ?>" href="index.php?page=education">
                            <i class="fas fa-graduation-cap nav-icon"></i> Боловсрол
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $request == 'experience' ? 'active' : ''; ?>" href="index.php?page=experience">
                            <i class="fas fa-briefcase nav-icon"></i> Туршлага
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $request == 'skills' ? 'active' : ''; ?>" href="index.php?page=skills">
                            <i class="fas fa-chart-bar nav-icon"></i> Ур чадвар
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $request == 'projects' ? 'active' : ''; ?>" href="index.php?page=projects">
                            <i class="fas fa-project-diagram nav-icon"></i> Төслүүд
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $request == 'contact' ? 'active' : ''; ?>" href="index.php?page=contact">
                            <i class="fas fa-envelope nav-icon"></i> Холбоо барих
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content py-5">
        <div class="container">
            <?php if (isset($_SESSION['admin_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['admin_message']['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show custom-alert" role="alert">
                    <div class="alert-icon">
                        <i class="fas fa-<?php echo $_SESSION['admin_message']['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    </div>
                    <div class="alert-content">
                        <?php echo $_SESSION['admin_message']['message']; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php unset($_SESSION['admin_message']);
            endif; ?>