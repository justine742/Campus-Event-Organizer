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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Records | Organizer Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { display:flex; min-height:100vh; background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b); color:#e0e0e0; transition:0.3s; }

/* Sidebar */
.sidebar { width:240px; background:#000; color:#fff; display:flex; flex-direction:column; padding:20px 0; position:fixed; height:100%; z-index:100; transition: transform 0.3s ease; }
.sidebar .logo { text-align:center; font-size:22px; font-weight:bold; margin-bottom:30px; }
.sidebar .logo i { color:#2979ff; margin-right:8px; }
.sidebar a { color:#e0e0e0; text-decoration:none; padding:12px 25px; display:flex; align-items:center; gap:10px; font-size:16px; border-left:4px solid transparent; transition:0.3s; }
.sidebar a:hover, .sidebar a.active { background:#333; border-left:4px solid #2979ff; }
.sidebar a.logout { color:#ff6b6b; margin-top:auto; border-top:1px solid #333; padding-top:15px; }

/* Hamburger */
.hamburger { display:none; position:fixed; top:15px; left:15px; font-size:1.8em; color:#fff; cursor:pointer; z-index:200; background:#000; border:none; padding:10px; border-radius:6px; }
.hamburger:hover { background:#111; }

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

/* Attendance status labels */
.status-label.present { color: #28a745; font-weight:bold; }
.status-label.absent { color: #dc3545; font-weight:bold; }
.status-label.notset { color: #aaa; font-weight:bold; }

/* Responsive */
@media (max-width:768px) {
    .hamburger { display:flex; }

    .sidebar { transform:translateX(-100%); position:fixed; top:0; left:0; height:100%; }
    .sidebar.show { transform:translateX(0); }

    .main-content { margin-left:0; padding-top:80px; padding-left:20px; padding-right:20px; }

    table, thead, tbody, th, td, tr { display:block; width:100%; }
    thead { display:none; }
    tr { margin-bottom:20px; background:#2a2a2a; padding:15px; border-radius:10px; }
    td { text-align:right; padding-left:50%; position:relative; border:none; margin-bottom:10px; }
    td::before { 
        content: attr(data-label); 
        position:absolute; 
        left:15px; 
        width:45%; 
        text-align:left; 
        font-weight:bold; 
        color:#aaa; 
    }
}
</style>
</head>
<body>

<!-- Sidebar -->
<button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
<div class="sidebar" id="sidebar">
    <div class="logo"><i class="fas fa-user-tie"></i> Organizer</div>
    <a href="organizer_dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="create_event.php"><i class="fas fa-calendar-plus"></i> Create Event</a>
    <a href="event_list.php"><i class="fas fa-list"></i> Event List</a>
    <a href="attendance_records.php" class="active"><i class="fas fa-clipboard-list"></i> Attendance Records</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="main-header">
        <h1>Attendance Records</h1>
        <p>View attendance details of students for each event.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Event Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Student Full Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
        while ($event = $events->fetch_assoc()):
            $students = $conn->query("SELECT fname, mname, lname, username FROM users WHERE role='student'");
            while ($student = $students->fetch_assoc()):
                $full_name = $student['fname'] . ' ' . ($student['mname'] ? $student['mname'] . ' ' : '') . $student['lname'];
                
                $att_stmt = $conn->prepare("SELECT status FROM attendance WHERE event_id=? AND student_username=?");
                $att_stmt->bind_param("is", $event['event_id'], $student['username']);
                $att_stmt->execute();
                $att_result = $att_stmt->get_result();
                $status = $att_result->num_rows > 0 ? $att_result->fetch_assoc()['status'] : 'Not set';
                $status_class = $status === 'present' ? 'present' : ($status === 'absent' ? 'absent' : 'notset');
        ?>
            <tr>
                <td data-label="Event Title"><?= htmlspecialchars($event['event_title']) ?></td>
                <td data-label="Date"><?= date("F d, Y", strtotime($event['event_date'])) ?></td>
                <td data-label="Time"><?= date("h:i A", strtotime($event['event_time'])) ?></td>
                <td data-label="Student Full Name"><?= htmlspecialchars($full_name) ?></td>
                <td data-label="Status"><span class="status-label <?= $status_class ?>"><?= ucfirst($status) ?></span></td>
            </tr>
        <?php endwhile; endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// Toggle sidebar on mobile
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");
hamburger.addEventListener("click", () => { sidebar.classList.toggle("show"); });

// Close sidebar when clicking outside on mobile
document.addEventListener("click", (e) => {
    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.classList.remove("show");
    }
});
</script>

</body>
</html>
