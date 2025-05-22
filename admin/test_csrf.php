<?php
// Test file to verify CSRF protection implementation
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ensure we have a CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Admin pages to check for CSRF protection
$admin_pages = [
  'skills_admin.php',
  'projects_admin.php',
  'experience_admin.php',
  'contact_admin.php',
  'login_admin.php',
  'personal_admin.php',
  'education_admin.php',
  'dashboard_admin.php'
];

// Check if the file contains the CSRF token input field and verification call
function check_csrf_implementation($file_path)
{
  $result = [];
  $result['file'] = basename($file_path);

  if (!file_exists($file_path)) {
    $result['status'] = 'ERROR';
    $result['message'] = 'File does not exist';
    return $result;
  }

  $content = file_get_contents($file_path);

  // Check if file contains a form
  $has_form = (strpos($content, '<form') !== false);

  // If it does not have a form, it might not need CSRF protection
  if (!$has_form) {
    $result['status'] = 'INFO';
    $result['message'] = 'No form detected, may not need CSRF protection';
    return $result;
  }

  // Check for CSRF token input field
  $has_token_field = (strpos($content, 'name="csrf_token"') !== false);

  // Check for verification function call
  $has_verification = (strpos($content, 'verify_csrf_token') !== false);

  if ($has_token_field && $has_verification) {
    $result['status'] = 'SUCCESS';
    $result['message'] = 'CSRF protection implemented correctly';
  } elseif ($has_token_field && !$has_verification) {
    $result['status'] = 'WARNING';
    $result['message'] = 'CSRF token field found, but verification function call missing';
  } elseif (!$has_token_field && $has_verification) {
    $result['status'] = 'WARNING';
    $result['message'] = 'Verification function call found, but CSRF token field missing';
  } else {
    $result['status'] = 'ERROR';
    $result['message'] = 'No CSRF protection implemented';
  }

  return $result;
}

// Perform checks
$test_results = [];
foreach ($admin_pages as $page) {
  $file_path = dirname(__FILE__) . '/' . $page;
  $test_results[] = check_csrf_implementation($file_path);
}

// Output results as HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CSRF Protection Test</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    h1 {
      color: #333;
    }

    .results {
      margin-top: 20px;
    }

    .test-item {
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 5px;
    }

    .test-item h3 {
      margin-top: 0;
    }

    .success {
      border-left: 5px solid #4CAF50;
    }

    .error {
      border-left: 5px solid #F44336;
    }

    .warning {
      border-left: 5px solid #FFC107;
    }

    .info {
      border-left: 5px solid #2196F3;
    }
  </style>
</head>

<body>
  <h1>CSRF Protection Test</h1>
  <div class="results">
    <?php foreach ($test_results as $result): ?>
      <div class="test-item <?php
                            echo $result['status'] === 'SUCCESS' ? 'success' : ($result['status'] === 'ERROR' ? 'error' : ($result['status'] === 'WARNING' ? 'warning' : 'info'));
                            ?>">
        <h3><?php echo htmlspecialchars($result['file']); ?></h3>
        <p><strong>Status:</strong> <?php echo $result['status']; ?></p>
        <p><strong>Message:</strong> <?php echo $result['message']; ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <div>
    <h2>How to Implement CSRF Protection</h2>
    <p>1. Add this hidden input field to your form:</p>
    <pre>&lt;input type="hidden" name="csrf_token" value="&lt;?php echo $_SESSION['csrf_token']; ?&gt;"&gt;</pre>

    <p>2. Add this verification at the beginning of your form processing code:</p>
    <pre>if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    verify_csrf_token();
    
    // Process form data
    // ...
}</pre>
  </div>
</body>

</html>