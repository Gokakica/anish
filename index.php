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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f5f7fa;
            color: #333;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #1f2937;
            padding: 15px 30px;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .nav-left img.logo {
            height: 50px;
            margin-right: 25px;
        }

        .nav-left ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-left a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .nav-left a:hover {
            color: #60a5fa;
        }

        .profile img {
            height: 45px;
            width: 45px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
        }

        .sidebar-toggle {
            font-size: 28px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            margin-right: 20px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: -240px;
            width: 220px;
            height: 100%;
            background-color: #111827;
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
            padding: 15px 25px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #1f2937;
        }

        .main-container {
            text-align: center;
            padding: 50px 20px;
            background: #ffffff;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            margin: 40px auto;
            width: 90%;
            border-radius: 16px;
        }

        .main-container h2 {
            font-size: 32px;
            color: #111827;
        }

        .services-section {
            padding: 60px 20px;
            background: #f0f4f8;
            text-align: center;
        }

        .services-title {
            font-size: 36px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 40px;
        }

        .services-grid {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 280px;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .service-name {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
        }

        .service-description {
            color: #6b7280;
            margin-top: 12px;
            font-size: 15px;
        }

        footer {
            margin-top: 80px;
            padding: 30px;
            text-align: center;
            color: #6b7280;
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

<!-- Services Section -->
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
