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

// Handle attendance action
if (isset($_GET['action'], $_GET['event_id'])) {
    $event_id = intval($_GET['event_id']);
    $status = $_GET['action'] === 'present' ? 'Present' : 'Absent';
    $student = $_SESSION['username'];

    $check = $conn->prepare("SELECT * FROM attendance WHERE event_id=? AND student_username=?");
    $check->bind_param("is", $event_id, $student);
    $check->execute();
    $res_check = $check->get_result();

    if ($res_check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE attendance SET status=?, timestamp=CURRENT_TIMESTAMP WHERE event_id=? AND student_username=?");
        $stmt->bind_param("sis", $status, $event_id, $student);
    } else {
        $stmt = $conn->prepare("INSERT INTO attendance (event_id, student_username, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $event_id, $student, $status);
    }
    $stmt->execute();
    header("Location: student_events.php");
    exit;
}

// Fetch upcoming events
$today = date('Y-m-d');
$query = "SELECT e.*, a.status AS attendance_status 
          FROM events e 
          LEFT JOIN attendance a ON e.event_id=a.event_id AND a.student_username=? 
          WHERE e.event_date >= ? 
          ORDER BY e.event_date ASC, e.event_time ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $_SESSION['username'], $today);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upcoming Events | Student Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* {
  margin:0; padding:0; box-sizing:border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
body {
  display:flex;
  min-height:100vh;
  background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b);
  color:#e0e0e0;
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

/* Main Content */
.main-content {
  margin-left:240px;
  padding:40px;
  width:100%;
  transition:0.3s;
}

/* Header */
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
.main-header p {
  color:#aaa;
  font-size:1em;
}

/* Event Table */
.event-table {
  width:100%;
  border-collapse:collapse;
  background:#1e1e1e;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 5px 15px rgba(0,0,0,0.5);
}
.event-table th, .event-table td {
  padding:15px 20px;
  text-align:left;
  border-bottom:1px solid #333;
}
.event-table th {
  background: linear-gradient(135deg, #202020, #252525);
  color:#2979ff;
  font-size:1em;
}
.event-table tr:hover { background:#2a2a2a; transition:0.3s; }

/* Attendance Buttons */
.attendance-btn {
  padding:6px 12px;
  border:none;
  border-radius:6px;
  cursor:pointer;
  font-weight:bold;
  font-size:0.9em;
  margin-right:5px;
  text-decoration:none;
  display:inline-block;
}
.attendance-btn.present { background:#2e7d32; color:#fff; }
.attendance-btn.absent { background:#c62828; color:#fff; }
.attendance-status.present { color:#2e7d32; font-weight:bold; }
.attendance-status.absent { color:#c62828; font-weight:bold; }

/* Mobile Responsive Table -> Card style */
@media(max-width:768px){
  .sidebar { transform:translateX(-100%); }
  .sidebar.show { transform:translateX(0); }
  .hamburger { display:block; }
  .main-content { margin-left:0; padding-top:70px; }

  .event-table, .event-table thead, .event-table tbody, .event-table th, .event-table td, .event-table tr {
    display:block;
    width:100%;
  }
  .event-table thead { display:none; }
  .event-table tr {
    margin-bottom:20px;
    border-radius:12px;
    background:#222;
    padding:15px;
    box-shadow:0 3px 10px rgba(0,0,0,0.4);
  }
  .event-table td {
    padding:10px 15px;
    text-align:left;
    position:relative;
  }
  .event-table td::before {
    content: attr(data-label);
    font-weight:bold;
    display:block;
    color:#aaa;
    margin-bottom:5px;
  }
  .attendance-btn { margin-bottom:5px; width:48%; text-align:center; }
}

/* Smaller devices adjustments */
@media(max-width:480px){
  .main-header h1 { font-size:1.6em; }
  .main-header p { font-size:0.9em; }
  .attendance-btn { font-size:0.8em; padding:5px 8px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
<div class="sidebar" id="sidebar">
  <div class="logo"><i class="fas fa-user-graduate"></i> Student</div>
  <a href="student_dashboard.php"> <i class="fas fa-home"></i> Home </a>
  <a href="student_events.php" class="active"> <i class="fas fa-calendar-check"></i> Upcoming Events </a>
  <a href="student_attendance.php"> <i class="fas fa-clipboard-list"></i> Attendance Records </a>
  <a href="logout.php" class="logout"> <i class="fas fa-sign-out-alt"></i> Logout </a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="main-header">
    <h1>Upcoming Events</h1>
    <p>Mark your attendance for upcoming events.</p>
  </div>

  <table class="event-table">
      <thead>
          <tr>
              <th>Title</th>
              <th>Description</th>
              <th>Date</th>
              <th>Time</th>
              <th>Organizer</th>
              <th>Action</th>
          </tr>
      </thead>
      <tbody>
          <?php if($result && $result->num_rows>0): ?>
              <?php while($row=$result->fetch_assoc()): ?>
              <tr>
                  <td data-label="Title"><?php echo htmlspecialchars($row['event_title']); ?></td>
                  <td data-label="Description"><?php echo htmlspecialchars($row['event_description']); ?></td>
                  <td data-label="Date"><?php echo htmlspecialchars($row['event_date']); ?></td>
                  <td data-label="Time"><?php echo htmlspecialchars($row['event_time']); ?></td>
                  <td data-label="Organizer"><?php echo htmlspecialchars($row['created_by']); ?></td>
                  <td data-label="Action">
                      <a href="?action=present&event_id=<?php echo $row['event_id']; ?>" class="attendance-btn present">Present</a>
                      <a href="?action=absent&event_id=<?php echo $row['event_id']; ?>" class="attendance-btn absent">Absent</a>
                      <?php if(!empty($row['attendance_status'])): ?>
                        <span class="attendance-status <?php echo strtolower($row['attendance_status']); ?>">
                          <?php echo $row['attendance_status']; ?>
                        </span>
                      <?php endif; ?>
                  </td>
              </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="6" style="text-align:center; color:#aaa;">No upcoming events.</td>
              </tr>
          <?php endif; ?>
      </tbody>
  </table>
</div>

<script>
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");
hamburger.addEventListener("click", () => sidebar.classList.toggle("show"));
</script>

</body>
</html>
