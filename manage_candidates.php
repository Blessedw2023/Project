<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../config/db.php';

// Protect page: Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Add Candidate
if (isset($_POST['add_candidate'])) {
    $name = trim($_POST['name']);
    $position = trim($_POST['position']);
    $election_id = intval($_POST['election_id']);

    if (!empty($name) && !empty($position) && $election_id > 0) {

        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755);

            // Clean filename
            $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES['photo']['name']));
            $targetFile = $uploadDir . $filename;

            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg','jpeg','png','gif'];

            if (!in_array($fileType, $allowedTypes)) {
                $message = "❌ Only JPG, JPEG, PNG, GIF files allowed";
            } elseif (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $stmt = $conn->prepare("INSERT INTO candidates (name, position, photo, election_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $name, $position, $filename, $election_id);

                if ($stmt->execute()) {
                    $message = "✅ Candidate added successfully!";
                } else {
                    $message = "❌ DB error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $message = "❌ Failed to upload photo";
            }
        } else {
            $message = "❌ Please select a photo";
        }
    } else {
        $message = "❌ Name, Position, and Election are required";
    }
}

// Handle Delete Candidate
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete photo if exists
    $stmt = $conn->prepare("SELECT photo FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['photo'] && file_exists('../uploads/' . $row['photo'])) {
            unlink('../uploads/' . $row['photo']);
        }
    }

    // Delete candidate
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $message = "✅ Candidate deleted successfully";
}

// Fetch all candidates with election title
$candidates = $conn->query("
    SELECT c.*, e.title AS election_title 
    FROM candidates c 
    LEFT JOIN elections e ON c.election_id = e.id
    ORDER BY c.election_id DESC, c.position, c.name
");

// Fetch all elections for dropdown
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .container { max-width: 950px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        h2, h3 { color: #333; }
        form input, form select, form button { padding: 10px; margin-top: 5px; width: 100%; }
        form button { cursor: pointer; background: #232526; color: white; border: none; border-radius: 6px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        .message { background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 6px; }
        a.delete { color: red; text-decoration: none; }
        a.delete:hover { text-decoration: underline; }
        img { border-radius: 6px; }
        a.back { margin-top: 15px; display: inline-block; text-decoration: none; color: #555; }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Candidates</h2>

    <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>

    <h3>Add New Candidate</h3>
    <form method="POST" enctype="multipart/form-data">
        
        <input type="text" name="name" placeholder="Candidate Full Name" required>
        <input type="text" name="position" placeholder="Position (e.g President, VP)" required>

        <!-- Election dropdown -->
        <select name="election_id" required>
            <option value="">Select Election</option>
            <?php while($row = $elections->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></option>
            <?php endwhile; ?>
        </select>

       <!-- Photo upload note -->
<p style="color:#555; font-size:14px; margin-top:10px;">
📌 <strong>Note:</strong> Please upload a clear passport-style photo of the candidate.
Accepted formats: JPG, JPEG, PNG, or GIF.
</p>

<input type="file" name="photo" accept="image/*" required>

<button name="add_candidate">Add Candidate</button>
    </form>

    <h3>All Candidates</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Name</th>
            <th>Position</th>
            <th>Election</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $candidates->fetch_assoc()): ?>
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
                <td><?php echo htmlspecialchars($row['election_title']); ?></td>
                <td>
                    <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this candidate?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a class="back" href="../dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
