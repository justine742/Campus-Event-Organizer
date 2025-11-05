<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Campus Event Tracker</title>
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
      z-index: 100;
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
      transition: 0.3s ease;
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

    /* Hero Section */
    .hero {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 60px 20px;
    }

    .hero-content {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      padding: 50px 40px;
      max-width: 800px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(8px);
    }

    .hero-content h1 {
      font-size: 2.3em;
      margin-bottom: 20px;
      color: #fff;
    }

    .hero-content h1 span {
      color: #2979ff;
    }

    .hero-content p {
      font-size: 1.1em;
      color: #ccc;
      margin-bottom: 30px;
      line-height: 1.8;
    }

    .btn {
      display: inline-block;
      padding: 12px 28px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      background: #2979ff;
      color: white;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #1565c0;
    }

    /* Responsive Navbar */
    @media (max-width: 768px) {
      nav ul {
        flex-direction: column;
        background: #111;
        position: absolute;
        top: 60px;
        right: 0;
        width: 220px;
        display: none;
        padding: 15px 0;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 6px 10px rgba(0,0,0,0.4);
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

      .hero-content {
        padding: 40px 25px;
      }

      .hero-content h1 {
        font-size: 2em;
        line-height: 1.3;
      }

      .hero-content p {
        font-size: 1em;
      }

      .btn {
        padding: 10px 22px;
        font-size: 0.95em;
      }
    }

    @media (max-width: 480px) {
      .hero-content h1 {
        font-size: 1.8em;
      }

      .hero-content p {
        font-size: 0.9em;
      }

      .btn {
        padding: 9px 18px;
        font-size: 0.9em;
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
      <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
      <li><a href="public/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
      <li><a href="public/register.php"><i class="fas fa-user-plus"></i> Register</a></li>
      <li><a href="public/about.php"><i class="fas fa-info-circle"></i> About Us</a></li>
    </ul>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to <span>Campus Event Tracker</span></h1>
      <p>Track and manage campus events effortlessly with dedicated dashboards for Students, Organizers, and Admins.</p>
      <a href="public/login.php" class="btn"><i class="fas fa-arrow-right"></i> Get Started</a>
    </div>
  </section>

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
