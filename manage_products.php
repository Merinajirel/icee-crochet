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

$sql = "SELECT * FROM products ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Products - Icee Crochet</title>

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

            <a href="admin.php">
                Dashboard
            </a>

            <a href="manage_products.php" class="active">
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

            <p class="admin-eyebrow">
                INVENTORY
            </p>

            <h1>
                Products
            </h1>

            <p class="admin-welcome">
                Manage your crochet products, pricing, and stock.
            </p>

        </div>


        <!-- =========================
             PRODUCT TABLE
             ========================= -->

        <section class="admin-table-section">

            <div class="admin-table-header">

                <div>

                    <h2>
                        Product Inventory
                    </h2>

                    <p>
                        <?php echo mysqli_num_rows($result); ?> products in your store
                    </p>

                </div>

                <a href="add_product.php" class="admin-primary-button">
                    + Add Product
                </a>

            </div>


            <?php if (mysqli_num_rows($result) == 0) { ?>

                <div class="admin-empty">

                    <h3>
                        No products yet
                    </h3>

                    <p>
                        Add your first crochet product to get started.
                    </p>

                    <a href="add_product.php" class="admin-primary-button">
                        Add Product
                    </a>

                </div>

            <?php } else { ?>

                <div class="admin-table-wrapper">

                    <table class="admin-product-table">

                        <thead>

                            <tr>

                                <th>ID</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th>Actions</th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php while ($product = mysqli_fetch_assoc($result)) { ?>

                            <tr>

                                <td class="product-id">
                                    #<?php echo $product['id']; ?>
                                </td>


                                <td>

                                    <div class="admin-product-name">

                                        <?php if (!empty($product['image'])) { ?>

                                            <img
                                                src="images/<?php echo htmlspecialchars($product['image']); ?>"
                                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                            >

                                        <?php } else { ?>

                                            <div class="admin-product-placeholder">
                                                —
                                            </div>

                                        <?php } ?>


                                        <span>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </span>

                                    </div>

                                </td>


                                <td class="product-price">
                                    Rs. <?php echo $product['price']; ?>
                                </td>


                                <td>

                                    <?php if ($product['stock'] > 0) { ?>

                                        <span class="stock-badge in-stock">
                                            <?php echo $product['stock']; ?> available
                                        </span>

                                    <?php } else { ?>

                                        <span class="stock-badge out-stock">
                                            Out of stock
                                        </span>

                                    <?php } ?>

                                </td>


                                <td>

                                    <span class="category-badge">
                                        <?php echo htmlspecialchars($product['category']); ?>
                                    </span>

                                </td>


                                <td>

                                    <div class="admin-table-actions">

                                        <a
                                            href="edit_product.php?id=<?php echo $product['id']; ?>"
                                            class="edit-action"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="delete_product.php?id=<?php echo $product['id']; ?>"
                                            class="delete-action"
                                            onclick="return confirm('Are you sure you want to delete this product?');"
                                        >
                                            Delete
                                        </a>

                                    </div>

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