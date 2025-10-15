<?php
session_start();
include 'db_connect.php';

// check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Gebruiker verwijderen
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: adminpagina.php");
    exit();
}

// Gebruiker toevoegen
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
    header("Location: adminpagina.php");
    exit();
}

// Lijst met gebruikers ophalen
$result = $conn->query("SELECT * FROM users");
?>



<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apothecare</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/adminstyles.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="main-header">
    <a href="index.html" class="logo-link">
        <div class="logo-container">
            <img src="assets/img/aptoocare.png" alt="Apothecare Logo" class="aptoocare-logo">
            <div class="logo-text">Apothecare</div>
        </div>
    </a>    
        <div class="header-right">
            <span class="cart-icon">🛒</span>
            <span class="profile-circle" id="profileCircle" tabindex="0">
                <span class="profile-icon-head"></span>
                <span class="profile-icon-body"></span>
            </span>
            <div class="profile-menu" id="profileMenu">
                <ul>
                    <li><a href="#">Profiel</a></li>
                    <li><a href="#">Instellingen</a></li>
                    <li><a href="#">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </header>

    <nav class="nav-bar">
        <div class="nav-tabs">
            <button class="tab-button active" onclick="openTab('Database')">Database</button>
            <button class="tab-button" onclick="openTab('Voorraad')">Voorraad</button>
            <button class="tab-button" onclick="openTab('Bestellingen')">Bestellingen</button>
            <button class="tab-button" onclick="openTab('Onbekend')">???</button>
        </div>
    </nav>

    <main class="content-area">
        <div id="Database" class="content-panel active">
    <h1>Gebruikersbeheer</h1>

    <!-- Gebruiker toevoegen -->
    <form method="POST" style="background:#fff; padding:15px; border-radius:8px; width:400px;">
        <h3>Nieuwe gebruiker toevoegen</h3>
        <input type="text" name="name" placeholder="Naam" required style="width:100%; margin-bottom:8px;">
        <input type="email" name="email" placeholder="E-mailadres" required style="width:100%; margin-bottom:8px;">
        <input type="password" name="password" placeholder="Wachtwoord" required style="width:100%; margin-bottom:8px;">
        <select name="role" required style="width:100%; margin-bottom:10px;">
            <option value="user">Gebruiker</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit" name="add_user" style="padding:8px 16px; background:#67a746; border:none; color:white; border-radius:5px;">Toevoegen</button>
    </form>

    <!-- Gebruikerslijst -->
    <h3 style="margin-top:30px;">Bestaande gebruikers</h3>
    <table class="user-table" style="width:80%; border-collapse:collapse; background:#fff;">

        <tr style="background:#2a7a8c; color:white;">
            <th style="padding:10px;">ID</th>
            <th>Naam</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acties</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td style="padding:10px; text-align:center;"><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td><a href="?delete=<?php echo $row['id']; ?>" style="color:red; font-weight:bold;">Verwijderen</a></td>
        </tr>
        <?php } ?>
    </table>
</div>

        <div id="Database" class="content-panel active">
            <h1>Database</h1>
            <p>Hier komt de inhoud voor de database. U kunt hier patiëntgegevens, medicatie-informatie en artsen beheren.</p>
        </div>

        <div id="Voorraad" class="content-panel">
            <h1>Voorraad</h1>
            <p>Hier kunt u de actuele voorraad van medicijnen en andere producten bekijken en beheren.</p>
        </div>

        <div id="Bestellingen" class="content-panel">
            <h1>Bestellingen</h1>
            <p>Overzicht van openstaande, verwerkte en verzonden bestellingen.</p>
        </div>
        
        <div id="Onbekend" class="content-panel">
            <h1>Onbekende Tab</h1>
            <p>Deze sectie is nog niet gedefinieerd.</p>
        </div>
    </main>

    <script>
        function openTab(tabName) {
            // Verberg alle content-panelen
            const contentPanels = document.querySelectorAll('.content-panel');
            contentPanels.forEach(panel => {
                panel.style.display = 'none';
                panel.classList.remove('active');
            });

            // Haal de 'active' class van alle tab-knoppen af
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            // Toon het specifieke content-paneel
            document.getElementById(tabName).style.display = 'block';
            document.getElementById(tabName).classList.add('active');
            
            // Voeg de 'active' class toe aan de geklikte knop
            // We zoeken de knop die de functie aanroept met de juiste tabName
            event.currentTarget.classList.add('active');
        }

        // Profiel menu functionaliteit
        const profileCircle = document.getElementById('profileCircle');
        const profileMenu = document.getElementById('profileMenu');
        document.addEventListener('click', function(e) {
            if (profileCircle.contains(e.target)) {
                profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
            } else {
                profileMenu.style.display = 'none';
            }
        });
        profileCircle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
            }
        });
    </script>

</body>
</html>