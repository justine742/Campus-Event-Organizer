<?php
require_once "../config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = md5($_POST["password"]);

    // Check user credentials
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Store session info
        $_SESSION["username"] = $row["username"];
        $_SESSION["role"] = $row["role"];
        $_SESSION["fname"] = $row["fname"];
        $_SESSION["lname"] = $row["lname"];

        // Combine full name
        $fullname = $row["fname"] . " " . $row["lname"];
        $role = $row["role"];
        $action = "Logged in";

        // Insert into system logs
        $stmt = $conn->prepare("INSERT INTO system_logs (user_fullname, role, action) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $role, $action);
        $stmt->execute();

        // Redirect by role
        if ($role == "admin") {
            header("Location: admin_dashboard.php");
        } elseif ($role == "organizer") {
            header("Location: organizer_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Event Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1c1c1c, #2a2a2a, #383838);
            color: #e0e0e0;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #000;
            padding: 15px 25px;
            color: #fff;
            position: relative;
        }

        nav .logo {
            font-size: 22px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: #e0e0e0;
            padding: 8px 14px;
            border-radius: 5px;
            transition: 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        nav ul li a:hover,
        nav ul li a.active {
            background: #333;
            color: #fff;
        }

        .hamburger {
            display: none;
            font-size: 1.8em;
            cursor: pointer;
            background: none;
            border: none;
            color: #fff;
        }

        .login-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .login-box {
            background: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #ffffff;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: none;
            border-radius: 8px;
            background: #2c2c2c;
            color: #e0e0e0;
            font-size: 1em;
        }

        .login-box input:focus {
            outline: 2px solid #2979ff;
        }

        .login-box button {
            width: 100%;
            padding: 14px;
            background: #2979ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .login-box button:hover {
            background: #1565c0;
        }

        .error-msg {
            color: #ff6b6b;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .login-box a {
            color: #2979ff;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            font-size: 0.9em;
        }

        .login-box a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                background: #111;
                position: absolute;
                top: 60px;
                right: 0;
                width: 200px;
                display: none;
                padding: 15px 0;
                border-radius: 0 0 8px 8px;
            }

            nav ul.show {
                display: flex;
            }

            nav ul li {
                text-align: center;
                width: 100%;
            }

            nav ul li a {
                width: 100%;
                padding: 12px;
                justify-content: center;
            }

            .hamburger {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 18px;
            }
            .login-box {
                padding: 30px;
            }
            .login-box h2 {
                font-size: 1.5em;
            }
            .login-box input {
                font-size: 0.9em;
            }
            .login-box button {
                font-size: 1em;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="logo"><i class="fas fa-calendar-check"></i> Campus Event Tracker</div>
        <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
        <ul id="nav-links">
            <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
        </ul>
    </nav>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-box">
            <h2><i class="fas fa-user-lock"></i> Login</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
            <?php if (!empty($error)) { ?>  
                <p class="error-msg"><?php echo $error; ?></p>
            <?php } ?>
            <a href="register.php"><i class="fas fa-user-plus"></i> Don't have an account? Register</a>
        </div>
    </div>

    <script>
        // Toggle mobile menu
        const hamburger = document.getElementById("hamburger");
        const navLinks = document.getElementById("nav-links");

        hamburger.addEventListener("click", () => {
            navLinks.classList.toggle("show");
        });
    </script>
</body>
</html>
