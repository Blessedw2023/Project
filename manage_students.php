<?php
session_start();
require '../config/db.php';

// Protect page (ONLY ADMIN)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$result = $conn->query("SELECT * FROM students");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Students</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
        }

        .container {
            width: 90%;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-dashboard {
            padding: 8px 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-dashboard:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .btn {
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 13px;
        }

        .approve {
            background: #28a745;
        }

        .reject {
            background: #dc3545;
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-size: 12px;
        }

        .pending { background: orange; }
        .approved { background: green; }
        .rejected { background: red; }

    </style>
</head>

<body>

<div class="container">

    <!-- Top Section -->
    <div class="top-bar">
        <h2>👥 Manage Students</h2>
        <a href="../dashboard.php" class="btn-dashboard">⬅ Dashboard</a>
    </div>

    <table>
        <tr>
            <th>Admission No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['admission_number']; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>

            <td>
                <span class="status <?php echo $row['status']; ?>">
                    <?php echo ucfirst($row['status']); ?>
                </span>
            </td>

            <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <a class="btn approve"
                       href="approve.php?id=<?php echo urlencode($row['admission_number']); ?>">
                       ✅ Approve
                    </a>

                    <a class="btn reject"
                       href="reject.php?id=<?php echo urlencode($row['admission_number']); ?>">
                       ❌ Reject
                    </a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>