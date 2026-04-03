<?php
session_start();
require 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admission = trim($_POST['admission_number']);
    $email = trim($_POST['email']); // add email field
    $password = $_POST['password'];

    // Prepared statement to select user by admission + email
    $stmt = $conn->prepare("SELECT * FROM students WHERE admission_number = ? AND email = ?");
    $stmt->bind_param("ss", $admission, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['admission_number'];
            $_SESSION['role'] = 'student';
            $_SESSION['name'] = $user['full_name']; // store name for welcome message

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password";
        }
    } else {
        $error = "Student not found with this admission number and email!";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; font-family: Arial, sans-serif; }

        body {
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 10px;
            color: #6a11cb;
        }

        .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #6a11cb;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #6a11cb;
            color: white;
            border: none;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn:hover {
            background: #4e0fa1;
        }

        .error {
            background: #ffe5e5;
            color: #d8000c;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 6px;
            font-size: 14px;
        }

        .links {
            margin-top: 15px;
            font-size: 14px;
        }

        .links a {
            color: #6a11cb;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .show-pass {
            text-align: left;
            font-size: 13px;
            margin-top: 5px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2>🎓 Student Login</h2>
    <div class="subtitle">Access your voting dashboard</div>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="admission_number" placeholder="Admission Number" required>

        <input type="email" name="email" placeholder="University Email (example: johndoe@zetech.ac.ke)" required>

        <input type="password" id="password" name="password" placeholder="Password" required>

        <div class="show-pass">
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </div>

        <button class="btn">Login</button>
    </form>

    <div class="links">
        Don't have an account? <a href="register.php">Register</a><br>
        <a href="index.php">⬅ Back to Home</a>
    </div>
</div>

<script>
function togglePassword() {
    var pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
