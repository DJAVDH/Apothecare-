<?php
session_start();
require_once 'db_connect.php';

try {
    // Gebruiker bijwerken logica
    if(isset($_POST['edit_user'])) {
        try {
            $update_query = "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id";
            $stmt_update = $pdo->prepare($update_query);
            
            $params = [
                ':id' => $_POST['user_id'],
                ':name' => $_POST['name'],
                ':email' => $_POST['email'],
                ':role' => $_POST['role']
            ];

            // Als er een nieuw wachtwoord is opgegeven, update dan ook het wachtwoord
            if(!empty($_POST['password'])) {
                $update_query = "UPDATE users SET name = :name, email = :email, role = :role, password = :password WHERE id = :id";
                $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $stmt_update = $pdo->prepare($update_query);
            if($stmt_update->execute($params)) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } catch(PDOException $e) {
            error_log("Fout bij bijwerken gebruiker: " . $e->getMessage());
        }
    }

    // Query om alle gebruikers op te halen
    $query = "SELECT * FROM users ORDER BY id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Gebruiker toevoegen logica
    if(isset($_POST['add_user'])) {
        try {
            $insert_query = "INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)";
            $stmt_insert = $pdo->prepare($insert_query);
            
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $params = [
                ':name' => $_POST['name'],
                ':email' => $_POST['email'],
                ':password' => $password,
                ':role' => $_POST['role']
            ];
            
            if($stmt_insert->execute($params)) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } catch(PDOException $e) {
            error_log("Fout bij toevoegen gebruiker: " . $e->getMessage());
        }
    }

    // Gebruiker verwijderen logica
    if(isset($_GET['delete'])) {
        try {
            $delete_query = "DELETE FROM users WHERE id = :id";
            $stmt_delete = $pdo->prepare($delete_query);
            
            if($stmt_delete->execute([':id' => $_GET['delete']])) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } catch(PDOException $e) {
            error_log("Fout bij verwijderen gebruiker: " . $e->getMessage());
        }
    }
} catch(PDOException $e) {
    // Stille foutafhandeling - alleen loggen, niet weergeven
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apothecare</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

    <header class="main-header">
    <a href="../index.php" class="logo-link">
        <div class="logo-container">
            <img src="../assets/img/aptoocare.png" alt="Apothecare Logo" class="aptoocare-logo">
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
        <div id="Database" class="content-panel">
            <h1>Gebruikersbeheer</h1>

            <!-- Gebruiker toevoegen -->
            <form method="POST" style="background:#fff; padding:15px; border-radius:8px; width:400px;">
                <h3>Nieuwe gebruiker toevoegen</h3>
                <input type="text" name="name" placeholder="Naam" required style="width:100%; margin-bottom:8px;">
                <input type="email" name="email" placeholder="E-mailadres" required style="width:100%; margin-bottom:8px;">
                <input type="password" name="password" placeholder="Wachtwoord" required style="width:100%; margin-bottom:8px;">
                <select name="role" required style="width:100%; margin-bottom:10px;">
                    <option value="customer">customer</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" name="add_user" style="padding:8px 16px; background:#67a746; border:none; color:white; border-radius:5px;">Toevoegen</button>
            </form>

    <!-- Gebruikerslijst -->
    <div style="margin-top:30px;">
        <h3>Bestaande gebruikers</h3>
        <div style="overflow-x:auto;">
            <table style="width:80%; border-collapse:collapse; background:#fff; margin:20px 0; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <thead>
                    <tr style="background:#2a7a8c; color:white;">
                        <th style="padding:12px 15px; text-align:left;">ID</th>
                        <th style="padding:12px 15px; text-align:left;">Naam</th>
                        <th style="padding:12px 15px; text-align:left;">Email</th>
                        <th style="padding:12px 15px; text-align:left;">Rol</th>
                        <th style="padding:12px 15px; text-align:left;">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = "SELECT * FROM users ORDER BY id";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute();
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if($users) {
                            foreach($users as $user) {
                                echo "<tr style='border-bottom:1px solid #eee;'>";
                                echo "<td style='padding:12px 15px; color:#333;'>" . htmlspecialchars($user['id']) . "</td>";
                                echo "<td style='padding:12px 15px; color:#333;'>" . htmlspecialchars($user['name']) . "</td>";
                                echo "<td style='padding:12px 15px; color:#333;'>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td style='padding:12px 15px; color:#333;'>" . htmlspecialchars($user['role']) . "</td>";
                                echo "<td style='padding:12px 15px;'>";
                                echo "<button onclick='openEditModal(" . json_encode($user) . ")' 
                                      style='color:#2a7a8c; text-decoration:none; padding:5px 10px; border-radius:3px; 
                                      background:#e3f2fd; border:none; cursor:pointer; margin-right:5px; transition:all 0.3s ease;'>
                                      Bewerken</button>";
                                echo "<a href='?delete=" . $user['id'] . "' 
                                      onclick='return confirm(\"Weet je zeker dat je deze gebruiker wilt verwijderen?\")' 
                                      style='color:#ff4444; text-decoration:none; padding:5px 10px; border-radius:3px; 
                                      background:#fff0f0; transition:all 0.3s ease;'>Verwijderen</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:15px;'>Geen gebruikers gevonden</td></tr>";
                        }
                    } catch(PDOException $e) {
                        error_log("Error bij ophalen gebruikers: " . $e->getMessage());
                        echo "<tr><td colspan='5' style='text-align:center; padding:15px; color:#721c24; 
                              background:#f8d7da; border-color:#f5c6cb;'>Er is een probleem opgetreden bij het laden van de gebruikers.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bewerk Modal -->
    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
         background:rgba(0,0,0,0.5); z-index:1000;">
        <div style="position:relative; background:white; width:400px; margin:100px auto; padding:20px; border-radius:8px;">
            <h3 style="color:#333; margin-bottom:20px;">Gebruiker Bewerken</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="user_id" id="edit_user_id">
                <input type="hidden" name="edit_user" value="1">
                
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; color:#333;">Naam:</label>
                    <input type="text" name="name" id="edit_name" required 
                           style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:#333;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; color:#333;">Email:</label>
                    <input type="email" name="email" id="edit_email" required 
                           style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:#333;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; color:#333;">Nieuw Wachtwoord: (laat leeg om niet te wijzigen)</label>
                    <input type="password" name="password" id="edit_password"
                           style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:#333;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; color:#333;">Rol:</label>
                    <select name="role" id="edit_role" required 
                            style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px; color:#333;">
                        <option value="customer">Klant</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" onclick="closeEditModal()" 
                            style="padding:8px 16px; background:#f44336; border:none; color:white; border-radius:4px; cursor:pointer;">
                            Annuleren</button>
                    <button type="submit" 
                            style="padding:8px 16px; background:#67a746; border:none; color:white; border-radius:4px; cursor:pointer;">
                            Opslaan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(user) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_password').value = ''; // Wachtwoord veld leeg laten
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Sluit modal als er buiten wordt geklikt
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
            </div>
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
        // Zorg ervoor dat de eerste tab actief is bij het laden van de pagina
        document.addEventListener('DOMContentLoaded', function() {
            // Maak alle panels onzichtbaar behalve Database
            document.querySelectorAll('.content-panel').forEach(panel => {
                panel.style.display = 'none';
                panel.classList.remove('active');
            });
            // Toon Database panel
            const databasePanel = document.getElementById('Database');
            if (databasePanel) {
                databasePanel.style.display = 'block';
                databasePanel.classList.add('active');
            }
        });

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
            const selectedPanel = document.getElementById(tabName);
            if (selectedPanel) {
                selectedPanel.style.display = 'block';
                selectedPanel.classList.add('active');
            }
            
            // Voeg de 'active' class toe aan de geklikte knop
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