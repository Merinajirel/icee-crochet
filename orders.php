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

    <h1>My Orders 📦</h1>

    <p>Hello, <?php echo $_SESSION['user_name']; ?>! 👋</p>

    <?php

    if (mysqli_num_rows($result) == 0) {

        echo "<p>You haven't placed any orders yet.</p>";

    } else {

        while ($order = mysqli_fetch_assoc($result)) {

    ?>

            <div class="order-card">

                <h3>
                    Order #<?php echo $order['id']; ?>
                </h3>

                <p>
                    Date:
                    <?php echo $order['created_at']; ?>
                </p>

                <p>
                    Total:
                    <strong>Rs. <?php echo $order['total_amount']; ?></strong>
                </p>

                <p>
                    Status:
                    <strong><?php echo $order['status']; ?></strong>
                </p>

            </div>

            <hr>

    <?php

        }
    }

    ?>

    <br>

    <a href="index.php">← Continue Shopping</a>

</body>

</html>