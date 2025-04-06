<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judge_code = trim($_POST['judge_code']);
    
    $stmt = $conn->prepare("SELECT * FROM judges WHERE judge_code = ?");
    $stmt->bind_param("s", $judge_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $judge = $result->fetch_assoc();
        $_SESSION['judge_id'] = $judge['id'];
        $_SESSION['judge_name'] = $judge['first_name'] . ' ' . $judge['last_name'];  // Changed to use first_name and last_name
        $_SESSION['event_id'] = $judge['event_id'];  // Added event_id from the database
        header("Location: pages/judge_homepage.php");
        exit();
    } else {
        $error = "Invalid judge code";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #FF6B6B, #ffa502);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #FF6B6B;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 20px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #FF6B6B;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #FF6B6B;
        }

        .back-link a i {
            margin-right: 5px;
        }

        #error-message {
            color: #ff4757;
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            background-color: rgba(255, 71, 87, 0.1);
            display: none;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .login-container {
                width: 100%;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>Judge Login</h1>
            <p>Enter your judge code to access the scoring system</p>
        </div>

        <form method="POST" action="">
            <div class="input-group">
                <input type="text" name="judge_code" placeholder="Enter Judge Code" required>
                <i class='bx bx-id-card'></i>
            </div>

            <?php if (isset($error)): ?>
                <div id="error-message" style="display: block;"><?php echo $error; ?></div>
            <?php endif; ?>

            <button type="submit" class="login-btn">Access Scoring System</button>
        </form>

        <div class="back-link">
            <a href="index.php"><i class='bx bx-left-arrow-alt'></i>Back to Portal Selection</a>
        </div>
    </div>

    <!-- Remove jQuery and the AJAX script since we're using regular form submission -->
</body>
</html>