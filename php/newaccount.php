<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(string: $_POST['email']);
    $password = trim(string: $_POST['password']);
    $name = trim(string: $_POST['name']);
    $role = 'customer';
}

if ($email) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $role]);
    header('Location: ../loginpage.php');
    exit;
}
?>