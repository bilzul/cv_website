<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Test icons
$icons = [
  'home' => 'Home',
  'graduation-cap' => 'Education',
  'briefcase' => 'Experience',
  'chart-bar' => 'Skills',
  'project-diagram' => 'Projects',
  'envelope' => 'Contact',
  'user' => 'User',
  'sign-out-alt' => 'Sign Out',
  'check-circle' => 'Success',
  'exclamation-circle' => 'Warning',
  'phone' => 'Phone',
  'map-marker-alt' => 'Location',
  'heart' => 'Heart',
  'angle-right' => 'Right Arrow',
  'facebook-f' => 'Facebook',
  'linkedin-in' => 'LinkedIn',
  'github' => 'GitHub',
  'twitter' => 'Twitter',
  'tachometer-alt' => 'Dashboard'
];

// Output results as HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Font Awesome Icon Test</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

  <!-- Main CSS (now includes icon fix) -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
    }

    h1,
    h2 {
      color: #333;
    }

    .icon-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 15px;
      margin-top: 20px;
    }

    .icon-item {
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 5px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .icon {
      font-size: 24px;
      width: 30px;
      text-align: center;
    }

    .icon-name {
      flex-grow: 1;
    }

    .test-section {
      margin-bottom: 40px;
    }

    .toggle-fix {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 15px;
      cursor: pointer;
      border-radius: 4px;
      font-size: 16px;
    }

    .toggle-fix.off {
      background-color: #F44336;
    }

    .fix-status {
      margin-bottom: 20px;
      padding: 10px;
      background-color: #f5f5f5;
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <h1>Font Awesome Icon Test</h1>

  <div class="fix-status">
    <p>Icon Fix Status: <span id="fixStatus">Enabled</span></p>
    <button id="toggleFix" class="toggle-fix">Disable Fix</button>
  </div>

  <div class="test-section">
    <h2>Standard Icons</h2>
    <div class="icon-grid">
      <?php foreach ($icons as $icon => $label): ?>
        <div class="icon-item">
          <div class="icon">
            <i class="fas fa-<?php echo $icon; ?>"></i>
          </div>
          <div class="icon-name"><?php echo $label; ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="test-section">
    <h2>Brand Icons</h2>
    <div class="icon-grid">
      <div class="icon-item">
        <div class="icon">
          <i class="fab fa-facebook-f"></i>
        </div>
        <div class="icon-name">Facebook</div>
      </div>
      <div class="icon-item">
        <div class="icon">
          <i class="fab fa-twitter"></i>
        </div>
        <div class="icon-name">Twitter</div>
      </div>
      <div class="icon-item">
        <div class="icon">
          <i class="fab fa-linkedin-in"></i>
        </div>
        <div class="icon-name">LinkedIn</div>
      </div>
      <div class="icon-item">
        <div class="icon">
          <i class="fab fa-github"></i>
        </div>
        <div class="icon-name">GitHub</div>
      </div>
    </div>
  </div>

  <div class="test-section">
    <h2>Fallback Icons</h2>
    <div class="icon-grid">
      <?php foreach ($icons as $icon => $label): ?>
        <div class="icon-item">
          <div class="icon">
            <?php echo get_icon_fallback($icon); ?>
          </div>
          <div class="icon-name"><?php echo $label; ?> (Fallback)</div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="<?php echo ASSETS_URL; ?>/js/main.js"></script>
  <script>
    // Toggle fix functionality
    document.getElementById('toggleFix').addEventListener('click', function() {
      // Toggle classes on all Font Awesome icons
      document.querySelectorAll('.fas, .fab').forEach(function(icon) {
        icon.classList.toggle('fix-disabled');
      });

      // Update button and status text
      const button = document.getElementById('toggleFix');
      const status = document.getElementById('fixStatus');

      if (button.textContent === 'Disable Fix') {
        button.textContent = 'Enable Fix';
        status.textContent = 'Disabled';
        button.classList.add('off');
      } else {
        button.textContent = 'Disable Fix';
        status.textContent = 'Enabled';
        button.classList.remove('off');
      }
    });
  </script>
</body>

</html>