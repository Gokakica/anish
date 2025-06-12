<?php
include("db.php");

$registerMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $registerMessage = "❌ Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $registerMessage = "✅ User registered successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="main-container">
    <h2>Register New User</h2>
    <?php if ($registerMessage): ?>
        <p><?php echo $registerMessage; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" required placeholder="Email"><br><br>
        <input type="password" name="password" required placeholder="Password"><br><br>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login Here</a></p>
</div>

</body>
</html>
