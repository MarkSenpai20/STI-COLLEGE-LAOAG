<?php
/**
 * Single File PHP Student Enrollment System (CRUD)
 * Created for: Mark Llanes Cruz
 * * Instructions:
 * 1. Place this file in your XAMPP htdocs folder.
 * 2. Ensure MySQL is running in XAMPP.
 * 3. Access via localhost in your browser.
 */

// --- DATABASE CONFIGURATION ---
$servername = "localhost";
$username = "root";     // Default XAMPP username
$password = "";         // Default XAMPP password
$dbname = "enrollment_db";

// 1. CONNECT TO SERVER & CREATE DATABASE IF NOT EXISTS
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!$conn->query($sql)) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// 2. CREATE TABLE IF NOT EXISTS
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

// --- HANDLE FORM SUBMISSIONS (CRUD LOGIC) ---
$edit_mode = false;
$update_id = 0;
$edit_name = "";
$edit_email = "";
$edit_phone = "";
$edit_course = "";
$message = "";

// INSERT (Create)
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
    <!-- Using Tailwind CSS via CDN for easy responsive styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar for table container */
        .table-container::-webkit-scrollbar {
            height: 8px;
        }
        .table-container::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 shadow-lg">
        <div class="container mx-auto">
            <h1 class="text-white text-2xl font-bold">Enrollment System</h1>
            <p class="text-blue-200 text-sm">Admin: Cryz Bernard Gonzales</p>
        </div>
    </nav>

    <div class="container mx-auto p-4 md:p-8">
        
        <!-- Status Message -->
        <?php if ($message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- FORM SECTION (Left Side on Desktop) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">
                        <?php echo $edit_mode ? 'Update Student' : 'Enroll New Student'; ?>
                    </h2>
                    
                    <form action="index.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $update_id; ?>">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="full_name">
                                Full Name
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                id="full_name" type="text" name="full_name" required 
                                value="<?php echo $edit_name; ?>" placeholder="e.g. John Doe">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                Email Address
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                id="email" type="email" name="email" required 
                                value="<?php echo $edit_email; ?>" placeholder="john@example.com">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                                Phone Number
                            </label>
                            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500" 
                                id="phone" type="text" name="phone" required 
                                value="<?php echo $edit_phone; ?>" placeholder="0912 345 6789">
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="course">
                                Course
                            </label>
                            <select class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="course" name="course">
                                <option value="Computer Science" <?php if($edit_course == 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                                <option value="Information Technology" <?php if($edit_course == 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                                <option value="Engineering" <?php if($edit_course == 'Engineering') echo 'selected'; ?>>Engineering</option>
                                <option value="Business Admin" <?php if($edit_course == 'Business Admin') echo 'selected'; ?>>Business Admin</option>
                                <option value="Nursing" <?php if($edit_course == 'Nursing') echo 'selected'; ?>>Nursing</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between">
                            <?php if ($edit_mode): ?>
                                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full mr-2" 
                                    type="submit" name="update">
                                    Update
                                </button>
                                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" 
                                    type="submit" name="save">
                                    Enroll Student
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABLE SECTION (Right Side on Desktop) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800">Enrolled Students</h2>
                    </div>
                    
                    <div class="overflow-x-auto table-container">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Contact
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Course
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = $conn->query("SELECT * FROM students ORDER BY id DESC");
                                if ($result->num_rows > 0):
                                    while($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">#<?php echo $row['id']; ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap font-semibold"><?php echo $row['full_name']; ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap"><?php echo $row['email']; ?></p>
                                        <p class="text-gray-600 text-xs"><?php echo $row['phone']; ?></p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                                            <span aria-hidden class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                                            <span class="relative"><?php echo $row['course']; ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="index.php?edit=<?php echo $row['id']; ?>" 
                                               class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-xs">
                                                Edit
                                            </a>
                                            <a href="index.php?delete=<?php echo $row['id']; ?>" 
                                               onclick="return confirm('Are you sure you want to delete this student?');" 
                                               class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-xs">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
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
        
        <footer class="mt-12 text-center text-gray-500 text-sm pb-4">
            <!-- &copy; <?php echo date("Y"); ?> Student Enrollment System. Developed by Cryz Bernard Gonzales. -->
        </footer>
    </div>

</body>
</html>
<?php $conn->close(); ?>