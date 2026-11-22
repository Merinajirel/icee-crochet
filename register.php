<?php
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password)
            VALUES ('$name', '$email', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        $message = "Registration successful!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">

    <div class="auth-card">

        <h1>Create an Account 🧶</h1>

        <?php if ($message != "") { ?>
            <p class="auth-message">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php } ?>

        <form method="POST">

            <label>Name</label>
            <input type="text" name="name" required>

            <label>Email</label>
            <input type="email" name="email" required>

           <label>Password</label>

<input type="password" name="password" id="register-password" required>

<span class="show-password"
      onclick="togglePassword('register-password', this)">
    Show password
</span>

            <button type="submit">Register</button>

        </form>

        <p class="auth-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>

</div>
<script>

function togglePassword(inputId, text) {

    const passwordInput = document.getElementById(inputId);

    if (passwordInput.type === "password") {

        passwordInput.type = "text";
        text.textContent = "Hide password";

    } else {

        passwordInput.type = "password";
        text.textContent = "Show password";

    }

}

</script>
</body>