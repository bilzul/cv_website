<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Check login status
$login_status = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Define test categories
$test_categories = [
  'security' => [
    'name' => 'Security Features',
    'icon' => 'shield-alt',
    'color' => 'primary',
    'tests' => [
      'csrf' => [
        'name' => 'CSRF Protection',
        'description' => 'Forms should include CSRF tokens and verify them on submission',
        'test_file' => 'test_csrf.php',
        'status' => null,
        'autorun' => false
      ],
      'sri' => [
        'name' => 'Subresource Integrity',
        'description' => 'Assets should include integrity hashes to prevent tampering',
        'test_file' => 'test_sri.php',
        'status' => null,
        'autorun' => false
      ],
      'csp' => [
        'name' => 'Content Security Policy',
        'description' => 'CSP headers should be set to prevent XSS attacks',
        'status' => null,
        'autorun' => true
      ],
      'secure_headers' => [
        'name' => 'Security Headers',
        'description' => 'Required security headers should be set',
        'status' => null,
        'autorun' => true
      ]
    ]
  ],
  'error_handling' => [
    'name' => 'Error Handling',
    'icon' => 'exclamation-circle',
    'color' => 'warning',
    'tests' => [
      'error_function' => [
        'name' => 'Error Handling Function',
        'description' => 'handle_app_error() function should be implemented and work correctly',
        'test_file' => 'test_error_handling.php',
        'status' => null,
        'autorun' => false
      ],
      'error_logging' => [
        'name' => 'Error Logging',
        'description' => 'Errors should be logged to file when appropriate',
        'test_file' => 'test_error_handling.php',
        'status' => null,
        'autorun' => false
      ],
      'output_buffering' => [
        'name' => 'Output Buffering',
        'description' => 'Output buffering should be enabled to prevent header errors',
        'status' => null,
        'autorun' => true
      ]
    ]
  ],
  'assets' => [
    'name' => 'Asset Management',
    'icon' => 'file-code',
    'color' => 'success',
    'tests' => [
      'fallback_css' => [
        'name' => 'CSS Fallbacks',
        'description' => 'Fallback CSS should be available if main stylesheet fails to load',
        'status' => null,
        'autorun' => true
      ],
      'fallback_js' => [
        'name' => 'JavaScript Fallbacks',
        'description' => 'Fallback JS should be available if main scripts fail to load',
        'status' => null,
        'autorun' => true
      ],
      'icon_fix' => [
        'name' => 'Font Awesome Fix',
        'description' => 'Fix for duplicate Font Awesome icons should be implemented',
        'test_file' => 'test_icons.php',
        'status' => null,
        'autorun' => false
      ]
    ]
  ]
];

// Run auto tests
function get_http_response_header($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  $response = curl_exec($ch);
  curl_close($ch);

  $headers = [];
  $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
  foreach (explode("\r\n", $header_text) as $header) {
    if (strpos($header, ': ') !== false) {
      list($key, $value) = explode(': ', $header, 2);
      $headers[strtolower($key)] = $value;
    }
  }

  return $headers;
}

// Run autorun tests
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$cv_url = $base_url . dirname($_SERVER['REQUEST_URI'], 2);
$headers = get_http_response_header($cv_url);

// Test CSP
$test_categories['security']['tests']['csp']['status'] =
  isset($headers['content-security-policy']) ? 'PASS' : 'FAIL';

// Test Security Headers
$required_headers = ['x-content-type-options', 'x-frame-options', 'x-xss-protection'];
$missing_headers = array_diff($required_headers, array_keys($headers));
$test_categories['security']['tests']['secure_headers']['status'] =
  empty($missing_headers) ? 'PASS' : 'FAIL';
$test_categories['security']['tests']['secure_headers']['message'] =
  empty($missing_headers) ? null : 'Missing headers: ' . implode(', ', $missing_headers);

// Test Output Buffering
$test_categories['error_handling']['tests']['output_buffering']['status'] =
  ob_get_level() > 0 ? 'PASS' : 'FAIL';

// Test CSS Fallback
$fallback_css_path = ROOT_PATH . '/assets/css/fallback.css';
$test_categories['assets']['tests']['fallback_css']['status'] =
  file_exists($fallback_css_path) ? 'PASS' : 'FAIL';

// Test JS Fallback
$fallback_js_path = ROOT_PATH . '/assets/js/fallback.js';
$test_categories['assets']['tests']['fallback_js']['status'] =
  file_exists($fallback_js_path) ? 'PASS' : 'FAIL';

// Count results
$total_tests = 0;
$passed_tests = 0;
$failed_tests = 0;
$manual_tests = 0;

