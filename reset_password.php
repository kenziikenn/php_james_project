<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if (!isset($_GET['token'])) {
    header("Location: index.php");
    exit();
}

$token = $_GET['token'];
$current_time = date('Y-m-d H:i:s');

// Verify token
$stmt = $conn->prepare("SELECT * FROM account WHERE reset_token = ? AND reset_expiry > ?");
$stmt->bind_param("ss", $token, $current_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "Invalid or expired reset link";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($error)) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $stmt = $conn->prepare("UPDATE account SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);
        $stmt->execute();
        
        header("Location: index.php?password_reset=success");
        exit();
    } else {
        $error = "Passwords do not match";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>
    <div class="wrapper">
        <form method="POST" action="">
            <h1>Reset Password</h1>
            
            <?php if (isset($error)): ?>
                <p style="color: red; margin-bottom: 20px;"><?php echo $error; ?></p>
            <?php else: ?>
                <div class="input-box">
                    <input type="password" name="password" placeholder="New Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <button type="submit" class="button">Update Password</button>
            <?php endif; ?>

            <div class="register-link">
                <p><a href="index.php">← Back to Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>