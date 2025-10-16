<?php
session_start();
include 'db_connect.php';

$error = '';    
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
     // check email en wachtwoord en pak gebruiker van database
    if($email && $password){
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }


if ($user && $user['role'] == 'admin' && $password == $user['password']) { // Admin inloggen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role']; 
            $_SESSION['logged_in'] = true;
} elseif ($user && $user['role'] == 'customer' && $password == $user['password']) { //Klant inloggen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;
} else {
            $error = 'INLOG FAILED';
            $_SESSION['invalid_info'] = true;

}

if ($_SESSION['user_role'] == 'admin') {
    header('Location: adminpagina.php');
} elseif ($_SESSION['user_role'] == 'customer') {
    header('Location: ../index.php');
} elseif ($_SESSION['invalid_info'] == true) {
    header('Location: ../loginpage.php');
}
}
?>