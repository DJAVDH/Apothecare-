<?php
session_start();
require_once 'php/db_connect.php';

$errors = [];
$successMessage = '';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="css/style.css">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <header class="main-header">
        <a href="index.php" class="logo-link">
            <div class="logo-container">
                <img src="assets/img/aptoocare.png" alt="Apothecare Logo" class="aptoocare-logo">
                <div class="logo-text">Apothecare</div>
            </div>
        </a>
    </header>
    <main class="main-content">
        <div class="login-container enhanced-login" style="text-align: center;">
            <h2>Registreren</h2>
            <form method="POST" class="login-form" action="php/newaccount.php">
                <label for="name">Naam</label>
                <input type="text" id="name" name="name" placeholder="Gebruikersnaam" required>

                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" placeholder="naam@voorbeeld.nl" required>

                <label for="password">Wachtwoord</label>
                <input type="password" id="password" name="password" placeholder="Wachtwoord" required>

                <label for="confirm_password">Bevestig wachtwoord</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Herhaal wachtwoord" required>

                <div class="form-buttons" style="margin-top: 15px;">
                    <button type="submit">Aanmaken</button>
                    <button type="button" onclick="window.location.href='loginpage.php';">Terug</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>