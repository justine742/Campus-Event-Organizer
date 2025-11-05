<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Campus Event Tracker</title>
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

        /* About Section */
        .about-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .about-box {
            background: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            max-width: 800px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
            text-align: justify;
        }

        .about-box h2 {
            margin-bottom: 20px;
            color: #ffffff;
            font-size: 2em;
            text-align: center;
        }

        .about-box p {
            margin-bottom: 20px;
            color: #cccccc;
            font-size: 1.1em;
            line-height: 1.8;
            text-align: justify;
        }

        .about-box .highlight {
            color: #2979ff;
            font-weight: bold;
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
            <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
            <li><a href="about.php" class="active"><i class="fas fa-info-circle"></i> About Us</a></li>
        </ul>
    </nav>

    <!-- About Section -->
    <div class="about-container">
        <div class="about-box">
            <h2><i class="fas fa-users"></i> About Us</h2>
            <p>
                Welcome to <span class="highlight">Campus Event Tracker</span> — your all-in-one platform 
                for managing, tracking, and organizing campus events.  
            </p>
            <p>
                This system provides dedicated dashboards for <span class="highlight">Students</span>, 
                <span class="highlight">Organizers</span>, and <span class="highlight">Admins</span>, 
                making it easier to connect the community through activities, seminars, 
                and events.
            </p>
            <p>
                Our goal is to enhance <span class="highlight">student engagement</span> and 
                <span class="highlight">event management</span> using technology, ensuring 
                every event is more accessible, organized, and memorable.
            </p>
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
