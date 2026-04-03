<?php
session_start();

$error = "";

// Fixed admin credentials
$admin_user = "Admin001";
$admin_pass = "admin123";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['user_id'] = $username;
        $_SESSION['role'] = "admin";

        // File is inside /admin/, dashboard.php is one level up
        header("Location: ../dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid admin credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            font-family: Arial;
            background: linear-gradient(135deg, #232526, #414345);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 320px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        h2 { color: #333; }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #000;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        a {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>🔐 Admin Login</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Admin Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button>Login</button>
    </form>

    <!-- File is inside /admin/, index.php is one level up -->
    <a href="../index.php">⬅ Back to Home</a>
</div>

</body>
</html>
