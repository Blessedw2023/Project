<?php
session_start();
require 'config/db.php';

// Block access
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$current = date("Y-m-d H:i:s");

/* ================= GET LATEST ELECTION ================= */

$electionQuery = $conn->query("
    SELECT * FROM elections
    ORDER BY end_date DESC
    LIMIT 1
");

$election = $electionQuery->fetch_assoc();
$election_id = $election ? $election['id'] : null;

/* ================= STUDENT CHECK ================= */

$alreadyVoted = false;

if ($role === "student" && $election_id) {

    $student_id = $_SESSION['user_id'];

    // Check approval
    $statusQuery = $conn->query("
        SELECT status FROM students 
        WHERE admission_number='$student_id'
    ");

    $statusRow = $statusQuery->fetch_assoc();

    if ($statusRow['status'] !== 'approved') {
        echo "<div style='text-align:center;margin-top:50px;'>
                <h3>⏳ Your account is awaiting admin approval.</h3>
                <a href='logout.php'>Logout</a>
              </div>";
        exit();
    }

    // Check if already voted
    $voteCheck = $conn->query("
        SELECT id FROM votes
        WHERE student_id='$student_id'
        AND election_id='$election_id'
    ");

    if ($voteCheck->num_rows > 0) {
        $alreadyVoted = true;
    }
}

/* ================= ADMIN STATS ================= */

$totalVotes = 0;
$totalStudents = 0;
$turnout = 0;

if ($role === "admin") {

   $votesQuery = $conn->query("
    SELECT COUNT(DISTINCT student_id) as total 
    FROM votes
");
$totalVotes = $votesQuery->fetch_assoc()['total'];

    $studentsQuery = $conn->query("SELECT COUNT(*) as total FROM students");
    $totalStudents = $studentsQuery->fetch_assoc()['total'];

    if ($totalStudents > 0) {
        $turnout = round(($totalVotes / $totalStudents) * 100);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body { font-family: Arial; background: #f4f6f9; }

        .container {
            max-width: 850px;
            width: 90%;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        h2 { color: #007bff; }

        .btn {
            display: block;
            margin: 10px 0;
            padding: 12px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn:hover { background: #0056b3; }

        .logout { background: #dc3545; }

        .success {
            background:#d4edda;
            color:#155724;
            padding:12px;
            border-radius:6px;
            margin-bottom:15px;
        }

        .warning {
            color: red;
            font-weight: bold;
        }

        .disabled {
            background: grey !important;
            pointer-events: none;
        }

        .role {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- ROLE -->
    <div class="role">
        Logged in as: <strong><?php echo strtoupper($role); ?></strong>
    </div>

    <!-- SUCCESS -->
    <?php if (isset($_GET['published'])): ?>
        <div class="success">📢 Results have been successfully published!</div>
    <?php endif; ?>

    <!-- ERROR POPUPS -->
    <?php if (isset($_GET['error'])): ?>
    <script>
        <?php if ($_GET['error'] == 'not_ended'): ?>
            alert("⏳ Election has not ended yet!");
        <?php elseif ($_GET['error'] == 'already_published'): ?>
            alert("⚠ Results already published!");
        <?php elseif ($_GET['error'] == 'failed'): ?>
            alert("❌ Failed to publish results.");
        <?php elseif ($_GET['error'] == 'no_id'): ?>
            alert("❌ No election selected.");
        <?php elseif ($_GET['error'] == 'not_found'): ?>
            alert("❌ Election not found.");
        <?php endif; ?>
    </script>
    <?php endif; ?>


    <!-- ================= STUDENT ================= -->
    <?php if ($role === "student"): ?>
        <h2>🎓 Student Dashboard</h2>

        <?php if ($alreadyVoted): ?>
            <a class="btn disabled">✔ Vote Submitted</a>
        <?php else: ?>
            <a href="vote.php" class="btn">🗳 Cast Vote</a>
        <?php endif; ?>

        <a href="results.php" class="btn">📊 View Results</a>

    <?php endif; ?>


    <!-- ================= ADMIN ================= -->
    <?php if ($role === "admin"): ?>
        <h2>🛠 Admin Panel</h2>

        <div style="background:#e9f3ff;padding:15px;border-radius:6px;margin-bottom:15px;">
            <strong>Registered Students:</strong> <?php echo $totalStudents; ?><br>
            <strong>Votes Cast:</strong> <?php echo $totalVotes; ?><br>
            <strong>Voter Turnout:</strong> <?php echo $turnout; ?>%
        </div>

        <a href="admin/create_election.php" class="btn">🗳 Create Election</a>
        <a href="admin/manage_candidates.php" class="btn">👤 Manage Candidates</a>
        <a href="admin/manage_students.php" class="btn">👥 Manage Students</a>
        <a href="admin/monitor_votes.php" class="btn">📈 Monitor Voting</a>
        <a href="results.php" class="btn">📊 View Results</a>

        <!-- PUBLISH BUTTON LOGIC -->
        <?php if ($election_id): ?>

            <?php if ($election['results_published'] == 1): ?>
                <a class="btn disabled">✔ Results Published</a>

            <?php elseif ($election['end_date'] > $current): ?>
                <a class="btn disabled">⏳ Election Still Running</a>

            <?php else: ?>
                <a href="admin/publish_results.php?id=<?php echo $election_id; ?>" 
                   class="btn"
                   onclick="return confirm('Publish official election results?')">
                   📢 Publish Final Results
                </a>
            <?php endif; ?>

        <?php else: ?>
            <a class="btn disabled">⚠ No Election Found</a>
        <?php endif; ?>

    <?php endif; ?>

    <!-- LOGOUT -->
    <a href="logout.php" class="btn logout">🚪 Logout</a>

</div>

</body>
</html>