<?php
session_start();
require_once 'db_connect.php';

try {
    // Product toevoegen logica
    if(isset($_POST['add_product'])) {
        try {
            $insert_query = "INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)";
            $stmt_insert = $pdo->prepare($insert_query);
            
            $params = [
                ':name' => $_POST['product_name'],
                ':price' => $_POST['price'],
                ':stock' => $_POST['stock']
            ];
            
            if($stmt_insert->execute($params)) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } catch(PDOException $e) {
            error_log("Fout bij toevoegen product: " . $e->getMessage());
        }
    }

    // Product bijwerken logica
    if(isset($_POST['edit_product'])) {
        try {
            $update_query = "UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id";
            $stmt_update = $pdo->prepare($update_query);
            
            $params = [
                ':id' => $_POST['product_id'],
                ':name' => $_POST['product_name'],
                ':price' => $_POST['price'],
                ':stock' => $_POST['stock']
            ];
            
            if($stmt_update->execute($params)) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } catch(PDOException $e) {
            error_log("Fout bij bijwerken product: " . $e->getMessage());
        }
    }

    // Product verwijderen logica
    if(isset($_GET['delete_product'])) {
        try {
            $delete_query = "DELETE FROM products WHERE id = :id";
            $stmt_delete = $pdo->prepare($delete_query);
            
            if($stmt_delete->execute([':id' => $_GET['delete_product']])) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            }
        } catch(PDOException $e) {
            error_log("Fout bij verwijderen product: " . $e->getMessage());
        }
    }
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
    <link rel="stylesheet" href="../css/adminstyles.css">
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
            <form method="POST" class="card-form">
                <h3>Nieuwe gebruiker toevoegen</h3>
                <input class="input-full" type="text" name="name" placeholder="Naam" required>
                <input class="input-full" type="email" name="email" placeholder="E-mailadres" required>
                <input class="input-full" type="password" name="password" placeholder="Wachtwoord" required>
                <select class="input-full" name="role" required>
                    <option value="customer">customer</option>
                    <option value="admin">Admin</option>
                </select>
                <button class="btn-primary" type="submit" name="add_user">Toevoegen</button>
            </form>

    <!-- Gebruikerslijst -->
        <div class="section-gap">
        <h3>Bestaande gebruikers</h3>
    <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr class="table-head">
                        <th>ID</th>
                        <th>Naam</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acties</th>
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
                                echo "<tr class='data-row'>";
                                echo "<td class='data-td'>" . htmlspecialchars($user['id']) . "</td>";
                                echo "<td class='data-td'>" . htmlspecialchars($user['name']) . "</td>";
                                echo "<td class='data-td'>" . htmlspecialchars($user['email']) . "</td>";
                                echo "<td class='data-td'>" . htmlspecialchars($user['role']) . "</td>";
                                echo "<td class='data-td'>";
                                echo "<button class='action-btn' onclick='openEditModal(" . json_encode($user) . ")'>Bewerken</button>";
                                echo "<a class='delete-link' href='?delete=" . $user['id'] . "' onclick='return confirm(\"Weet je zeker dat je deze gebruiker wilt verwijderen?\")'>Verwijderen</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='no-data-row'><td colspan='5' class='no-data'>Geen gebruikers gevonden</td></tr>";
                        }
                        } catch(PDOException $e) {
                        error_log("Error bij ophalen gebruikers: " . $e->getMessage());
                        echo "<tr class='error-row'><td colspan='5' class='error-cell'>Er is een probleem opgetreden bij het laden van de gebruikers.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bewerk Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-container">
            <h3 class="modal-title">Gebruiker Bewerken</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="user_id" id="edit_user_id">
                <input type="hidden" name="edit_user" value="1">

                <div class="form-row">
                    <label class="form-label">Naam:</label>
                    <input class="input-field" type="text" name="name" id="edit_name" required>
                </div>

                <div class="form-row">
                    <label class="form-label">Email:</label>
                    <input class="input-field" type="email" name="email" id="edit_email" required>
                </div>

                <div class="form-row">
                    <label class="form-label">Nieuw Wachtwoord: (laat leeg om niet te wijzigen)</label>
                    <input class="input-field" type="password" name="password" id="edit_password">
                </div>

                <div class="form-row">
                    <label class="form-label">Rol:</label>
                    <select class="input-field" name="role" id="edit_role" required>
                        <option value="customer">Klant</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-danger" onclick="closeEditModal()">Annuleren</button>
                    <button type="submit" class="btn-primary">Opslaan</button>
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
            <h1>Voorraadbeheer</h1>

            <!-- Product toevoegen -->
            <form method="POST" class="card-form">
                <h3>Nieuw product toevoegen</h3>
                <input class="input-full" type="text" name="product_name" placeholder="Product naam" required>
                <input class="input-full" type="number" name="price" placeholder="Prijs" step="0.01" required>
                <input class="input-full" type="number" name="stock" placeholder="Voorraad" required>
                <button class="btn-primary" type="submit" name="add_product">Product Toevoegen</button>
            </form>

            <!-- Voorraadlijst -->
            <div class="section-gap">
                <h3>Huidige voorraad</h3>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr class="table-head">
                                <th>ID</th>
                                <th>Productnaam</th>
                                <th>Prijs</th>
                                <th>Voorraad</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $query = "SELECT * FROM products ORDER BY id";
                                $stmt = $pdo->prepare($query);
                                $stmt->execute();
                                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if($products) {
                                    foreach($products as $product) {
                                        echo "<tr class='data-row'>";
                                        echo "<td class='data-td'>" . htmlspecialchars($product['id']) . "</td>";
                                        echo "<td class='data-td'>" . htmlspecialchars($product['name']) . "</td>";
                                        echo "<td class='data-td'>€" . htmlspecialchars(number_format($product['price'], 2)) . "</td>";
                                        echo "<td class='data-td'>" . htmlspecialchars($product['stock']) . "</td>";
                                        echo "<td class='data-td'>";
                                        echo "<button class='action-btn' onclick='openEditProductModal(" . json_encode($product) . ")'>Bewerken</button>";
                                        echo "<a class='delete-link' href='?delete_product=" . $product['id'] . "' onclick='return confirm(\"Weet je zeker dat je dit product wilt verwijderen?\")'>Verwijderen</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr class='no-data-row'><td colspan='5' class='no-data'>Geen producten gevonden</td></tr>";
                                }
                            } catch(PDOException $e) {
                                error_log("Error bij ophalen producten: " . $e->getMessage());
                                echo "<tr class='error-row'><td colspan='5' class='error-cell'>Er is een probleem opgetreden bij het laden van de producten.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Product bewerken modal -->
            <div id="editProductModal" class="modal-overlay">
                <div class="modal-container">
                    <h3 class="modal-title">Product Bewerken</h3>
                    <form method="POST" id="editProductForm">
                        <input type="hidden" name="product_id" id="edit_product_id">
                        <input type="hidden" name="edit_product" value="1">

                        <div class="form-row">
                            <label class="form-label">Productnaam:</label>
                            <input class="input-field" type="text" name="product_name" id="edit_product_name" required>
                        </div>

                        <div class="form-row">
                            <label class="form-label">Prijs:</label>
                            <input class="input-field" type="number" name="price" id="edit_price" step="0.01" required>
                        </div>

                        <div class="form-row">
                            <label class="form-label">Voorraad:</label>
                            <input class="input-field" type="number" name="stock" id="edit_stock" required>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-danger" onclick="closeEditProductModal()">Annuleren</button>
                            <button type="submit" class="btn-primary">Opslaan</button>
                        </div>
                    </form>
                </div>
            </div>
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

        // Product modal functies
        function openEditProductModal(product) {
            document.getElementById('editProductModal').style.display = 'block';
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_product_name').value = product.name;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock').value = product.stock;
        }

        function closeEditProductModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        // Sluit product modal als er buiten wordt geklikt
        window.onclick = function(event) {
            const modal = document.getElementById('editProductModal');
            if (event.target == modal) {
                closeEditProductModal();
            }
        }
    </script>

</body>
</html>