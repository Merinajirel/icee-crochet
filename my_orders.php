<?php

session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders
        WHERE user_id = '$user_id'
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>My Orders - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="my-orders-page">

    <div class="my-orders-card">

        <h1>My Orders 📦</h1>

        <?php

        if (mysqli_num_rows($result) == 0) {

            echo "<p>You haven't placed any orders yet.</p>";

        } else {

            while ($order = mysqli_fetch_assoc($result)) {

        ?>

            <div class="order-card">

                <h2>
                    Order #<?php echo $order['id']; ?>
                </h2>

                <p>
                    <strong>Total:</strong>
                    Rs. <?php echo $order['total_amount']; ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?php echo htmlspecialchars($order['status']); ?>
                </p>

                <p>
                    <strong>Delivery Address:</strong><br>
                    <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                </p>

                <p>
                    <strong>Order Date:</strong>
                    <?php echo $order['created_at']; ?>
                </p>

            </div>

        <?php

            }

        }

        ?>

        <a href="index.php">← Continue Shopping</a>

    </div>

</div>

</body>

</html>