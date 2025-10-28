<?php

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
            <a href="#" class="nav-button secondary">Winkelmandje</a>
            <a id="LoginButton" href="loginpage.php" class="nav-button primary">Log In</a>
        </nav>
    </header>

    <!--Main-->
    <main class="product-main">
        <section class="product-detail">
            <div class="product-gallery">
                <img src="assets/img/pillen.jpg" alt="Product 1" class="product-image">
            </div>
            <div class="product-info">
                <h1 class="product-title">ProductNaam</h1>
                <p class="product-description">voorbeeldomschrijving</p>
                <ul class="product-meta">
                    <li><span>Gewicht:</span> gewicht</li>
                    <li><span>Prijs:</span> €0,00</li>
                    <li><span>Voorraad:</span> Op voorraad</li>
                </ul>
                <div class="product-actions">
                    <span class="product-price">€0,00</span>
                    <button class="winkelmandje-button">Voeg toe aan winkelmandje</button>
                </div>
            </div>
        </section>

        <section class="product-specificaties">
            <h2>Waarom dit product?</h2>
            <ul>
                <li>Specificatie1</li>
                <li>Specificatie2</li>
                <li>Specificatie3</li>
            </ul>
        </section>
    </main>
</body>
</html>