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

// Profile image logic from DB
$defaultProfile = '../images/profile.png';
$profileImage = $defaultProfile;

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT profile_photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!empty($user['profile_photo']) && file_exists("../uploads/" . $user['profile_photo'])) {
        $profileImage = "../uploads/" . $user['profile_photo'] . '?v=' . time();
    }
}
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
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
            transition: 0.3s ease;
            z-index: 1000;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.2);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #444;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2c2c2c;
            padding: 12px 20px;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .nav-left img.logo {
            height: 42px;
            margin-right: 15px;
        }

        .profile img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #fff;
        }

        .main-container {
            padding: 30px;
            margin-left: 0;
            transition: margin-left 0.3s ease;
            background: #ffffff;
            min-height: calc(100vh - 60px);
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        }

        .main-container.shifted {
            margin-left: 220px;
        }

        .menu-form {
            margin: 20px auto;
            max-width: 600px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .menu-form input, .menu-form button {
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            transition: all 0.2s ease;
        }

        .menu-form button {
            background-color: crimson;
            color: white;
            border: none;
            cursor: pointer;
        }

        .menu-form button:hover {
            background-color: #d32f2f;
        }

        .horizontal-menu {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            list-style: none;
            gap: 15px;
            padding: 0;
            margin: 30px auto;
        }

        .horizontal-menu li {
            background: #f1f1f1;
            padding: 10px 20px;
            border-radius: 8px;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: background 0.3s ease;
        }

        .horizontal-menu li:hover {
            background: #e9e9e9;
        }

        .horizontal-menu .delete-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: crimson;
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }

        footer {
            margin-top: 50px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>

    <script>
        function toggleProfileForm() {
            const form = document.getElementById("profileForm");
            form.style.display = form.style.display === "block" ? "none" : "block";
        }

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            const main = document.getElementById("mainContent");
            sidebar.classList.toggle("active");
            main.classList.toggle("shifted");
        }

        function toggleDropdown() {
            const menu = document.getElementById('dropdown-menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        window.addEventListener('click', function (e) {
            const dropdown = document.getElementById("dropdown-menu");
            const profileDropdown = document.querySelector('.profile-dropdown');
            if (!profileDropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
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
    <div class="profile-dropdown" style="display: flex; align-items: center; gap: 10px; position: relative;">
        <button onclick="toggleDropdown()" id="profile-toggle" style="padding: 6px 12px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">ADMIN</button>
        <img src="<?php echo $profileImage; ?>" alt="Profile Photo" class="icon" style="cursor: pointer; height: 32px;" onclick="toggleDropdown()">

        <div id="dropdown-menu" class="dropdown-menu" style="display: none; position: absolute; top: 40px; right: 0; background-color: white; border: 1px solid #ccc; border-radius: 5px; z-index: 10; min-width: 180px;">
            <a href="profile_detail.php">Profile Details</a>
            <a href="change_password.php">Change Password</a>
            <a href="profile.php">History</a>
           

            <a href="logout.php" style="color: red; border-top: 1px solid #eee;">Logout</a>
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

    <h3>Welcome to the ADMIN PAGE!</h3>
</div>

<script>
    document.getElementById("pfpForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("upload.php", {
            method: "POST",
            body: formData,
        })
        .then(res => res.text())
        .then(response => {
            console.log(response);
            if (response.includes("✅ Upload successful")) {
    const profileImg = document.querySelector(".profile-dropdown img");
    profileImg.src = profileImg.src.split('?')[0] + '?v=' + new Date().getTime();
    alert("Profile picture updated!");
}
        })
        .catch(err => alert("Upload failed. Error: " + err));
    });

    document.getElementById("cardPhotoForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("upload.php", {
        method: "POST",
        body: formData,
    })
    .then(res => res.text())
    .then(response => {
        console.log(response);
        if (response.includes("✅ Card image uploaded")) {
            alert("Profile Card Photo updated!");
        } else {
            alert("Card upload error:\n" + response);
        }
    })
    .catch(err => alert("Upload failed. Error: " + err));
});

</script>



<?php include("../includes/footer.php"); ?>
</body>
</html>
