<?php
// start sessie
session_start();
include 'db_connect.php';
$error = ''; // // error bericht als er iets mis gaat

// // alleen POST verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // // simpele input check
    if ($email === '' || $password === '') {
        $_SESSION['invalid_info'] = true; // // voor error message op login pagina
        header('Location: ../loginpage.php');
        exit;
    }

    // // haal user op (alleen benodigde velden)
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // // user data als array

    // // controleer wachtwoord met password_verify (hash)
    if ($user && password_verify($password, $user['password'])) {
        // // success: sessie regenereren tegen fixation
        session_regenerate_id(true);

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['logged_in']  = true;

        // // optioneel: rehash als algoritme/cost is veranderd
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
            $upd = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $upd->execute([$newHash, $user['id']]);
        }

        // // role-based redirect
        if ($user['role'] === 'admin') {
            header('Location: adminpagina.php');
            exit;
        } else {
            header('Location: ../index.php');
            exit;
        }
    } else {
        // // inloggen mislukt
        $error = 'INLOG FAILED';
        $_SESSION['invalid_info'] = true; // // voor error message op login pagina
        header('Location: ../loginpage.php');
        exit;
    }
}
?>
