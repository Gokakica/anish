<?php
session_start();
include("db.php");

if (isset($_POST['delete_service_id'])) {
    $idToDelete = intval($_POST['delete_service_id']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $idToDelete);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['add_service']) || isset($_POST['addService'])) {
    $service = trim($_POST['service_name'] ?? $_POST['serviceName']);
    if (!empty($service)) {
        $stmt = $conn->prepare("INSERT INTO services (name) VALUES (?)");
        $stmt->bind_param("s", $service);
        $stmt->execute();
    }
}

$mostUsed = [];
$result = $conn->query("SELECT name FROM services ORDER BY id DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $mostUsed[] = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #eef2f3, #8e9eab);
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .section {
            margin-bottom: 40px;
        }

        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-inline input {
            padding: 10px;
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .form-inline button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-inline button:hover {
            background: #0056b3;
        }

        .stats-list {
            list-style: none;
            padding-left: 0;
        }

        .stats-list li {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-list form {
            display: inline;
        }

        .stats-list button {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .stats-list button:hover {
            background: #c0392b;
        }

        canvas {
            width: 100% !important;
            height: auto !important;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            background: #17a2b8;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .back-button:hover {
            background: #117a8b;
        }
    </style>
</head>
<body>

<div class="container">
<a href="home.php" class="back-button">← Back to Home</a>


    <div class="section">
        <h2>Add New Service</h2>
        <form method="POST" class="form-inline">
            <input type="text" name="service_name" placeholder="Enter service name" required>
            <button type="submit" name="add_service">Add Service</button>
        </form>
    </div>

    <div class="section">
        <h2>Services List</h2>
        <ul class="stats-list">
            <?php
            $result = $conn->query("SELECT id, name FROM services");
            while ($row = $result->fetch_assoc()) {
                echo '<li>' . htmlspecialchars($row['name']) . '
                    <form method="POST" action="">
                        <input type="hidden" name="delete_service_id" value="' . $row['id'] . '">
                        <button type="submit" onclick="return confirm(\'Delete this service?\')">Delete</button>
                    </form>
                </li>';
            }
            ?>
        </ul>
    </div>

    <div class="section">
        <h2>Service Usage Stats</h2>
        <canvas id="statsChart" width="400" height="200"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('statsChart').getContext('2d');
    const statsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($mostUsed); ?>,
            datasets: [{
                label: 'Service Usage',
                data: [120, 90, 70].slice(0, <?php echo count($mostUsed); ?>),
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>

</body>
</html>
