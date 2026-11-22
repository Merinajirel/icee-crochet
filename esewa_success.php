<?php

session_start();
include "db.php";

if (!isset($_GET['data'])) {
    die("Payment response not received.");
}

/* Decode eSewa response */
$decoded_data = base64_decode($_GET['data']);

/* Convert JSON into PHP array */
$response = json_decode($decoded_data, true);

if (!$response) {
    die("Invalid payment response.");
}

/* Get response values */
$status = $response['status'];
$transaction_uuid = $response['transaction_uuid'];
$total_amount = $response['total_amount'];
$transaction_code = $response['transaction_code'];
$product_code = $response['product_code'];
$received_signature = $response['signature'];


/* ---------------------------------
   VERIFY SIGNATURE
---------------------------------- */

$secret_key = "8gBm/:&EnhH.1/q";

$signed_field_names = $response['signed_field_names'];

$fields = explode(",", $signed_field_names);

$message_parts = [];

foreach ($fields as $field) {

    if (!isset($response[$field])) {
        die("Required payment field is missing.");
    }

    $message_parts[] = $field . "=" . $response[$field];
}

$message = implode(",", $message_parts);

$hash = hash_hmac(
    "sha256",
    $message,
    $secret_key,
    true
);

$generated_signature = base64_encode($hash);


/* Compare signatures */

if (!hash_equals($generated_signature, $received_signature)) {

    die("Payment verification failed: Invalid signature.");
}


/* ---------------------------------
   CHECK PAYMENT STATUS
---------------------------------- */

if ($status != "COMPLETE") {

    echo "<h1>Payment Not Completed</h1>";

    echo "<p>Payment status: " . htmlspecialchars($status) . "</p>";

    echo "<a href='index.php'>Return to Home</a>";

    exit;
}


/* ---------------------------------
   FIND PAYMENT IN OUR DATABASE
---------------------------------- */

$sql = "SELECT * FROM payments
        WHERE transaction_uuid = '$transaction_uuid'
        AND amount = '$total_amount'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {

    die("Payment record not found.");
}

$payment = mysqli_fetch_assoc($result);

$order_id = $payment['order_id'];
$stored_amount = $payment['amount'];


/* ---------------------------------
   CHECK AMOUNT
---------------------------------- */

if ((float)$stored_amount != (float)$total_amount) {

    die("Payment amount does not match the order amount.");
}
/* ---------------------------------
   VERIFY TRANSACTION WITH eSEWA
---------------------------------- */

$status_url = "https://rc.esewa.com.np/api/epay/transaction/status/"
            . "?product_code=" . urlencode($product_code)
            . "&total_amount=" . urlencode($total_amount)
            . "&transaction_uuid=" . urlencode($transaction_uuid);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $status_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$status_response = curl_exec($ch);

if ($status_response === false) {
    die("cURL Error: " . curl_error($ch));
}

$status_data = json_decode($status_response, true);

if (!$status_data) {
    die("Invalid response from eSewa.");
}

if ($status_data['status'] != "COMPLETE") {

    die("eSewa has not confirmed this payment.");
}


/* ---------------------------------
   UPDATE PAYMENT
---------------------------------- */
if ($payment['status'] != 'success') {

    /* Mark payment as successful */
    $sql = "UPDATE payments
            SET transaction_code = '$transaction_code',
                status = 'success'
            WHERE transaction_uuid = '$transaction_uuid'";

    mysqli_query($conn, $sql);

    /* Mark order as paid */
    $sql = "UPDATE orders
            SET status = 'paid'
            WHERE id = '$order_id'";

    mysqli_query($conn, $sql);

    /* Decrease product stock only once */
    $sql = "SELECT product_id, quantity
            FROM order_items
            WHERE order_id = '$order_id'";

    $result = mysqli_query($conn, $sql);

    while ($item = mysqli_fetch_assoc($result)) {

        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        $sql = "UPDATE products
                SET stock = stock - $quantity
                WHERE id = '$product_id'";

        mysqli_query($conn, $sql);
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Payment Successful - Icee Crochet</title>
        <link rel="stylesheet" href="style.css">

    </head>
<body>

<div class="payment-success-page">

    <div class="payment-success-card">

        <div class="success-icon">🎉</div>

        <h1>Payment Successful!</h1>

        <p>Your eSewa payment has been verified successfully.</p>

        <p>
            Order ID:
            <strong>#<?php echo htmlspecialchars($order_id); ?></strong>
        </p>

        <p>
            Transaction Code:
            <strong><?php echo htmlspecialchars($transaction_code); ?></strong>
        </p>

        <p>
            Amount Paid:
            <strong>Rs. <?php echo htmlspecialchars($total_amount); ?></strong>
        </p>

        <p>Thank you for shopping with Icee Crochet! 🧶💗</p>

        <a href="index.php">Continue Shopping</a>

    </div>

</div>

</body>