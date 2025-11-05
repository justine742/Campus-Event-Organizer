<?php
require_once "../config/config.php";

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not student
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

$username = $_SESSION["username"];

// ✅ Count upcoming events
$today = date('Y-m-d');
$upcoming_query = $conn->prepare("SELECT COUNT(*) AS total FROM events WHERE event_date >= ?");
$upcoming_query->bind_param("s", $today);
$upcoming_query->execute();
$upcoming_result = $upcoming_query->get_result()->fetch_assoc();
$total_upcoming = $upcoming_result['total'] ?? 0;

// ✅ Count attendance
$attendance_query = $conn->prepare("SELECT COUNT(*) AS total FROM attendance WHERE student_username=? AND status='Present'");
$attendance_query->bind_param("s", $username);
$attendance_query->execute();
$attendance_result = $attendance_query->get_result()->fetch_assoc();
$total_attendance = $attendance_result['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard | Campus Event Tracker</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* {
  margin:0; padding:0; box-sizing:border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
  background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b);
  color:#e0e0e0;
  display:flex;
  min-height:100vh;
}

/* Sidebar */
.sidebar {
  width:240px;
  background:#000;
  color:#fff;
  display:flex;
  flex-direction:column;
  padding:20px 0;
  position:fixed;
  height:100%;
  transition:all 0.3s ease;
  z-index:100;
}
.sidebar .logo {
  text-align:center;
  font-size:22px;
  font-weight:bold;
  margin-bottom:30px;
}
.sidebar .logo i { color:#2979ff; margin-right:8px; }

.sidebar a {
  color:#e0e0e0;
  text-decoration:none;
  padding:12px 25px;
  display:flex;
  align-items:center;
  gap:10px;
  font-size:16px;
  border-left:4px solid transparent;
  transition:0.3s;
}
.sidebar a:hover,
.sidebar a.active {
  background:#333;
  border-left:4px solid #2979ff;
}
.sidebar a.logout {
  color:#ff6b6b;
  margin-top:auto;
  border-top:1px solid #333;
  padding-top:15px;
}

/* Hamburger */
.hamburger {
  display:none;
  position:fixed;
  top:15px;
  left:15px;
  font-size:1.8em;
  color:#fff;
  cursor:pointer;
  z-index:200;
  background:none;
  border:none;
}

/* Main content */
.main-content {
  margin-left:240px;
  padding:40px;
  width:100%;
  transition:0.3s;
}
.main-header {
  margin-bottom:30px;
  border-bottom:2px solid #2979ff;
  padding-bottom:10px;
  text-align:center;
}
.main-header h1 {
  color:#fff;
  margin-bottom:5px;
  font-size:2em;
}
.main-header p { color:#aaa; font-size:1em; }

/* Dashboard cards */
.stats-container {
  display:grid;
  grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
  gap:25px;
  margin-bottom:40px;
}
.stat-card {
  padding:25px;
  border-radius:12px;
  text-align:center;
  color:#fff;
  transition:0.3s;
  box-shadow:0 5px 15px rgba(0,0,0,0.4);
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:10px;
  text-decoration:none;
}
.stat-card i { font-size:2.5em; margin-bottom:10px; }
.stat-card h3 { font-size:1.4em; margin-bottom:5px; }
.stat-card p { font-size:1em; opacity:0.9; }

/* Card colors */
.blue { background:linear-gradient(135deg, #1565c0, #1e88e5); }
.green { background:linear-gradient(135deg, #2e7d32, #43a047); }

/* Hover effect */
.stat-card:hover {
  transform:translateY(-5px);
  box-shadow:0 8px 20px rgba(0,0,0,0.6);
}

/* Info section */
.info-section {
  background:linear-gradient(135deg, #202020, #252525, #2d2d2d);
  border-left:4px solid #2979ff;
  border-radius:12px;
  padding:30px 40px;
  box-shadow:0 5px 20px rgba(0,0,0,0.4);
  line-height:1.7;
  max-width:900px;
  margin:0 auto;
}
.info-section h2 {
  color:#2979ff;
  margin-bottom:15px;
  font-size:1.6em;
  text-align:center;
}
.info-section p {
  color:#ccc;
  text-align:justify;
  margin-bottom:10px;
}

/* ================== Responsive ================== */
@media (max-width:1024px) {
  .main-content { padding:25px; }
  .stat-card { padding:20px; }
}

@media (max-width:768px) {
  .sidebar { transform:translateX(-100%); }
  .sidebar.show { transform:translateX(0); }
  .hamburger { display:block; }
  .main-content { margin-left:0; padding-top:70px; }
  .stats-container { grid-template-columns:1fr; }
  .info-section { padding:20px; }
  .main-header h1 { font-size:1.6em; }
}

@media (max-width:480px) {
  .stat-card i { font-size:2em; }
  .stat-card h3 { font-size:1.1em; }
  .stat-card p { font-size:0.9em; }
  .info-section h2 { font-size:1.3em; }
  .info-section p { font-size:0.9em; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
<div class="sidebar" id="sidebar">
  <div class="logo"><i class="fas fa-user-graduate"></i> Student</div>
  <a href="student_dashboard.php" class="active"><i class="fas fa-home"></i> Home</a>
  <a href="student_events.php"><i class="fas fa-calendar-check"></i> Upcoming Events</a>
  <a href="student_attendance.php"><i class="fas fa-clipboard-list"></i> Attendance Records</a>
  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="main-header">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Stay updated with your upcoming campus events and attendance records.</p>
  </div>

  <!-- Quick Stats -->
  <div class="stats-container">
    <a href="student_events.php" class="stat-card blue">
      <i class="fas fa-calendar-check"></i>
      <h3><?php echo $total_upcoming; ?> Upcoming Events</h3>
      <p>Check out events happening soon!</p>
    </a>
    <a href="student_attendance.php" class="stat-card green">
      <i class="fas fa-clipboard-list"></i>
      <h3><?php echo $total_attendance; ?> Attendances</h3>
      <p>Track your participation in past events.</p>
    </a>
  </div>

  <!-- Info Section -->
  <div class="info-section">
    <h2>Dashboard Overview</h2>
    <p>
      This dashboard allows you to view upcoming events, monitor your attendance,
      and stay informed about campus activities. Explore different sections using
      the sidebar for easy navigation.
    </p>
    <p>
      Enjoy participating in events and make the most out of your university experience!
    </p>
  </div>
</div>

<script>
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");
hamburger.addEventListener("click", () => sidebar.classList.toggle("show"));
</script>

</body>
</html>
