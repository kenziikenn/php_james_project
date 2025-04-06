<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logout-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            padding: 60px 80px;
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            transition: all 0.5s ease;
        }

        .logout-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .logout-container h2 {
            font-size: 3rem;
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        .logout-container h2::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 4px;
            bottom: -10px;
            left: 0;
            background: linear-gradient(90deg, transparent, #fff, transparent);
            animation: shimmer 2s infinite;
        }

        .logout-container p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
        }

        .timer-circle {
            width: 120px;
            height: 120px;
            position: relative;
            margin: 30px auto;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
        }

        .timer-circle svg {
            transform: rotate(-90deg);
            width: 100%;
            height: 100%;
        }

        .timer-circle circle {
            fill: none;
            stroke-width: 6;
            stroke-linecap: round;
        }

        .timer-bg {
            stroke: rgba(255, 255, 255, 0.1);
        }

        .timer-progress {
            stroke: #fff;
            stroke-dasharray: 339.292;
            stroke-dashoffset: 0;
            transition: stroke-dashoffset 1s linear;
            filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.5));
        }

        .timer-number {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .countdown {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 20px;
            letter-spacing: 1px;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes shimmer {
            0% { opacity: 0.3; }
            50% { opacity: 1; }
            100% { opacity: 0.3; }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <h2>Goodbye!</h2>
        <p>Thank you for using our system. See you next time!</p>
        <div class="timer-circle">
            <svg>
                <circle class="timer-bg" cx="60" cy="60" r="54"></circle>
                <circle class="timer-progress" cx="60" cy="60" r="54"></circle>
            </svg>
            <div class="timer-number" id="timer">5</div>
        </div>
        <div class="countdown">Redirecting you To the Login Page.....</div>
    </div>

    <script>
        let timeLeft = 2;
        const timerElement = document.getElementById('timer');
        const timerProgress = document.querySelector('.timer-progress');
        const container = document.querySelector('.logout-container');
        
        const dashOffset = 339.292; // 2 * π * 54 (circle radius)
        timerProgress.style.strokeDasharray = dashOffset;

        const countdown = setInterval(() => {
            timeLeft--;
            timerElement.textContent = timeLeft;
            
            const progress = (timeLeft / 2) * dashOffset;
            timerProgress.style.strokeDashoffset = dashOffset - progress;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                container.style.transform = 'scale(0.8) translateY(50px)';
                container.style.opacity = '0';
                container.style.transition = 'all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                
                setTimeout(() => {
                    window.location.href = '../index.php';
                }, 600);
            }
        }, 1000);
    </script>
</body>
</html>
