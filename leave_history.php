<?php
require_once '../config/database.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

$filter_type = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

if ($filter_type == 'custom' && $from_date && $to_date) {
    $query = "SELECT * FROM leave_requests WHERE user_id = $user_id AND applied_date BETWEEN '$from_date' AND '$to_date' ORDER BY applied_date DESC";
    $chart_condition = "AND applied_date BETWEEN '$from_date' AND '$to_date'";
} elseif ($filter_type == '6') {
    $six_months_ago = date('Y-m-d', strtotime('-6 months'));
    $query = "SELECT * FROM leave_requests WHERE user_id = $user_id AND applied_date >= '$six_months_ago' ORDER BY applied_date DESC";
    $chart_condition = "AND applied_date >= '$six_months_ago'";
} else {
    $query = "SELECT * FROM leave_requests WHERE user_id = $user_id ORDER BY applied_date DESC";
    $chart_condition = "";
}

$result = mysqli_query($conn, $query);

$chart_query = "SELECT leave_type, COUNT(*) as count FROM leave_requests WHERE user_id = $user_id $chart_condition GROUP BY leave_type";
$chart_result = mysqli_query($conn, $chart_query);

$leave_types = [];
$leave_counts = [];
while ($row = mysqli_fetch_assoc($chart_result)) {
    $leave_types[] = $row['leave_type'];
    $leave_counts[] = $row['count'];
}

$status_query = "SELECT status, COUNT(*) as count FROM leave_requests WHERE user_id = $user_id $chart_condition GROUP BY status";
$status_result = mysqli_query($conn, $status_query);

$statuses = [];
$status_counts = [];
while ($row = mysqli_fetch_assoc($status_result)) {
    $statuses[] = ucfirst($row['status']);
    $status_counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave History</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .filter-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-tabs .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }
        .date-range-search {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 2px solid #e0e0e0;
        }
        .date-range-search h5 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        .date-inputs {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        .date-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .date-group label {
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }
        .date-group input[type="date"] {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            min-width: 160px;
            transition: border-color 0.3s;
        }
        .date-group input[type="date"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .search-btn-group {
            display: flex;
            gap: 10px;
        }
        .btn-search {
            padding: 10px 25px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }
        .btn-search:hover {
            background: #5568d3;
        }
        .btn-clear {
            padding: 10px 25px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-clear:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Leave History</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="apply_leave.php">Apply Leave</a></li>
                <li><a href="pending_leaves.php">Pending Leaves</a></li>
                <li><a href="leave_history.php" class="active">Leave History</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
        </div>
        
        <div class="content">
            <div class="filter-container">
                <h4>Filter Leave Records</h4>
                
                <div class="filter-tabs">
                    <a href="leave_history.php" class="btn <?php echo $filter_type == 'all' ? 'btn-primary' : 'btn-info'; ?>">
                        All Records
                    </a>
                    <a href="leave_history.php?filter=6" class="btn <?php echo $filter_type == '6' ? 'btn-primary' : 'btn-info'; ?>">
                        Last 6 Months
                    </a>
                </div>
                
                <div class="date-range-search">
                    <h5>Search by Date Range</h5>
                    <form method="GET" action="leave_history.php">
                        <input type="hidden" name="filter" value="custom">
                        <div class="date-inputs">
                            <div class="date-group">
                                <label for="from_date">From Date:</label>
                                <input type="date" id="from_date" name="from_date" value="<?php echo $from_date; ?>" required>
                            </div>
                            <div class="date-group">
                                <label for="to_date">To Date:</label>
                                <input type="date" id="to_date" name="to_date" value="<?php echo $to_date; ?>" required>
                            </div>
                            <div class="search-btn-group">
                                <button type="submit" class="btn-search">Search</button>
                                <?php if ($filter_type == 'custom'): ?>
                                    <a href="leave_history.php" class="btn-clear">Clear</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="chart-grid">
                <div class="chart-container">
                    <h3>Leave Types Distribution</h3>
                    <?php if (count($leave_types) > 0): ?>
                        <canvas id="barChart"></canvas>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #666;">No data available for chart</p>
                    <?php endif; ?>
                </div>
                <div class="chart-container">
                    <h3>Leave Status Distribution</h3>
                    <?php if (count($statuses) > 0): ?>
                        <canvas id="pieChart"></canvas>
                    <?php else: ?>
                        <p style="text-align: center; padding: 40px; color: #666;">No data available for chart</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <h3>Leave History Records
                    <?php 
                    if ($filter_type == '6') {
                        echo "- Last 6 Months";
                    } elseif ($filter_type == 'custom' && $from_date && $to_date) {
                        echo "- From " . date('d-m-Y', strtotime($from_date)) . " to " . date('d-m-Y', strtotime($to_date));
                    }
                    ?>
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php 
                            mysqli_data_seek($result, 0);
                            while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                                <tr>
                                    <td><?php echo $row['leave_type']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['start_date'])); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['end_date'])); ?></td>
                                    <td><?php echo $row['days_requested']; ?></td>
                                    <td><?php echo $row['reason']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['status']; ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d-m-Y', strtotime($row['applied_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No leave records found for selected period</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        // Date validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            
            if (fromDate && toDate) {
                if (new Date(fromDate) > new Date(toDate)) {
                    e.preventDefault();
                    alert('From Date cannot be later than To Date');
                }
            }
        });
        
        <?php if (count($leave_types) > 0): ?>
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($leave_types); ?>,
                datasets: [{
                    label: 'Number of Leaves',
                    data: <?php echo json_encode($leave_counts); ?>,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)'
                    ],
                    borderColor: [
                        'rgba(102, 126, 234, 1)',
                        'rgba(118, 75, 162, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(23, 162, 184, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        <?php endif; ?>
        
        <?php if (count($statuses) > 0): ?>
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($statuses); ?>,
                datasets: [{
                    label: 'Leave Status',
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>