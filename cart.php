<?php
session_start();

include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="cart-page">

    <h1>Your Cart 🛒</h1>
    <?php

if (isset($_SESSION['cart_message'])) {

    echo "<div class='cart-message'>";
    echo htmlspecialchars($_SESSION['cart_message']);
    echo "</div>";

    unset($_SESSION['cart_message']);
}

?>

    <?php

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {

        echo "<p>Your cart is empty.</p>";

    } else {

    $grand_total = 0;

    foreach ($_SESSION['cart'] as $product_id => $quantity) {

        $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");

        $product = mysqli_fetch_assoc($result);

        $item_total = $product['price'] * $quantity;

        $grand_total += $item_total;
        echo "<div class='cart-product-image'>";
echo "<img src='images/" . htmlspecialchars($product['image']) . "' alt='" . htmlspecialchars($product['name']) . "'>";
echo "</div>";

        echo "<h3>" . $product['name'] . "</h3>";
        echo "<p>Price: Rs. " . $product['price'] . "</p>";
        echo "<form action='update_cart.php' method='POST'>";
echo "<input type='hidden' name='product_id' value='" . $product_id . "'>";
echo "<button type='submit' name='action' value='decrease'>−</button>";
echo " " . $quantity . " ";
echo "<button type='submit' name='action' value='increase'>+</button>";
echo "</form>";
echo "<form action='update_cart.php' method='POST'>";
echo "<input type='hidden' name='product_id' value='" . $product_id . "'>";
echo "<button type='submit' name='action' value='remove'>Remove 🗑️</button>";
echo "</form>";

        echo "<p>Item Total: Rs. " . $item_total . "</p>";
        echo "<hr>";
    }

    echo "<h2>Grand Total: Rs. " . $grand_total . "</h2>";
    echo "<br>";
echo "<a href='checkout.php'>Proceed to Checkout 💳</a>";
}

    ?>

        <a href="index.php">← Continue Shopping</a>

</div>

</body>
</html>