<?php

/**
 * Script to generate SRI hashes for CSS and JavaScript files
 * Run this script when you update assets to get new hashes
 */

// Files to generate SRI for
$files = [
  __DIR__ . '/assets/js/main.js',
  __DIR__ . '/assets/css/style.css',
];

// Generate SRI hash
function generateSRI($file)
{
  if (!file_exists($file)) {
    return ['error' => "File $file does not exist."];
  }

  $content = file_get_contents($file);
  $hash = base64_encode(hash('sha384', $content, true));

  return [
    'file' => basename($file),
    'hash' => "sha384-$hash"
  ];
}

// Generate SRI for each file
echo "SRI Hashes for Local Files:\n";
echo "==========================\n";

foreach ($files as $file) {
  $result = generateSRI($file);

  if (isset($result['error'])) {
    echo "{$result['error']}\n";
    continue;
  }

  echo "{$result['file']}: integrity=\"{$result['hash']}\"\n";
}

echo "\n";
echo "Add these integrity attributes to your script and link tags.\n";
echo "Example: <script src=\"assets/js/main.js\" integrity=\"{hash}\" crossorigin=\"anonymous\"></script>\n";
