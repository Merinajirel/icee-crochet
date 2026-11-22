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

$sql = "SELECT orders.*, users.name
        FROM orders
        JOIN users ON orders.user_id = users.id
        ORDER BY orders.created_at DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Orders - Icee Crochet</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="admin-body">

<div class="admin-layout">

    <!-- SIDEBAR -->

    <aside class="admin-sidebar">

        <div class="admin-brand">

            <div class="admin-brand-name">
                Icee Crochet
            </div>

            <div class="admin-brand-subtitle">
                Store Management
            </div>

        </div>


        <nav class="admin-nav">

            <a href="admin.php">
                Dashboard
            </a>

            <a href="manage_products.php">
                Products
            </a>

            <a href="admin_orders.php" class="active">
                Orders
            </a>

            <a href="add_product.php">
                Add Product
            </a>

        </nav>


        <div class="admin-sidebar-bottom">

            <a href="index.php">
                View Store
            </a>

            <a href="logout.php">
                Logout
            </a>

        </div>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="admin-main">

        <div class="admin-header">

            <p class="admin-eyebrow">
                ORDER MANAGEMENT
            </p>

            <h1>
                Orders
            </h1>

            <p class="admin-welcome">
                Review customer orders and manage their delivery status.
            </p>

        </div>


        <!-- ORDERS TABLE -->

        <section class="admin-table-section">

            <div class="admin-table-header">

                <div>

                    <h2>
                        Customer Orders
                    </h2>

                    <p>
                        <?php echo mysqli_num_rows($result); ?> orders in your store
                    </p>

                </div>

            </div>


            <?php if (mysqli_num_rows($result) == 0) { ?>

                <div class="admin-empty">

                    <h3>
                        No orders yet
                    </h3>

                    <p>
                        Customer orders will appear here once they are placed.
                    </p>

                </div>

            <?php } else { ?>

                <div class="admin-table-wrapper">

                    <table class="admin-product-table">

                        <thead>

                            <tr>

                                <th>Order</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Delivery Address</th>
                                <th>Status</th>
                                <th>Date</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php while ($order = mysqli_fetch_assoc($result)) { ?>

                            <tr>

                                <td class="product-id">

                                    #<?php echo $order['id']; ?>

                                </td>


                                <td>

                                    <div class="admin-order-customer">

                                        <span>
                                            <?php echo htmlspecialchars($order['name']); ?>
                                        </span>

                                    </div>

                                </td>


                                <td class="product-price">

                                    Rs. <?php echo $order['total_amount']; ?>

                                </td>


                                <td class="admin-order-address">

                                    <?php echo htmlspecialchars($order['address']); ?>

                                </td>


                                <td>

                                    <form
                                        action="update_order_status.php"
                                        method="POST"
                                        class="admin-status-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="order_id"
                                            value="<?php echo $order['id']; ?>"
                                        >

                                        <select name="status">

                                            <option value="pending"
                                                <?php if ($order['status'] == 'pending') echo 'selected'; ?>>
                                                Pending
                                            </option>

                                            <option value="paid"
                                                <?php if ($order['status'] == 'paid') echo 'selected'; ?>>
                                                Paid
                                            </option>

                                            <option value="processing"
                                                <?php if ($order['status'] == 'processing') echo 'selected'; ?>>
                                                Processing
                                            </option>

                                            <option value="shipped"
                                                <?php if ($order['status'] == 'shipped') echo 'selected'; ?>>
                                                Shipped
                                            </option>

                                            <option value="delivered"
                                                <?php if ($order['status'] == 'delivered') echo 'selected'; ?>>
                                                Delivered
                                            </option>

                                        </select>


                                        <button type="submit">
                                            Update
                                        </button>

                                    </form>

                                </td>


                                <td class="admin-order-date">

                                    <?php echo $order['created_at']; ?>

                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            <?php } ?>

        </section>


        <a href="admin.php" class="admin-back-link">
            ← Back to Dashboard
        </a>

    </main>

</div>

</body>

</html>