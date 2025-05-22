<?php
// Note: config.php and functions.php are already included in index.php
// No need to include them again

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: index.php?page=admin");
  exit;
}

// Get the documentation content
$doc_file = dirname(__DIR__) . '/security_and_error_handling.md';
$doc_content = file_exists($doc_file) ? file_get_contents($doc_file) : 'Documentation file not found.';

// Parse Markdown (simple implementation)
function parse_markdown($text)
{
  // Headers
  $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
  $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
  $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);

  // Lists
  $text = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $text);
  $text = preg_replace('/(<li>.*?<\/li>\n)+/s', '<ul>$0</ul>', $text);

  // Code
  $text = preg_replace('/`(.*?)`/s', '<code>$1</code>', $text);

  // Paragraphs
  $text = preg_replace('/^(?!<h|<ul|<\/ul|<li|<\/li|<code)(.*?)$/m', '<p>$1</p>', $text);

  return $text;
}

// Test link info
$test_links = [
  [
    'name' => 'SRI Implementation Test',
    'url' => 'test_sri.php',
    'description' => 'Test the Subresource Integrity implementation to ensure assets are properly protected.'
  ],
  [
    'name' => 'CSRF Protection Test',
    'url' => 'test_csrf.php',
    'description' => 'Verify that all admin forms are protected against Cross-Site Request Forgery attacks.'
  ],
  [
    'name' => 'Error Handling Test',
    'url' => 'test_error_handling.php',
    'description' => 'Test the unified error handling system and verify error logging functionality.'
  ],
  [
    'name' => 'Font Awesome Icon Test',
    'url' => 'test_icons.php',
    'description' => 'Test Font Awesome icons to ensure they display correctly and verify fallback mechanisms.'
  ]
];

// Output the documentation page
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Security Documentation</title>
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

    .doc-header {
      padding-bottom: 20px;
      margin-bottom: 30px;
      border-bottom: 1px solid #eee;
    }

    .sidebar {
      position: sticky;
      top: 20px;
    }

    .doc-content {
      font-size: 16px;
      line-height: 1.6;
    }

    .doc-content h1 {
      margin-top: 40px;
      margin-bottom: 20px;
    }

    .doc-content h2 {
      margin-top: 30px;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .doc-content h3 {
      margin-top: 25px;
      margin-bottom: 10px;
    }

    .doc-content ul {
      margin-bottom: 20px;
      padding-left: 25px;
    }

    .doc-content li {
      margin-bottom: 5px;
    }

    .doc-content code {
      background-color: #f5f5f5;
      padding: 2px 5px;
      border-radius: 3px;
    }

    .test-card {
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="doc-header">
      <h1><i class="fas fa-shield-alt"></i> Security Documentation</h1>
      <p class="lead">Comprehensive documentation of security and error handling improvements for the CV website.</p>
      <a href="index.php?page=dashboard_admin" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
      </a>
    </div>

    <div class="row">
      <div class="col-md-3">
        <div class="sidebar">
          <div class="card">
            <div class="card-header">
              <h5><i class="fas fa-list"></i> Contents</h5>
            </div>
            <div class="card-body">
              <nav id="toc" class="nav flex-column">
                <a class="nav-link" href="#security">Security Enhancements</a>
                <a class="nav-link" href="#error-handling">Error Handling</a>
                <a class="nav-link" href="#tests">Test Tools</a>
              </nav>
            </div>
          </div>
          <div class="card mt-4">
            <div class="card-header">
              <h5><i class="fas fa-flask"></i> Test Tools</h5>
            </div>
            <div class="card-body">
              <div class="list-group">
                <a href="test_security.php" class="list-group-item list-group-item-action list-group-item-primary" target="_blank">
                  <i class="fas fa-shield-alt"></i> Complete Security Test Suite
                </a>
                <?php foreach ($test_links as $link): ?>
                  <a href="<?php echo $link['url']; ?>" class="list-group-item list-group-item-action" target="_blank">
                    <?php echo htmlspecialchars($link['name']); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="doc-content" id="security">
          <?php echo parse_markdown($doc_content); ?>
        </div>

        <div id="tests">
          <h2>Test Tools</h2>
          <p>Use these tools to verify the correct implementation of security features and error handling mechanisms:</p>

          <div class="row">
            <?php foreach ($test_links as $link): ?>
              <div class="col-md-6">
                <div class="card test-card">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($link['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($link['description']); ?></p>
                    <a href="<?php echo $link['url']; ?>" class="btn btn-primary" target="_blank">
                      Run Test <i class="fas fa-external-link-alt"></i>
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
</body>

</html>