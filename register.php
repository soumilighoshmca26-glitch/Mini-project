<?php
session_start();

// DB setup
$dsn = "mysql:host=localhost;dbname=expense_tracker;charset=utf8mb4";
$username = "root";
$password = "";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Database connection
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed.");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Sanitize inputs
    $uname = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $pwd = $_POST['password'];

    // reCAPTCHA verification
    $captcha_secret = '6Ld33pIrAAAAABuOlc5fPObAm9rbhl9fx0wWS_2E';
    $captcha_response = $_POST['g-recaptcha-response'] ?? '';

    $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($captcha_secret) . "&response=" . urlencode($captcha_response));
    $response_data = json_decode($verify_response);
    $is_verified = ($response_data && $response_data->success) ? 1 : 0;

    // Input validation
    if (empty($uname) || empty($email) || empty($pwd)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^(?=.*[!@#$%^&*(),.?":{}|<>]).{6,}$/', $pwd)) {
        $error = "Password must be at least 6 characters and contain a special character.";
    } elseif (preg_match('/[\r\n]/', $uname) || preg_match('/[\r\n]/', $email)) {
        $error = "Invalid characters detected.";
    } else {
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_verified) VALUES (?, ?, ?, ?)");
            $stmt->execute([$uname, $email, $hashedPwd, $is_verified]);

            if ($is_verified == 1) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $uname;
                header("Location: index.php?registered=1");
                exit();
            } else {
                $success = "Registered but CAPTCHA verification failed. Please verify your account.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username or email already exists.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Expense Tracker</title>
    <link rel="stylesheet" href="styler.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">
        <div class="login-left">
            <h1>WELCOME BACK!</h1>
            <p>Already a member? <br>Great to see you again.<br>Sign in to manage your finances.</p>
        </div>
        <div class="login-right">
            <h2>Register</h2>
            <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="input-box">
                    <input type="text" name="username" required placeholder=" " value="<?= isset($uname) ? htmlspecialchars($uname) : '' ?>">
                    <label>Username</label>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" required placeholder=" " value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                    <label>Email</label>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required placeholder=" ">
                    <label>Password</label>
                    <i class='bx bxs-lock'></i>
                </div>

                <!-- CAPTCHA -->
                <div class="g-recaptcha" data-sitekey="6Ld33pIrAAAAAKngM4Mv-nZQHIEX32kpKbMwiA_q"></div>

                <button type="submit" class="btn">Register</button>
                <p class="signup">Already have an account? <a href="index.php">Sign In</a></p>
                <input type="hidden" name="register" value="1">
            </form>
        </div>
    </div>
</body>
</html>
