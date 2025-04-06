<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            background: linear-gradient(135deg, #6C5CE7, #a393eb);
            display: flex;
            align-items: center;
            justify-content: center;
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
            border-color: #6C5CE7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            font-size: 14px;
        }

        .forgot-password {
            color: #6C5CE7;
            text-decoration: none;
            font-size: 14px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #6C5CE7;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #5849c2;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            color: #6C5CE7;
        }

        #error-message {
            color: #ff4757;
            text-align: center;
            margin-bottom: 15px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>Admin Login</h1>
            <p>Welcome back! Please enter your credentials</p>
        </div>

        <form id="loginForm">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <div class="options">
                <label class="remember-me">
                    <input type="checkbox" id="showPassword">
                    <span>Show Password</span>
                </label>
                <a href="../forgot_password.php" class="forgot-password">Forgot Password?</a>
            </div>

            <div id="error-message">Invalid username or password</div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="back-link">
            <a href="../index.php">← Back to Portal Selection</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#showPassword').change(function() {
                var passwordField = $('#password');
                passwordField.attr('type', this.checked ? 'text' : 'password');
            });

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '../function/actions.php?login',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.trim() === "admin") {
                            window.location.href = 'hp.php';
                        } else {
                            $('#error-message').show();
                        }
                    }
                });
            });

            $('input').on('input', function() {
                $('#error-message').hide();
            });
        });
    </script>
</body>
</html>