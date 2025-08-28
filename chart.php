<?php
session_start();
include('db.php');

// Fetch expenses from DB
$stmt = $pdo->query("SELECT date, category, amount FROM transactions ORDER BY date ASC");
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Expense Analytics</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }
    /* Sidebar */
    .sidebar {
        height: 100%;
        width: 220px;
        position: fixed;
        background-color: #111;
        padding-top: 20px;
        color: #fff;
    }
    .sidebar h2 {
        text-align: center;
        color: #ff69b4;
    }
    .sidebar a {
        display: block;
        padding: 12px;
        color: #fff;
        text-decoration: none;
        margin: 5px 0;
        border-left: 3px solid transparent;
    }
    .sidebar a:hover {
        background-color: #222;
        border-left: 3px solid #ff69b4;
    }
    /* Main Content */
    .container {
        margin-left: 240px;
        padding: 20px;
    }
    h1 {
        color: #ff69b4;
    }
    .chart-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
        width: 100%; /* Full width charts */
    }
    canvas {
        background: #fff;
        border-radius: 8px;
        padding: 10px;
        max-height: 350px;
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Daily Expense Tracker</h2>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="add_expense.php"><i class="fas fa-plus-circle"></i> Add Expenses</a>
    <a href="manage_expenses.php"><i class="fas fa-tasks"></i> Manage Expenses</a>
    <a href="expense_report.php"><i class="fas fa-file-alt"></i> Expense Report</a>
    <a href="expense_analytics.php"><i class="fas fa-chart-pie"></i> Expense Analytics</a>
    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="container">
    <h1  style="color:black"><i class="fas fa-chart-line"></i> Expense Analytics</h1>

    <div class="chart-card">
        <h2 style="color:#ff69b4;">📈 Expenses Over Time</h2>
        <canvas id="lineChart"></canvas>
    </div>

    <div class="chart-card">
        <h2 style="color:#ff69b4;">📊 Expenses by Category</h2>
        <canvas id="barChart"></canvas>
    </div>

    <div class="chart-card">
        <h2 style="color:#ff69b4;">🥧 Category Distribution</h2>
        <canvas id="pieChart"></canvas>
    </div>
</div>

<script>
const expenses = <?php echo json_encode($expenses); ?>;

// Prepare data
const dates = [...new Set(expenses.map(e => e.date))];
const dateTotals = dates.map(d => expenses.filter(e => e.date === d)
                    .reduce((sum, e) => sum + parseFloat(e.amount), 0));

const categories = [...new Set(expenses.map(e => e.category))];
const categoryTotals = categories.map(c => expenses.filter(e => e.category === c)
                    .reduce((sum, e) => sum + parseFloat(e.amount), 0));

// Line Chart
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Total Expense',
            data: dateTotals,
            borderColor: '#ff69b4',
            backgroundColor: 'rgba(255,105,180,0.2)',
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#ff69b4'
        }]
    },
    options: {
        plugins: { legend: { labels: { color: '#000' } } },
        scales: {
            x: { ticks: { color: '#000' } },
            y: { ticks: { color: '#000' } }
        }
    }
});

// Bar Chart
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: categories,
        datasets: [{
            label: 'Expense by Category',
            data: categoryTotals,
            backgroundColor: '#ff69b4'
        }]
    },
    options: {
        plugins: { legend: { labels: { color: '#000' } } },
        scales: {
            x: { ticks: { color: '#000' } },
            y: { ticks: { color: '#000' } }
        }
    }
});

// Pie Chart
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: categories,
        datasets: [{
            label: 'Category Distribution',
            data: categoryTotals,
            backgroundColor: [
                '#ff69b4',
                '#ff8ec3',
                '#ffb6d5',
                '#ffcee5',
                '#ffdff0'
            ]
        }]
    },
    options: {
        plugins: { legend: { labels: { color: '#000' } } }
    }
});
</script>

</body>
</html>
