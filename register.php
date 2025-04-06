

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-up</title>
    <link rel="stylesheet" href="register.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
</head>
<body>
    <div class="wrapper">
    <form method="post" action="function/actions.php?submit">
        <h1>Sign-up</h1>

        <div class="input-box">
            <input type="text" id="fn" name="fn" placeholder="First Name" required />
            <i class='bx bxs-user'></i>
        </div>

        <div class="input-box">
            <input type="text" id="ln" name="ln" placeholder="Last Name" required />
            <i class='bx bxs-user'></i>
        </div>

        <div class="input-box">
            <input type="text" id="un" name="un" placeholder="Username" required />
            <i class='bx bxs-user'></i>
        </div>


        <div class="input-box">
            <input type="password" id="pw" name="pw" placeholder="Password" required />
            <i class='bx bxs-lock-alt'></i>
        </div>
        
        <button type="submit" id="submit" name="submit" class="button">Sign-up</button>

        <div class="register-link">
            <p>Already have an account? <a href="index.php">Login</a></p>
        </div>
        
    </form>
    </div>
</body>
</html>

<style>
    /* Add these styles to match your existing design */
    .input-box select {
        width: 100%;
        height: 45px;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, .2);
        border-radius: 40px;
        font-size: 16px;
        padding: 0 45px 0 20px;
        color: #fff;
    }

    .input-box select option {
        background: #1f293a;
        color: #fff;
    }

    .input-box select:focus {
        border-color: #fff;
    }
</style>
