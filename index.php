<?php
session_start();
include "db.php";

$result = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Icee Crochet - Handmade with Love</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>
<?php if (isset($_GET['logout']) && $_GET['logout'] == 'success') { ?>

    <div class="logout-message">
        You have been logged out successfully. 💗
    </div>

<?php } ?>
<!-- =========================
     NAVBAR
     ========================= -->
<nav>

    <a href="index.php" class="logo">
        <img src="images/logo.png" alt="Icee Crochet Logo">
    </a>

    <div>

        <a href="index.php">Home</a>

        <a href="#products">Shop</a>

        <a href="cart.php">Cart 🛒</a>

        <?php if (isset($_SESSION['user_id'])) { ?>

            <a href="my_orders.php">My Orders 📦</a>

            <a href="logout.php">Logout</a>

        <?php } else { ?>

            <a href="login.php">Login</a>

            <a href="register.php">Register</a>

        <?php } ?>

    </div>

</nav>


<!-- =========================
     HERO SECTION
     ========================= -->

<section class="hero">

    <div class="hero-content">

        <p class="hero-small-text">
            Handmade with love 🧶
        </p>

        <h1>
            Little stitches,<br>
            made with love.
        </h1>

        <p class="hero-description">
            Discover unique handmade crochet pieces,
            carefully crafted to bring a little more
            warmth and joy into your everyday life.
        </p>

        <a href="#products" class="shop-button">
            Explore Collection ✨
        </a>

    </div>

</section>


<!-- =========================
     PRODUCTS SECTION
     ========================= -->

<section class="products-section" id="products">

    <div class="section-heading">

        <p class="section-small-title">
            Our collection
        </p>

        <h2>
            Made just for you 🌸
        </h2>

        <p>
            Handmade crochet pieces created with patience,
            care, and lots of love.
        </p>

    </div>


    <div class="products">

        <?php
        while ($product = mysqli_fetch_assoc($result)) {
        ?>

            <div class="product-card">

                <a
                    href="product.php?id=<?php echo $product['id']; ?>"
                    class="product-link"
                >

                    <div class="product-image">
    <img
    src="images/<?php echo htmlspecialchars($product['image']); ?>"
    alt="<?php echo htmlspecialchars($product['name']); ?>"
    >
</div>

                    <h3>
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>

                </a>

                <p class="product-description">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <p class="product-price">
                    Rs. <?php echo $product['price']; ?>
                </p>

                <p class="product-stock">
                    <?php
                    if ($product['stock'] > 0) {
                        echo "In stock · " . $product['stock'] . " available";
                    } else {
                        echo "Out of stock";
                    }
                    ?>
                </p>

                <?php if ($product['stock'] > 0) { ?>

                    <form action="add_to_cart.php" method="POST">

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?php echo $product['id']; ?>"
                        >

                        <button type="submit">
                            Add to Cart 🛒
                        </button>

                    </form>

                <?php } else { ?>

                    <button type="button" disabled>
                        Out of Stock
                    </button>

                <?php } ?>

            </div>

        <?php
        }
        ?>

    </div>

</section>


<!-- =========================
     WHY ICEE CROCHET
     ========================= -->

<section class="why-section">

    <div class="section-heading">

        <p class="section-small-title">
            Why Icee Crochet?
        </p>

        <h2>
            Made slowly. Made with care. 🩵
        </h2>

    </div>


    <div class="why-cards">

        <div class="why-card">

            <div class="why-icon">
                🧶
            </div>

            <h3>Handmade</h3>

            <p>
                Every piece is carefully crocheted
                by hand, one stitch at a time.
            </p>

        </div>


        <div class="why-card">

            <div class="why-icon">
                💗
            </div>

            <h3>Made with Care</h3>

            <p>
                Each creation receives time,
                patience, and attention to detail.
            </p>

        </div>


        <div class="why-card">

            <div class="why-icon">
                🎁
            </div>

            <h3>Made to Gift</h3>

            <p>
                Thoughtful handmade pieces for
                yourself or someone special.
            </p>

        </div>

    </div>

</section>


<!-- =========================
     FOOTER
     ========================= -->

<footer>

    <h3>🧶 Icee Crochet</h3>

    <p>
        Handmade with love, one stitch at a time. 🩵
    </p>

    <p>
        © 2026 Icee Crochet. All rights reserved.
    </p>

</footer>


</body>

</html>