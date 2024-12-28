<?php
include '../database/db_connect.php';


$sql = "SELECT id, username, email, role, profile_photo FROM userdata";
$result = $conn->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Users List</h2>

    
    <a href="AddUser.php" class="btn btn-success mb-3">Add New User</a>

  
    <a href="logout.php" class="btn btn-danger mb-3">Log out</a>
    
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Profile Photo</th>  
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    $profilePhotoPath = $row["profile_photo"];
                  
                    $profilePhotoDisplay = $profilePhotoPath ? "<img src='$profilePhotoPath' alt='Profile Photo' style='width: 50px; height: 50px; border-radius: 50%;'>" : "No Photo";
                    
                    echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["username"] . "</td>
                            <td>" . $row["email"] . "</td>
                            <td>" . $row["role"] . "</td>
                            <td>" . $profilePhotoDisplay . "</td> 
                            <td>
                                <a href='UpdateUser.php?id=" . $row["id"] . "' class='btn btn-sm btn-primary'>Edit</a>
                                 <a href='SetUserPassword.php?id=" . $row["id"] . "' class='btn btn-sm btn-danger'>Set Password</a>
                                <a href='DeleteUser.php?id=" . $row["id"] . "' class='btn btn-sm btn-danger'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No results found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
