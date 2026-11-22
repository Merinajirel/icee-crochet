<?php

session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    die("Access denied. Admins only.");
}

if (!isset($_GET['id'])) {
    die("Product ID is missing.");
}

$product_id = $_GET['id'];

$sql = "DELETE FROM products WHERE id = '$product_id'";

if (mysqli_query($conn, $sql)) {

    header("Location: manage_products.php");
    exit;

} else {

    echo "Error deleting product: " . mysqli_error($conn);

}

?>