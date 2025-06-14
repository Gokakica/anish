<?php
session_start();
include("db.php");

// Check session
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['id'];

// Handle Profile Photo Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload logic
    if (isset($_POST['uploadPfp']) && isset($_FILES['profilePhoto'])) {
        $ext = strtolower(pathinfo($_FILES['profilePhoto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $newFilename = "profile_" . $adminId . "." . $ext;
            $uploadPath = "../uploads/" . $newFilename;
            
            if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $uploadPath)) {
                $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                $stmt->bind_param("si", $newFilename, $adminId);
                $stmt->execute();

                header("Location: profile.php");
                exit();
            } else {
                echo "<script>alert('Failed to upload image.');</script>";
            }
        } else {
            echo "<script>alert('Only JPG, JPEG, PNG formats allowed.');</script>";
        }
    }
    if (isset($_POST['uploadCardPhoto'])) {
        $cardPhoto = $_FILES['cardPhoto'];
    
        if ($cardPhoto['error'] === 0) {
            $ext = pathinfo($cardPhoto['name'], PATHINFO_EXTENSION);
            $fileName = "card_" . $adminId . "." . $ext;
            $filePath = "../uploads/" . $fileName;
    
            if (move_uploaded_file($cardPhoto['tmp_name'], $filePath)) {
                $stmt = $conn->prepare("UPDATE users SET card_photo = ? WHERE id = ?");
                $stmt->bind_param("si", $fileName, $adminId);
                $stmt->execute();
            } else {
                echo "<script>alert('Failed to upload card photo.');</script>";
            }
        }
    }
    

    // Remove logic
    if (isset($_POST['removePfp'])) {
        $pattern = glob("../uploads/profile_" . $adminId . ".*");
        foreach ($pattern as $file) {
            unlink($file);
        }

        $stmt = $conn->prepare("UPDATE users SET profile_photo = NULL WHERE id = ?");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();

        header("Location: profile.php");
        exit();
    }
}

// Fetch current admin details
$stmt = $conn->prepare("SELECT name, email, password, profile_photo, login_count FROM users WHERE id = ?");

$stmt->bind_param("i", $adminId);
$stmt->execute();
$profileResult = $stmt->get_result();

if ($profileResult->num_rows === 0) {
    echo "Admin not found.";
    exit();
}
$admin = $profileResult->fetch_assoc();


// ✅ Add this block here to fetch login history
$loginHistory = [];
$historyStmt = $conn->prepare("SELECT login_time FROM login_history WHERE user_id = ? ORDER BY login_time DESC");
$historyStmt->bind_param("i", $adminId);
$historyStmt->execute();
$historyResult = $historyStmt->get_result();

while ($hrow = $historyResult->fetch_assoc()) {
    $loginHistory[] = date("d M Y, h:i A", strtotime($hrow['login_time']));
}


// Fetch all admin users
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$usersResult = $stmt->get_result();

if (!$usersResult) {
    die("Query failed: " . $conn->error);
}

// Determine profile photo path
$photoPath = (!empty($admin['profile_photo']) && file_exists("../uploads/" . $admin['profile_photo']))
    ? "../uploads/" . $admin['profile_photo']
    : "../images/default.jpg";


    // After successful login
    $_SESSION['id'];

$currentLoginTime = date("Y-m-d H:i:s");

$updateQuery = "UPDATE users SET login_time = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("si", $currentLoginTime, $adminId);
$stmt->execute();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .main-container {
        max-width: 1200px;
        margin: 30px auto;
        background: #fff;
        padding: 30px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    h2 {
        text-align: center;
        color: #343a40;
        margin-bottom: 30px;
    }

    .pfp-container {
        text-align: center;
        margin-bottom: 30px;
    }

    .pfp-container img {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.15);
    }

    .pfp-container form {
        margin-top: 12px;
    }

    .pfp-container input[type="file"] {
        margin-bottom: 10px;
    }

    .pfp-container button {
        padding: 6px 14px;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }

    .pfp-container button[name="uploadPfp"] {
        background-color: #28a745;
        color: white;
    }

    .pfp-container button[name="removePfp"] {
        background-color: #dc3545;
        color: white;
    }

    .profile-table {
        width: 100%;
        margin-bottom: 40px;
        border-collapse: collapse;
    }

    .profile-table th, .profile-table td {
        padding: 14px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        font-size: 16px;
    }

    .profile-table th {
        background-color: #e9ecef;
        color: #343a40;
    }

    .view-btn {
        text-align: center;
        margin-bottom: 40px;
    }

    .view-btn button {
        padding: 10px 18px;
        font-size: 15px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    table {
    width: 95%;
    margin: 30px auto;
    border-collapse: collapse;
    table-layout: auto;
    word-wrap: break-word;
}
th, td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    vertical-align: middle;
}
th {
    background-color: #111;
    color: white;
}


