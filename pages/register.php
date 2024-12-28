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

    $checkEmailStmt = $conn->prepare("SELECT email FROM userdata WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "bg-primary"; 
    } else {
       
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);  
        }

        
        if ($profilePhoto['error'] == 0) {
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($profilePhoto['type'], $allowedTypes)) {
                
                $photoName = preg_replace('/\s+/', '_', basename($profilePhoto['name']));
                $photoPath = $uploadDir . $photoName;

                
                if (move_uploaded_file($profilePhoto['tmp_name'], $photoPath)) {
             
                    $stmt = $conn->prepare("INSERT INTO userdata (username, email, password, role, profile_photo) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $username, $email, $password, $role, $photoPath);

                    if ($stmt->execute()) {
                        $message = "Account created successfully";
                        $toastClass = "bg-success"; 
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
            $message = "Error uploading image";
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
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<section class="d-flex justify-content-center align-items-center vh-100" style="background-color: #D1E7DD;">
    <div class="BoxForm bg-white p-5 rounded shadow" style="width: 380px;">
        <div class="Form">
            <div class="title text-center mb-4">
                <h1 class="mb-2">Sign Up</h1>
                <?php if ($message): ?>
                    <div class="toast align-items-center text-white <?php echo $toastClass; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo $message; ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fa-solid fa-envelope me-2"></i>Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label"><i class="fa-solid fa-user me-2"></i>Full Name</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label"><i class="fa-solid fa-lock me-2"></i>Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label"><i class="fa-solid fa-camera-retro me-2"></i>Profile Photo</label>
                    <input type="file" class="form-control" id="photo" name="profile_photo" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label"><i class="fa-solid fa-user-shield me-2"></i>Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100" style="background-color:rgb(36, 179, 114);">Sign Up</button>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php" style="color:rgb(36, 179, 114);" class="text-decoration-none">Login</a></p>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let toastElList = [].slice.call(document.querySelectorAll('.toast'))
    let toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl, { delay: 3000 });
    });
    toastList.forEach(toast => toast.show());
</script>
</body>
</html>
