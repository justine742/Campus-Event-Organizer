<?php
require_once "../config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only admin can access
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../login.php");
    exit;
}

// Fetch all organizers
$query = "SELECT fname, mname, lname, username, created_at FROM users WHERE role='organizer' ORDER BY lname ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Organizer List | Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #141414, #1e1e1e, #2b2b2b);
      color: #e0e0e0;
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
      width: 240px;
      background: #000;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 20px 0;
      position: fixed;
      height: 100%;
      transition: transform 0.3s ease;
      z-index: 200;
    }

    .sidebar .logo {
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 30px;
    }

    .sidebar .logo i {
      color: #43a047;
      margin-right: 8px;
    }

    .sidebar a {
      color: #e0e0e0;
      text-decoration: none;
      padding: 12px 25px;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s ease;
      font-size: 16px;
      border-left: 4px solid transparent;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #333;
      color: #fff;
      border-left: 4px solid #43a047;
    }

    .sidebar a.logout {
      color: #ff6b6b;
      margin-top: auto;
      border-top: 1px solid #333;
      padding-top: 15px;
    }

    /* Hamburger (mobile) */
    .hamburger {
      display: none;
      position: fixed;
      top: 15px;
      left: 15px;
      background: none;
      border: none;
      font-size: 1.8em;
      color: #fff;
      cursor: pointer;
      z-index: 300;
    }

    /* Overlay for mobile */
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: 150;
    }

    .overlay.show {
      display: block;
    }

    /* Main content */
    .main-content {
      margin-left: 240px;
      padding: 40px;
      width: 100%;
      transition: 0.3s;
    }

    .main-header {
      margin-bottom: 30px;
      border-bottom: 2px solid #43a047;
      padding-bottom: 10px;
      text-align: center;
    }

    .main-header h1 {
      color: #fff;
      margin-bottom: 5px;
      font-size: 2em;
    }

    .main-header p {
      color: #aaa;
      font-size: 1em;
    }

    /* Table styling */
    .table-container {
      overflow-x: auto;
      background: #1e1e1e;
      border-radius: 8px;
      padding: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 700px;
    }

    th,
    td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #333;
      white-space: nowrap;
    }

    th {
      background: #43a047;
      color: #fff;
    }

    tr:hover {
      background: #333;
    }

    td {
      color: #e0e0e0;
    }

    .remove-btn {
      color: #ff6b6b;
      text-decoration: none;
      font-weight: bold;
    }

    .remove-btn:hover {
      text-decoration: underline;
    }

    /* Responsive styles */
    @media (max-width: 1024px) {
      .main-content {
        padding: 20px;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .hamburger {
        display: block;
      }

      .main-content {
        margin-left: 0;
        padding-top: 70px;
      }

      .main-header h1 {
        font-size: 1.6em;
      }

      th, td {
        padding: 10px;
        font-size: 14px;
      }
    }

    @media (max-width: 480px) {
      .sidebar {
        width: 200px;
      }

      .main-header h1 {
        font-size: 1.3em;
      }

      .main-header p {
        font-size: 0.85em;
      }

      table {
        font-size: 13px;
      }

      th, td {
        padding: 8px;
      }

      .remove-btn {
        font-size: 12px;
      }
    }
  </style>
</head>
<body>
  <!-- Hamburger -->
  <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>

  <!-- Overlay -->
  <div class="overlay" id="overlay"></div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="logo"><i class="fas fa-user-shield"></i> Admin</div>
    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Home</a>
    <a href="studentlist.php"><i class="fas fa-user-graduate"></i> Student List</a>
    <a href="organizerlist.php" class="active"><i class="fas fa-user-tie"></i> Organizer List</a>
    <a href="eventlist.php"><i class="fas fa-calendar-days"></i> Event List</a>
    <a href="system_logs.php"><i class="fas fa-shield-alt"></i> System Logs</a>
    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Organizer List</h1>
      <p>All registered organizers in the system.</p>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Username</th>
            <th>Registered At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['fname'].' '.($row['mname'] ? $row['mname'].' ' : '').$row['lname']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars(date("M d, Y H:i", strtotime($row['created_at']))); ?></td>
                <td>
                  <a href="delete_organizer.php?username=<?php echo urlencode($row['username']); ?>" 
                     class="remove-btn" 
                     onclick="return confirm('Are you sure you want to remove this organizer?');">
                     Remove
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No organizers found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Toggle sidebar for mobile
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");
    const overlay = document.getElementById("overlay");

    hamburger.addEventListener("click", () => {
      sidebar.classList.toggle("show");
      overlay.classList.toggle("show");
    });

    overlay.addEventListener("click", () => {
      sidebar.classList.remove("show");
      overlay.classList.remove("show");
    });
  </script>
</body>
</html>
