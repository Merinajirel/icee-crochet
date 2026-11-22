<?php

session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];
$user_id = $_SESSION['user_id'];
if ($rating < 1 || $rating > 5) {
    die("Invalid rating.");
}

$sql = "INSERT INTO reviews
        (product_id, user_id, rating, review)
        VALUES
        ('$product_id', '$user_id', '$rating', '$review')";

if (mysqli_query($conn, $sql)) {

    header("Location: product.php?id=$product_id");
    exit;

} else {

    echo "Error submitting review: " . mysqli_error($conn);

}

?>