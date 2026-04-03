<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/db.php';

// Protect page: only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
    header("Location: ../dashboard.php");
    exit();
}

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST['title']);

    if (empty($title)) {
        $error = "Election title is required!";
    } else {

        // ✅ AUTO SET TIME (THIS IS THE FIX)
        $start_date = date("Y-m-d H:i:s"); // NOW
        $end_date = date("Y-m-d H:i:s", strtotime("+24 hours")); // +24 HOURS

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO elections (title, start_date, end_date) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $start_date, $end_date);

        if ($stmt->execute()) {
            $success = "✅ Election created successfully! (Runs for 24 hours)";
        } else {
            $error = "❌ Error creating election: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Election</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        input, button {
            padding: 10px;
            margin-top: 5px;
            width: 100%;
        }

        button {
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            margin-top: 10px;
        }

        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 6px;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .info {
            background: #e9f3ff;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            color: #004085;
        }

        a.back {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Election</h2>

    <div class="info">
        ⏱ This election will automatically run for <strong>24 hours</strong> from the time you create it.
    </div>

    <?php if ($success) echo "<div class='message success'>$success</div>"; ?>
    <?php if ($error) echo "<div class='message error'>$error</div>"; ?>

    <form method="POST">
        <input type="text" name="title" placeholder="Election Title" required>
        <button type="submit">Create Election</button>
    </form>

    <a class="back" href="../dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>