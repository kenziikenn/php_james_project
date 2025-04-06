<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
    <style>
        /* Same styles as admin-login.php but with different color scheme */
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
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        /* ... Rest of the styles same as admin-login.php but replace #6C5CE7 with #FF6B6B ... */
        
        .login-btn {
            background: #FF6B6B;
        }

        .login-btn:hover {
            background: #ff5252;
        }

        .forgot-password {
            color: #FF6B6B;
        }

        .back-link a:hover {
            color: #FF6B6B;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="header">
            <h1>Judge Login</h1>
            <p>Enter your judge code to access the scoring system</p>
        </div>

        <form id="loginForm">
            <div class="input-group">
                <input type="text" name="judgecode" placeholder="Judge Code" required>
                <i class='bx bx-id-card'></i>
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
            </div>

            <div id="error-message">Invalid judge code or password</div>

            <button type="submit" class="login-btn">Login as Judge</button>
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
                    url: '../function/actions.php?judge_login',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.trim() === "judge") {
                            window.location.href = 'judge-dashboard.php';
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