tr:hover {
    background-color: #f9f9f9;
}

th:nth-child(4), td:nth-child(4) {
    width: 20%;
}

th:nth-child(5), td:nth-child(5) {
    width: 20%;
}

.actions {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.actions a button {
    padding: 6px 14px;
    font-size: 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.actions a:first-child button {
    background-color: #17a2b8;
    color: white;
}

.actions a:last-child button {
    background-color: #dc3545;
    color: white;
}

.admin-table {
    width: 90%;
    margin: 30px auto;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}

.admin-table th, .admin-table td {
    padding: 16px 20px;
    text-align: left;
    border-bottom: 1px solid #eee;
    font-size: 16px;
}

.admin-table th {
    background-color: #343a40;
    color: white;
    font-weight: 600;
}

.admin-table tr:hover {
    background-color: #f5f5f5;
}

.admin-table td:last-child {
    font-family: monospace;
    font-size: 14px;
    word-break: break-all;
}



    .back-button {
        display: block;
        margin: 40px auto 0;
        padding: 10px 18px;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .timestamp-cell {
    white-space: normal; /* Allow multiple lines */
    line-height: 1.6;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
}

.timestamp-entry {
    white-space: nowrap; /* Prevent line break */
    display: block;
    margin-bottom: 5px;
}

    
</style>


</head>
<body>

<div class="main-container">
    <h2 style="text-align:center;">Logged-in Admin Profile</h2>
    
    <div class="pfp-container">
        <img src="<?= $photoPath ?>" alt="Profile Photo">
        <!-- Upload -->
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="profilePhoto" required>
            <br>
            <button type="submit" name="uploadPfp" style="background-color: #28a745; color: white;">Upload PFP</button>
        </form>
        <!-- Remove -->
        <form method="POST">
            <button type="submit" name="removePfp" style="background-color: #dc3545; color: white;">Remove Photo</button>
        </form>
    </div>

    <table class="profile-table">
        <tr>
            <th style="color: black;">Name:</th>
            <td style="color: black;"><?= htmlspecialchars($admin['name']) ?></td>
        </tr>
        <tr>
            <th style="color: black;">Email:</th>
            <td style="color: black;"><?= htmlspecialchars($admin['email']) ?></td>
        </tr>
        <tr>
            <th style="color: black;">Password (Hashed):</th>
            <td style="color: black;"><?= htmlspecialchars($admin['password']) ?></td>
        </tr>
    </table>
    
    <div class="view-btn">
        <a href="profile_detail.php"><button>View Profile Detail</button></a>
    </div>
    <h2 style="text-align: center;">Your Login History</h2>

    <table>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Password (Hashed)</th>
    <th>Last Login</th>
    <th>Total Logins</th>
    <th>Login Timestamp</th>
</tr>

<?php while ($row = $usersResult->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['password']) ?></td>
    <td><?= date("d M Y, h:i A", strtotime($row['login_time'])) ?></td>
    <td><?= htmlspecialchars($row['login_count']) ?></td>
    <td class="timestamp-cell">
    <?php
    $userId = $row['id'];
    $historyStmt = $conn->prepare("SELECT login_time FROM login_history WHERE user_id = ? ORDER BY login_time DESC");
    $historyStmt->bind_param("i", $userId);
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();
    while ($historyRow = $historyResult->fetch_assoc()) {
        echo '<div class="timestamp-entry">' . date("d M Y, h:i A", strtotime($historyRow['login_time'])) . '</div>';
    }
    ?>
</td>

</tr>
<?php endwhile; ?>
</table>



    <button class="back-button" onclick="history.back()">← Back</button>
</div>

<?php include("../includes/footer.php"); ?>

</body>
</html>
