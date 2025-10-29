<?php
session_start();
include 'db_connect.php';

// // alleen POST verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']   ?? '');
    $password = trim($_POST['password']?? '');
    $name     = trim($_POST['name']    ?? '');
    $role     = 'customer'; // // standaard rol

    // // basisvalidatie
    if ($email === '' || $password === '' || $name === '') {
        $_SESSION['invalid_info'] = true; // // toon een nette melding in je form
        header('Location: ../register.php'); // // pas aan naar jouw registratiepagina
        exit;
    }

    // // optioneel: valideer e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['invalid_info'] = true;
        header('Location: ../register.php');
        exit;
    }

    // // hash wachtwoord (bcrypt default of argon2id als PHP dat heeft)
    // // je kan de cost verhogen: ['cost' => 12]
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // // check of e-mail al bestaat (uniek)
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['invalid_info'] = true; // // e-mail bestaat al
        header('Location: ../register.php');
        exit;
    }

    // // nieuwe gebruiker aanmaken met gehashte password
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hash, $role]);

    header('Location: ../loginpage.php');
    exit;
}
?>
