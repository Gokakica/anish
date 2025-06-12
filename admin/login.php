<?php
session_start();
include("db.php");

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: home.php");
    exit;
}

$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔍 Fetch user from DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // ✅ Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 🔒 Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            header("Location: home.php");
            exit;
        } else {
            $loginError = "Invalid email or password.";
        }
    } else {
        $loginError = "Invalid email or password.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="main-container">
    <div class="login-box">
        <h2>Login</h2>
        <?php if ($loginError): ?>
            <p class="error"><?php echo $loginError; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>New user? <a href="register.php">Register Here</a></p>
    </div>
</div>

</body>
</html>
