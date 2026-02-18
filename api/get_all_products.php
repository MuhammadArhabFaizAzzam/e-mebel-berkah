<?php
require_once dirname(__DIR__) . '/db_config.php';

header('Content-Type: application/json');

try {
    // Get all products from database
    $query = "SELECT id, name, price, stock, image, COALESCE(description, '') as description, is_featured FROM products ORDER BY id DESC";
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Generate badge berdasarkan is_featured
        $row['badge'] = $row['is_featured'] ? 'Featured' : 'New';
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>
