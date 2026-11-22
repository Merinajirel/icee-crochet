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

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Dashboard - Icee Crochet</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="admin-body">

<div class="admin-layout">

    <!-- =========================
         SIDEBAR
         ========================= -->

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

            <a href="admin.php" class="active">
                Dashboard
            </a>

            <a href="manage_products.php">
                Products
            </a>

            <a href="admin_orders.php">
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


    <!-- =========================
         MAIN CONTENT
         ========================= -->

    <main class="admin-main">

        <div class="admin-header">

            <div>

                <p class="admin-eyebrow">
                    ADMINISTRATION
                </p>

                <h1>
                    Dashboard
                </h1>

                <p class="admin-welcome">
                    Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.
                </p>

            </div>

        </div>


        <!-- =========================
             OVERVIEW CARDS
             ========================= -->

        <section class="admin-overview">

            <div class="admin-stat-card">

                <div class="admin-stat-label">
                    Store
                </div>

                <div class="admin-stat-value">
                    Active
                </div>

                <p>
                    Your storefront is ready to receive orders.
                </p>

            </div>


            <div class="admin-stat-card">

                <div class="admin-stat-label">
                    Products
                </div>

                <div class="admin-stat-value">
                    Manage
                </div>

                <p>
                    Add, edit, or remove products from your store.
                </p>

            </div>


            <div class="admin-stat-card">

                <div class="admin-stat-label">
                    Orders
                </div>

                <div class="admin-stat-value">
                    Manage
                </div>

                <p>
                    Review orders and update their status.
                </p>

            </div>

        </section>


        <!-- =========================
             QUICK ACTIONS
             ========================= -->

        <section class="admin-section">

            <div class="admin-section-heading">

                <div>

                    <p class="admin-eyebrow">
                        QUICK ACTIONS
                    </p>

                    <h2>
                        Store management
                    </h2>

                </div>

            </div>


            <div class="admin-actions">

                <a href="add_product.php" class="admin-action-card">

                    <div class="admin-action-icon">
                        +
                    </div>

                    <div>

                        <h3>
                            Add Product
                        </h3>

                        <p>
                            Add a new crochet item to your store.
                        </p>

                    </div>

                    <span class="admin-action-arrow">
                        →
                    </span>

                </a>


                <a href="manage_products.php" class="admin-action-card">

                    <div class="admin-action-icon">
                        P
                    </div>

                    <div>

                        <h3>
                            Manage Products
                        </h3>

                        <p>
                            Edit product details, prices, and stock.
                        </p>

                    </div>

                    <span class="admin-action-arrow">
                        →
                    </span>

                </a>


                <a href="admin_orders.php" class="admin-action-card">

                    <div class="admin-action-icon">
                        O
                    </div>

                    <div>

                        <h3>
                            Manage Orders
                        </h3>

                        <p>
                            View customer orders and update status.
                        </p>

                    </div>

                    <span class="admin-action-arrow">
                        →
                    </span>

                </a>

            </div>

        </section>


        <!-- =========================
             STORE NOTE
             ========================= -->

        <section class="admin-note">

            <div class="admin-note-line"></div>

            <div>

                <p class="admin-eyebrow">
                    ICEE CROCHET
                </p>

                <h2>
                    Handmade, one order at a time.
                </h2>

                <p>
                    Use the dashboard to keep your products,
                    inventory, and customer orders organized.
                </p>

            </div>

        </section>

    </main>

</div>

</body>

</html>