CREATE DATABASE campus_event_tracker;
USE campus_event_tracker;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fname` VARCHAR(100)       NOT NULL,
  `mname` VARCHAR(100)       NULL,
  `lname` VARCHAR(100)       NOT NULL,
  `year_level` ENUM('1st Year','2nd Year','3rd Year','4th Year') DEFAULT NULL,
  `program` VARCHAR(150)     DEFAULT NULL,
  `sex` ENUM('Male','Female') DEFAULT NULL,
  `username` VARCHAR(100)    NOT NULL UNIQUE,
  `password` VARCHAR(255)    NOT NULL,
  `role` ENUM('student','organizer','admin') NOT NULL DEFAULT 'student',
  `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP     NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin account
INSERT INTO `users` (fname, lname, username, password, role)
VALUES ('System', 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 'admin');

CREATE TABLE attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  student_username VARCHAR(100) NOT NULL,
  status ENUM('Present','Absent') DEFAULT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(event_id)
    ON DELETE CASCADE
);

CREATE TABLE system_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_fullname VARCHAR(150) NOT NULL,
    role ENUM('admin', 'organizer', 'student') NOT NULL,
    action VARCHAR(255) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

