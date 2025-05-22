<?php
// Test file to verify SRI implementation
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Files to test
$test_files = [
  ASSETS_URL . '/css/style.css',
  ASSETS_URL . '/js/main.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// Test function
function test_sri($file)
{
  $result = [];
  $result['file'] = $file;

  if (strpos($file, 'http') === 0) {
    // External file
    $result['type'] = 'external';
    $result['message'] = 'External files should have hardcoded integrity values in header.php';
    $result['status'] = 'MANUAL CHECK REQUIRED';
  } else {
    // Local file
    $result['type'] = 'local';
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($file, '/');

    if (!file_exists($full_path)) {
      $result['status'] = 'ERROR';
      $result['message'] = 'File does not exist';
    } else {
      $integrity = calculate_sri_hash($full_path);
      if (!$integrity) {
        $result['status'] = 'ERROR';
        $result['message'] = 'Could not calculate integrity hash';
      } else {
        $result['status'] = 'SUCCESS';
        $result['integrity'] = $integrity;
        $result['full_tag'] = '<link rel="stylesheet" href="' . $file . '" integrity="' . $integrity . '" crossorigin="anonymous">';
      }
    }
  }

  return $result;
}

// Perform tests
$test_results = [];
foreach ($test_files as $file) {
  $test_results[] = test_sri($file);
}

// Output results as HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SRI Implementation Test</title>
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

    .manual {
      border-left: 5px solid #FFC107;
    }

    .tag {
      background-color: #f5f5f5;
      padding: 10px;
      border-radius: 3px;
      font-family: monospace;
      word-break: break-all;
    }
  </style>
</head>

<body>
  <h1>SRI Implementation Test</h1>
  <div class="results">
    <?php foreach ($test_results as $result): ?>
      <div class="test-item <?php
                            echo $result['status'] === 'SUCCESS' ? 'success' : ($result['status'] === 'ERROR' ? 'error' : 'manual');
                            ?>">
        <h3><?php echo htmlspecialchars($result['file']); ?></h3>
        <p><strong>Type:</strong> <?php echo $result['type']; ?></p>
        <p><strong>Status:</strong> <?php echo $result['status']; ?></p>
        <p><strong>Message:</strong> <?php echo $result['message'] ?? 'N/A'; ?></p>

        <?php if (isset($result['integrity'])): ?>
          <p><strong>Integrity Hash:</strong> <?php echo $result['integrity']; ?></p>
          <div class="tag"><?php echo htmlspecialchars($result['full_tag']); ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div>
    <h2>How to Use SRI</h2>
    <p>For local files, use the <code>get_asset_with_integrity()</code> function:</p>
    <pre>echo get_asset_with_integrity(ASSETS_URL . '/css/style.css');</pre>

    <p>For external files, include the integrity attribute with the hash value:</p>
    <pre>&lt;link href="https://cdn.example.com/style.css" rel="stylesheet" integrity="sha384-HASH" crossorigin="anonymous"&gt;</pre>
  </div>
</body>

</html>