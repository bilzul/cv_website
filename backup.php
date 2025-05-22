<?php

/**
 * CV Website Database Backup Script
 * 
 * This script creates a backup of your MySQL database and website files.
 * For security, delete this file after use or restrict access with .htaccess.
 */

// Start session and include configuration
session_start();
include_once "config/config.php";
include_once "config/database.php";
include_once "includes/functions.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. You must be logged in as an administrator.");
}

// Check if a specific backup type is requested
$backup_type = $_GET['type'] ?? 'all';
$valid_types = ['database', 'files', 'all'];

if (!in_array($backup_type, $valid_types)) {
    $backup_type = 'all';
}

// Database connection
$db = new Database();
$dbInfo = [
    'host' => $db->host ?? 'localhost',
    'name' => $db->db_name ?? 'cv_db',
    'user' => $db->username ?? 'root',
    'pass' => $db->password ?? ''
];

// Create backup directory if it doesn't exist
$backup_dir = 'backups';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Generate filename with date
$date = date("Y-m-d-H-i-s");
$result = ['success' => false, 'messages' => []];

// Database backup
if ($backup_type === 'database' || $backup_type === 'all') {
    $backup_file = "{$backup_dir}/{$dbInfo['name']}-{$date}.sql";

    // Command to export database
    $command = sprintf(
        'mysqldump --opt -h %s -u %s --password=%s %s > %s',
        escapeshellarg($dbInfo['host']),
        escapeshellarg($dbInfo['user']),
        escapeshellarg($dbInfo['pass']),
        escapeshellarg($dbInfo['name']),
        escapeshellarg($backup_file)
    );

    // Execute command
    system($command, $return_var);

    if ($return_var === 0) {
        $result['messages'][] = "Database backup created: " . basename($backup_file);
        $result['success'] = true;
    } else {
        $result['messages'][] = "Database backup failed!";
    }
}

// Files backup
if ($backup_type === 'files' || $backup_type === 'all') {
    $exclude_dirs = [
        'backups',
        'vendor',
        'node_modules'
    ];

    $files_backup = "{$backup_dir}/files-{$date}.zip";

    // Create zip archive with website files
    $zip = new ZipArchive();
    if ($zip->open($files_backup, ZipArchive::CREATE) === TRUE) {
        // Get all files recursively
        $rootPath = realpath('./');
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories and excluded paths
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Check if file is in an excluded directory
                $excluded = false;
                foreach ($exclude_dirs as $dir) {
                    if (strpos($relativePath, $dir . '/') === 0 || $relativePath === $dir) {
                        $excluded = true;
                        break;
                    }
                }

                if (!$excluded) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
        $result['messages'][] = "Files backup created: " . basename($files_backup);
        $result['success'] = true;
    } else {
        $result['messages'][] = "Files backup failed!";
    }
}

// Output HTML
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Website Backup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
        }

        .backup-list {
            margin-top: 20px;
        }

        .message-success {
            color: #28a745;
        }

        .message-error {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-server"></i> CV Website Backup</h1>

        <div class="card">
            <div class="card-header">
                <h5>Backup Results</h5>
            </div>
            <div class="card-body">
                <div class="alert <?php echo $result['success'] ? 'alert-success' : 'alert-danger'; ?>">
                    <h4>
                        <?php echo $result['success'] ?
                            '<i class="fas fa-check-circle"></i> Backup Completed Successfully' :
                            '<i class="fas fa-exclamation-triangle"></i> Backup Failed'; ?>
                    </h4>
                    <ul>
                        <?php foreach ($result['messages'] as $message): ?>
                            <li><?php echo htmlspecialchars($message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <a href="index.php?page=dashboard_admin" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>

                <div class="btn-group">
                    <a href="backup.php?type=database" class="btn btn-outline-primary">
                        <i class="fas fa-database"></i> Backup Database Only
                    </a>
                    <a href="backup.php?type=files" class="btn btn-outline-primary">
                        <i class="fas fa-file-archive"></i> Backup Files Only
                    </a>
                    <a href="backup.php?type=all" class="btn btn-outline-primary">
                        <i class="fas fa-server"></i> Backup All
                    </a>
                </div>
            </div>
        </div>

        <div class="card backup-list">
            <div class="card-header">
                <h5>Available Backups</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $backups = glob("$backup_dir/*");
                        usort($backups, function ($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });

                        foreach ($backups as $backup) {
                            $filename = basename($backup);
                            $filesize = round(filesize($backup) / 1024 / 1024, 2); // Convert to MB
                            $filetime = date("Y-m-d H:i:s", filemtime($backup));
                            $type = strpos($filename, 'files') === 0 ? 'Files' : 'Database';

                            echo "<tr>";
                            echo "<td>$filename</td>";
                            echo "<td>$type</td>";
                            echo "<td>$filetime</td>";
                            echo "<td>{$filesize} MB</td>";
                            echo "<td><a href='$backup_dir/$filename' class='btn btn-sm btn-primary'><i class='fas fa-download'></i> Download</a></td>";
                            echo "</tr>";
                        }

                        if (count($backups) === 0) {
                            echo "<tr><td colspan='5' class='text-center'>No backups available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> For security reasons, consider downloading your backups and then deleting them from the server.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
$result['messages'][] = "Database backup created: " . basename($backup_file);
$result['success'] = true;
} else {
$result['messages'][] = "Database backup failed!";
}
}

// Files backup
if ($backup_type === 'files' || $backup_type === 'all') {
$exclude_dirs = [
'backups',
'vendor',
'node_modules'
];

$excluded_dirs_str = '';
foreach ($exclude_dirs as $dir) {
$excluded_dirs_str .= " --exclude='$dir'";
}

$files_backup = "{$backup_dir}/files-{$date}.zip";

// Create zip archive with website files
$zip = new ZipArchive();
if ($zip->open($files_backup, ZipArchive::CREATE) === TRUE) {
// Get all files recursively
$rootPath = realpath('./');
$files = new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($rootPath),
RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
// Skip directories and excluded paths
if (!$file->isDir()) {
$filePath = $file->getRealPath();
$relativePath = substr($filePath, strlen($rootPath) + 1);

// Check if file is in an excluded directory
$excluded = false;
foreach ($exclude_dirs as $dir) {
if (strpos($relativePath, $dir . '/') === 0 || $relativePath === $dir) {
$excluded = true;
break;
}
}

if (!$excluded) {
$zip->addFile($filePath, $relativePath);
}
}
}

$zip->close();
$result['messages'][] = "Files backup created: " . basename($files_backup);
$result['success'] = true;
} else {
$result['messages'][] = "Files backup failed!";
}
}
// Backup successful, offer the file for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($backup_file));
readfile($backup_file);

// Delete the file from server after download
unlink($backup_file);
exit;
} else {
echo "Database backup failed. Please ensure that the mysql command-line tools are installed and your database configuration is correct.";

// Alternative manual backup instructions
echo "<p>Alternatively, you can perform a backup through phpMyAdmin:</p>";
echo "<ol>";
    echo "<li>Open phpMyAdmin and select your database</li>";
    echo "<li>Click the 'Export' tab</li>";
    echo "<li>Choose 'Quick' export method with SQL format</li>";
    echo "<li>Click 'Go' to download the SQL backup file</li>";
    echo "</ol>";
}
?>