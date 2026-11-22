<?php

session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    die("Order ID is missing.");
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

/* Get order */
$sql = "SELECT * FROM orders
        WHERE id = '$order_id'
        AND user_id = '$user_id'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Order not found.");
}
$order = mysqli_fetch_assoc($result);

/* Prevent paying for an already paid order */
if ($order['status'] == 'paid') {
    die("This order has already been paid.");
}

$total_amount = $order['total_amount'];

/* eSewa details */
$product_code = "EPAYTEST";

/* Check if a pending payment already exists for this order */
$sql = "SELECT * FROM payments
        WHERE order_id = '$order_id'
        AND status = 'pending'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {

    /* Use existing pending payment */
    $payment = mysqli_fetch_assoc($result);

    $transaction_uuid = $payment['transaction_uuid'];

} else {

    /* Create a new transaction ID */
    $transaction_uuid = "ORDER-" . $order_id . "-" . time();

    /* Save payment information */
    $sql = "INSERT INTO payments
            (order_id, transaction_uuid, amount, status)
            VALUES
            ('$order_id', '$transaction_uuid', '$total_amount', 'pending')";

    mysqli_query($conn, $sql);
}

/* Load private payment credentials */
require_once "secrets.php";

$secret_key = ESEWA_SECRET_KEY;

/* Create signature */
$message = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";

$hash = hash_hmac("sha256", $message, $secret_key, true);

$signature = base64_encode($hash);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay with eSewa - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="payment-page">

    <div class="payment-card">

        <div class="payment-icon">💳</div>

        <h1>Pay for Your Order</h1>

    <p>Order ID: <strong>#<?php echo $order_id; ?></strong></p>

    <p>Total Amount:
        <strong>Rs. <?php echo $total_amount; ?></strong>
    </p>

    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">

        <input type="hidden" name="amount"
               value="<?php echo $total_amount; ?>">

        <input type="hidden" name="tax_amount" value="0">

        <input type="hidden" name="total_amount"
               value="<?php echo $total_amount; ?>">

        <input type="hidden" name="transaction_uuid"
               value="<?php echo $transaction_uuid; ?>">

        <input type="hidden" name="product_code"
               value="<?php echo $product_code; ?>">

        <input type="hidden" name="product_service_charge" value="0">

        <input type="hidden" name="product_delivery_charge" value="0">

        <input type="hidden" name="success_url"
               value="http://localhost/icee-crochet/esewa_success.php">

        <input type="hidden" name="failure_url"
               value="http://localhost/icee-crochet/esewa_failure.php">

        <input type="hidden" name="signed_field_names"
               value="total_amount,transaction_uuid,product_code">

        <input type="hidden" name="signature"
               value="<?php echo $signature; ?>">

        <button type="submit">
            Pay with eSewa 💚
        </button>

    </form>
    </div>
    </div>

</body>
</html>