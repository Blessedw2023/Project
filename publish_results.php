<?php
session_start();
require '../config/db.php';

// ================= PROTECT PAGE =================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ================= VALIDATE ID =================
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../dashboard.php?error=no_id");
    exit();
}

$id = intval($_GET['id']);

// ================= CHECK IF ELECTION EXISTS =================
$checkExists = $conn->prepare("SELECT id, end_date, results_published FROM elections WHERE id = ?");
$checkExists->bind_param("i", $id);
$checkExists->execute();
$result = $checkExists->get_result();

if ($result->num_rows === 0) {
    header("Location: ../dashboard.php?error=not_found");
    exit();
}

$election = $result->fetch_assoc();

// ================= CHECK IF ELECTION HAS ENDED =================
if ($election['end_date'] > date("Y-m-d H:i:s")) {
    header("Location: ../dashboard.php?error=not_ended");
    exit();
}

// ================= CHECK IF ALREADY PUBLISHED =================
if ($election['results_published'] == 1) {
    header("Location: ../dashboard.php?error=already_published");
    exit();
}

// ================= UPDATE RESULTS =================
$stmt = $conn->prepare("
    UPDATE elections 
    SET results_published = 1 
    WHERE id = ?
");

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {
        header("Location: ../dashboard.php?published=1");
        exit();
    } else {
        header("Location: ../dashboard.php?error=failed");
        exit();
    }

} else {
    header("Location: ../dashboard.php?error=failed");
    exit();
}
?>