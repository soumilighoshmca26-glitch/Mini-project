<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Database connection
$dsn = "mysql:host=localhost;dbname=expense_tracker;charset=utf8mb4";
$username = "root";
$password = "";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// reCAPTCHA secret key
$captcha_secret = '6Ld33pIrAAAAABuOlc5fPObAm9rbhl9fx0wWS_2E';

// Error message holder
$error = "";

// --- Login Logic ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $captcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Step 1: reCAPTCHA validation
    if (!empty($captcha_response)) {
        $verify_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$captcha_secret}&response={$captcha_response}");
        $captcha_data = json_decode($verify_response);

        if ($captcha_data->success) {
            // Step 2: Login verification
            $user = trim($_POST['username']);
            $pass = $_POST['password'];

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$user]);
            $data = $stmt->fetch();

            if ($data && password_verify($pass, $data['password'])) {
                session_start();
                session_regenerate_id(true);
                $_SESSION['user'] = $data['username'];

                // ✅ NEW: Role-based session
                $_SESSION['role'] = $data['role'] ?? 'user';

                // ✅ NEW: Redirect based on role
                if ($_SESSION['role'] === 'admin') {
                    header("Location: register.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "reCAPTCHA verification failed. Please try again.";
        }
    } else {
        $error = "Please complete the reCAPTCHA.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Expense Tracker</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        /* --- Your CSS from above --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #1f1f1f;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            display: flex;
            width: 900px;
            height: 500px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px #ff005d;
        }

        .login-left {
            flex: 1;
            background-color: #1f1f1f;
            padding: 60px 40px;
            position: relative;
        }

        .login-left h2 {
            font-size: 32px;
            margin-bottom: 30px;
        }

        .input-box {
            position: relative;
            margin-bottom: 25px;
        }

        .input-box input {
            width: 100%;
            padding: 12px 40px 12px 10px;
            background: transparent;
            border: none;
            border-bottom: 2px solid #fff;
            color: #fff;
            font-size: 16px;
        }

        .input-box label {
            position: absolute;
            left: 10px;
            top: 12px;
            font-size: 14px;
            color: #ccc;
            transition: 0.3s;
            pointer-events: none;
        }

        .input-box input:focus + label,
        .input-box input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: #ff005d;
        }

        .input-box i {
            position: absolute;
            right: 10px;
            top: 12px;
            color: #fff;
        }

        .forgot {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot a {
            color: #ff005d;
            text-decoration: none;
            font-size: 14px;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, #ff005d, #c90050);
            border: none;
            border-radius: 30px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(255, 0, 93, 0.4);
            transition: 0.3s;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .signup {
            margin-top: 20px;
            font-size: 14px;
            color: #fff;
        }

        .signup a {
            color: #ff005d;
            font-weight: bold;
            text-decoration: none;
        }

        .error {
            color: #ff4d4d;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .g-recaptcha {
            margin: 15px 0;
        }

        /* Right Angular Box */
        .login-right {
            flex: 1;
            background: linear-gradient(135deg, #c90050 0%, #ff005d 100%);
            clip-path: polygon(20% 0%, 100% 0%, 100% 100%, 0% 100%);
            padding: 80px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .login-right h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .login-right p {
            font-size: 16px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-left">
        <h2>Login</h2>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="POST" novalidate>
            <div class="input-box">
                <input type="text" name="username" required placeholder=" " autocomplete="username">
                <label>Username</label>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" required placeholder=" " id="password" autocomplete="current-password">
                <label>Password</label>
                <i class='bx bxs-lock'></i>
            </div>
            <div class="forgot">
                <a href="forgot.php">Forgot password?</a>
            </div>

            <!-- CAPTCHA -->
            <div class="g-recaptcha" data-sitekey="6Ld33pIrAAAAAKngM4Mv-nZQHIEX32kpKbMwiA_q"></div>

            <button type="submit" class="btn" name="login">Login</button>

            <p class="signup">Don't have an account? <a href="register.php">Sign Up</a></p>
        </form>
    </div>
    <div class="login-right">
        <h1>WELCOME<br>BACK!</h1>
        <p>Manage your expenses smartly and take control of your finances.</p>
    </div>
</div>
</body>
</html>