foreach ($test_categories as $category) {
  foreach ($category['tests'] as $test) {
    $total_tests++;
    if ($test['status'] === 'PASS') {
      $passed_tests++;
    } elseif ($test['status'] === 'FAIL') {
      $failed_tests++;
    } else {
      $manual_tests++;
    }
  }
}

// Output the documentation page
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Security Test Suite</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <style>
    body {
      padding-top: 20px;
      padding-bottom: 40px;
    }

    .container {
      max-width: 1200px;
    }

    .page-header {
      padding-bottom: 20px;
      margin-bottom: 30px;
      border-bottom: 1px solid #eee;
    }

    .test-card {
      margin-bottom: 20px;
    }

    .test-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .status-badge {
      font-size: 14px;
      font-weight: bold;
    }

    .pass {
      background-color: #28a745;
      color: white;
    }

    .fail {
      background-color: #dc3545;
      color: white;
    }

    .manual {
      background-color: #ffc107;
      color: black;
    }

    .progress {
      height: 25px;
      margin-bottom: 20px;
    }

    .category-title {
      margin-top: 30px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="page-header">
      <h1><i class="fas fa-check-circle"></i> Security Test Suite</h1>
      <p class="lead">Comprehensive testing of all security features and error handling mechanisms.</p>
      <?php if ($login_status): ?>
        <a href="index.php?page=dashboard_admin" class="btn btn-primary">
          <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
      <?php else: ?>
        <a href="index.php?page=admin" class="btn btn-primary">
          <i class="fas fa-lock"></i> Login to Admin
        </a>
      <?php endif; ?>
    </div>

    <!-- Test Summary -->
    <div class="card mb-4">
      <div class="card-header">
        <h5><i class="fas fa-chart-pie"></i> Test Summary</h5>
      </div>
      <div class="card-body">
        <div class="progress">
          <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($passed_tests / $total_tests) * 100; ?>%;"
            aria-valuenow="<?php echo $passed_tests; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_tests; ?>">
            <?php echo $passed_tests; ?> Passed
          </div>
          <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo ($failed_tests / $total_tests) * 100; ?>%;"
            aria-valuenow="<?php echo $failed_tests; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_tests; ?>">
            <?php echo $failed_tests; ?> Failed
          </div>
          <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo ($manual_tests / $total_tests) * 100; ?>%;"
            aria-valuenow="<?php echo $manual_tests; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_tests; ?>">
            <?php echo $manual_tests; ?> Manual
          </div>
        </div>

        <div class="row text-center">
          <div class="col-md-4">
            <div class="card bg-light mb-3">
              <div class="card-body">
                <h5 class="card-title text-success"><?php echo $passed_tests; ?> Passed</h5>
                <p class="card-text">Tests that automatically passed verification.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light mb-3">
              <div class="card-body">
                <h5 class="card-title text-danger"><?php echo $failed_tests; ?> Failed</h5>
                <p class="card-text">Tests that failed automatic verification.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light mb-3">
              <div class="card-body">
                <h5 class="card-title text-warning"><?php echo $manual_tests; ?> Manual</h5>
                <p class="card-text">Tests that require manual verification.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Test Categories -->
    <?php foreach ($test_categories as $category_id => $category): ?>
      <h2 class="category-title">
        <i class="fas fa-<?php echo $category['icon']; ?> text-<?php echo $category['color']; ?>"></i>
        <?php echo $category['name']; ?>
      </h2>

      <div class="row">
        <?php foreach ($category['tests'] as $test_id => $test): ?>
          <div class="col-md-6">
            <div class="card test-card">
              <div class="card-header test-header">
                <h5 class="mb-0"><?php echo $test['name']; ?></h5>
                <?php if ($test['status']): ?>
                  <span class="badge status-badge <?php echo strtolower($test['status']); ?>">
                    <?php echo $test['status']; ?>
                  </span>
                <?php else: ?>
                  <span class="badge status-badge manual">MANUAL CHECK</span>
                <?php endif; ?>
              </div>
              <div class="card-body">
                <p><?php echo $test['description']; ?></p>
                <?php if (isset($test['message'])): ?>
                  <p class="text-<?php echo $test['status'] === 'PASS' ? 'success' : 'danger'; ?>">
                    <?php echo $test['message']; ?>
                  </p>
                <?php endif; ?>

                <?php if (isset($test['test_file'])): ?>
                  <a href="<?php echo $test['test_file']; ?>" class="btn btn-primary" target="_blank">
                    Run Detailed Test <i class="fas fa-external-link-alt"></i>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
</body>

</html>