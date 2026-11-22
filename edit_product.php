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

$sql = "SELECT * FROM products WHERE id = '$product_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Product not found.");
}

$product = mysqli_fetch_assoc($result);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $sql = "UPDATE products SET
            name = '$name',
            description = '$description',
            price = '$price',
            stock = '$stock',
            category = '$category'
            WHERE id = '$product_id'";

    if (mysqli_query($conn, $sql)) {

        $message = "Product updated successfully! 🎉";

        $sql = "SELECT * FROM products WHERE id = '$product_id'";
        $result = mysqli_query($conn, $sql);
        $product = mysqli_fetch_assoc($result);

    } else {

        $message = "Error: " . mysqli_error($conn);

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Product - Icee Crochet</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <h1>Edit Product ✏️🧶</h1>

    <p><?php echo $message; ?></p>

    <form method="POST">

        <label>Product Name</label><br>
        <input
            type="text"
            name="name"
            value="<?php echo htmlspecialchars($product['name']); ?>"
            required
        >

        <br><br>

        <label>Description</label><br>

        <textarea name="description" rows="5" required><?php
            echo htmlspecialchars($product['description']);
        ?></textarea>

        <br><br>

        <label>Price</label><br>

        <input
            type="number"
            name="price"
            step="0.01"
            value="<?php echo $product['price']; ?>"
            required
        >

        <br><br>

        <label>Stock</label><br>

        <input
            type="number"
            name="stock"
            value="<?php echo $product['stock']; ?>"
            required
        >

        <br><br>

        <label>Category</label><br>

        <input
            type="text"
            name="category"
            value="<?php echo htmlspecialchars($product['category']); ?>"
            required
        >

        <br><br>

        <button type="submit">Update Product ✏️</button>

    </form>

    <br>

    <a href="admin.php">← Back to Admin Dashboard</a>

</body>

</html>