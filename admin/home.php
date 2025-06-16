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
    <!-- In <head> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>Admin Panel Home</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
:root {
    --primary-color: #203a43;
    --dark-color: #0f2027;
    --highlight-color: #2c5364;
    --bg-color: #121c24;
    --card-bg: #1e2a33;
    --text-color: #ffffff;
    --muted-text: #dddddd;
    --border-radius: 12px;
    --transition-fast: 0.3s ease-in-out;
  
}

body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: var(--bg-color);
    color: var(--text-color);
}

.sidebar-toggle {
    font-size: 24px;
    cursor: pointer;
    padding: 10px;
    color: var(--text-color);
}

.sidebar {
    width: 220px;
    background-color: var(--dark-color);
    height: 100vh;
    position: fixed;
    top: 0;
    left: -220px;
    transition: var(--transition-fast);
    z-index: 1000;
    padding-top: 60px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.2);
}

.sidebar.active {
    left: 0;
}

.sidebar a {
    display: block;
    color: var(--muted-text);
    padding: 15px 20px;
    text-decoration: none;
    font-size: 15px;
    transition: background var(--transition-fast);
}

.sidebar a:hover {
    background-color: var(--highlight-color);
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: var(--dark-color);
    padding: 12px 20px;
    color: var(--text-color);
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
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
    transition: margin-left var(--transition-fast);
    background: var(--bg-color);
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
    border-radius: var(--border-radius);
    border: 1px solid #444;
    transition: var(--transition-fast);
}

.menu-form input[type="text"] {
    background: var(--card-bg);
    color: var(--text-color);
}

.menu-form input:focus {
    border-color: var(--highlight-color);
    box-shadow: 0 0 5px var(--highlight-color);
}

.menu-form button {
    background-color: var(--primary-color);
    color: white;
    border: none;
    cursor: pointer;
}

.menu-form button:hover {
    background-color: var(--highlight-color);
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
    background: var(--card-bg);
    padding: 10px 20px;
    border-radius: var(--border-radius);
    position: relative;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: background var(--transition-fast);
    color: #ffffff !important;
    font-weight: 500;
}



.horizontal-menu li:hover {
    background: var(--highlight-color);
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

.dropdown-menu {
    background: var(--card-bg);
    border: 1px solid #333;
    border-radius: var(--border-radius);
    color: var(--muted-text);
    padding: 5px 0;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    color: var(--muted-text);
    text-decoration: none;
    transition: background var(--transition-fast);
}

.dropdown-menu a:hover {
    background-color: var(--highlight-color);
    color: white;
}

footer {
    margin-top: 50px;
    text-align: center;
    font-size: 14px;
    color: #ccc;
    background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
    padding: 40px 20px 20px;
    font-family: 'Segoe UI', sans-serif;
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
