-- Table for storing Student Identity (The Class List)
CREATE TABLE IF NOT EXISTS `students` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `student_id` varchar(50) NOT NULL UNIQUE,
    `student_name` varchar(100) NOT NULL,
    `class_name` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Daily Attendance Logs
CREATE TABLE IF NOT EXISTS `attendance_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `student_id` varchar(50) NOT NULL,
    `student_name` varchar(100) NOT NULL,
    `class_name` varchar(100) NOT NULL,
    `session_date` date NOT NULL,
    `timestamp` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create a dedicated user named 'teacher' with password 'sti123'
CREATE USER 'teacher'@'localhost' IDENTIFIED BY 'school123';
GRANT ALL PRIVILEGES ON school_db.* TO 'teacher'@'localhost';
FLUSH PRIVILEGES;

