<?php
require_once "../config/config.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect if not student
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../login.php");
    exit;
}

$student_username = $_SESSION["username"];

// Fetch attendance records for this student
$query = "
    SELECT e.event_id, e.event_title, e.event_date, e.event_time, a.status
    FROM events e
    LEFT JOIN attendance a ON e.event_id = a.event_id AND a.student_username = ?
    ORDER BY e.event_date ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Records | Student Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { display:flex; min-height:100vh; background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b); color:#e0e0e0; }

/* Sidebar */
.sidebar { width:240px; background:#000; color:#fff; display:flex; flex-direction:column; padding:20px 0; position:fixed; height:100%; z-index:100; transition:0.3s; }
.sidebar .logo { text-align:center; font-size:22px; font-weight:bold; margin-bottom:30px; }
.sidebar .logo i { color:#2979ff; margin-right:8px; }
.sidebar a { color:#e0e0e0; text-decoration:none; padding:12px 25px; display:flex; align-items:center; gap:10px; font-size:16px; border-left:4px solid transparent; transition:0.3s; }
.sidebar a:hover, .sidebar a.active { background:#333; border-left:4px solid #2979ff; }
.sidebar a.logout { color:#ff6b6b; margin-top:auto; border-top:1px solid #333; padding-top:15px; }

/* Hamburger */
.hamburger { display:none; position:fixed; top:15px; left:15px; font-size:1.8em; color:#fff; cursor:pointer; z-index:200; background:none; border:none; }

/* Main content */
.main-content { margin-left:240px; padding:40px; width:100%; transition:0.3s; }
.main-header { margin-bottom:30px; border-bottom:2px solid #2979ff; padding-bottom:10px; text-align:center; }
.main-header h1 { color:#fff; margin-bottom:5px; font-size:2em; }
.main-header p { color:#aaa; font-size:1em; }

/* Table */
table { width:100%; border-collapse: collapse; background: #1e1e1e; border-radius:12px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.5); }
th, td { padding:15px 20px; text-align:left; border-bottom:1px solid #333; }
th { background: linear-gradient(135deg, #202020, #252525); color:#2979ff; font-size:1em; }
tr:hover { background:#2a2a2a; transition:0.3s; }

/* Status text */
.status-label {
    display:inline-block;
    min-width:100px;
    text-align:center;
    font-weight:bold;
    font-size:0.95em;
}
.status-label.present { color:#28a745; }
.status-label.absent { color:#dc3545; }
.status-label.notset { color:#aaa; }

/* =================== Responsive =================== */
@media(max-width:768px) { 
    .sidebar { transform: translateX(-100%); } 
    .sidebar.show { transform: translateX(0); } 
    .hamburger { display: block; } 
    .main-content { margin-left: 0; padding-top: 70px; } 

    table, thead, tbody, th, td, tr { display:block; width:100%; }
    thead { display:none; }
    tr { margin-bottom:20px; background:#222; border-radius:12px; padding:15px; box-shadow:0 3px 10px rgba(0,0,0,0.4); }
    td { text-align:left; padding:10px 15px; position:relative; }
    td::before { content: attr(data-label); display:block; font-weight:bold; color:#aaa; margin-bottom:5px; }
}

@media(max-width:480px){
    .main-header h1 { font-size:1.6em; }
    .main-header p { font-size:0.9em; }
    td { padding:8px 12px; }
    .status-label { min-width:auto; font-size:0.85em; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
<div class="sidebar" id="sidebar">
    <div class="logo"><i class="fas fa-user-graduate"></i> Student</div>
    <a href="student_dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="student_events.php"><i class="fas fa-calendar-check"></i> Upcoming Events</a>
    <a href="student_attendance.php" class="active"><i class="fas fa-clipboard-list"></i> Attendance Records</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="main-header">
        <h1>Attendance Records</h1>
        <p>Check your attendance status for each event.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $status = $row['status'] ?? 'Not set';
                    $status_class = strtolower(str_replace(' ', '', $status)); // present, absent, notset
                ?>
                <tr>
                    <td data-label="Event Title"><?= htmlspecialchars($row['event_title']) ?></td>
                    <td data-label="Date"><?= date("F d, Y", strtotime($row['event_date'])) ?></td>
                    <td data-label="Time"><?= date("h:i A", strtotime($row['event_time'])) ?></td>
                    <td data-label="Status"><span class="status-label <?= $status_class ?>"><?= ucfirst($status) ?></span></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:#aaa;">No attendance records found.</td>
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
