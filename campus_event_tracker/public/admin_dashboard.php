<?php
require_once "../config/config.php";

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch counts from database
$studentCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'];
$organizerCount = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='organizer'")->fetch_assoc()['total'];
$eventCount = $conn->query("SELECT COUNT(*) as total FROM events")->fetch_assoc()['total'];
$logCount = $conn->query("SELECT COUNT(*) as total FROM system_logs")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Campus Event Tracker</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b); color: #e0e0e0; display: flex; min-height: 100vh; }

    /* Sidebar */
    .sidebar { width: 240px; background: #000; color: #fff; display: flex; flex-direction: column; padding: 20px 0; position: fixed; height: 100%; transition: all 0.3s ease; z-index: 100; }
    .sidebar .logo { text-align: center; font-size: 22px; font-weight: bold; margin-bottom: 30px; }
    .sidebar .logo i { color: #2979ff; margin-right: 8px; }
    .sidebar a { color: #e0e0e0; text-decoration: none; padding: 12px 25px; display: flex; align-items: center; gap: 10px; transition: all 0.3s ease; font-size: 16px; border-left: 4px solid transparent; }
    .sidebar a:hover { background: #333; color: #fff; border-left: 4px solid #2979ff; }
    .sidebar a.active { background: #333; color: #fff; border-left: 4px solid #2979ff; }
    .sidebar a.logout { color: #ff6b6b; margin-top: auto; border-top: 1px solid #333; padding-top: 15px; }

    /* Hamburger (mobile) */
    .hamburger { display: none; position: fixed; top: 15px; left: 15px; background: none; border: none; font-size: 1.8em; color: #fff; cursor: pointer; z-index: 200; }

    /* Main content */
    .main-content { margin-left: 240px; padding: 40px; width: 100%; transition: 0.3s; }
    .main-header { margin-bottom: 30px; border-bottom: 2px solid #2979ff; padding-bottom: 10px; text-align: center; }
    .main-header h1 { color: #fff; margin-bottom: 5px; font-size: 2em; }
    .main-header p { color: #aaa; font-size: 1em; }

    /* Dashboard cards */
    .stats-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 40px; }
    .stat-card { padding: 25px; border-radius: 12px; text-align: center; color: #fff; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.4); cursor: pointer; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .stat-card i { font-size: 2.2em; margin-bottom: 10px; }
    .stat-card h3 { font-size: 1.3em; margin-bottom: 5px; }
    .stat-card p { font-size: 0.95em; opacity: 0.9; }
    .stat-card span { font-size: 1.8em; font-weight: bold; }

    /* Card colors */
    .blue { background: linear-gradient(135deg, #1565c0, #1e88e5); }
    .green { background: linear-gradient(135deg, #2e7d32, #43a047); }
    .orange { background: linear-gradient(135deg, #ef6c00, #fb8c00); }
    .purple { background: linear-gradient(135deg, #6a1b9a, #8e24aa); }

    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.6); }

    /* Info section */
    .info-section { background: linear-gradient(135deg, #202020, #252525, #2d2d2d); border-left: 4px solid #2979ff; border-radius: 12px; padding: 30px 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.4); line-height: 1.7; max-width: 900px; margin: 0 auto; }
    .info-section h2 { color: #2979ff; margin-bottom: 15px; font-size: 1.6em; text-align: center; }
    .info-section p { color: #ccc; text-align: justify; margin-bottom: 10px; }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.show { transform: translateX(0); }
      .hamburger { display: block; }
      .main-content { margin-left: 0; padding-top: 70px; }
      .stats-container { grid-template-columns: 1fr; }
      .info-section { padding: 25px; }
    }
  </style>
</head>
<body>

   <!-- Sidebar -->
    <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
    <div class="sidebar" id="sidebar">
        <div class="logo"><i class="fas fa-user-shield"></i> Admin</div>
        <a href="admin_dashboard.php" class="active"><i class="fas fa-home"></i> Home</a>
        <a href="studentlist.php"><i class="fas fa-user-graduate"></i> Student List</a>
        <a href="organizerlist.php"><i class="fas fa-user-tie"></i> Organizer List</a>
        <a href="eventlist.php"><i class="fas fa-calendar-days"></i> Event List</a>
        <a href="system_logs.php"><i class="fas fa-shield-alt"></i> System Logs</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Welcome, Admin!</h1>
      <p>Monitor your system activities and manage users efficiently.</p>
    </div>

    <!-- Quick Stats -->
    <div class="stats-container">
      <a href="studentlist.php" class="stat-card blue">
        <i class="fas fa-user-graduate"></i>
        <h3>Students</h3>
        <span><?= $studentCount ?></span>
        <p>View all registered students in the system.</p>
      </a>
      <a href="organizerlist.php" class="stat-card green">
        <i class="fas fa-user-tie"></i>
        <h3>Organizers</h3>
        <span><?= $organizerCount ?></span>
        <p>View all registered organizers in the system.</p>
      </a>
      <a href="eventlist.php" class="stat-card purple">
        <i class="fas fa-calendar-days"></i>
        <h3>Events</h3>
        <span><?= $eventCount ?></span>
        <p>Manage and monitor campus events efficiently.</p>
      </a>
      <a href="system_logs.php" class="stat-card orange">
        <i class="fas fa-shield-alt"></i>
        <h3>System Logs</h3>
        <span><?= $logCount ?></span>
        <p>Review recent activity and security logs.</p>
      </a>
    </div>

    <!-- Info Section -->
    <div class="info-section">
      <h2>System Overview</h2>
      <p>
        The <strong>Campus Event Tracker Admin Dashboard</strong> gives administrators total control 
        over user management and event monitoring. You can oversee all registered users, 
        view detailed activity logs, and ensure that events are properly managed across 
        the campus community.
      </p>
      <p>
        Navigate using the sidebar or the dashboard cards to access specific modules. Each section is designed to be 
        intuitive, clean, and optimized for productivity — whether you're checking reports, 
        managing users, or reviewing system security updates.
      </p>
    </div>
  </div>

  <script>
    // Toggle sidebar for mobile
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");
    hamburger.addEventListener("click", () => { sidebar.classList.toggle("show"); });
  </script>
</body>
</html>
