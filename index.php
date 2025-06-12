<?php
session_start();
include("admin/db.php");

// Fetch menu items
$menuItems = [];
$result = $conn->query("SELECT name FROM menus");
while ($row = $result->fetch_assoc()) {
    $menuItems[] = $row['name'];
}

// Fetch services
$services = [];
$result = $conn->query("SELECT name FROM services");
while ($row = $result->fetch_assoc()) {
    $services[] = $row['name'];
}

// Logo logic
$defaultLogo = 'images/logo.png';
$uploadedLogo = 'uploads/logo.png';
$logoImage = file_exists($uploadedLogo) ? $uploadedLogo . '?v=' . time() : $defaultLogo;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client View</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
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

        .nav-left ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .nav-left li {
            margin: 0 10px;
        }

        .nav-left a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-left a:hover {
            text-decoration: underline;
        }

        .profile {
            position: relative;
        }

        .profile img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .sidebar-toggle {
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            margin-right: 15px;
            cursor: pointer;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 220px;
            height: 100%;
            background-color: #1f1f1f;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
            z-index: 1000;
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

        .main-container {
            text-align: center;
            padding: 30px;
            background: #f2f2f2;
        }

        footer {
            margin-top: 50px;
        }

        /* New modern services section */
        .services-section {
            padding: 40px;
            background: #f9f9f9;
            text-align: center;
        }

        .services-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .services-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .service-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 250px;
            transition: transform 0.2s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .service-name {
            font-size: 20px;
            font-weight: bold;
        }

        .service-description {
            color: #555;
            margin-top: 8px;
            font-size: 14px;
        }
    </style>

    <script>
        function toggleProfileForm() {
            const form = document.getElementById("profileForm");
            if (form) {
                form.style.display = form.style.display === "block" ? "none" : "block";
            }
        }

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
        }

        document.addEventListener("click", function (event) {
            const form = document.getElementById("profileForm");
            const profile = document.getElementById("profileImage");
            if (form && !form.contains(event.target) && !profile.contains(event.target)) {
                form.style.display = "none";
            }
        });
    </script>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <a href="javascript:void(0)" onclick="toggleSidebar()" style="font-weight: bold; color: crimson;">← Back</a>
    <a href="index.php">Home</a>
    <a href="#">About Us</a>
    <a href="#">Contact</a>
</div>

<!-- Navbar -->
<div class="navbar">
    <div class="nav-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
        <img class="logo" src="<?php echo $logoImage; ?>" alt="Logo">
        <ul>
            <?php foreach ($menuItems as $item): ?>
                <li><a href="#"><?php echo htmlspecialchars($item); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="profile">
        <img id="profileImage" src="images/profile.png" alt="Profile" onclick="toggleProfileForm()">
    </div>
</div>

<!-- Main Content -->
<div class="main-container">
    <h2>Welcome to the Client View!</h2>
</div>

<!-- Modern Services Section -->
<div class="services-section">
    <div class="services-title">Our Services</div>
    <div class="services-grid">
        <?php if (empty($services)): ?>
            <div class="service-card">
                <div class="service-icon">😔</div>
                <div class="service-name">No services</div>
                <div class="service-description">We haven’t added services yet.</div>
            </div>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <?php
                        $icon = '✨';
                        if (stripos($service, 'host') !== false) $icon = '🔧';
                        elseif (stripos($service, 'seo') !== false) $icon = '🔍';
                        elseif (stripos($service, 'analytic') !== false) $icon = '📊';
                        ?>
                        <?= $icon ?>
                    </div>
                    <div class="service-name"><?= htmlspecialchars($service) ?></div>
                    <div class="service-description">
                        <?php
                        if (stripos($service, 'host') !== false)
                            echo "Reliable hosting with 99.9% uptime";
                        elseif (stripos($service, 'seo') !== false)
                            echo "Boost your Google ranking easily";
                        elseif (stripos($service, 'analytic') !== false)
                            echo "Visualize your performance easily";
                        else
                            echo "Professional and trusted service";
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
</body>
</html>
