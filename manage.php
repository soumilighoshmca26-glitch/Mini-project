<?php
// =====================
// Database connection
// =====================
$servername = "localhost";
$username   = "root"; // XAMPP default user
$password   = "";     // XAMPP default password
$dbname     = "expense_tracker"; // Change if different

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// =====================
// Handle delete request
// =====================
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = mysqli_prepare($conn, "DELETE FROM transactions WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // preserve current filter after delete
    $back = 'manage.php';
    if (!empty($_GET['filter'])) { $back .= '?filter=' . urlencode($_GET['filter']); }
    header("Location: $back");
    exit();
}

// =====================
// Optional filter from dashboard cards
// =====================
$filter = isset($_GET['filter']) ? strtolower($_GET['filter']) : '';

$today       = date('Y-m-d');
$yesterday   = date('Y-m-d', strtotime('-1 day'));
$last7       = date('Y-m-d', strtotime('-7 days'));
$last30      = date('Y-m-d', strtotime('-30 days'));
$currentYear = date('Y');

$whereClause = "";   // default: no filter

switch ($filter) {
    case 'todaysexpense':
    case 'today':
        $whereClause = "WHERE date = '$today'";
        break;

    case 'yesterdaysexpense':
    case 'yesterday':
        $whereClause = "WHERE date = '$yesterday'";
        break;

    case 'last7days':
    case 'last7daysexpense':
        $whereClause = "WHERE date BETWEEN '$last7' AND '$today'";
        break;

    case 'last30days':
    case 'last30daysexpense':
        $whereClause = "WHERE date BETWEEN '$last30' AND '$today'";
        break;

    case 'currentyear':
    case 'currentyearexpense':
        $whereClause = "WHERE YEAR(date) = $currentYear";
        break;

    case 'totalexpense':
    default:
        $whereClause = ""; // show all
        break;
}

// =====================
// Fetch expenses (filtered if applicable)
// =====================
$sql = "SELECT id, description, category, amount, date 
        FROM transactions 
        $whereClause
        ORDER BY date DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Expenses</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            background: #f4f4f4;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #1c1c1c;
            color: white;
            height: 100vh;
            padding: 20px;
        }
        .sidebar h2 {
            color: #ff69b4;
            text-align: center;
            margin-bottom: 10px;
        }
        .sidebar .user-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .user-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .sidebar ul { list-style: none; }
        .sidebar ul li {
            padding: 12px 10px;
            margin: 5px 0;
            background: #2c2c2c;
            border-left: 4px solid transparent;
            cursor: pointer;
        }
        .sidebar ul li:hover, .sidebar ul li.active {
            border-left: 4px solid #ff69b4;
            background: #3a3a3a;
        }
        .logout-link { text-decoration: none; color: white; }
        .logout-link .nav-button {
            background-color: #333;
            padding: 10px 15px;
            margin-top: 5px;
            display: block;
            border-left: 5px solid transparent;
            transition: all 0.3s;
        }
        .logout-link .nav-button:hover {
            background-color: #444;
            border-left: 5px solid #ff0066;
        }
        .main {
            flex: 1;
            padding: 20px;
            background-color: white;
        }
        h1 { margin-bottom: 20px; }
        .filter-chip {
            display:inline-block;
            margin-bottom:12px;
            padding:6px 10px;
            border-radius:16px;
            background:#ffe6f3;
            color:#b3007a;
            font-size:12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }
        table thead {
            background-color:#AB0080;
            color: white;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-update {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-delete:hover { background-color: #c0392b; }
        .btn-update:hover { background-color: #2980b9; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Daily Expense Tracker</h2>
    <div class="user-info">
      <img src="im.png" alt="User">
      <div>Test User</div>
      <small style="color: #00ff00">● ONLINE</small>
    </div>
    <ul>
      <li><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></li>
      <li><a href="add_expense.php" style="text-decoration: none; color: inherit;">Add Expenses</a></li>
      <li class="active"><a href="manage.php" style="text-decoration: none; color: inherit;">Manage Expenses</a></li>
      <li>Expense Report</li>
      <li>Profile</li>
      <li><a href="register.php" style="text-decoration: none; color: inherit;">Register</a></li>
      <li><a href="logout.php" class="logout-link">Logout</a></li>
    </ul>
</div>

<div class="main">
    <h1>Manage Expenses</h1>

    <?php if ($filter): ?>
      <div class="filter-chip">Filter: <?= htmlspecialchars($filter) ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Expense Item</th>
                <th>Category</th>
                <th>Expense Cost</th>
                <th>Expense Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result && mysqli_num_rows($result) > 0) {
                $count = 1;
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $count++; ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= htmlspecialchars($row['category']); ?></td>
                        <td><?= number_format((float)$row['amount'], 2); ?></td>
                        <td><?= htmlspecialchars($row['date']); ?></td>
                        <td>
                            <a href="manage.php?delete_id=<?= (int)$row['id']; ?><?= $filter ? '&filter='.urlencode($filter) : '' ?>" onclick="return confirm('Delete this expense?')">
                                <button class="btn-delete">Delete</button>
                            </a>
                            <a href="update.php?id=<?= (int)$row['id']; ?>">
                                <button class="btn-update">Update</button>
                            </a>
                        </td>
                    </tr>
            <?php } 
            } else {
                echo "<tr><td colspan='6'>No expenses found.</td></tr>";
            } ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
