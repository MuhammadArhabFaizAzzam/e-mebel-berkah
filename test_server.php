<?php
/**
 * Server Test Script untuk InfinityFree
 * Berkah Mebel Ayu
 * 
 * Cara penggunaan:
 * 1. Upload ke server
 * 2. Akses: https://domainkamu.com/test_server.php
 * 3. Hapus setelah digunakan!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [];

// Test 1: PHP Version
$results['php_version'] = [
    'name' => 'PHP Version',
    'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'value' => PHP_VERSION,
    'message' => PHP_VERSION >= '7.4.0' ? 'OK - PHP version compatible' : 'Warning - PHP too old'
];

// Test 2: MySQLi Extension
$results['mysqli'] = [
    'name' => 'MySQLi Extension',
    'status' => extension_loaded('mysqli'),
    'value' => extension_loaded('mysqli') ? 'Enabled' : 'Disabled',
    'message' => extension_loaded('mysqli') ? 'MySQL support available' : 'MySQLi extension required!'
];

// Test 3: JSON Extension
$results['json'] = [
    'name' => 'JSON Extension',
    'status' => extension_loaded('json'),
    'value' => 'Enabled',
    'message' => 'JSON support available'
];

// Test 4: Session
$results['session'] = [
    'name' => 'Session Support',
    'status' => session_status() === PHP_SESSION_ACTIVE,
    'value' => session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive',
    'message' => 'Session working correctly'
];

// Test 5: File Upload
$results['upload'] = [
    'name' => 'File Upload',
    'status' => ini_get('file_uploads') == 1,
    'value' => ini_get('upload_max_filesize'),
    'message' => 'File upload enabled'
];

// Test 6: Write Permission (uploads folder)
$results['write_perm'] = [
    'name' => 'Write Permission',
    'status' => is_writable('uploads') || is_writable('.'),
    'value' => is_writable('uploads') ? 'uploads/ writable' : (is_writable('.') ? 'Root writable' : 'Not writable'),
    'message' => is_writable('uploads') || is_writable('.') ? 'Can write files' : 'Cannot write - check permissions'
];

// Test 7: Database Connection (if configured)
$db_connected = false;
$db_message = 'Database not configured yet';
if (file_exists('db_config.php')) {
    require_once 'db_config.php';
    if (isset($conn) && $conn && !($conn->connect_error)) {
        $db_connected = true;
        $db_message = 'Connected to database successfully!';
    } else {
        $db_message = 'Database connection failed - check db_config.php';
    }
}

$results['database'] = [
    'name' => 'Database Connection',
    'status' => $db_connected,
    'value' => $db_connected ? 'Connected' : 'Not Connected',
    'message' => $db_message
];

// Test 8: Required Files
$required_files = ['index.php', 'config.php', 'db_config.php', 'login.php', 'register.php'];
$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        $missing_files[] = $file;
    }
}

$results['files'] = [
    'name' => 'Required Files',
    'status' => empty($missing_files),
    'value' => empty($missing_files) ? 'All present' : 'Missing: ' . implode(', ', $missing_files),
    'message' => empty($missing_files) ? 'All required files exist' : 'Some files are missing!'
];

// Test 9: .htaccess
$results['htaccess'] = [
    'name' => '.htaccess',
    'status' => file_exists('.htaccess'),
    'value' => file_exists('.htaccess') ? 'Present' : 'Missing',
    'message' => file_exists('.htaccess') ? 'Configuration file present' : 'Missing - create .htaccess'
];

// Test 10: Memory Limit
$results['memory'] = [
    'name' => 'Memory Limit',
    'status' => true,
    'value' => ini_get('memory_limit'),
    'message' => 'Memory limit: ' . ini_get('memory_limit')
];

// Calculate overall status
$all_passed = true;
foreach ($results as $result) {
    if (!$result['status']) {
        $all_passed = false;
        break;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Test - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #5d4037 0%, #a67c52 100%); min-height: 100vh; }
    </style>
</head>
<body class="p-4 md:p-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 <?php echo $all_passed ? 'bg-green-100' : 'bg-red-100'; ?> rounded-full mb-4">
                <i class="fas <?php echo $all_passed ? 'fa-check' : 'fa-exclamation-triangle'; ?> text-3xl <?php echo $all_passed ? 'text-green-600' : 'text-red-600'; ?>"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Server Test Results</h1>
            <p class="text-amber-200 mt-2">Berkah Mebel Ayu - InfinityFree</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <?php foreach ($results as $key => $result): ?>
            <div class="p-4 border-b border-gray-100 flex items-center justify-between <?php echo $result['status'] ? '' : 'bg-red-50'; ?>">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center <?php echo $result['status'] ? 'bg-green-100' : 'bg-red-100'; ?>">
                        <i class="fas <?php echo $result['status'] ? 'fa-check' : 'fa-times'; ?> <?php echo $result['status'] ? 'text-green-600' : 'text-red-600'; ?>"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800"><?php echo $result['name']; ?></h3>
                        <p class="text-sm text-gray-500"><?php echo $result['message']; ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-semibold <?php echo $result['status'] ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo $result['value']; ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-8 text-center">
            <?php if ($all_passed): ?>
                <div class="bg-green-500/20 border border-green-400/30 rounded-xl p-4 text-green-200">
                    <i class="fas fa-thumbs-up mr-2"></i>
                    Semua test berhasil! Website siap di-deploy.
                </div>
            <?php else: ?>
                <div class="bg-red-500/20 border border-red-400/30 rounded-xl p-4 text-red-200">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Beberapa test gagal. Silakan periksa konfigurasi.
                </div>
            <?php endif; ?>
            
            <a href="index.php" class="inline-block mt-4 bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-bold transition">
                <i class="fas fa-home mr-2"></i> Ke Halaman Utama
            </a>
        </div>
        
        <div class="mt-8 text-center text-white/50 text-sm">
            <p>Hapus file ini setelah testing selesai!</p>
        </div>
    </div>
</body>
</html>
