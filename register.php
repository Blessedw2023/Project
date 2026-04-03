<?php
require 'config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $admission = $_POST['admission'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $allowedDomain = "@zetech.ac.ke";

if (substr($email, -strlen($allowedDomain)) !== $allowedDomain) {
    $message = "Only Zetech University emails (@zetech.ac.ke) are allowed!";
}
    $campus = $_POST['campus'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

   // Stop if email invalid
if ($message) {

} 
// Check password match
elseif ($password !== $confirm) {
    $message = "Passwords do not match!";
}
else {

        // Check duplicate admission number
        $check = mysqli_query($conn, "SELECT * FROM students WHERE admission_number='$admission'");

        if (mysqli_num_rows($check) > 0) {
            $message = "Admission number already registered!";
        } else {

            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Insert student
            $sql = "INSERT INTO students (admission_number, full_name, email, campus, password)
                    VALUES ('$admission', '$name', '$email', '$campus', '$hashed')";

            if (mysqli_query($conn, $sql)) {
                header("Location: login.php");
                exit();
            } else {
                $message = "Registration failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
        }
        .box {
            width: 400px;
            margin: 50px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
        }
        .msg {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Student Registration</h2>

    <?php if ($message) echo "<div class='msg'>$message</div>"; ?>

    <form method="POST">
        <input type="text" name="admission" placeholder="Admission Number" required>

        <input type="text" name="name" placeholder="Full Name" required>

       <input type="email" name="email" pattern=".+@zetech\.ac\.ke" 
placeholder="University Email (example: johndoe@zetech.ac.ke)" required>

        <select name="campus" required>
            <option value="">Select Campus</option>
            <option>Mangu</option>
            <option>TRC</option>
            <option>Town</option>
        </select>

        <input type="password" name="password" placeholder="Password" required>

        <input type="password" name="confirm" placeholder="Confirm Password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already registered? <a href="login.php">Login</a></p>
</div>

</body>
</html>
