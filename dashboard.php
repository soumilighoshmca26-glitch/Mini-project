<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$dsn = "mysql:host=localhost;dbname=expense_tracker;charset=utf8mb4";
$user = "root";
$pass = "";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// DATES
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$last7 = date('Y-m-d', strtotime('-7 days'));
$last30 = date('Y-m-d', strtotime('-30 days'));
$currentYear = date('Y');

function getExpense($pdo, $start, $end = null) {
  $query = $end ?
    "SELECT SUM(amount) as total FROM transactions WHERE type='expense' AND date BETWEEN '$start' AND '$end'" :
    "SELECT SUM(amount) as total FROM transactions WHERE type='expense' AND date = '$start'";
  return $pdo->query($query)->fetch()['total'] ?? 0;
}

$todayExpense = getExpense($pdo, $today);
$yesterdayExpense = getExpense($pdo, $yesterday);
$last7Expense = getExpense($pdo, $last7, $today);
$last30Expense = getExpense($pdo, $last30, $today);
$currentYearExpense = $pdo->query("SELECT SUM(amount) as total FROM transactions WHERE type='expense' AND YEAR(date) = $currentYear")->fetch()['total'] ?? 0;
$totalExpense = $pdo->query("SELECT SUM(amount) as total FROM transactions WHERE type='expense'")->fetch()['total'] ?? 0;

function calculatePercentage($value, $max = 10000) {
  $percentage = ($value / $max) * 100;
  return $percentage > 100 ? 100 : $percentage;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Daily Expense Tracker</title>
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
    .sidebar ul {
      list-style: none;
    }
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
    .main {
      flex: 1;
      padding: 30px;
    }
    .header {
      font-size: 24px;
      margin-bottom: 20px;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      position: relative;
      transition: transform .08s ease-in-out;
    }
    .card:hover { transform: translateY(-2px); }
    .circle {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: conic-gradient(#ff69b4 calc(var(--percent) * 1%), #eee 0);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      font-size: 1.2rem;
      font-weight: bold;
      color: #333;
    }
    .card h3 {
      color: #666;
      margin-bottom: 10px;
    }
    .footer {
      text-align: center;
      margin-top: 40px;
      color: #999;
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
    @media(max-width: 768px) {
      .sidebar { display: none; }
    }
    a.card-link { text-decoration:none; color:inherit; }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Daily Expense Tracker</h2>
    <div class="user-info">
      <img src="im.png" alt="User">
      <div><?php echo htmlspecialchars($_SESSION['user']); ?></div>
      <small style="color: #00ff00">● ONLINE</small>
    </div>
    <ul>
      <li class="active"><i class="fas fa-home" class="active"></i>Dashboard</li>
      <li><a href="add_expense.php" style="text-decoration: none; color: inherit;"><i class="fas fa-plus-circle"></i> Add Expenses</a></li>
      <li><a href="manage.php" style="text-decoration: none; color: inherit;"><i class="fas fa-tasks"></i>Manage Expenses</a></li>
      <li><a href="chart.php" style="text-decoration: none; color: inherit;"><i class="fas fa-file-alt"></i> Expense Report</a></li>
      <li><i class="fas fa-user"></i>Profile</li>
      <li><a href="register.php" style="text-decoration: none; color: inherit;"><i class="fas fa-sign-out-alt"></i>Register</a></li>
      <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
    </ul>


  </div>

  <div class="main">
    <div class="header">Dashboard</div>

    <div class="cards">
      <?php
        $cards = [
          ['label' => "Today's Expense",   'value' => $todayExpense],
          ['label' => "Yesterday's Expense",'value' => $yesterdayExpense],
          ['label' => "Last 7 Days",       'value' => $last7Expense],
          ['label' => "Last 30 Days",      'value' => $last30Expense],
          ['label' => "Current Year",      'value' => $currentYearExpense],
          ['label' => "Total Expense",     'value' => $totalExpense],
        ];
        foreach ($cards as $card):
          $percent = calculatePercentage($card['value']);
          // build a simple slug like: todaysexpense, yesterdaysexpense, last7days, last30days, currentyear, totalexpense
          $slug = strtolower(str_replace([" ", "'", "-"], "", $card['label']));
      ?>
        <a class="card-link" href="manage.php?filter=<?= urlencode($slug) ?>">
          <div class="card">
            <div class="circle" style="--percent: <?= $percent ?>;">₹<?= number_format($card['value'], 2) ?></div>
            <h3><?= htmlspecialchars($card['label']) ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
