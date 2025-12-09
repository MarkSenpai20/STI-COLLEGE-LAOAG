<?php 
$servername = "localhost";
$username = "root";     
$password = "";         
$dbname = "enrollment_db";

// CONNECT SERVER & CREATE DATABASE IF NOT EXISTS
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!$conn->query($sql)) {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($dbname);

// CREATE TABLE IF NOT EXISTS
$tableSql = "CREATE TABLE IF NOT EXISTS students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    course VARCHAR(50) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($tableSql)) {
    die("Error creating table: " . $conn->error);
}

//               CRUD LOGIC
$edit_mode = false;
$update_id = 0;
$edit_name = "";
$edit_email = "";
$edit_phone = "";
$edit_course = "";
$message = "";

// INSERT
if (isset($_POST['save'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];

    $stmt = $conn->prepare("INSERT INTO students (full_name, email, phone, course) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $course);
    
    if ($stmt->execute()) {
        $message = "Student enrolled successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    $message = "Record deleted!";
    // Redirect to clear URL parameters
    header("Location: index.php"); 
    exit();
}

// PREPARE EDIT
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $update_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM students WHERE id=$update_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $edit_name = $row['full_name'];
        $edit_email = $row['email'];
        $edit_phone = $row['phone'];
        $edit_course = $row['course'];
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $course = $_POST['course'];

    $stmt = $conn->prepare("UPDATE students SET full_name=?, email=?, phone=?, course=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $course, $id);
    
    if ($stmt->execute()) {
        $message = "Student updated successfully!";
        // Reset edit mode
        $edit_mode = false;
        $edit_name = ""; $edit_email = ""; $edit_phone = ""; $edit_course = "";
    } else {
        $message = "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment System</title>
    <style>
      
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /*   NAVBAR   */
        .navbar {
            background-color: #2563eb; 
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            color: white;
        }
        .navbar h1 { margin: 0; font-size: 1.5rem; }
        .navbar p { margin: 5px 0 0; font-size: 0.9rem; opacity: 0.8; }

        /*   LAYOUT CONTAINER   */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap; 
            align-items: flex-start; 
        }

        /* Columns */
        .col-form {
            flex: 1;
            min-width: 300px; 
        }
        .col-table {
            flex: 2; 
            min-width: 300px;
        }

        /* CARDS (White Boxes)   */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden; 
            margin-bottom: 20px;
        }
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            background-color: #f8fafc;
        }
        .card-header h2 { margin: 0; font-size: 1.25rem; color: #444; }
        .card-body { padding: 20px; }

        /*  FORMS   */
        .form-group { margin-bottom: 15px; }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            color: #555;
        }
        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; 
            font-size: 1rem;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /*   BUTTONS   */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            color: white;
            width: 100%;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }
        .btn-green { background-color: #10b981; }
        .btn-green:hover { background-color: #059669; }
        
        .btn-blue { background-color: #2563eb; }
        .btn-blue:hover { background-color: #1d4ed8; }

        .btn-gray { background-color: #6b7280; }
        .btn-gray:hover { background-color: #4b5563; }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
            width: auto;
            margin-right: 5px;
        }
        .btn-yellow { background-color: #f59e0b; color: white; }
        .btn-red { background-color: #ef4444; color: white; }

        .form-actions {
            display: flex;
            gap: 10px;
        }

        /*   TABLE   */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        th, td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        tr:last-child td { border-bottom: none; }
        
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
            background-color: #dbeafe;
            color: #1e40af;
        }

        /*  ALERTS   */
        .alert {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
            color: #065f46;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        /*   RESPONSIVE   */
        @media (max-width: 768px) {
            .container {
                flex-direction: column; 
            }
            .col-form, .col-table {
                width: 100%; 
            }
            .table-wrapper {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <h1>Enrollment System</h1>
            <p>Admin: Cryz Bernard Gonzales</p>
        </div>
    </nav>

    <div class="container">
        
        <!-- Status Message (Full Width) -->
        <?php if ($message): ?>
            <div style="width: 100%;">
                <div class="alert" role="alert">
                    <p><?php echo $message; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- LEFT COLUMN: FORM -->
        <div class="col-form">
            <div class="card">
                <div class="card-header">
                    <h2><?php echo $edit_mode ? 'Update Student' : 'Enroll New Student'; ?></h2>
                </div>
                <div class="card-body">
                    <form action="index.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $update_id; ?>">
                        
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required 
                                value="<?php echo $edit_name; ?>" placeholder="e.g. John Doe">
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required 
                                value="<?php echo $edit_email; ?>" placeholder="john@example.com">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" required 
                                value="<?php echo $edit_phone; ?>" placeholder="0912 345 6789">
                        </div>

                        <div class="form-group">
                            <label for="course">Course</label>
                            <select id="course" name="course">
                                <option value="Computer Science" <?php if($edit_course == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                                <option value="Information Technology" <?php if($edit_course == 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                                <option value="Engineering" <?php if($edit_course == 'Engineering') echo 'selected'; ?>>Engineering</option>
                                <option value="Business Admin" <?php if($edit_course == 'Business Admin') echo 'selected'; ?>>Business Admin</option>
                                <option value="Nursing" <?php if($edit_course == 'Nursing') echo 'selected'; ?>>Nursing</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <?php if ($edit_mode): ?>
                                <button class="btn btn-blue" type="submit" name="update">Update</button>
                                <a href="index.php" class="btn btn-gray">Cancel</a>
                            <?php else: ?>
                                <button class="btn btn-green" type="submit" name="save">Enroll Student</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: TABLE -->
        <div class="col-table">
            <div class="card">
                <div class="card-header">
                    <h2>Enrolled Students</h2>
                </div>
                
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th> 
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Course</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM students ORDER BY id ASC");
                            if ($result->num_rows > 0):
                                $count = 1; 
                                while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>#<?php echo $count++; ?></td>
                                <td>
                                    <strong><?php echo $row['full_name']; ?></strong>
                                </td>
                                <td>
                                    <?php echo $row['email']; ?><br>
                                    <small style="color: #666;"><?php echo $row['phone']; ?></small>
                                </td>
                                <td>
                                    <span class="badge"><?php echo $row['course']; ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-yellow">Edit</a>
                                    <a href="index.php?delete=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this student?');" 
                                       class="btn btn-sm btn-red">Delete</a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px; color: #888;">
                                    No students enrolled yet.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    
    <footer style="text-align: center; padding: 20px; font-size: 0.8rem; color: #888;">
    </footer>

</body>
</html>
<?php $conn->close(); ?>