<?php

session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$address = $_POST['address'];

$grand_total = 0;

/* Check stock and calculate total */
foreach ($_SESSION['cart'] as $product_id => $quantity) {

    $result = mysqli_query(
        $conn,
        "SELECT * FROM products WHERE id = $product_id"
    );

    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        die("Product not found.");
    }

    /* Check latest stock */
    if ($quantity > $product['stock']) {

        $_SESSION['cart_message'] =
            "Sorry! " . $product['name'] .
            " does not have enough stock available.";

        header("Location: cart.php");
        exit;
    }

    $grand_total += $product['price'] * $quantity;
}
/* Create order */
$sql = "INSERT INTO orders (user_id, total_amount, address)
        VALUES ('$user_id', '$grand_total', '$address')";

mysqli_query($conn, $sql);

$order_id = mysqli_insert_id($conn);

/* Add products to order_items */
foreach ($_SESSION['cart'] as $product_id => $quantity) {

    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");

    $product = mysqli_fetch_assoc($result);

    $price = $product['price'];

    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES ('$order_id', '$product_id', '$quantity', '$price')";

    mysqli_query($conn, $sql);
}

/* Clear cart */
unset($_SESSION['cart']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Created - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="order-success">

    <div class="order-success-card">

        <div class="success-icon">🎉</div>

        <h1>Order Created Successfully!</h1>

        <p>Thank you for shopping with Icee Crochet! 🧶</p>

    <p>
        Your Order ID is:
        <strong>#<?php echo $order_id; ?></strong>
    </p>

    <p>
        Total Amount:
        <strong>Rs. <?php echo $grand_total; ?></strong>
    </p>

    <br>

    <a href="esewa_payment.php?order_id=<?php echo $order_id; ?>">
    <button type="button">Pay with eSewa 💳</button>
</a>

<br><br>

<a href="khalti_payment.php?order_id=<?php echo $order_id; ?>">
    <button type="button">Pay with Khalti 💜</button>
</a>

<br><br>

<a href="index.php">Continue Shopping</a>
</div>
</div>
</body>
</html>