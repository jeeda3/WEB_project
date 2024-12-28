<?php
include '../database/db_connect.php';

$id = $_GET['id'];


$stmt = $conn->prepare("DELETE FROM userdata WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "User deleted successfully";
    header("Location: ViewUsers.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>
