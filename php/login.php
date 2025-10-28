<?php
//start sessie
session_start();
include 'db_connect.php';

$error = ''; //error bericht als er is mis gaat

//als server een post request is dan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); //maak variable van email verwijder spaties in mail
    $password = $_POST['password']; //maak variable van wachtwoord
    if($email && $password){ //als email en wachtwoord zijn ingevuld dan maak sql statement
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]); //voer sql statement uit met email als parameter
    $user = $stmt->fetch(PDO::FETCH_ASSOC); //haal user data op uit database en verander in array
    }


if ($user && $user['role'] == 'admin' && $password == $user['password']) { //check admin inloggen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role']; 
            $_SESSION['logged_in'] = true;
} elseif ($user && $user['role'] == 'customer' && $password == $user['password']) { //check klant inloggen
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['logged_in'] = true;
} else { //als inloggen mislukt
            $error = 'INLOG FAILED';
            $_SESSION['invalid_info'] = true; //dit is voor error message op login pagina

}

if ($_SESSION['user_role'] == 'admin') { //als (role) dan naar (site)
    header('Location: adminpagina.php');
} elseif ($_SESSION['user_role'] == 'customer') {
    header('Location: ../index.php');
} elseif ($_SESSION['invalid_info'] == true) {
    header('Location: ../loginpage.php');
}
}
?>