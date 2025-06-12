<?php
session_start();
include("db.php");
if (isset($_POST['delete_service_id'])) {
    $idToDelete = intval($_POST['delete_service_id']);
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $idToDelete);
    $stmt->execute();
    // Optional: Refresh to prevent resubmission
    header("Location: dashboard.php");
    exit();
}


// Handle service addition
if (isset($_POST['add_service']) || isset($_POST['addService'])) {
    $service = trim($_POST['service_name'] ?? $_POST['serviceName']);
    if (!empty($service)) {
        $stmt = $conn->prepare("INSERT INTO services (name) VALUES (?)");
        $stmt->bind_param("s", $service);
        $stmt->execute();
    }
}

// Fetch services for "Most Used" section (you can enhance this with actual usage counts later)
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
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f2f2f2; }
        .container { padding: 30px; }
        h2 { margin-bottom: 10px; }

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-inline input, .form-inline button {
            padding: 10px;
            margin-right: 10px;
        }

        .stats-list li {
            background: #eee;
            margin: 8px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .stats-list form {
    display: inline;
}
.stats-list button {
    background: #e74c3c;
    border: none;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 10px;
}
.stats-list button:hover {
    background: #c0392b;
}

        canvas {
            max-width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="section">
        <h2>Add Services</h2>
        <form method="POST" class="form-inline">
            <input type="text" name="service_name" placeholder="Enter service name" required>
            <button type="submit" name="add_service">Add</button>
        </form>
    </div>

    <div class="section">
    <h2>Services List</h2>
    <ul class="stats-list">
        <?php
        $services = [];
        $result = $conn->query("SELECT id, name FROM services");
        while ($row = $result->fetch_assoc()) {
            echo '<li>' . htmlspecialchars($row['name']) . '
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="delete_service_id" value="' . $row['id'] . '">
                    <button type="submit" onclick="return confirm(\'Delete this service?\')">Delete</button>
                </form>
            </li>';
        }
        ?>
    </ul>
</div>
<div class="section">
    <h2>View Statistics</h2>
    <canvas id="statsChart" width="400" height="200"></canvas>
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
                y: { beginAtZero: true }
            }
        }
    });
</script>

</body>
</html>
