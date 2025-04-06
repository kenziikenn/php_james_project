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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>Judge Login</title>
    <!-- Keep PHP code unchanged at the top -->
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
    
        body {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            animation: gradientMove 15s ease infinite;
            margin: 0;
            padding: 0;
        }
    
        .wrapper {
            width: 400px;
            padding: 40px 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-out;
        }
    
        h1 {
            color: #fff;
            font-size: 24px;
            text-align: center;
            margin-bottom: 30px;
        }
    
        .input-box {
            margin-bottom: 20px;
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
        }
    
        .input-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 20px;
            z-index: 2;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    
        .input-box input {
            width: 100%;
            height: 55px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 0 25px 0 50px;
            color: #fff;
            font-size: 16px;
            transition: all 0.4s ease;
        }
    
        .input-box input:focus {
            border-color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.2);
        }
    
        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
    
        .button {
            width: 100%;
            height: 55px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 16px;
            color: #e73c7e;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            margin: 15px 0;
        }
    
        .button:hover {
            transform: translateY(-3px);
            background: #fff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
    
        .back-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            font-size: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
    
        .back-link a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
    
        .back-link a:hover {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
    
        .error-message {
            color: #fff;
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            padding: 10px;
            background: rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(5px);
        }
    
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
    
    <!-- Keep HTML structure unchanged -->
</head>
<body>
    <div class="wrapper">
        <h1>Judge Sign In</h1>
        <form method="POST" action="">
            <div class="input-box">
                <input type="text" name="judge_code" placeholder="Enter Judge Code" required>
                <i class='bx bxs-key'></i>
            </div>
            <button type="submit" class="button">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="back-link">
            <p><a href="index.php"><i class='bx bx-arrow-back'></i> Back to Main Login</a></p>
        </div>
    </div>
</body>
</html>