<?php
// Хуудасны дугаар болон нийт бүртгэлээс хуудаслалт үүсгэх
function pagination($total_records, $records_per_page, $current_page, $url_parameter)
{
    $total_pages = ceil($total_records / $records_per_page);

    if ($total_pages > 1) {
        echo '<ul class="pagination">';

        // Өмнөх
        if ($current_page > 1) {
            echo '<li><a href="?page=' . $url_parameter . '&pg=' . ($current_page - 1) . '">Өмнөх</a></li>';
        }

        // Хуудаснууд
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo '<li class="active"><span>' . $i . '</span></li>';
            } else {
                echo '<li><a href="?page=' . $url_parameter . '&pg=' . $i . '">' . $i . '</a></li>';
            }
        }

        // Дараагийн
        if ($current_page < $total_pages) {
            echo '<li><a href="?page=' . $url_parameter . '&pg=' . ($current_page + 1) . '">Дараах</a></li>';
        }

        echo '</ul>';
    }
}

// Аюулгүй байдлын үүднээс string утгыг цэвэрлэх
function clean($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// CSRF токеныг шалгах
function verify_csrf_token()
{
    if (
        !isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
        $_POST['csrf_token'] !== $_SESSION['csrf_token']
    ) {
        // CSRF шалгалт амжилтгүй, алдааны мессеж харуулах
        $_SESSION['admin_message'] = [
            'type' => 'error',
            'message' => 'Аюулгүй байдлын шалгалт амжилтгүй боллоо. Хуудсыг дахин ачаалж оролдоно уу.'
        ];
        // Одоогийн хуудас руу буцах
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Токеныг шинэчлэх
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}

// Зургийн файлыг хадгалах
function saveImage($file, $target_directory)
{
    // Хавтас байхгүй бол үүсгэх
    if (!file_exists($target_directory)) {
        mkdir($target_directory, 0777, true);
    }

    // Аюулгүй файлын нэр үүсгэх
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $file_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
    $target_file = $target_directory . $file_name;
    $uploadOk = 1;

    // Файл зураг мөн эсэхийг шалгах
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "message" => "Файл зураг биш байна."];
    }

    // Файлын хэмжээг шалгах (5MB-аас хэтрэхгүй)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "Файлын хэмжээ хэт том байна."];
    }

    // Зөвшөөрөгдсөн файлын төрлүүд
    $allowed_types = ["jpg", "jpeg", "png", "gif", "svg"];
    if (!in_array($file_extension, $allowed_types)) {
        return ["success" => false, "message" => "Зөвхөн JPG, JPEG, PNG, GIF, SVG файлууд зөвшөөрөгдөнө."];
    }    // This check is already handled by the in_array check above, so this redundant check is removed

    // Файлыг хуулах
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_name" => $file_name];
    } else {
        return ["success" => false, "message" => "Файл хуулахад алдаа гарлаа."];
    }
}

// HTML Special Chars түр зуур хасах (WYSIWYG editor-т ашиглана)
function html_decode($str)
{
    return htmlspecialchars_decode($str, ENT_QUOTES);
}

// Нэгдсэн алдаа болон нотификейшн удирдлага
function handle_app_error($error, $type = 'error', $log_to_file = false, $redirect = null)
{
    // Алдааг сешнд хадгалах
    if (!isset($_SESSION['app_errors'])) {
        $_SESSION['app_errors'] = [];
    }

    $error_data = [
        'type' => $type, // error, warning, info, success  
        'message' => $error,
        'time' => date('Y-m-d H:i:s')
    ];

    // Add to session
    $_SESSION['app_errors'][] = $error_data;

    // Optionally log to file
    if ($log_to_file) {
        $log_dir = dirname(__DIR__) . '/logs';
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $log_file = $log_dir . '/app_errors.log';
        $log_message = date('Y-m-d H:i:s') . ' [' . strtoupper($type) . '] ' . $error . PHP_EOL;
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }

    // If redirect is specified, redirect to that page
    if ($redirect) {
        header("Location: " . $redirect);
        exit;
    }

    return $error_data;
}

// Нэвтрэх хуудасны алдаа
function login_error($message)
{
    $_SESSION['login_error'] = $message;
    header("Location: index.php?page=admin");
    exit;
}

// Админ хуудаснуудын бичлэг засах, нэмэх, устгахын алдаа
function admin_message($type, $message, $redirect)
{
    $_SESSION['admin_message'] = [
        'type' => $type, // success, error
        'message' => $message
    ];
    header("Location: index.php?page=" . $redirect);
    exit;
}

// Огноог хүний унших хэлбэрт хөрвүүлэх
function format_date($date_string)
{
    if (empty($date_string) || $date_string == '0000-00-00') {
        return 'Одоог хүртэл';
    }

    $date = new DateTime($date_string);
    return $date->format('Y оны n-р сарын j');
}

// Текстийг товчлох
function truncate($text, $length = 100)
{
    if (strlen($text) <= $length) {
        return $text;
    }

    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . '...';
}

// Calculate SRI hash for a local file
function calculate_sri_hash($file_path)
{
    if (!file_exists($file_path)) {
        return '';
    }

    $content = file_get_contents($file_path);
    $hash = base64_encode(hash('sha384', $content, true));

    return "sha384-$hash";
}

// Get asset with SRI integrity
function get_asset_with_integrity($asset_path)
{
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($asset_path, '/');

    // CSS or JS file - adding integrity check
    if (preg_match('/\.(css|js)$/', $asset_path)) {
        $integrity = calculate_sri_hash($full_path);
        if ($integrity) {
            return $asset_path . '" integrity="' . $integrity . '" crossorigin="anonymous';
        }
    }

    return $asset_path;
}

// Generate a nonce for scripts
function get_csp_nonce()
{
    if (!isset($_SESSION['csp_nonce'])) {
        $_SESSION['csp_nonce'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csp_nonce'];
}

// Эсрэг тохиолдолд font awesome ашиглах нөөц арга
function get_icon_fallback($icon_name)
{
    $icon_map = [
        'home' => '&#x1F3E0;', // 🏠
        'graduation-cap' => '&#x1F393;', // 🎓
        'briefcase' => '&#x1F4BC;', // 💼
        'chart-bar' => '&#x1F4CA;', // 📊
        'project-diagram' => '&#x1F4C1;', // 📁
        'envelope' => '&#x2709;', // ✉
        'user' => '&#x1F464;', // 👤
        'sign-out-alt' => '&#x1F6AA;', // 🚪
        'check-circle' => '&#x2705;', // ✅
        'exclamation-circle' => '&#x26A0;', // ⚠
        'phone' => '&#x1F4DE;', // 📞
        'map-marker-alt' => '&#x1F4CD;', // 📍
        'heart' => '&#x2764;', // ❤️
        'angle-right' => '&#x25B6;', // ▶
        'facebook-f' => 'FB',
        'linkedin-in' => 'IN',
        'github' => 'GH',
        'twitter' => 'TW',
        'tachometer-alt' => '&#x1F4F8;', // 📸
    ];

    return $icon_map[$icon_name] ?? '&#x2753;'; // ❓ as default
}

// Асуудал гарсан тохиолдолд asset-ын fallback олгох
function get_fallback_asset($asset_type)
{
    $fallbacks = [
        'css' => ASSETS_URL . '/css/fallback.css',
        'js' => ASSETS_URL . '/js/fallback.js',
        'image' => ASSETS_URL . '/images/placeholder.png'
    ];

    return $fallbacks[$asset_type] ?? '';
}
