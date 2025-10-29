<?php
//haal database connectie en login info 
include 'php/db_connect.php';
include 'php/login.php';
//check als user is ingelogd
$isLoggedIn = isset($_SESSION['user_id']);
//haal producten uit database
try {
    $productStmt = $pdo->query("SELECT id, name, description, price, imglink FROM products ORDER BY id DESC LIMIT 8");
    $products = $productStmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    error_log('Product fetch failed: ' . $e->getMessage());
}
$productsAvailable = !empty($products);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apothecare - Uw Online Apotheek</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="main-body">
    <!--Header-->
    <header class="main-header">
    <a href="index.php" class="logo-link">
        <div class="logo-container">
            <img src="assets/img/aptoocare.png" alt="Apothecare Logo" class="aptoocare-logo">
            <div class="logo-text">Apothecare</div>
        </div>
    </a> 
        <nav class="main-nav">
            <a href="php/adminpagina.php" hidden id="admin-button" class="nav-button tertiary">Admin</a>
            <a href="#" class="nav-button secondary">Winkelmandje</a>
            <a id="LoginButton" href="loginpage.php" class="nav-button primary">Log In</a>
        </nav>
    </header>
    <!--Main-->
    <main class="content-area">
        <div class="search-section">
            <h1 id="welkomMessage" class="welkom-text">WELKOM</h1>
            <div class="search-container">
                <div class="search-bar">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" placeholder="Zoek voor medicijnen, gezondheidsproducten..." class="search-input">
                </div>
            </div>
        </div>
    <!--Producten-->
    <section id="products" class="products-section">
        <h2 class="products-title">Onze Producten</h2>
        <div class="products-grid">
            <?php if ($productsAvailable): ?>
                <?php foreach ($products as $product): ?>
                    <?php
                        $imageLink = $product['imglink'] ?? '';
                        $imageSrc = $imageLink !== '' ? $imageLink : 'assets/img/pillen.jpg';
                    ?>
                    <div class="product-card"
                        data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        data-description="<?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        data-price="<?php echo htmlspecialchars(number_format((float) ($product['price'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>"
                        data-image="<?php echo htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8'); ?>"
                        data-detail-url="productpage.php?id=<?php echo urlencode($product['id']); ?>">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p>
                                <?php
                                $description = $product['description'] ?? '';
                                $excerpt = mb_strimwidth($description, 0, 100, '...');
                                echo htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8');
                                ?>
                            </p>
                            <img src="<?php echo htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="product-card-image">
                            <div class="product-card-footer">
                                <span class="product-price">€ <?php echo htmlspecialchars(number_format((float) ($product['price'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></span>
                                <button class="details-button" type="button">Bekijk details</button>
                                <button class="add-to-cart-button" type="button">Voeg toe aan winkelmandje</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-products-message">Er zijn momenteel geen producten beschikbaar.</p>
            <?php endif; ?>
        </div>
    </section>

    </main>
    <!--Chat-->
    <button class="chat-button" aria-label="Open AI Chat">
        <span class="chat-icon">+</span>
    </button>

    <div id="productModal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-header">
                <h3 id="modalTitle" class="modal-title"></h3>
                <button id="modalClose" class="modal-close-button">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalDescription"></p>
            </div>
        </div>
    </div>

    <div id="aiChatbox" class="chat-container hidden">
        <div class="chat-header">
            <h3>Apothecare AI Assistent</h3>
            <button id="closeChat" class="chat-close-button">&times;</button>
        </div>
        <div id="chatMessages" class="chat-messages">
            <div class="message ai-message">
                <p>Hallo! Hoe kan ik u vandaag helpen?</p>
            </div>
        </div>
        <div class="chat-input-container">
            <input type="text" id="chatInput" placeholder="Stel uw vraag...">
            <button id="sendChat" aria-label="Verstuur bericht">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2 .01 7z"/>
                </svg>
            </button>
        </div>
    </div>
    <!--Script en inlog ding-->
    <script src="js/mainpage.js"></script>
    <script>
<?php if ($isLoggedIn): ?>
    const btn = document.getElementById("LoginButton");
    const welkom = document.getElementById("welkomMessage");
    if (btn) {
        btn.textContent = "Log out";
        btn.href = "php/logout.php";
        welkom.textContent = "WELKOM, <?php echo htmlspecialchars($_SESSION['user_name']); ?>";
    }

<?php endif; ?>
<?php if ($_SESSION['user_role'] == 'admin') { ?>
    document.getElementById("admin-button").hidden = false;
<?php } ?>
    </script>
</body>
</html>