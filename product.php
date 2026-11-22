<?php

session_start();
include "db.php";

if (!isset($_GET['id'])) {
    die("Product ID is missing.");
}

$product_id = $_GET['id'];

$sql = "SELECT * FROM products WHERE id = '$product_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Product not found.");
}

$product = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html>

<head>

    <title><?php echo htmlspecialchars($product['name']); ?> - Icee Crochet</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="product-detail">

    <div class="product-detail-image">
        <img
            src="images/<?php echo htmlspecialchars($product['image']); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>"
        >
    </div>

    <div class="product-detail-info">

        <p class="product-category">
            <?php echo htmlspecialchars($product['category']); ?>
        </p>

        <h1>
            <?php echo htmlspecialchars($product['name']); ?>
        </h1>

        <p class="product-detail-description">
            <?php echo htmlspecialchars($product['description']); ?>
        </p>

        <p class="product-detail-price">
            Rs. <?php echo $product['price']; ?>
        </p>

        <p class="product-detail-stock">
            <?php
            if ($product['stock'] > 0) {
                echo "In stock · " . $product['stock'] . " available";
            } else {
                echo "Out of stock";
            }
            ?>
        </p>

    </div>

</div>

<section class="reviews-section">
<?php

$rating_sql = "SELECT AVG(rating) AS average_rating,
                      COUNT(*) AS total_reviews
               FROM reviews
               WHERE product_id = '$product_id'";

$rating_result = mysqli_query($conn, $rating_sql);
$rating_data = mysqli_fetch_assoc($rating_result);

$average_rating = $rating_data['average_rating'];
$total_reviews = $rating_data['total_reviews'];

?>
<h2 class="reviews-title">⭐ Customer Reviews</h2>
<?php if ($total_reviews > 0) { ?>

    <div class="rating-summary">

    <span class="average-rating">
        ⭐ <?php echo number_format($average_rating, 1); ?>/5
    </span>

    <span class="review-count">
        (<?php echo $total_reviews; ?> reviews)
    </span>

</div>

<?php } else { ?>

    <p>No ratings yet.</p>

<?php } ?>

<?php

if (isset($_SESSION['user_id'])) {
?>

<h3 class="leave-review-title">Leave a Review 💭</h3>
<form action="submit_review.php" method="POST" class="review-form">
        <input
            type="hidden"
            name="product_id"
            value="<?php echo $product_id; ?>"
        >

        <label>Rating</label><br>

        <select name="rating" required>

            <option value="">Select Rating</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>

        </select>

        <br><br>

        <label>Your Review</label><br>

       <textarea
    name="review"
    rows="5"
    placeholder="Tell us what you think about this piece... 💗"
    required
></textarea>

        <br><br>

        <button type="submit">Submit Review ⭐</button>

    </form>

<?php
} else {
?>

    <p>
        Please <a href="login.php">login</a> to leave a review.
    </p>

<?php
}
?>

<hr>
<?php

$sql = "SELECT reviews.*, users.name
        FROM reviews
        JOIN users ON reviews.user_id = users.id
        WHERE reviews.product_id = '$product_id'
        ORDER BY reviews.created_at DESC";

$reviews_result = mysqli_query($conn, $sql);

if (mysqli_num_rows($reviews_result) == 0) {

    echo "<p>No reviews yet.</p>";

} else {

    while ($review = mysqli_fetch_assoc($reviews_result)) {
?>

<div class="review-card">
           <h3 class="reviewer-name">
    <?php echo htmlspecialchars($review['name']); ?>
</h3>

         <p class="review-stars">
    <?php
    echo str_repeat("⭐", $review['rating']);
    ?>
</p>

            <p class="review-text">
    <?php echo htmlspecialchars($review['review']); ?>
</p>

            <small>
                <?php echo $review['created_at']; ?>
            </small>

        </div>

        <hr>

<?php
    }
}
?>
</section>
<br>

<a href="index.php">← Back to Products</a>

</body>

</html>