<?php
session_start();
include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="checkout-page">

    <div class="checkout-card">

        <h1>Checkout 🧾</h1>

        <h2>Delivery Information</h2>

        <form action="place_order.php" method="POST">

        <label>Full Name</label><br>
        <input type="text" name="name" required>
        <br><br>

        <label>Address</label><br>
        <textarea name="address" required></textarea>
        <br><br>

        <button type="submit">Place Order</button>

    </form>
    </div>
    </div>

</body>
</html>