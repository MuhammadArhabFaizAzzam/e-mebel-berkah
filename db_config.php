<?php
/**
 * Berkah Mebel Ayu - Database Configuration
 * Konfigurasi koneksi ke MySQL database (InfinityFree)
 * 
 * IMPORTANT: Update nilai di bawah ini dengan data dari InfinityFree Control Panel
 * Atau gunakan setup_database.php untuk konfigurasi otomatis
 */

// ==================== KONFIGURASI DATABASE ====================
// Isi sesuai dengan data dari InfinityFree Control Panel
// Kamu bisa edit manual atau gunakan setup_database.php

define('DB_HOST', 'sql312.infinityfree.com');    // Contoh: sql312.infinityfree.com
define('DB_USER', 'epiz_41076893');              // Username InfinityFree (format: epiz_XXXXXXX)
define('DB_PASS', 'YOUR_PASSWORD_HERE');         // Password MySQL kamu
define('DB_NAME', 'epiz_41076893_berkah_mebel'); // Nama database (format: epiz_XXXXXXX_namadb)

// ==================== KONEKSI DATABASE ====================
$conn = null;
$db_error = false;
$db_error_message = '';

try {
    // Mulai koneksi
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Cek koneksi
    if ($conn->connect_error) {
        throw new Exception("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Set charset UTF-8
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    $db_error = true;
    $db_error_message = $e->getMessage();
    error_log("Database Connection Error: " . $e->getMessage());
}

// ==================== FUNGSI-FUNGSI DATABASE ====================

/**
 * Eksekusi query dengan prepared statement (AMAN dari SQL Injection)
 */
function executeQuery($query, $params = []) {
    global $conn;
    
    if (!$conn || !($conn instanceof mysqli)) {
        throw new Exception("Koneksi database tidak tersedia");
    }
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_null($param)) {
                $types .= 's';
                $values[] = null;
                continue;
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        if (!empty($values)) {
            $stmt->bind_param($types, ...$values);
        }
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    return $stmt;
}

/**
 * Get semua hasil query SELECT sebagai array asosiatif
 */
function getQueryResult($query, $params = []) {
    try {
        $stmt = executeQuery($query, $params);
        $result = $stmt->get_result();
        
        if ($result === false) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Query Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get satu baris dari query SELECT
 */
function getQueryRow($query, $params = []) {
    try {
        $stmt = executeQuery($query, $params);
        $result = $stmt->get_result();
        
        if ($result === false) {
            return null;
        }
        
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Query Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Get last insert ID
 */
function getLastInsertId() {
    global $conn;
    if ($conn && !($conn instanceof mysqli_sql_exception)) {
        return $conn->insert_id;
    }
    return null;
}

/**
 * Get affected rows
 */
function getAffectedRows() {
    global $conn;
    if ($conn && !($conn instanceof mysqli_sql_exception)) {
        return $conn->affected_rows;
    }
    return 0;
}

/**
 * Cek apakah koneksi database aktif
 */
function isDatabaseConnected() {
    global $conn;
    return ($conn !== null && !$conn->connect_error && $conn instanceof mysqli);
}

/**
 * Tutup koneksi database
 */
function closeDatabase() {
    global $conn;
    if ($conn && !($conn instanceof mysqli_sql_exception)) {
        $conn->close();
    }
}

// Auto-close connection when script ends
register_shutdown_function('closeDatabase');
?>
