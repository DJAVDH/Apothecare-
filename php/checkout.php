<?php
require 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $_SESSION['user_id'];
    $user_id = $_SESSION['user_id'];
    $streetname = $_POST['streetname'];
    $streetnumber = $_POST['streetnumber'];
    $city = $_POST['city'];
    $postalcode = $_POST['postalcode'];
    $total = $_POST['total'];
    $status = 'Nieuw';

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, streetname, streetnumber, city, postalcode, total, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $streetname, $streetnumber, $city, $postalcode, $total, $status]);

    $order_id = $pdo->lastInsertId();

    foreach ($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    }

    $_SESSION['cart'] = [];
    header('Location: ../winkelmandjepage.php');
    exit;
}
?>