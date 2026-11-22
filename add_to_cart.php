<?php

session_start();

include "db.php";

$product_id = $_POST['product_id'];

/* Get product stock */
$result = mysqli_query(
    $conn,
    "SELECT stock FROM products WHERE id = $product_id"
);

$product = mysqli_fetch_assoc($result);

if (!$product) {
    die("Product not found.");
}

$stock = $product['stock'];

/* Current quantity already in cart */
$current_quantity = $_SESSION['cart'][$product_id] ?? 0;

/* Check stock */
if ($current_quantity >= $stock) {

    die("Sorry! You cannot add more than the available stock.");

}

/* Add product to cart */

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {

    $_SESSION['cart'][$product_id]++;

} else {

    $_SESSION['cart'][$product_id] = 1;

}

header("Location: cart.php");
exit;

?>