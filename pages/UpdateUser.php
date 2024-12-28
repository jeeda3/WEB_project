<?php
include '../database/db_connect.php';

$id = $_GET['id'];


$stmt = $conn->prepare("SELECT username, email, role, profile_photo FROM userdata WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username, $email, $role, $profile_photo);
$stmt->fetch();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_role = $_POST['role'];
    
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
     
        $target_dir = "uploads/";  
        $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

   
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
          
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
               
                $profile_photo = $target_file;
            } else {
                echo '<div class="alert alert-danger">Error uploading image.</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Invalid image format. Only JPG, JPEG, PNG & GIF are allowed.</div>';
        }
    }


    $updateStmt = $conn->prepare("UPDATE userdata SET username = ?, email = ?, role = ?, profile_photo = ? WHERE id = ?");
    $updateStmt->bind_param("ssssi", $new_username, $new_email, $new_role, $profile_photo, $id);

    if ($updateStmt->execute()) {
        echo '<div class="alert alert-success">User updated successfully</div>';
        header("Location: ViewUsers.php");
    } else {
        echo '<div class="alert alert-danger">Error: ' . $updateStmt->error . '</div>';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Update User Information</h2>
    <form method="post" action="" enctype="multipart/form-data" class="bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="user" <?php if ($role == 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="profile_photo" class="form-label">Profile Photo</label>
            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
            <small class="form-text text-muted">Leave blank if you don't want to change the photo.</small>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update User</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
