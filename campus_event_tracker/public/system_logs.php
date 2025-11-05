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

// Handle remove action
if (isset($_POST['remove_selected']) && !empty($_POST['log_ids'])) {
    $ids = implode(',', array_map('intval', $_POST['log_ids']));
    $deleteQuery = "DELETE FROM system_logs WHERE id IN ($ids)";
    $conn->query($deleteQuery);
    header("Location: system_logs.php");
    exit;
}

// Fetch system logs (latest first)
$query = "SELECT id, user_fullname, role, action, log_time FROM system_logs ORDER BY log_time DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>System Logs | Campus Event Tracker</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b); color:#e0e0e0; display:flex; min-height:100vh; }

/* Sidebar */
.sidebar { width:240px; background:#000; color:#fff; display:flex; flex-direction:column; padding:20px 0; position:fixed; height:100%; transition:all 0.3s ease; z-index:100; }
.sidebar .logo { text-align:center; font-size:22px; font-weight:bold; margin-bottom:30px; }
.sidebar .logo i { color:#2979ff; margin-right:8px; }
.sidebar a { color:#e0e0e0; text-decoration:none; padding:12px 25px; display:flex; align-items:center; gap:10px; transition:all 0.3s ease; font-size:16px; border-left:4px solid transparent; }
.sidebar a:hover { background:#333; color:#fff; border-left:4px solid #2979ff; }
.sidebar a.active { background:#333; color:#fff; border-left:4px solid #2979ff; }
.sidebar a.logout { color:#ff6b6b; margin-top:auto; border-top:1px solid #333; padding-top:15px; }

/* Hamburger */
.hamburger { display:none; position:fixed; top:15px; left:15px; background:none; border:none; font-size:1.8em; color:#fff; cursor:pointer; z-index:200; }

/* Main content */
.main-content { margin-left:240px; padding:40px; width:100%; transition:0.3s; }
.main-header { margin-bottom:30px; border-bottom:2px solid #2979ff; padding-bottom:10px; text-align:center; }
.main-header h1 { color:#fff; margin-bottom:5px; font-size:2em; }
.main-header p { color:#aaa; font-size:1em; }

/* Table container */
.table-container { background:#222; border-radius:10px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.3); overflow-x:auto; display:flex; flex-direction:column; gap:15px; }
table { width:100%; border-collapse:collapse; color:#ddd; }
thead { background:#111; }
th, td { padding:14px 12px; text-align:left; border-bottom:1px solid #333; }
th { color:#2979ff; font-weight:600; text-transform:uppercase; font-size:0.9em; }
tr:hover { background:#2b2b2b; transition:0.3s; }

/* Remove Selected Button */
.remove-btn {
    display: inline-block;
    background-color: #ff6b6b;
    color: #fff;
    font-weight: bold;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    align-self: flex-end; /* float right */
}

.remove-btn:hover {
    background-color: #ff4b4b;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width:768px) {
  .sidebar { transform:translateX(-100%); }
  .sidebar.show { transform:translateX(0); }
  .hamburger { display:block; }
  .main-content { margin-left:0; padding-top:70px; }
}
</style>
</head>
<body>
<!-- Sidebar -->
<button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
<div class="sidebar" id="sidebar">
  <div class="logo"><i class="fas fa-user-shield"></i> Admin</div>
  <a href="admin_dashboard.php"> <i class="fas fa-home"></i> Home</a>
  <a href="studentlist.php"> <i class="fas fa-user-graduate"></i> Student List</a>
  <a href="organizerlist.php"> <i class="fas fa-user-tie"></i> Organizer List</a>
  <a href="eventlist.php"><i class="fas fa-calendar-days"></i> Event List</a>
  <a href="system_logs.php" class="active"> <i class="fas fa-shield-alt"></i> System Logs</a>
  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="main-header">
    <h1>System Logs</h1>
    <p>Track all login, logout, and system activities of users.</p>
  </div>

  <form method="POST" onsubmit="return confirm('Are you sure you want to remove selected logs?');">
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th><input type="checkbox" id="select_all"></th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Action</th>
            <th>Date and Time</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" name="log_ids[]" value="<?= $row['id'] ?>"></td>
                <td><?= htmlspecialchars($row['user_fullname']) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                <td><?= htmlspecialchars($row['action']) ?></td>
                <td><?= htmlspecialchars($row['log_time']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center; color:#aaa;">No logs found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <button type="submit" name="remove_selected" class="remove-btn">Remove Selected</button>
    </div>
  </form>
</div>

<script>
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");
hamburger.addEventListener("click", () => sidebar.classList.toggle("show"));

// Select/Deselect all checkboxes
document.getElementById('select_all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="log_ids[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
</body>
</html>
