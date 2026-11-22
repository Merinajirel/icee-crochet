<?php

session_start();
include "db.php";

$product_id = $_POST['product_id'];
$action = $_POST['action'];

/* Check if product exists in cart */
if (isset($_SESSION['cart'][$product_id])) {

    if ($action == "increase") {

        /* Get current stock */
        $result = mysqli_query(
            $conn,
            "SELECT stock FROM products WHERE id = $product_id"
        );

        $product = mysqli_fetch_assoc($result);

        if (!$product) {
            die("Product not found.");
        }

        /* Check stock before increasing */
        if ($_SESSION['cart'][$product_id] < $product['stock']) {

            $_SESSION['cart'][$product_id]++;

        } else {

$_SESSION['cart_message'] = "Sorry! You cannot add more than the available stock.";
header("Location: cart.php");
exit;
        }

    } elseif ($action == "decrease") {

        $_SESSION['cart'][$product_id]--;

        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }

    } elseif ($action == "remove") {

        unset($_SESSION['cart'][$product_id]);

    }
}

header("Location: cart.php");
exit;

?>