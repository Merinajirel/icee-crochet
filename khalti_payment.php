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
/* Load private payment credentials */
require_once "secrets.php";

$secret_key = KHALTI_SECRET_KEY;
/* Convert amount from rupees to paisa */
$amount_paisa = $total_amount * 100;

/* Prepare payment request */
$data = [
    "return_url" => "http://localhost/icee-crochet/khalti_success.php",
    "website_url" => "http://localhost/icee-crochet/",
    "amount" => $amount_paisa,
    "purchase_order_id" => "ORDER-" . $order_id,
    "purchase_order_name" => "Icee Crochet Order",
    "customer_info" => [
        "name" => "Icee Crochet Customer"
    ]
];

/* Send request to Khalti */
$ch = curl_init("https://dev.khalti.com/api/v2/epayment/initiate/");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Key " . $secret_key,
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if ($response === false) {
    die("Khalti connection error: " . curl_error($ch));
}

curl_close($ch);

/* Convert Khalti response into PHP data */
$result = json_decode($response, true);

/* Check whether Khalti accepted the request */
if (!isset($result['payment_url'])) {
    echo "<h3>Khalti payment initiation failed.</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    exit;
}

/* Redirect customer to Khalti */
header("Location: " . $result['payment_url']);
exit;

?>