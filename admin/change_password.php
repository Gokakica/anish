<?php
session_start();
include("db.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Fetch current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    // Check password match
    if (!password_verify($current, $admin['password'])) {
        $message = "❌ Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $message = "❌ New passwords do not match.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $adminId);
        $stmt->execute();
        $message = "✅ Password successfully changed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #e0f7fa, #ffffff);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 420px;
            margin: 80px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 25px;
            color: #333;
        }

        label {
            float: left;
            font-weight: 600;
            margin-top: 10px;
            margin-bottom: 5px;
            color: #444;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
            margin-bottom: 15px;
            transition: border-color 0.3s;
        }

        input[type="password"]:focus {
            border-color: #28a745;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #218838;
        }

        .msg {
            margin-bottom: 15px;
            color: #d9534f;
            font-weight: bold;
        }

        .success {
            color: #28a745;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>🔒 Change Your Password</h2>
        <?php if ($message): ?>
            <div class="msg <?= strpos($message, 'successfully') !== false ? 'success' : '' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="current_password">Current Password</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">New Password</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Update Password</button>
        </form>

        <a href="home.php">← Back to Dashboard</a>
    </div>
</body>
</html>
