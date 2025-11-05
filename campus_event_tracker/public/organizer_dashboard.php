<?php
require_once "../config/config.php";

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not organizer
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "organizer") {
    header("Location: ../login.php");
    exit;
}

// Get organizer username
$organizer = $_SESSION['username'];

// Fetch counts
$eventCount = $conn->query("SELECT COUNT(*) as total FROM events WHERE created_by='$organizer'")->fetch_assoc()['total'];

// Attendance count for organizer's events
$attendanceCountQuery = "
    SELECT COUNT(a.id) as total 
    FROM attendance a
    JOIN events e ON a.event_id = e.event_id
    WHERE e.created_by='$organizer'
";
$attendanceCount = $conn->query($attendanceCountQuery)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organizer Dashboard | Campus Event Tracker</title>
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
    <div class="logo"><i class="fas fa-user-tie"></i> Organizer</div>
    <a href="organizer_dashboard.php" class="active"><i class="fas fa-home"></i> Home</a>
    <a href="create_event.php"><i class="fas fa-calendar-plus"></i> Create Event</a>
    <a href="event_list.php"><i class="fas fa-list"></i> Event List</a>
    <a href="attendance_records.php"><i class="fas fa-clipboard-list"></i> Attendance Records</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="main-header">
        <h1>Welcome, Organizer!</h1>
        <p>Manage your events and track attendance efficiently.</p>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats-container">
        <a href="create_event.php" class="stat-card blue">
            <i class="fas fa-calendar-plus"></i>
            <h3>Create Event</h3>
            <p>Plan and schedule upcoming campus activities.</p>
        </a>
        <a href="event_list.php" class="stat-card purple">
            <i class="fas fa-list"></i>
            <h3>Event List</h3>
            <span><?= $eventCount ?></span>
            <p>View and manage all your events.</p>
        </a>
        <a href="attendance_records.php" class="stat-card green">
            <i class="fas fa-clipboard-list"></i>
            <h3>Attendance Records</h3>
            <span><?= $attendanceCount ?></span>
            <p>Monitor attendance for your events.</p>
        </a>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <h2>Dashboard Overview</h2>
        <p>
            As an organizer, you have access to create new events, view existing events, and track attendance of participants.
            Use the sidebar or dashboard cards to navigate quickly and manage campus activities with ease.
        </p>
        <p>
            Keep your events organized, ensure smooth coordination, and provide a seamless experience for all participants.
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
