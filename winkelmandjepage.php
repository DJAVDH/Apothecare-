<?php
include 'php/db_connect.php';
include 'php/winkelmandje.php';
include 'php/checkout.php';
$redirectUrl = $_SERVER['PHP_SELF'] ?? 'winkelmandjepage.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['removeProductId'])) {
        $productId = (int) $_POST['removeProductId'];
        $quantity = (int) ($_POST['removeQuantity'] ?? 1);
        removeItem($productId, $quantity);
    } elseif (isset($_POST['clearCart'])) {
        clearCart();
    }

    header('Location: ' . $redirectUrl);
    exit;
}

$cartItems = $_SESSION['cart'] ?? [];
$totalItems = 0;
$cartTotal = 0.0;

foreach ($cartItems as $item) {
    $quantity = (int) ($item['quantity'] ?? 0);
    $price = (float) ($item['price'] ?? 0);
    $totalItems += $quantity;
    $cartTotal += $price * $quantity;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winkelmandje - Apothecare</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
<main class="cart-page">
    <section class="cart-items-card">
        <div class="cart-card-header">
            <h2>Winkelmandje</h2>
            <?php if ($totalItems > 0): ?>
                <span class="cart-card-counter"><?php echo $totalItems; ?> artikelen</span>
            <?php endif; ?>
        </div>
        <?php if (!empty($cartItems)): ?>
            <ul class="cart-items-list">
                <?php foreach ($cartItems as $item): ?>
                    <?php
                        $itemName = htmlspecialchars($item['name'] ?? 'Onbekend product', ENT_QUOTES, 'UTF-8');
                        $itemQuantity = (int) ($item['quantity'] ?? 0);
                        $itemPrice = (float) ($item['price'] ?? 0);
                        $itemTotal = $itemPrice * $itemQuantity;
                        $itemImage = htmlspecialchars($item['imglink'] ?? '', ENT_QUOTES, 'UTF-8');
                    ?>
                    <li class="cart-item">
                        <div class="cart-item-main">
                            <?php if (!empty($itemImage)): ?>
                                <img src="<?php echo $itemImage; ?>" alt="<?php echo $itemName; ?>" class="cart-product-image">
                            <?php endif; ?>
                            <div class="cart-item-text">
                                <span class="cart-item-name"><?php echo $itemName; ?></span>
                                <span class="cart-item-meta">Aantal: <?php echo $itemQuantity; ?></span>
                            </div>
                        </div>
                        <div class="cart-item-pricing">
                            <span class="cart-item-price">€ <?php echo number_format($itemPrice, 2, ',', '.'); ?></span>
                            <span class="cart-item-total">Totaal: € <?php echo number_format($itemTotal, 2, ',', '.'); ?></span>
                        </div>
                        <form method="POST" class="cart-item-remove">
                            <input type="hidden" name="removeProductId" value="<?php echo (int) ($item['id'] ?? 0); ?>">
                            <input type="hidden" name="removeQuantity" value="1">
                            <button type="submit" class="remove-button">Verwijder</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="cart-empty">
                <i class="fas fa-shopping-basket" aria-hidden="true"></i>
                <p>Je winkelmandje is leeg.</p>
                <a class="cart-empty-link" href="productpage.php">Ga naar producten</a>
            </div>
        <?php endif; ?>
    </section>
    <?php if ($totalItems > 0): ?>
        <aside class="cart-summary-card">
            <h3>Samenvatting</h3>
            <div class="cart-summary-row">
                <span>Artikelen</span>
                <span><?php echo $totalItems; ?></span>
            </div>
            <div class="cart-summary-row cart-summary-total">
                <span>Totaal</span>
                <span>€ <?php echo number_format($cartTotal, 2, ',', '.'); ?></span>
            </div>
            <form class="checkout-form" method="post" action="php/checkout.php">
                <h4>Bezorgadres</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="checkout-street">Straatnaam</label>
                        <input type="text" id="checkout-street" name="streetname" placeholder="Bijv. Voorbeeldstraat" required>
                    </div>
                    <div class="form-group">
                        <label for="checkout-house-number">Huisnummer</label>
                        <input type="text" id="checkout-house-number" name="streetnumber" placeholder="Bijv. 12A" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="checkout-postcode">Postcode</label>
                        <input type="text" id="checkout-postcode" name="postalcode" placeholder="Bijv. 1234 AB" required>
                    </div>
                    <div class="form-group">
                        <label for="checkout-city">Plaats</label>
                        <input type="text" id="checkout-city" name="city" placeholder="Bijv. Amsterdam" required>
                    </div>
                </div>
                <button type="submit" class="checkout-button">Afrekenen</button>
            </form>
            <form method="POST" class="cart-clear">
                <input type="hidden" name="clearCart" value="1">
                <button type="submit" class="clear-button">Leeg winkelmandje</button>
            </form>
            <a href="index.php" class="checkout-button">Verder winkelen</a>
        </aside>
    <?php endif; ?>
</main>
