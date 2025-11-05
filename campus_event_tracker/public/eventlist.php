<?php
require_once "../config/config.php";

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only admin can access
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch all events ordered by date and time
$query = "SELECT * FROM events ORDER BY event_date ASC, event_time ASC";
$result = $conn->query($query);

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM events WHERE event_id = $delete_id");
    header("Location: eventlist.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event List | Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * {
      margin:0; padding:0; box-sizing:border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b);
      color:#e0e0e0; display:flex; min-height:100vh;
    }

    /* Sidebar */
    .sidebar {
      width:240px; background:#000; color:#fff;
      display:flex; flex-direction:column;
      padding:20px 0; position:fixed; height:100%;
      transition:all 0.3s ease; z-index:100;
    }

    .sidebar .logo {
      text-align:center; font-size:22px; font-weight:bold;
      margin-bottom:30px;
    }

    .sidebar .logo i { color:#2979ff; margin-right:8px; }

    .sidebar a {
      color:#e0e0e0; text-decoration:none;
      padding:12px 25px; display:flex; align-items:center; gap:10px;
      transition:all 0.3s ease; font-size:16px;
      border-left:4px solid transparent;
    }

    .sidebar a:hover {
      background:#333; color:#fff; border-left:4px solid #2979ff;
    }

    .sidebar a.active {
      background:#333; color:#fff; border-left:4px solid #2979ff;
    }

    .sidebar a.logout {
      color:#ff6b6b; margin-top:auto;
      border-top:1px solid #333; padding-top:15px;
    }

    /* Hamburger */
    .hamburger {
      display:none; position:fixed; top:15px; left:15px;
      background:none; border:none; font-size:1.8em;
      color:#fff; cursor:pointer; z-index:200;
    }

    /* Main content */
    .main-content {
      margin-left:240px; padding:40px; width:100%;
      transition:0.3s;
    }

    .main-header {
      margin-bottom:30px; border-bottom:2px solid #2979ff;
      padding-bottom:10px; text-align:center;
    }

    .main-header h1 { color:#fff; margin-bottom:5px; font-size:2em; }
    .main-header p { color:#aaa; font-size:1em; }

    /* Table styling */
    table {
      width:100%; border-collapse:collapse;
      margin-top:20px; background:#1e1e1e;
      border-radius:8px; overflow:hidden;
    }

    th, td {
      padding:12px; text-align:left; border-bottom:1px solid #333;
    }

    th { background:#2979ff; color:#fff; }
    tr:hover { background:#333; }
    td { color:#e0e0e0; }

    .remove-btn {
      color:#ff6b6b; text-decoration:none; font-weight:bold;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 1024px) {
      .main-content { padding:20px; }
    }

    @media (max-width:768px) {
      .sidebar { transform:translateX(-100%); }
      .sidebar.show { transform:translateX(0); }
      .hamburger { display:block; }
      .main-content { margin-left:0; padding-top:70px; }
    }

    /* Table turns into cards on small screens */
    @media (max-width: 700px) {
      table, thead, tbody, th, td, tr { display:block; }
      thead { display:none; }

      tr {
        margin-bottom:15px;
        background:#1e1e1e;
        border:1px solid #333;
        border-radius:10px;
        padding:15px;
      }

      td {
        border:none;
        display:flex;
        justify-content:space-between;
        padding:10px 0;
      }

      td::before {
        content: attr(data-label);
        font-weight:bold;
        color:#2979ff;
      }

      .remove-btn {
        background:#ff6b6b;
        color:#fff;
        padding:6px 10px;
        border-radius:6px;
        text-align:center;
        display:inline-block;
        margin-top:5px;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>

  <div class="sidebar" id="sidebar">
    <div class="logo"><i class="fas fa-user-shield"></i> Admin</div>
    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="studentlist.php"><i class="fas fa-user-graduate"></i> Student List</a>
    <a href="organizerlist.php"><i class="fas fa-user-tie"></i> Organizer List</a>
    <a href="eventlist.php" class="active"><i class="fas fa-calendar-days"></i> Event List</a>
    <a href="system_logs.php"><i class="fas fa-shield-alt"></i> System Logs</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Event List</h1>
      <p>All scheduled events in the system.</p>
    </div>

    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Date</th>
          <th>Time</th>
          <th>Created By</th>
          <th>Created At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td data-label="Title"><?= htmlspecialchars($row['event_title']) ?></td>
              <td data-label="Description"><?= htmlspecialchars($row['event_description']) ?></td>
              <td data-label="Date"><?= htmlspecialchars(date("M d, Y", strtotime($row['event_date']))) ?></td>
              <td data-label="Time"><?= htmlspecialchars(date("h:i A", strtotime($row['event_time']))) ?></td>
              <td data-label="Created By"><?= htmlspecialchars($row['created_by']) ?></td>
              <td data-label="Created At"><?= htmlspecialchars(date("M d, Y H:i", strtotime($row['created_at']))) ?></td>
              <td data-label="Action">
                <a href="eventlist.php?delete_id=<?= $row['event_id'] ?>"
                   class="remove-btn"
                   onclick="return confirm('Are you sure you want to remove this event?');">Remove</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" style="text-align:center;">No events found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    // Toggle sidebar for mobile
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");
    hamburger.addEventListener("click", () => sidebar.classList.toggle("show"));
  </script>
</body>
</html>
