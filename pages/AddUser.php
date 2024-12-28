<?php
include '../database/db_connect.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; 
    $profilePhoto = $_FILES['profile_photo']; 

    // التحقق من وجود البريد الإلكتروني في قاعدة البيانات
    $checkEmailStmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "bg-primary"; 
    } else {
        // إعداد المجلد لتحميل الصور إذا لم يكن موجودًا
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);  
        }

        // التحقق من رفع الصورة
        if ($profilePhoto['error'] == 0) {
            // التحقق من نوع الصورة
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($profilePhoto['type'], $allowedTypes)) {
                $photoName = preg_replace('/\s+/', '_', basename($profilePhoto['name']));
                $photoPath = $uploadDir . $photoName;

                // نقل الصورة إلى المجلد
                if (move_uploaded_file($profilePhoto['tmp_name'], $photoPath)) {
                    // إدخال البيانات في قاعدة البيانات
                    $stmt = $conn->prepare("INSERT INTO userdata (username, email, password, role, profile_photo) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $username, $email, $password, $role, $photoPath);

                    if ($stmt->execute()) {
                        $message = "Account created successfully";
                        $toastClass = "bg-success"; 
                        header("Location: ViewUsers.php");
                    } else {
                        $message = "Error: " . $stmt->error;
                        $toastClass = "bg-danger"; 
                    }

                    $stmt->close();
                } else {
                    $message = "Error uploading profile photo";
                    $toastClass = "bg-danger"; 
                }
            } else {
                $message = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
                $toastClass = "bg-danger"; 
            }
        } else {
            $message = "Error uploading image. Error Code: " . $profilePhoto['error'];
            $toastClass = "bg-danger"; 
        }
    }

    $checkEmailStmt->close();
    $conn->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Add New User</h2>
    <form method="post" action="" enctype="multipart/form-data" class="bg-light p-4 rounded shadow">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="profile_photo" class="form-label">Profile Picture</label>
            <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary w-100">Add User</button>
    </form>
    <?php if ($message != ""): ?>
        <div class="toast <?= $toastClass ?>" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                <?= $message ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
