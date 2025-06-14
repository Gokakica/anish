<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include("db.php");

// ✅ DEBUGGING
echo "🟢 uploadPfp.php Loaded<br>";


// ✅ Upload Profile Photo (final version)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePhoto'])) {
    echo "🚀 profilePhoto upload triggered<br>";

    $error = $_FILES['profilePhoto']['error'];
    echo "🔍 Error code: $error<br>";

    if ($error === 0) {
        $ext = strtolower(pathinfo($_FILES['profilePhoto']['name'], PATHINFO_EXTENSION));
        echo "🔍 File extension: $ext<br>";

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (!isset($_SESSION['id'])) {
                echo "❌ User not logged in.";
                exit;
            }

            $userId = $_SESSION['id'];
            echo "👤 User ID: $userId<br>";

            $newFileName = 'admin_' . $userId . '.' . $ext;
            $uploadPath = "../uploads/" . $newFileName;

            echo "📁 Upload path: $uploadPath<br>";

            if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $uploadPath)) {
                $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                $stmt->bind_param("si", $newFileName, $userId);
                $stmt->execute();
                echo "✅ Upload successful!";
            } else {
                echo "❌ Failed to move uploaded file. Check file/folder permissions.";
            }
        } else {
            echo "❌ Invalid file type: $ext";
        }
    } else {
        echo "❌ PHP Upload Error: $error";
    }
}

// ✅ Upload Profile Card Photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cardPhoto'])) {
    echo "🚀 cardPhoto upload triggered<br>";

    $error = $_FILES['cardPhoto']['error'];
    echo "🔍 Card Error code: $error<br>";

    if ($error === 0) {
        $ext = strtolower(pathinfo($_FILES['cardPhoto']['name'], PATHINFO_EXTENSION));
        echo "🔍 Card extension: $ext<br>";

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (!isset($_SESSION['id'])) {
                echo "❌ User not logged in.";
                exit;
            }

            $userId = $_SESSION['id'];
            echo "👤 User ID: $userId<br>";

            $cardFileName = 'card_' . $userId . '.' . $ext;
            $cardUploadPath = "../uploads/" . $cardFileName;

            echo "📁 Card Upload path: $cardUploadPath<br>";

            if (move_uploaded_file($_FILES['cardPhoto']['tmp_name'], $cardUploadPath)) {
                $stmt = $conn->prepare("UPDATE users SET card_photo = ? WHERE id = ?");
                $stmt->bind_param("si", $cardFileName, $userId);
                $stmt->execute();
                echo "✅ Card image uploaded!";
            } else {
                echo "❌ Failed to upload card image.";
            }
        } else {
            echo "❌ Invalid file type for card.";
        }
    } else {
        echo "❌ Card upload error code: $error";
    }
}

// Remove Logo
if (isset($_POST['removeLogo'])) {
    echo "🗑️ Removing logo<br>";
    $logoPath = '../uploads/logo.png';
    if (file_exists($logoPath)) {
        unlink($logoPath);
        echo "✅ Logo removed<br>";
    } else {
        echo "ℹ️ Logo file does not exist<br>";
    }
}

// Add Menu
if (isset($_POST['addMenu'])) {
    echo "➕ Adding menu<br>";
    $menuFile = '../menu.json';
    $newMenu = trim($_POST['menuName']);

    if (!empty($newMenu)) {
        $menuItems = file_exists($menuFile)
            ? json_decode(file_get_contents($menuFile), true)
            : [];

        if (!in_array($newMenu, $menuItems)) {
            $menuItems[] = $newMenu;
            file_put_contents($menuFile, json_encode($menuItems, JSON_PRETTY_PRINT));
            echo "✅ Menu added: $newMenu<br>";
        } else {
            echo "⚠️ Menu already exists<br>";
        }
    } else {
        echo "❌ Empty menu name<br>";
    }
}

// Optional: comment this during testing to see outputs
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>

<!-- ✅ This HTML must be outside PHP -->
<!-- 👇 This is for Profile Card Upload -->
<form method="POST" action="upload.php" enctype="multipart/form-data">

    <label>Profile Photo</label>
    <input type="file" name="profilePhoto" required>
    <button type="submit">Upload Profile Photo</button>
</form>
