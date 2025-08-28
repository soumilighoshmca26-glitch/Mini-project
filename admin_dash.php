<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Rubik', sans-serif;
            background-color: #121212;
            color: #f0f0f0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #1f1f1f;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 5px rgba(255, 255, 255, 0.05);
        }

        .sidebar h2 {
            color: #ff4081;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .sidebar a {
            text-decoration: none;
            color: #aaa;
            padding: 12px 10px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: 0.3s;
            margin-bottom: 15px;
        }

        .sidebar a:hover {
            background-color: #292929;
            color: #fff;
        }

        .sidebar i {
            margin-right: 12px;
            font-size: 18px;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            background-color: #181818;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome {
            font-size: 24px;
            font-weight: 600;
            color: #ff4081;
        }

        .logout-btn {
            background-color: #ff4081;
            border: none;
            padding: 10px 20px;
            color: #fff;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #d8366c;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .card {
            background-color: #252525;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.05);
            transition: 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.1);
        }

        .card h3 {
            margin: 0;
            margin-bottom: 10px;
            color: #ff4081;
        }

        .card p {
            color: #ccc;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="#"><i class='bx bxs-dashboard'></i>Dashboard</a>
        <a href="#"><i class='bx bxs-user'></i>Manage Users</a>
        <a href="#"><i class='bx bxs-bar-chart-alt-2'></i>View Reports</a>
        <a href="#"><i class='bx bxs-cog'></i>Settings</a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div class="welcome">Welcome, <?= htmlspecialchars($_SESSION['user']) ?> 👋</div>
            <form action="logout.php" method="POST">
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>

        <div class="cards">
            <div class="card">
                <h3><i class='bx bx-user-check'></i> Total Users</h3>
                <p>124 users registered.</p>
            </div>
            <div class="card">
                <h3><i class='bx bx-file'></i> Reports</h3>
                <p>8 new reports submitted.</p>
            </div>
            <div class="card">
                <h3><i class='bx bx-history'></i> System Logs</h3>
                <p>View recent activities and logs.</p>
            </div>
            <div class="card">
                <h3><i class='bx bx-cog'></i> System Settings</h3>
                <p>Configure admin privileges & roles.</p>
            </div>
        </div>
    </div>

</body>
</html>
