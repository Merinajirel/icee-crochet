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

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$sql = "UPDATE orders
        SET status = '$status'
        WHERE id = '$order_id'";

if (mysqli_query($conn, $sql)) {

    header("Location: admin_orders.php");
    exit;

} else {

    echo "Error updating order status: " . mysqli_error($conn);

}

?>