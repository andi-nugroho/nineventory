<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Backup Database Function
 * Membuat backup database ke file SQL dengan timestamp
 * 
 * @param string $backupName Nama prefix untuk file backup
 * @param string $backupDir Direktori untuk menyimpan backup
 * @return array Result dengan status dan informasi file
 */
function backupDatabase($backupName = 'backup', $backupDir = null) {
    // Load .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    // Get DB config from .env
    $dbHost = $_ENV['DB_HOST'] ?? 'localhost';
    $dbUser = $_ENV['DB_USER'] ?? 'root';
    $dbPass = $_ENV['DB_PASS'] ?? '';
    $dbName = $_ENV['DB_NAME'] ?? 'nineventory';
    $dbPort = $_ENV['DB_PORT'] ?? '3306';
    
    // Set default backup directory
    if ($backupDir === null) {
        $backupDir = __DIR__ . '/../backups';
    }
    
    // Create backup directory if not exists
    if (!is_dir($backupDir)) {
        if (!mkdir($backupDir, 0755, true)) {
            return [
                'success' => false,
                'error' => 'Gagal membuat direktori backup'
            ];
        }
    }
    
    // Create filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $filename = $backupName . '_' . $timestamp . '.sql';
    $filepath = $backupDir . '/' . $filename;
    
    // Path to mysqldump (XAMPP)
    $mysqldumpPath = 'c:\\xampp\\mysql\\bin\\mysqldump.exe';
    
    // Build mysqldump command
    if (empty($dbPass)) {
        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s %s > "%s" 2>&1',
            $mysqldumpPath,
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbName),
            $filepath
        );
    } else {
        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s --password=%s %s > "%s" 2>&1',
            $mysqldumpPath,
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            $filepath
        );
    }
    
    // Execute command
    exec($command, $output, $returnCode);
    
    // Check if backup was successful
    if ($returnCode === 0 && file_exists($filepath) && filesize($filepath) > 0) {
        return [
            'success' => true,
            'message' => 'Database telah berhasil dibackup',
            'file' => $filename,
            'filepath' => $filepath,
            'size' => formatFileSize(filesize($filepath)),
            'timestamp' => $timestamp
        ];
    }
    
    // Cleanup failed backup file
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    return [
        'success' => false,
        'error' => 'Backup gagal: ' . implode("\n", $output)
    ];
}

/**
 * Get list of backup files
 * 
 * @param string $backupDir Direktori backup
 * @return array List of backup files
 */
function getBackupFiles($backupDir = null) {
    if ($backupDir === null) {
        $backupDir = __DIR__ . '/../backups';
    }
    
    if (!is_dir($backupDir)) {
        return [];
    }
    
    $files = [];
    $sqlFiles = glob($backupDir . '/*.sql');
    
    foreach ($sqlFiles as $file) {
        $files[] = [
            'name' => basename($file),
            'path' => $file,
            'size' => formatFileSize(filesize($file)),
            'date' => date('Y-m-d H:i:s', filemtime($file))
        ];
    }
    
    // Sort by date descending (newest first)
    usort($files, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $files;
}

/**
 * Delete backup file
 * 
 * @param string $filename Nama file backup
 * @param string $backupDir Direktori backup
 * @return array Result
 */
function deleteBackupFile($filename, $backupDir = null) {
    if ($backupDir === null) {
        $backupDir = __DIR__ . '/../backups';
    }
    
    $filepath = $backupDir . '/' . basename($filename);
    
    if (!file_exists($filepath)) {
        return [
            'success' => false,
            'error' => 'File tidak ditemukan'
        ];
    }
    
    if (unlink($filepath)) {
        return [
            'success' => true,
            'message' => 'File backup berhasil dihapus'
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Gagal menghapus file backup'
    ];
}

/**
 * Download backup file
 * 
 * @param string $filename Nama file backup
 * @param string $backupDir Direktori backup
 */
function downloadBackupFile($filename, $backupDir = null) {
    if ($backupDir === null) {
        $backupDir = __DIR__ . '/../backups';
    }
    
    $filepath = $backupDir . '/' . basename($filename);
    
    if (!file_exists($filepath)) {
        http_response_code(404);
        echo 'File tidak ditemukan';
        return;
    }
    
    // Send headers for download
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, must-revalidate');
    
    // Output file
    readfile($filepath);
    exit;
}

/**
 * Format file size to human readable
 * 
 * @param int $bytes Size in bytes
 * @return string Formatted size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}
?>
