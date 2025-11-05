<?php
require_once "../config/config.php";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname    = trim($_POST["fname"]);
    $mname    = trim($_POST["mname"]);
    $lname    = trim($_POST["lname"]);
    $year     = $_POST["year_level"];
    $program  = trim($_POST["program"]);
    $sex      = $_POST["sex"];
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role     = $_POST["role"];

    if (empty($username)) {
        $msg = "⚠️ Username is required!";
    } elseif (strlen($password) < 8) {
        $msg = "⚠️ Password must be at least 8 characters long!";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $msg = "⚠️ Password must contain at least 1 uppercase letter!";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $msg = "⚠️ Password must contain at least 1 lowercase letter!";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $msg = "⚠️ Password must contain at least 1 number!";
    } elseif (!preg_match("/[\W]/", $password)) {
        $msg = "⚠️ Password must contain at least 1 special character!";
    } else {
        $hashedPassword = md5($password);

        $check = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            $msg = "⚠️ Account with this username already exists!";
        } else {
            $sql = "INSERT INTO users (fname, mname, lname, year_level, program, sex, username, password, role) 
                    VALUES ('$fname','$mname','$lname','$year','$program','$sex','$username','$hashedPassword','$role')";
            if ($conn->query($sql) === TRUE) {
                $msg = "✅ Registration successful! <a href='login.php'>Login</a>";
            } else {
                $msg = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Campus Event Tracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Reset */
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

        /* Navbar */
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

        /* Hamburger */
        .hamburger {
            display: none;
            font-size: 1.8em;
            cursor: pointer;
            background: none;
            border: none;
            color: #fff;
        }

        /* Register Container */
        .register-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .register-box {
            background: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        }

        .register-box h2 {
            margin-bottom: 20px;
            color: #ffffff;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        form input, form select {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #2c2c2c;
            color: #e0e0e0;
            font-size: 1em;
            width: 100%;
        }

        form input:focus, form select:focus {
            outline: 2px solid #2979ff;
        }

        .full { grid-column: span 2; }

        button {
            grid-column: span 2;
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

        button:hover {
            background: #1565c0;
        }

        .msg {
            margin-top: 15px;
            font-size: 0.9em;
        }

        .msg a {
            color: #2979ff;
            text-decoration: none;
        }

        .msg a:hover {
            text-decoration: underline;
        }

        /* Responsive */
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
            .register-box {
                padding: 30px;
            }
            .register-box h2 {
                font-size: 1.5em;
            }
            form input, form select {
                font-size: 0.9em;
            }
            button {
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
            <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="register.php" class="active"><i class="fas fa-user-plus"></i> Register</a></li>
            <li><a href="about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
        </ul>
    </nav>

    <!-- Register Form -->
    <div class="register-container">
        <div class="register-box">
            <h2><i class="fas fa-user-plus"></i> Register</h2>
            <form method="POST">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="mname" placeholder="Middle Name">
                <input type="text" name="lname" placeholder="Last Name" required>
                <select name="year_level" required>
                    <option value="">Select Year Level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                </select>
                <input type="text" name="program" placeholder="Program" required>
                <select name="sex" required>
                    <option value="">Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <input type="text" name="username" placeholder="Username" required class="full">
                <input type="password" name="password" placeholder="Password" required class="full">
                <select name="role" required class="full">
                    <option value="student">Student</option>
                    <option value="organizer">Organizer</option>
                </select>
                <button type="submit"><i class="fas fa-user-plus"></i> Register</button>
            </form>
            <?php if (!empty($msg)) { ?>
                <p class="msg"><?php echo $msg; ?></p>
            <?php } ?>
            <p class="msg"><a href="login.php"><i class="fas fa-sign-in-alt"></i> Back to Login</a></p>
        </div>
    </div>

    <script>
        const hamburger = document.getElementById("hamburger");
        const navLinks = document.getElementById("nav-links");
        hamburger.addEventListener("click", () => {
            navLinks.classList.toggle("show");
        });
    </script>
</body>
</html>
