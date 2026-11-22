<?php
session_start();
include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($_SESSION['role'] == 'admin') {

                header("Location: admin.php");

            } else {

                header("Location: index.php");

            }

            exit;

        } else {

            $message = "Incorrect password.";

        }

    } else {

        $message = "Account not found.";

    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - Icee Crochet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="auth-page">

    <div class="auth-card">

        <h1>Login 🧶</h1>

        <?php if ($message != "") { ?>

            <p class="auth-message">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php } ?>

        <form method="POST">

            <label>Email</label>

            <input type="email" name="email" required>


            <label>Password</label>

            <input
                type="password"
                name="password"
                id="login-password"
                required
            >

            <span
                class="show-password"
                onclick="togglePassword('login-password', this)"
            >
                Show password
            </span>


            <button type="submit">
                Login
            </button>

        </form>

        <p class="auth-link">
            Don't have an account?
            <a href="register.php">Register</a>
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

</html>