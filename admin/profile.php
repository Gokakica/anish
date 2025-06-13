<?php
session_start();
include("db.php");

// Check session
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch current admin details
$adminId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT name, email, password, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$profileResult = $stmt->get_result();

if ($profileResult->num_rows === 0) {
    echo "Admin not found.";
    exit();
}
$admin = $profileResult->fetch_assoc();

// Fetch all admin users
$usersResult = $conn->query("SELECT * FROM admin_users ORDER BY login_time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .profile-table {
            margin: 20px auto;
            border-collapse: collapse;
            width: 60%;
        }
        .profile-table th, .profile-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .profile-table th {
            background-color: #f5f5f5;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #111;
            color: white;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        .actions button {
            padding: 5px 10px;
            margin: 0 5px;
        }
        .view-btn {
            text-align: center;
            margin-top: 10px;
        }
        .view-btn a button {
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php include("../includes/header.php"); ?>

<div class="main-container">
    <h2 style="text-align:center;">Logged-in Admin Profile</h2>
    <table class="profile-table">
        <tr><th>Name:</th><td><?= htmlspecialchars($admin['name']) ?></td></tr>
        <tr><th>Email:</th><td><?= htmlspecialchars($admin['email']) ?></td></tr>
        <tr><th>Password (Hashed):</th><td><?= htmlspecialchars($admin['password']) ?></td></tr>
    </table>
    
    <div class="view-btn">
        <a href="profile_detail.php"><button>View Profile Detail</button></a>
    </div>

    <h2 style="text-align: center;">All Admin User Profiles</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Password (Hashed)</th>
            <th>Last Login</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $usersResult->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['password']) ?></td>
            <td><?= $row['login_time'] ?></td>
            <td class="actions">
                <a href="edit_admin.php?id=<?= $row['id'] ?>"><button>Edit</button></a>
                <a href="delete_admin.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this admin?')"><button>Delete</button></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
