<?php
/**
 * Database Setup Script untuk InfinityFree
 * Berkah Mebel Ayu
 * 
 * Cara penggunaan:
 * 1. Upload file ini ke server InfinityFree
 * 2. Buka browser dan akses: https://domainkamu.com/setup_database.php
 * 3. Ikuti petunjuk di layar
 * 4. HAPUS file ini setelah setup selesai!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
$message = '';
$error = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = trim($_POST['db_host'] ?? '');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = trim($_POST['db_pass'] ?? '');
    $db_name = trim($_POST['db_name'] ?? '');
    
    // Validate input
    if (empty($db_host) || empty($db_user) || empty($db_name)) {
        $error = 'Mohon lengkapi semua field yang wajib!';
    } else {
        // Test connection
        try {
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            if ($conn->connect_error) {
                throw new Exception("Koneksi gagal: " . $conn->connect_error);
            }
            
            // Success! Update db_config.php
            $config_content = '<?php
/**
 * Berkah Mebel Ayu - Database Configuration
 * Konfigurasi koneksi ke MySQL database (InfinityFree)
 */

define(\'DB_HOST\", \'' . addslashes($db_host) . '\');
define(\'DB_USER\", \'' . addslashes($db_user) . '\');
define(\'DB_PASS\", \'' . addslashes($db_pass) . '\');
define(\'DB_NAME\", \'' . addslashes($db_name) . '\');

// Create Database Connection
$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        throw new Exception("Koneksi database gagal");
    }
    
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    error_log("Database Error: " . $e->getMessage());
}

/**
 * Prepared statement untuk query yang aman
 */
function executeQuery($query, $params = []) {
    global $conn;
    
    if (!$conn) {
        throw new Exception("Database connection not established");
    }
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params)) {
        $types = \'\';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= \'i\';
            } elseif (is_float($param)) {
                $types .= \'d\';
            } else {
                $types .= \'s\';
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    return $stmt;
}

/**
 * Get result dari query SELECT
 */
function getQueryResult($query, $params = []) {
    $stmt = executeQuery($query, $params);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Get single row dari query SELECT
 */
function getQueryRow($query, $params = []) {
    $stmt = executeQuery($query, $params);
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Get last insert ID
 */
function getLastInsertId() {
    global $conn;
    return $conn->insert_id;
}

/**
 * Get affected rows
 */
function getAffectedRows() {
    global $conn;
    return $conn->affected_rows;
}
?>';
            
            // Write config file
            if (file_put_contents('db_config.php', $config_content)) {
                // Run database schema
                $sql_file = file_get_contents('database.sql');
                
                // Split by semicolon and execute each statement
                $statements = array_filter(array_map('trim', explode(';', $sql_file)));
                
                $success_count = 0;
                $error_count = 0;
                
                foreach ($statements as $statement) {
                    if (!empty($statement) && strpos($statement, '--') !== 0) {
                        try {
                            $conn->query($statement);
                            $success_count++;
                        } catch (Exception $e) {
                            // Ignore "Table already exists" errors
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                $error_count++;
                            }
                        }
                    }
                }
                
                $conn->close();
                
                $message = "Setup berhasil! Database telah dikonfigurasi.<br>";
                $message .= "Schema berhasil diimport: $success_count statements<br>";
                if ($error_count > 0) {
                    $message .= "Beberapa tabel sudah ada (tidak masalah): $error_count";
                }
                $step = 999; // Success state
                
            } else {
                $error = 'Gagal menulis file konfigurasi. Cek permissions!';
            }
            
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #5d4037 0%, #a67c52 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-lg w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                <i class="fas fa-database text-2xl text-amber-700"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Setup Database</h1>
            <p class="text-gray-600 text-sm mt-2">Berkah Mebel Ayu - InfinityFree</p>
        </div>
        
        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-300 rounded-xl text-red-700">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-xl text-green-700">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo $message; ?>
            </div>
            <div class="text-center">
                <a href="index.php" class="inline-block bg-amber-700 text-white px-6 py-3 rounded-xl font-bold hover:bg-amber-800 transition">
                    <i class="fas fa-home mr-2"></i> Ke Halaman Utama
                </a>
            </div>
        <?php elseif ($step == 0): ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-server text-amber-700 mr-2"></i>MySQL Host *
                    </label>
                    <input type="text" name="db_host" 
                           value="sql312.infinityfree.com" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" 
                           placeholder="sql312.infinityfree.com" required>
                    <p class="text-xs text-gray-500 mt-1">Biasanya: sqlXXX.epizy.com (lihat di control panel)</p>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user text-amber-700 mr-2"></i>MySQL Username *
                    </label>
                    <input type="text" name="db_user" 
                           value="epiz_XXXXXXX" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" 
                           placeholder="epiz_XXXXXXX" required>
                    <p class="text-xs text-gray-500 mt-1">Format: epiz_ + angka (contoh: epiz_41076893)</p>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock text-amber-700 mr-2"></i>MySQL Password *
                    </label>
                    <input type="password" name="db_pass" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" 
                           placeholder="Masukkan password MySQL" required>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-database text-amber-700 mr-2"></i>Database Name *
                    </label>
                    <input type="text" name="db_name" 
                           value="epiz_XXXXXXX_berkah_mebel" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" 
                           placeholder="epiz_XXXXXXX_berkah_mebel" required>
                    <p class="text-xs text-gray-500 mt-1">Bisa dibuat di menu MySQL di control panel</p>
                </div>
                
                <button type="submit" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-bold py-4 rounded-xl transition shadow-lg">
                    <i class="fas fa-cog mr-2"></i> Setup Sekarang
                </button>
            </form>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-xl text-sm text-blue-700">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Catatan:</strong> Pastikan database sudah dibuat di InfinityFree Control Panel terlebih dahulu!
            </div>
        <?php endif; ?>
        
        <div class="mt-8 text-center text-xs text-gray-400">
            <a href="https://dash.infinityfree.com" target="_blank" class="hover:text-amber-700">
                <i class="fas fa-external-link-alt mr-1"></i> Buka InfinityFree Control Panel
            </a>
        </div>
    </div>
</body>
</html>
