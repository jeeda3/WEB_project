<?php
include '../database/db_connect.php';
$id = $_GET['id']; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password']; 

    $stmt = $conn->prepare("UPDATE userdata SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $id);

    if ($stmt->execute()) {
        echo "Password updated successfully";
        header("Location: ViewUsers.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<form method="post" action="">
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Set Password</button>
</form>
