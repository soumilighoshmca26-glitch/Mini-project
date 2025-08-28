<?php
// Database connection details
$host = 'localhost';
$dbname = 'expense_tracker';
$user = 'root';
$pass = '';

// Connect to MySQL using PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Current user (static for demo)
$current_username = 'Soumili Ghosh';
$current_user_id = 1;

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amount = $_POST['amount'] ?? '';

    if ($date && $category && $amount && is_numeric($amount) && $amount > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO transactions (username, type, category, description, amount, date, user_id) VALUES (?, 'expense', ?, ?, ?, ?, ?)");
            $stmt->execute([$current_username, $category, $description, $amount, $date, $current_user_id]);
            $success = "Expense added successfully!";
        } catch (Exception $e) {
            $error = "Error saving expense: " . $e->getMessage();
        }
    } else {
        $error = "Please fill all required fields correctly.";
    }
}

// Fetch all expenses for this user
try {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'expense' ORDER BY date DESC, id DESC");
    $stmt->execute([$current_user_id]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching expenses: " . $e->getMessage());
}

// Calculate total expense
$total = array_sum(array_column($expenses, 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Daily Expense Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Segoe UI',sans-serif}
body{background:#f4f4f4;display:flex;min-height:100vh}
.sidebar{width:250px;background:#1c1c1c;color:white;padding:20px}
.sidebar h2{color:#ff69b4;text-align:center;margin-bottom:10px}
.sidebar .user-info{text-align:center;margin-bottom:30px}
.sidebar .user-info img{width:60px;height:60px;border-radius:50%;margin-bottom:10px}
.sidebar ul{list-style:none}
.sidebar ul li{padding:12px 10px;margin:5px 0;background:#2c2c2c;border-left:4px solid transparent;cursor:pointer}
.sidebar ul li:hover,.sidebar ul li.active{border-left:4px solid #ff69b4;background:#3a3a3a}
main{flex:1;padding:30px 40px}
h1{font-size:2rem;margin-bottom:20px}
form{display:grid;grid-template-columns:1fr 1fr;gap:20px;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:25px}
label{font-weight:600;margin-bottom:6px;display:block}
input[type="text"],input[type="date"],input[type="number"],select{
    width:100%;padding:10px;border:1px solid #ccc;border-radius:7px;font-size:1rem;background-color:white
}
select{
    appearance:none;
    background-image: url("data:image/svg+xml;utf8,<svg fill='%23ff4081' height='24' viewBox='0 0 24 24' width='24'><path d='M7 10l5 5 5-5z'/></svg>");
    background-repeat:no-repeat;
    background-position:right 10px center;
    background-size:20px;
}
input[type="text"]:focus,input[type="date"]:focus,input[type="number"]:focus,select:focus{
    border-color:#ff4081;outline:none;box-shadow:0 0 5px rgba(255,64,129,0.4)
}
input[type="submit"]{
    grid-column:span 2;
    background:linear-gradient(135deg,#ff4081,#ff7eb3);
    border:none;color:white;
    padding:14px 0;
    border-radius:50px;
    font-weight:bold;
    font-size:1.1rem;
    cursor:pointer;
    box-shadow:0 5px 15px rgba(255,64,129,0.3);
    transition:all 0.3s ease
}
input[type="submit"]:hover{
    background:linear-gradient(135deg,#ff2d6b,#ff6699);
    box-shadow:0 8px 20px rgba(255,64,129,0.5);
    transform:translateY(-2px)
}
.error{color:#e74c3c;font-weight:600;margin-bottom:10px;grid-column:span 2;text-align:center}
.success{color:green;font-weight:600;margin-bottom:10px;grid-column:span 2;text-align:center}
table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
th,td{padding:12px 15px;border-bottom:1px solid #eee}
th{background:#ff4081;color:white;text-transform:uppercase;font-size:0.85rem}
tbody tr:hover{background:#ffe6f1}
.total{text-align:right;font-weight:bold;font-size:1.2rem;margin-top:15px;color:#ff4081}
@media(max-width:768px){form{grid-template-columns:1fr}input[type="submit"]{grid-column:1}}
</style>
</head>
<body>

<aside class="sidebar">
    <h2>Daily Expense<br>Tracker</h2>
    <div class="user-info">
        <img src="im.png" alt="User">
        <div class="username"><?= htmlspecialchars($current_username) ?></div>
        <div class="status" style="color:#4caf50">● ONLINE</div>
    </div>
    <ul>
        <li ><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></li>
        <li class="active"> Add Expenses</li>
        <li><a href="manage.php" style="text-decoration: none; color: inherit;">Manage Expenses</a></li>
        <li>Expense Report</li>

        <li>Profile</li>
        <li>Register</li>
        <li>Logout</li>
    </ul>
</aside>

<main>
    <h1>Dashboard</h1>

    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
        <div>
            <label>Date *</label>
            <input type="date" name="date" required max="<?= date('Y-m-d') ?>">
        </div>
        <div>
            <label>Category *</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <option value="Food">Food</option>
                <option value="Transport">Transport</option>
                <option value="Shopping">Shopping</option>
                <option value="Bills">Bills</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Health">Health</option>
                <option value="Education">Education</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div style="grid-column:span 2;">
            <label>Description</label>
            <input type="text" name="description" placeholder="Optional details">
        </div>
        <div>
            <label>Amount (₹) *</label>
            <input type="number" name="amount" placeholder="0.00" step="0.01" min="0" required>
        </div>
        <input type="submit" value="Add Expense">
    </form>

    <div class="total">Total Expense: ₹ <?= number_format($total, 2) ?></div>
</main>

</body>
</html>
