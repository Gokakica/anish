<?php
session_start();
include("db.php");

// Check session
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");


if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $adminId);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Admin not found.";
    exit();
}

$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Details</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }
        .profile-card {
            width: 400px;
            margin: 60px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-row .label {
            font-weight: bold;
            color: #555;
        }
        .edit-btn {
            text-align: center;
            margin-top: 20px;
        }
        .edit-btn a button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .edit-btn a button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>




<<div class="profile-card">
    <h2>Your Profile</h2>

    <?php
    // Use default if no profile photo set
    $photoPath = !empty($admin['profile_photo']) ? "../uploads/" . $admin['profile_photo'] : "../images/default.jpg";
    ?>

    <!-- Profile Photo -->
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="<?= $photoPath ?>" alt="Profile Photo"
             style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 2px solid #007bff;">
    </div>

    <!-- Profile Info -->
    <div class="info-row">
        <div class="label">Name:</div>
        <div><?= htmlspecialchars($admin['name']) ?></div>
    </div>
    <div class="info-row">
        <div class="label">Email:</div>
        <div><?= htmlspecialchars($admin['email']) ?></div>
    </div>
    <div class="info-row">
        <div class="label">Password:</div>
        <div>••••••••</div>
    </div>

    <!-- Action Buttons -->
    <div class="edit-btn">
        <a href="edit_admin.php?id=<?= $admin['id'] ?>"><button>Edit Profile</button></a>
        <br><br>
        <a href="home.php"><button style="background-color: #6c757d;">Back</button></a>
    </div>
</div>


<?php include("../includes/footer.php"); ?>

</body>
</html>
