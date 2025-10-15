<?php
session_start();
include 'db_connect.php';

$error = '';    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
     // check email en wachtwoord en pak gebruiker van database
    if($email && $password){
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }


if ($user && $user['role'] == 'admin' && $password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'] ?? ''; // if you store name
            header('Location: adminpagina.html');

} elseif ($user && $user['role'] == 'customer' && $password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'] ?? ''; // if you store name
            header('Location: ../index.php');

} else {
            $error = 'INLOG FAILED';
            header('Location: ../login.html');

}
}
?>