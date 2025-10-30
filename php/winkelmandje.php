<?php
require 'db_connect.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function addItem($productId, int $quantity = 1)
{
    global $pdo;

    $productId = (int) $productId;
    $quantity = max(1, (int) $quantity);

    if ($productId <= 0) {
        return false;
    }

    if (!isset($_SESSION['cart'][$productId])) {
        $stmt = $pdo->prepare('SELECT id, name, price, imglink FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            return false;
        }

        $_SESSION['cart'][$productId] = [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'imglink' => $product['imglink'],
            'quantity' => 0,
        ];
    }

    $_SESSION['cart'][$productId]['quantity'] += $quantity;

    return $_SESSION['cart'][$productId];
}

function removeItem($productId, int $quantity = 1)
{
    $productId = (int) $productId;
    $quantity = max(1, (int) $quantity);

    if ($productId <= 0) {
        return false;
    }

    if (!isset($_SESSION['cart'][$productId])) {
        return false;
    }

    $currentQuantity = (int) ($_SESSION['cart'][$productId]['quantity'] ?? 0);

    if ($currentQuantity <= $quantity) {
        unset($_SESSION['cart'][$productId]);

        return [
            'status' => 'removed',
            'item' => null,
        ];
    }

    $_SESSION['cart'][$productId]['quantity'] = $currentQuantity - $quantity;

    return [
        'status' => 'updated',
        'item' => $_SESSION['cart'][$productId],
    ];
}

function getCartSummary(): array
{
    $totalItems = 0;
    $totalAmount = 0.0;

    foreach ($_SESSION['cart'] as $item) {
        $quantity = (int) ($item['quantity'] ?? 0);
        $price = (float) ($item['price'] ?? 0);
        $totalItems += $quantity;
        $totalAmount += $quantity * $price;
    }

    return [
        'items' => $totalItems,
        'amount' => $totalAmount,
    ];
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function getRequestPayload(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        $rawInput = file_get_contents('php://input');
        $decoded = json_decode($rawInput, true);
        return is_array($decoded) ? $decoded : [];
    }

    return $_POST;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $data = getRequestPayload();
    $action = $data['action'] ?? 'add';
    $productId = $data['productId'] ?? null;
    $quantity = $data['quantity'] ?? 1;

    $response = ['success' => false];

    if ($action === 'add' && $productId !== null) {
        $item = addItem($productId, $quantity);
        if ($item !== false) {
            $summary = getCartSummary();
            $response = [
                'success' => true,
                'item' => $item,
                'cart' => array_values($_SESSION['cart']),
                'total_items' => $summary['items'],
                'total_amount' => $summary['amount'],
            ];
        }
    } elseif ($action === 'remove' && $productId !== null) {
        $removal = removeItem($productId, $quantity);
        if ($removal !== false) {
            $summary = getCartSummary();
            $response = [
                'success' => true,
                'status' => $removal['status'],
                'item' => $removal['item'],
                'removed_id' => $removal['status'] === 'removed' ? $productId : null,
                'cart' => array_values($_SESSION['cart']),
                'total_items' => $summary['items'],
                'total_amount' => $summary['amount'],
            ];
        }
    } elseif ($action === 'clear') {
        clearCart();
        $summary = getCartSummary();
        $response = [
            'success' => true,
            'status' => 'cleared',
            'cart' => array_values($_SESSION['cart']),
            'total_items' => $summary['items'],
            'total_amount' => $summary['amount'],
        ];
    } else {
        $response['error'] = 'Unsupported action or missing productId';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>