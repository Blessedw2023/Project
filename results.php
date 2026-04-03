<?php
session_start();
require 'config/db.php';

// Protect page
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$current_time = date("Y-m-d H:i:s");

// Get latest ACTIVE election (real-time)
$electionQuery = mysqli_query($conn, "
    SELECT * FROM elections 
    WHERE start_date <= '$current_time' 
    AND end_date >= '$current_time'
    ORDER BY start_date DESC
    LIMIT 1
");

$election = mysqli_fetch_assoc($electionQuery);

$message = "";

if (!$election) {
    $message = "No active election at the moment.";
}

// Fetch results
$results = [];
$totalVotes = 0;

if ($election) {
    $candQuery = mysqli_query($conn, "
        SELECT c.*, COUNT(v.id) AS votes 
        FROM candidates c
        LEFT JOIN votes v 
        ON c.id = v.candidate_id 
        AND v.election_id='".$election['id']."'
        WHERE c.election_id='".$election['id']."'
        GROUP BY c.id
        ORDER BY c.position, votes DESC
    ");

    while ($row = mysqli_fetch_assoc($candQuery)) {
        $results[$row['position']][] = $row;
        $totalVotes += $row['votes'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Election Results</title>

    <style>
        body { font-family: Arial; background: #f4f6f9; }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
        }

        h2 { color: #007bff; text-align: center; }

        .role {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .position { margin-top: 30px; }

        .candidate {
            display: flex;
            align-items: center;
            margin-top: 12px;
            padding: 10px;
            border-radius: 6px;
            background: #f9f9f9;
        }

        .candidate img {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            margin-right: 15px;
        }

        .winner {
            background: #e6ffe6;
            border-left: 6px solid green;
        }

        .message {
            margin-top: 20px;
            color: red;
            font-weight: bold;
            text-align: center;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            background: #dc3545;
            color: white;
        }

        .total-votes {
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
        }

        .winner-tag {
            color: green;
            font-weight: bold;
            margin-left: 8px;
        }

        .percentage {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="role">
        Logged in as: <strong><?php echo strtoupper($role); ?></strong>
    </div>

    <h2>
        Election Results: 
        <?php echo $election ? htmlspecialchars($election['title']) : ''; ?>
    </h2>

    <?php if ($election): ?>

        <?php if ($election['results_published'] == 0): ?>
            <h3 style="color:orange; text-align:center;">
                📡 Live Results (Unofficial)
            </h3>
        <?php else: ?>
            <h3 style="color:green; text-align:center;">
                🏆 Official Election Results
            </h3>
        <?php endif; ?>

    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
        <a href="dashboard.php" class="btn">⬅ Back</a>

    <?php else: ?>

        <?php if ($role === 'admin'): ?>
            <div class="total-votes">
                Total Votes Cast: <?php echo $totalVotes; ?>
            </div>
        <?php endif; ?>

        <?php foreach ($results as $position => $candidates): ?>

            <?php
            // Total votes per position
            $positionTotal = 0;
            foreach ($candidates as $c) {
                $positionTotal += $c['votes'];
            }
            ?>

            <div class="position">
                <h3><?php echo htmlspecialchars($position); ?></h3>

                <?php foreach ($candidates as $index => $cand): ?>

                    <?php
                    $percentage = ($positionTotal > 0)
                        ? round(($cand['votes'] / $positionTotal) * 100)
                        : 0;

                    $isWinner = ($index === 0);

                    $photoPath = $cand['photo'] && file_exists('uploads/'.$cand['photo']) 
                        ? 'uploads/'.$cand['photo'] 
                        : '';
                    ?>

                    <div class="candidate <?php echo $isWinner ? 'winner' : ''; ?>">

                        <?php if ($photoPath): ?>
                            <img src="<?php echo $photoPath; ?>">
                        <?php endif; ?>

                        <div>
                            <strong>
                                <?php echo htmlspecialchars($cand['name']); ?>

                                <?php if ($isWinner && $election['results_published'] == 1): ?>
                                    <span class="winner-tag">🏆 WINNER</span>
                                <?php endif; ?>
                            </strong>

                            <div>
                                <?php echo $cand['votes']; ?> votes
                            </div>

                            <div class="percentage">
                                <?php echo $percentage; ?>%
                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>

        <a href="dashboard.php" class="btn">⬅ Back to Dashboard</a>

    <?php endif; ?>

</div>

</body>
</html>