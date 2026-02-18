<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once 'db_config.php';
require_once 'config.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_quantity':
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);

        if ($product_id > 0 && $quantity > 0) {
            // Check stock availability
            $product = getQueryRow("SELECT stock FROM products WHERE id = ?", [$product_id]);
            if ($product && $quantity <= $product['stock']) {
                $_SESSION['cart'][$product_id] = $quantity;
                echo json_encode(['success' => true, 'message' => 'Quantity updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        }
        break;

    case 'remove_item':
        $product_id = intval($_POST['product_id'] ?? 0);

        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            echo json_encode(['success' => true, 'message' => 'Item removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
        }
        break;

    case 'apply_coupon':
        $coupon_code = trim($_POST['coupon_code'] ?? '');

        // Simple coupon validation (you can expand this with database)
        $valid_coupons = [
            'DISCOUNT10' => ['type' => 'percentage', 'value' => 10, 'min_order' => 500000],
            'DISCOUNT20' => ['type' => 'percentage', 'value' => 20, 'min_order' => 1000000],
            'FLASH50' => ['type' => 'fixed', 'value' => 50000, 'min_order' => 2000000],
        ];

        if (isset($valid_coupons[$coupon_code])) {
            $coupon = $valid_coupons[$coupon_code];

            // Calculate current subtotal
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product = getQueryRow("SELECT price, discount_price FROM products WHERE id = ?", [$product_id]);
                if ($product) {
                    $price = $product['discount_price'] ?? $product['price'];
                    $subtotal += $price * $quantity;
                }
            }

            if ($subtotal >= $coupon['min_order']) {
                $_SESSION['applied_coupon'] = $coupon_code;
                echo json_encode(['success' => true, 'message' => 'Coupon applied successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Minimum order not met for this coupon']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid coupon code']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
