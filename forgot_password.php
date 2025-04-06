<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();
        
        // Send reset link (for demo, we'll just show it)
        $reset_link = "http://localhost/automated/automated/reset_password.php?token=" . $token;
        $message = "Password reset link created: " . $reset_link;
        $success = true;
    } else {
        $error = "Email not found in our records";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>
    <div class="wrapper">
        <form method="POST" action="">
            <h1>Forgot Password</h1>
            
            <div class="input-box">
                <input type="email" name="email" placeholder="Enter your email" required>
                <i class='bx bxs-envelope'></i>
            </div>

            <button type="submit" class="button">Reset Password</button>

            <?php if (isset($error)): ?>
                <p style="color: red; margin-top: 10px;"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <p style="color: green; margin-top: 10px;"><?php echo $message; ?></p>
            <?php endif; ?>

            <div class="register-link">
                <p><a href="index.php">← Back to Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>