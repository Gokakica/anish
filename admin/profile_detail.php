<?php
session_start();
include("db.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['id'];

if (isset($_POST['uploadPfp']) && isset($_FILES['profilePhoto'])) {
    $file = $_FILES['profilePhoto'];

    if ($file['error'] === 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = "profile_" . $adminId . "." . $ext;
        $uploadPath = "../uploads/" . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
            $stmt->bind_param("si", $fileName, $adminId);
            $stmt->execute();
        } else {
            echo "<script>alert('Failed to move uploaded file.');</script>";
        }
    } else {
        echo "<script>alert('File upload error.');</script>";
    }
}

if (isset($_POST['removePfp'])) {
    $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!empty($data['profile_photo'])) {
        $filePath = "../uploads/" . $data['profile_photo'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $stmt = $conn->prepare("UPDATE users SET profile_photo = NULL WHERE id = ?");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
    }
}

$stmt = $conn->prepare("SELECT id, name, email, profile_photo, card_photo FROM users WHERE id = ?");
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(to right, #e0f7fa, #fff3e0);
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        .profile-card {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease;
        }
        .profile-card:hover {
            transform: translateY(-5px);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 26px;
        }
        .photo-section {
            text-align: center;
            margin-bottom: 25px;
        }
        .profile-photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            margin-bottom: 15px;
            transition: 0.3s ease;
        }
        .profile-photo:hover {
            scale: 1.05;
        }
        input[type="file"] {
            padding: 6px;
            border: none;
            background-color: #f5f5f5;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            font-size: 15px;
        }
        .btn-upload {
            background-color: #28a745;
            color: white;
            margin-bottom: 8px;
        }
        .btn-upload:hover {
            background-color: #218838;
        }
        .btn-remove {
            background-color: #dc3545;
            color: white;
        }
        .btn-remove:hover {
            background-color: #c82333;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }
        .info-row .label {
            font-weight: bold;
            color: #444;
        }
        .edit-btn {
            text-align: center;
            margin-top: 30px;
        }
        .edit-btn a button {
            background-color: #007bff;
            color: white;
            margin: 5px;
        }
        .edit-btn a button:hover {
            background-color: #0056b3;
        }
        .back-btn {
            background-color: #6c757d !important;
        }
        .back-btn:hover {
            background-color: #5a6268 !important;
        }

        @media screen and (max-width: 540px) {
            .profile-card {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="profile-card">
<h2>Your Profile</h2>
<p style="text-align:center; color:#555; font-size:16px;">
    Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>
</p>

    <?php
    $photoPath = (!empty($admin['profile_photo']) && file_exists("../uploads/" . $admin['profile_photo']))
        ? "../uploads/" . $admin['profile_photo']
        : "../images/default.jpg";
    ?>

    <div class="photo-section">
        <img src="<?= $photoPath ?>" alt="Profile Photo" class="profile-photo">

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="profilePhoto" accept="image/*" required>
            <br>
            <button type="submit" name="uploadPfp" class="btn-upload">Upload PFP</button>
        </form>

        <form method="POST">
            <button type="submit" name="removePfp" class="btn-remove">Remove Photo</button>
        </form>
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

    <!-- Buttons -->
    <div class="edit-btn">
        <a href="edit_admin.php?id=<?= $admin['id'] ?>"><button>Edit Profile</button></a>
        <a href="home.php"><button class="back-btn">Back</button></a>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
