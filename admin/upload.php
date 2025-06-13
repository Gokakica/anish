<?php
// Upload Logo
if (isset($_FILES['profilePhoto'])) {

    if ($_FILES['logoImage']['error'] === 0) {
        move_uploaded_file($_FILES['logoImage']['tmp_name'], '../uploads/logo.png');
    }
}
// ✅ Upload Profile Photo
// Upload Profile Photo

// Handle profile picture upload


ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profilePhoto'])) {
        $error = $_FILES['profilePhoto']['error'];
        echo "🔍 Error code: $error<br>";

        if ($error === 0) {
            $ext = strtolower(pathinfo($_FILES['profilePhoto']['name'], PATHINFO_EXTENSION));
            echo "🔍 File extension: $ext<br>";

            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $uploadPath = "../uploads/profile.png"; // ✅ Ensure folder is writable

                if (move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $uploadPath)) {
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
    } else {
        echo "❌ No file uploaded.";
    }
}






// Remove Logo
if (isset($_POST['removeLogo'])) {
    $logoPath = '../uploads/logo.png';
    if (file_exists($logoPath)) {
        unlink($logoPath);
    }
}

// Add Menu
if (isset($_POST['addMenu'])) {
    $menuFile = '../menu.json'; // Store in root-level menu.json
    $newMenu = trim($_POST['menuName']);

    if (!empty($newMenu)) {
        $menuItems = file_exists($menuFile)
            ? json_decode(file_get_contents($menuFile), true)
            : [];

        // Avoid duplicates
        if (!in_array($newMenu, $menuItems)) {
            $menuItems[] = $newMenu;
            file_put_contents($menuFile, json_encode($menuItems, JSON_PRETTY_PRINT));
        }
    }
}

// Redirect back
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>

<!-- ✅ This HTML must be outside PHP -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="profilePhoto" required>
    <button type="submit">Upload Test Image</button>
</form>