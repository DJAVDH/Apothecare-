<?php
include("php/db_connect.php");
session_start();
include("php/winkelmandje.php");

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$productId) {
    http_response_code(400);
    exit('Ongeldige product-ID');
}

$cartNotice = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addToCart'])) {
    $postedProductId = (int) ($_POST['productId'] ?? 0);
    $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

    if ($postedProductId === $productId) {
        $addedItem = addItem($postedProductId, $quantity);
        if ($addedItem !== false) {
            $cartNotice = [
                'type' => 'success',
                'message' => 'Product toegevoegd aan je winkelmandje.',
            ];
        } else {
            $cartNotice = [
                'type' => 'error',
                'message' => 'Product kon niet worden toegevoegd. Probeer het later opnieuw.',
            ];
        }
    } else {
        $cartNotice = [
            'type' => 'error',
            'message' => 'Ongeldige product selectie.',
        ];
    }
}

// voorbeeld: product uit database halen
$stmt = $pdo->prepare('SELECT id, name, description, price, stock, category, imglink, dose, quantity, brand FROM products WHERE id = :id');
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    exit('Product niet gevonden.');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">    
</head>
<body class="product-page">

<!--Header-->
        <header class="main-header">
    <a href="index.php" class="logo-link">
        <div class="logo-container">
            <img src="assets/img/aptoocare.png" alt="Apothecare Logo" class="aptoocare-logo">
            <div class="logo-text">Apothecare</div>
        </div>
    </a> 
        <nav class="main-nav">
            <a href="winkelmandjepage.php" class="nav-button secondary">Winkelmandje</a>
            <a id="LoginButton" href="loginpage.php" class="nav-button primary">Log In</a>
        </nav>
    </header>

    <!--Main-->
    <main class="product-main">
        <section class="product-detail">
            <div class="product-gallery">
                <img src="<?php echo $product['imglink']; ?>" alt="Product 1" class="product-image">
            </div>
            <div class="product-info">
                <h1 class="product-title" id="productTitle"><?php echo $product['name']; ?></h1>
                <p class="product-description"><?php echo $product['description']; ?></p>
                <p class="product-description">Categorie: <?php echo $product['category']; ?></p>
                <ul class="product-meta">
                    <li><span>Prijs:</span>€<?php echo $product['price']; ?></li>
                    <li><span>Voorraad:</span>
                        <?php if ($product['stock'] > 0): ?>
                            <span class="in-stock">In voorraad</span>
                        <?php else: ?>
                            <span class="out-of-stock">Op voorraad</span>
                        <?php endif; ?>
                    </li>
                </ul>
                <?php if ($cartNotice): ?>
                    <div class="cart-notice <?php echo htmlspecialchars($cartNotice['type'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($cartNotice['message'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
                <div class="product-actions">
                    <span class="product-price">€<?php echo $product['price']; ?></span>
                    <form method="POST" class="product-cart-form">
                        <input type="hidden" name="productId" value="<?php echo (int) ($product['id'] ?? 0); ?>">
                        <label for="quantity" class="quantity-label">Aantal:</label>
                        <input type="number" id="quantity" name="quantity" class="quantity-input" value="1" min="1">
                        <button type="submit" name="addToCart" class="winkelmandje-button">Voeg toe aan winkelmandje</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="product-specificaties">
            <h2>Specificaties van product</h2>
            <ul>
                <li>Dosering: <?php echo $product['dose']; ?></li>
                <li>Hoeveelheid: <?php echo $product['quantity']; ?></li>
                <li>Merk: <?php echo $product['brand']; ?></li>
            </ul>
        </section>
    </main>
</body>
</html>