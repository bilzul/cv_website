<?php
// Test file to verify error handling implementation
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Define some test errors to trigger
$test_errors = [
  [
    'name' => 'Simple Error',
    'message' => 'This is a test error message',
    'type' => 'error',
    'log' => true,
    'redirect' => null
  ],
  [
    'name' => 'Warning Message',
    'message' => 'This is a test warning message',
    'type' => 'warning',
    'log' => true,
    'redirect' => null
  ],
  [
    'name' => 'Info Message',
    'message' => 'This is a test info message',
    'type' => 'info',
    'log' => false,
    'redirect' => null
  ],
  [
    'name' => 'Success Message',
    'message' => 'This is a test success message',
    'type' => 'success',
    'log' => false,
    'redirect' => null
  ]
];

// Clear previous test errors
if (isset($_SESSION['app_errors'])) {
  unset($_SESSION['app_errors']);
}

// Trigger test errors
$results = [];
foreach ($test_errors as $error) {
  $result = handle_app_error(
    $error['message'],
    $error['type'],
    $error['log'],
    $error['redirect']
  );

  $results[] = [
    'name' => $error['name'],
    'result' => $result
  ];
}

// Check if log file exists and is writable
$log_dir = dirname(__DIR__) . '/logs';
$log_file = $log_dir . '/app_errors.log';
$log_status = [
  'directory_exists' => file_exists($log_dir),
  'directory_writable' => is_writable($log_dir),
  'file_exists' => file_exists($log_file),
  'file_writable' => file_exists($log_file) ? is_writable($log_file) : false,
  'recent_logs' => file_exists($log_file) ? array_slice(file($log_file, FILE_IGNORE_NEW_LINES), -5) : []
];

// Output results as HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error Handling Test</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    h1,
    h2 {
      color: #333;
    }

    .results {
      margin: 20px 0;
    }

    .test-item,
    .log-status {
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 5px;
    }

    .test-item h3,
    .log-status h3 {
      margin-top: 0;
    }

    .error {
      background-color: #FFEBEE;
    }

    .warning {
      background-color: #FFF8E1;
    }

    .info {
      background-color: #E3F2FD;
    }

    .success {
      background-color: #E8F5E9;
    }

    pre {
      background-color: #f5f5f5;
      padding: 10px;
      border-radius: 3px;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    th,
    td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <h1>Error Handling Test</h1>

  <h2>Test Results</h2>
  <div class="results">
    <?php foreach ($results as $result): ?>
      <div class="test-item <?php echo $result['result']['type']; ?>">
        <h3><?php echo htmlspecialchars($result['name']); ?></h3>
        <p><strong>Type:</strong> <?php echo $result['result']['type']; ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($result['result']['message']); ?></p>
        <p><strong>Time:</strong> <?php echo $result['result']['time']; ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <h2>Error Logging Status</h2>
  <div class="log-status">
    <table>
      <tr>
        <th>Check</th>
        <th>Status</th>
      </tr>
      <tr>
        <td>Log Directory Exists</td>
        <td><?php echo $log_status['directory_exists'] ? '✅ Yes' : '❌ No'; ?></td>
      </tr>
      <tr>
        <td>Log Directory Writable</td>
        <td><?php echo $log_status['directory_writable'] ? '✅ Yes' : '❌ No'; ?></td>
      </tr>
      <tr>
        <td>Log File Exists</td>
        <td><?php echo $log_status['file_exists'] ? '✅ Yes' : '❌ No'; ?></td>
      </tr>
      <tr>
        <td>Log File Writable</td>
        <td><?php echo $log_status['file_writable'] ? '✅ Yes' : '❌ No'; ?></td>
      </tr>
    </table>

    <?php if (!empty($log_status['recent_logs'])): ?>
      <h3>Recent Log Entries</h3>
      <pre><?php echo htmlspecialchars(implode("\n", $log_status['recent_logs'])); ?></pre>
    <?php else: ?>
      <p>No recent log entries found.</p>
    <?php endif; ?>
  </div>

  <h2>Session Stored Errors</h2>
  <pre><?php print_r($_SESSION['app_errors'] ?? 'No errors in session'); ?></pre>

  <div>
    <h2>How to Use Error Handling</h2>
    <pre>// Simple error
handle_app_error('Something went wrong', 'error');

// Warning with logging
handle_app_error('Resource is deprecated', 'warning', true);

// Error with redirect
handle_app_error('Invalid form submission', 'error', true, 'index.php');</pre>
  </div>
</body>

</html>