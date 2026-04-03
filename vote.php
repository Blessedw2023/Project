<?php
session_start();
require 'config/db.php';

// Protect page: only students
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$message = "";

// Fetch active election
$current_time = date("Y-m-d H:i:s");
$electionQuery = mysqli_query($conn, "
    SELECT * FROM elections
    WHERE start_date <= '$current_time'
    AND end_date >= '$current_time'
    ORDER BY end_date ASC
    LIMIT 1
");
$election = mysqli_fetch_assoc($electionQuery);

// If no active election
if (!$election) {
    $message = "Voting is not active right now. It may not have started or has already ended.";
}

// Handle vote submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && $election) {
    $election_id = $election['id'];
    $votes = $_POST['vote'] ?? [];

    // Prevent double voting
    $checkVote = mysqli_query($conn, "
        SELECT * FROM votes 
        WHERE student_id='$student_id' 
        AND election_id='$election_id'
    ");

    if (mysqli_num_rows($checkVote) > 0) {
        header("Location: dashboard.php?already_voted=1");
        exit();
    }

    // Save votes
    foreach ($votes as $position => $candidate_id) {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO votes (student_id, candidate_id, election_id) 
            VALUES (?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, "sii", $student_id, $candidate_id, $election_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: dashboard.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cast Your Vote</title>
    <style>
        body { font-family: Arial; background: #f4f6f9; }
        .container { width: 750px; margin: 40px auto; background: white; padding: 25px; border-radius: 8px; }
        h2 { color: #007bff; text-align: center; }
        .position { margin-top: 25px; }
        .candidate { display: flex; align-items: center; margin-top: 10px; }
        .candidate img { width: 60px; height: 60px; border-radius: 6px; margin-right: 15px; }
        .candidate label { font-size: 16px; cursor: pointer; }
        .btn { display: inline-block; padding: 10px 15px; margin-top: 15px; border-radius: 5px; text-decoration: none; }
        .btn.submit { background: #28a745; color: white; border: none; cursor: pointer; }
        .btn.back { background: #dc3545; color: white; }
        .message { margin-top: 20px; color: red; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h2>Election: <?php echo $election ? htmlspecialchars($election['title']) : 'No Active Election'; ?></h2>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
        <a href="dashboard.php" class="btn back">⬅ Back to Dashboard</a>
    <?php endif; ?>

    <?php if ($election && !$message): ?>
    <form method="POST" onsubmit="return confirm('Are you sure you want to submit your votes? You cannot change them after submitting.')">

        <?php
        // Fetch positions for this election
        $positionsQuery = mysqli_query($conn, "
            SELECT DISTINCT position 
            FROM candidates 
            WHERE election_id='".$election['id']."'
        ");

        while ($pos = mysqli_fetch_assoc($positionsQuery)) {
            $position = $pos['position'];
            echo "<div class='position'><strong>$position</strong>";

            // Fetch candidates for each position
            $candidatesQuery = mysqli_query($conn, "
                SELECT * FROM candidates 
                WHERE election_id='".$election['id']."' 
                AND position='$position'
            ");

            while ($cand = mysqli_fetch_assoc($candidatesQuery)) {
                $photoPath = $cand['photo'] && file_exists('uploads/'.$cand['photo']) ? 'uploads/'.$cand['photo'] : '';
                echo "<div class='candidate'>
                        <input type='radio' name='vote[$position]' value='".$cand['id']."' required id='cand_".$cand['id']."'>
                        ".($photoPath ? "<img src='$photoPath' alt='Photo'>" : "")."
                        <label for='cand_".$cand['id']."'>".$cand['name']."</label>
                      </div>";
            }

            echo "</div>";
        }
        ?>

        <button type="submit" class="btn submit">✅ Submit Vote</button>
        <a href="dashboard.php" class="btn back">⬅ Cancel</a>
    </form>
    <?php endif; ?>

</div>

</body>
</html>
