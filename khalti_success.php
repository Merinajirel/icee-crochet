
<?php

session_start();
include "db.php";

/* Check if Khalti sent pidx */
if (!isset($_GET['pidx'])) {
    die("Payment information is missing.");
}

$pidx = $_GET['pidx'];

/* Khalti secret key */
$secret_key = "693e2d10a6bd419cac10fec0da3eba14";

/* Send lookup request to Khalti */
$data = [
    "pidx" => $pidx
];

$ch = curl_init("https://dev.khalti.com/api/v2/epayment/lookup/");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Key " . $secret_key,
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if ($response === false) {
    die("Khalti verification error: " . curl_error($ch));
}

curl_close($ch);

/* Convert Khalti response into PHP array */
$result = json_decode($response, true);

/* Check whether Khalti returned a status */
if (!isset($result['status'])) {
    echo "<h3>Unable to verify payment.</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit;
}

/* Only Completed is treated as successful */
if ($result['status'] == "Completed") {

    /* Get amount from Khalti response */
    $amount_paisa = $result['total_amount'];

    /* Convert paisa back to rupees */
    $amount_rupees = $amount_paisa / 100;

    /* Get order ID from purchase_order_id */
    if (!isset($_GET['purchase_order_id'])) {
        die("Order information is missing.");
    }

    $purchase_order_id = $_GET['purchase_order_id'];

    /*
       Our purchase_order_id was created as:
       ORDER-<order_id>
    */
    $order_id = str_replace("ORDER-", "", $purchase_order_id);

    /* Get the order */
    $sql = "SELECT * FROM orders WHERE id = '$order_id'";

    $order_result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($order_result) != 1) {
        die("Order not found.");
    }

    $order = mysqli_fetch_assoc($order_result);

    /* Check that the Khalti amount matches our order amount */
    if ((float)$amount_rupees != (float)$order['total_amount']) {
        die("Payment amount does not match the order amount.");
    }

    /* Check whether this payment was already recorded */
    $check_sql = "SELECT * FROM payments
                  WHERE transaction_code = '$pidx'
                  LIMIT 1";

    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {

        /*
           Create an internal transaction UUID
           because our payments table requires it.
        */
        $transaction_uuid =
            "KHALTI-ORDER-" . $order_id . "-" . time();

        /*
           Save successful Khalti payment.
           pidx is stored as transaction_code.
        */
        $insert_sql = "INSERT INTO payments
                       (order_id, transaction_uuid, amount,
                        transaction_code, status)
                       VALUES
                       ('$order_id',
                        '$transaction_uuid',
                        '$amount_rupees',
                        '$pidx',
                        'completed')";

        if (!mysqli_query($conn, $insert_sql)) {
            die("Payment record could not be saved: " . mysqli_error($conn));
        }
    }

    /*
       Mark the order as paid and decrease stock only once.
    */
    if ($order['status'] != 'paid') {

        /* Mark order as paid */
        $update_sql = "UPDATE orders
                       SET status = 'paid'
                       WHERE id = '$order_id'";

        mysqli_query($conn, $update_sql);

        /* Decrease product stock */
        $stock_sql = "SELECT product_id, quantity
                      FROM order_items
                      WHERE order_id = '$order_id'";

        $stock_result = mysqli_query($conn, $stock_sql);

        while ($item = mysqli_fetch_assoc($stock_result)) {

            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            $update_stock = "UPDATE products
                             SET stock = stock - $quantity
                             WHERE id = '$product_id'";

            mysqli_query($conn, $update_stock);
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

<div class="order-success">

    <div class="order-success-card">

        <div class="success-icon">🎉</div>

        <h1>Payment Successful!</h1>

        <p>
            Your Khalti payment has been verified successfully.
        </p>

        <p>
            Order ID:
            <strong>#<?php echo $order_id; ?></strong>
        </p>

        <p>
            Amount Paid:
            <strong>Rs. <?php echo $amount_rupees; ?></strong>
        </p>

        <p>
            Payment Method:
            <strong>Khalti</strong>
        </p>

        <br>

        <a href="index.php">
            <button type="button">
                Continue Shopping 🧶
            </button>
        </a>

    </div>

</div>

</body>
</html>

<?php

} else {

    /* Payment was not completed */

    echo "<h1>Payment Not Completed</h1>";
    echo "<p>Payment status: " . htmlspecialchars($result['status']) . "</p>";
    echo "<p>Your order has NOT been marked as paid.</p>";
}

?>
```
