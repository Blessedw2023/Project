<?php
session_start();
require '../config/db.php';

// Protect page: Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch candidates with vote counts
$sql = "
SELECT c.id, c.name, c.position, c.photo, COUNT(v.id) AS votes
FROM candidates c
LEFT JOIN votes v ON c.id = v.candidate_id
GROUP BY c.id
ORDER BY c.position, votes DESC
";
$result = $conn->query($sql);

// Get total votes
$totalVotes = $conn->query("SELECT COUNT(*) AS total FROM votes")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitor Voting</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { max-width: 950px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        img { border-radius: 6px; }
        .total { margin-top: 10px; font-weight: bold; }
        a.back { margin-top: 15px; display: inline-block; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2>Monitor Voting</h2>

    <div class="total">Total Votes Cast: <?php echo $totalVotes; ?></div>

    <table>
        <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Position</th>
            <th>Votes</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <?php if($row['photo'] && file_exists('../uploads/'.$row['photo'])): ?>
                        <img src="../uploads/<?php echo $row['photo']; ?>" width="60" alt="Photo">
                    <?php else: ?>
                        No photo
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td><?php echo $row['votes']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a class="back" href="../dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
