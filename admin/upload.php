<?php
// Upload Logo
if (isset($_POST['uploadLogo'])) {
    if ($_FILES['logoImage']['error'] === 0) {
        move_uploaded_file($_FILES['logoImage']['tmp_name'], '../uploads/logo.png');
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
