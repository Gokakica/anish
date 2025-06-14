<?php
session_start();
include("db.php");

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: home.php");
    exit;
}

$loginError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND name = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $email, $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
            
                // Update login time and login count
                $update = $conn->prepare("UPDATE users SET login_time = NOW(), login_count = login_count + 1 WHERE email = ?");
                $update->bind_param("s", $email);
                $update->execute();
            
                // Insert into login_history table
                $insertHistory = $conn->prepare("INSERT INTO login_history (user_id) VALUES (?)");
                $insertHistory->bind_param("i", $user['id']);
                $insertHistory->execute();
            
                header("Location: home.php");
                exit;
            }
            
            
             else {
                $loginError = "Invalid email, name, or password.";
            }
        } else {
            $loginError = "Invalid email, name, or password.";
        }
    } else {
        $loginError = "Database error.";
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
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>


        <p>New user? <a href="register.php">Register Here</a></p>
    </div>
</div>

</body>
</html>
