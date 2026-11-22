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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    /* =========================
       IMAGE UPLOAD
       ========================= */

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

$upload_folder = __DIR__ . "/images/";
    /* Create a unique filename */
    $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);

    $new_image_name = uniqid() . "." . $image_extension;

    $image_path = $upload_folder . $new_image_name;

    /* Move image to images folder */
   /* Move image to images folder */
if (move_uploaded_file($image_tmp, $image_path)) {

    $sql = "INSERT INTO products
            (name, description, price, stock, category, image)
            VALUES
            ('$name', '$description', '$price', '$stock', '$category', '$new_image_name')";

    if (mysqli_query($conn, $sql)) {

        $message = "Product added successfully! 🎉";

    } else {

        $message = "Database error: " . mysqli_error($conn);

    }

} else {

    $message = "PHP received the image, but could not move it to: " . $image_path;

}
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Product - Icee Crochet</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <h1>Add New Product ➕🧶</h1>

    <p><?php echo htmlspecialchars($message); ?></p>

    <form method="POST" enctype="multipart/form-data">

        <label>Product Name</label><br>

        <input type="text" name="name" required>

        <br><br>


        <label>Description</label><br>

        <textarea name="description" rows="5" required></textarea>

        <br><br>


        <label>Price</label><br>

        <input type="number" name="price" step="0.01" required>

        <br><br>


        <label>Stock</label><br>

        <input type="number" name="stock" required>

        <br><br>


        <label>Category</label><br>

        <input type="text" name="category" required>

        <br><br>


        <label>Product Image</label><br>

        <input type="file" name="image" accept="image/*" required>

        <br><br>


        <button type="submit">
            Add Product 🧶
        </button>

    </form>

    <br>

    <a href="admin.php">← Back to Admin Dashboard</a>

</body>

</html>