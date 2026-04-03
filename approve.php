<?php

require '../config/db.php';

$admission_number = $_GET['id'];

$conn->query("UPDATE students SET status='approved' WHERE admission_number='$admission_number'");

header("Location: manage_students.php");
?>