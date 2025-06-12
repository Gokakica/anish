<?php
session_start();
include("db.php");

// Handle adding new menu item
if (isset($_POST['add'])) {
    $new = strtolower(trim($_POST['menu'])); // Normalize

    if (!empty($new)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO menus (name) VALUES (?)");
        $stmt->bind_param("s", $new);
        $stmt->execute();
    }
}

// Handle deleting a menu item
if (isset($_POST['delete'])) {
    $deleteItem = $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM menus WHERE name = ?");
    $stmt->bind_param("s", $deleteItem);
    $stmt->execute();
}

// Load menu items from DB
$menuItems = [];
$result = $conn->query("SELECT name FROM menus");
while ($row = $result->fetch_assoc()) {
    $menuItems[] = $row['name'];
}

// Logo logic
$defaultLogo = '../images/logo.png';
$uploadedLogo = '../uploads/logo.png';
$logoImage = file_exists($uploadedLogo) ? $uploadedLogo . '?v=' . time() : $defaultLogo;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel Home</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar-toggle {
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            color: white;
        }

        .sidebar {
            width: 220px;
            background-color: #1f1f1f;
            height: 100vh;
            position: fixed;
            top: 0;
            left: -220px;
            transition: 0.3s;
            z-index: 1000;
            padding-top: 60px;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 10px 20px;
            color: white;
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .nav-left img.logo {
            height: 40px;
            margin-right: 20px;
        }

        .profile img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .profile-form {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            z-index: 99;
        }

        .profile-form input,
        .profile-form button {
            margin: 5px 0;
            font-size: 12px;
        }

        .main-container {
            text-align: center;
            padding: 30px;
            background: #f2f2f2;
            margin-left: 0;
            transition: margin-left 0.3s;
        }

        .main-container.shifted {
            margin-left: 220px;
        }

        .menu-form input,
        .menu-form button {
            padding: 10px;
            margin: 10px 5px;
        }

        .horizontal-menu {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            list-style: none;
            gap: 15px;
            padding: 0;
        }

        .horizontal-menu li {
            background: #eee;
            padding: 10px 20px;
            border-radius: 8px;
            position: relative;
        }

        .horizontal-menu .delete-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: crimson;
            color: white;
            border: none;
            border-radius: 50%;
            font-weight: bold;
            width: 22px;
            height: 22px;
            line-height: 18px;
            font-size: 14px;
            cursor: pointer;
        }

        .logout {
            margin-top: 30px;
        }

        .logout a {
            background-color: crimson;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            border: none;
        }

        footer {
            margin-top: 50px;
        }
    </style>
    <script>
        function toggleProfileForm() {
            const form = document.getElementById("profileForm");
            form.style.display = form.style.display === "block" ? "none" : "block";
        }

        document.addEventListener("click", function(event) {
            const form = document.getElementById("profileForm");
            const profile = document.getElementById("profileImage");
            if (!form.contains(event.target) && !profile.contains(event.target)) {
                form.style.display = "none";
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const main = document.getElementById("mainContent");
            sidebar.classList.toggle("active");
            main.classList.toggle("shifted");
        }
    </script>
</head>
<body>

<div class="sidebar" id="sidebar">
    <a href="javascript:void(0)" onclick="toggleSidebar()" style="font-weight: bold; color: crimson;">&larr; Back</a>
    <a href="dashboard.php">Dashboard</a>

    <a href="#">Settings</a>
    <a href="#">Reports</a>
</div>

<div class="navbar">
    <div class="nav-left">
        <span class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</span>
        <img class="logo" src="<?php echo $logoImage; ?>" alt="Logo">
        <span style="font-size: 18px; font-weight: bold; margin-left: 10px;">Admin Panel</span>
    </div>
    <div class="profile">
        <img id="profileImage" src="../images/profile.png" alt="Profile" onclick="toggleProfileForm()">
        <div class="profile-form" id="profileForm">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <input type="file" name="logoImage" accept="image/*" required>
                <button type="submit" name="uploadLogo">Upload Logo</button>
            </form>
            <form action="upload.php" method="post">
                <button type="submit" name="removeLogo" style="margin-top: 5px;">Remove Logo</button>
            </form>
        </div>
    </div>
</div>

<div class="main-container" id="mainContent">
    <h2>Add Menu Item</h2>
    <form method="POST" class="menu-form">
        <input type="text" name="menu" placeholder="Enter menu name" required>
        <button type="submit" name="add">Add Menu</button>
    </form>

    <ul class="horizontal-menu">
        <?php foreach ($menuItems as $menu): ?>
            <li>
                <a href="#"><?php echo htmlspecialchars($menu); ?></a>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="delete" value="<?php echo htmlspecialchars($menu); ?>">
                    <button type="submit" class="delete-btn">&times;</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

    <h3>Welcome to the Home Page!</h3>
</div>

<?php include("../includes/footer.php"); ?>
</body>
</html>
