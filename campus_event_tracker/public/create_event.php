<?php
require_once "../config/config.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect if not organizer
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "organizer") {
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_title = trim($_POST["event_title"]);
    $event_description = trim($_POST["event_description"]);
    $event_date = $_POST["event_date"];
    $event_time = $_POST["event_time"];

    if (!empty($event_title) && !empty($event_date) && !empty($event_time)) {
        $stmt = $conn->prepare("INSERT INTO events (event_title, event_description, event_date, event_time, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $event_title, $event_description, $event_date, $event_time, $_SESSION["username"]);

        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO attendance (event_id) VALUES (?)");
            $stmt2->bind_param("i", $event_id);
            $stmt2->execute();
            $success = "Event created successfully!";
        } else {
            $error = "Error creating event.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Event | Organizer Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { display:flex; min-height:100vh; background: linear-gradient(135deg,#141414,#1e1e1e,#2b2b2b); color:#e0e0e0; transition: 0.3s; }

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
  transition: all 0.3s ease;
  z-index:100;
}
.sidebar.hide { transform:translateX(-100%); }
.sidebar .logo { text-align:center; font-size:22px; font-weight:bold; margin-bottom:30px; }
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
.sidebar a:hover, .sidebar a.active { background:#333; border-left:4px solid #2979ff; }
.sidebar a.logout { color:#ff6b6b; margin-top:auto; border-top:1px solid #333; padding-top:15px; }

/* Hamburger Button */
.hamburger {
  position:fixed;
  top:15px;
  left:15px;
  width:45px;
  height:45px;
  background:#000;
  border:none;
  border-radius:6px;
  color:#fff;
  font-size:1.5em;
  cursor:pointer;
  z-index:200;
  display:none;
  align-items:center;
  justify-content:center;
}
.hamburger:hover { background:#111; }

/* Main content */
.main-content { margin-left:240px; padding:40px; width:100%; transition:0.3s; }
.main-header { margin-bottom:30px; border-bottom:2px solid #2979ff; padding-bottom:10px; text-align:center; }
.main-header h1 { color:#fff; margin-bottom:5px; font-size:2em; }
.main-header p { color:#aaa; font-size:1em; }

/* Messages */
.message { margin-bottom:20px; padding:12px; border-radius:6px; text-align:center; font-weight:bold; }
.success { background:#2e7d32; color:#fff; }
.error { background:#c62828; color:#fff; }

/* Form Panel */
.form-panel {
  background: linear-gradient(135deg,#202020,#252525,#2d2d2d);
  padding:30px 40px;
  border-radius:12px;
  box-shadow:0 5px 20px rgba(0,0,0,0.4);
  width:100%;
  max-width:600px;
  margin:0 auto;
}
.form-panel h2 { color:#2979ff; margin-bottom:20px; text-align:center; }
label { display:block; margin-bottom:8px; color:#ccc; font-weight:500; }
input, textarea { width:100%; padding:12px; margin-bottom:20px; border:none; border-radius:6px; background:#333; color:#fff; font-size:1em; }
button { background:#2979ff; color:#fff; border:none; padding:14px 20px; border-radius:6px; cursor:pointer; font-size:1em; transition:0.3s; width:100%; }
button:hover { background:#1c5ed6; }

/* Responsive adjustments */
@media(max-width:1024px){
  .main-content { padding:25px; }
}
@media(max-width:768px){
  .hamburger { display:flex; }
  .sidebar { transform:translateX(-100%); position:fixed; top:0; left:0; }
  .sidebar.show { transform:translateX(0); }
  .main-content { margin-left:0; padding-top:70px; padding-left:20px; padding-right:20px; }
}
@media(max-width:480px){
  .form-panel { padding:20px; }
  .main-header h1 { font-size:1.6em; }
  .main-header p { font-size:0.9em; }
  input, textarea, button { font-size:0.95em; padding:10px; }
}
</style>
</head>
<body>

<!-- Hamburger -->
<button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="logo"><i class="fas fa-user-tie"></i> Organizer</div>
  <a href="organizer_dashboard.php"><i class="fas fa-home"></i> Home</a>
  <a href="create_event.php" class="active"><i class="fas fa-calendar-plus"></i> Create Event</a>
  <a href="event_list.php"><i class="fas fa-list"></i> Event List</a>
  <a href="attendance_records.php"><i class="fas fa-clipboard-list"></i> Attendance Records</a>
  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="main-header">
    <h1>Create New Event</h1>
    <p>Schedule campus events and automatically generate attendance records.</p>
  </div>

  <?php if(isset($success)) echo "<div class='message success'>$success</div>"; ?>
  <?php if(isset($error)) echo "<div class='message error'>$error</div>"; ?>

  <div class="form-panel">
    <h2>Event Details</h2>
    <form method="POST" action="">
      <label for="event_title">Event Title</label>
      <input type="text" name="event_title" id="event_title" placeholder="Enter event title" required>

      <label for="event_description">Event Description</label>
      <textarea name="event_description" id="event_description" rows="4" placeholder="Enter a short description..."></textarea>

      <label for="event_date">Event Date</label>
      <input type="date" name="event_date" id="event_date" required>

      <label for="event_time">Event Time</label>
      <input type="time" name="event_time" id="event_time" required>

      <button type="submit"><i class="fas fa-plus-circle"></i> Create Event</button>
    </form>
  </div>
</div>

<script>
// Toggle sidebar for mobile
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");

hamburger.addEventListener("click", () => {
    sidebar.classList.toggle("show");
});

// Close sidebar when clicking outside on mobile
document.addEventListener("click", (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.classList.remove("show");
    }
});
</script>

</body>
</html>
