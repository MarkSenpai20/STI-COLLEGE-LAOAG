<?php
// --- CONFIGURATION & DATABASE CONNECTION ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "transitionizer_db";
$uploadDir = "uploads/";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// --- CRUD OPERATIONS ---

// 1. CREATE (Upload Image)
if (isset($_POST['upload'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $uploadDir . time() . "_" . $fileName; // Add timestamp to prevent overwrites
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $sql = "INSERT INTO images (title, filename) VALUES ('$title', '" . time() . "_" . $fileName . "')";
            if ($conn->query($sql)) {
                $msg = "Image uploaded successfully.";
                $msg_type = "success";
            } else {
                $msg = "Database error.";
                $msg_type = "error";
            }
        } else {
            $msg = "Sorry, there was an error uploading your file.";
            $msg_type = "error";
        }
    } else {
        $msg = "Sorry, only JPG, JPEG, PNG, & GIF files are allowed.";
        $msg_type = "error";
    }
}

// 2. DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = $conn->query("SELECT filename FROM images WHERE id = $id");
    if ($row = $query->fetch_assoc()) {
        $filePath = $uploadDir . $row['filename'];
        if (file_exists($filePath)) {
            unlink($filePath); // Delete physical file
        }
        $conn->query("DELETE FROM images WHERE id = $id"); // Delete DB record
        header("Location: index.php"); // Refresh to clear URL parameters
        exit();
    }
}

// 3. UPDATE (Edit Title)
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("UPDATE images SET title = '$title' WHERE id = $id");
    $msg = "Title updated successfully.";
    $msg_type = "success";
}

// 4. READ (Fetch all images)
$result = $conn->query("SELECT * FROM images ORDER BY id DESC");
$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Transitionizer CRUD</title>
    <!-- Using Tailwind CSS for instant responsiveness without external CSS files -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Styles for Transitions */
        .slider-container {
            position: relative;
            height: 400px; /* Adjustable height */
            overflow: hidden;
            background-color: #000;
        }
        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .slide.active {
            opacity: 1;
        }
        .slide img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .slide-caption {
            position: absolute;
            bottom: 20px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto px-4 py-8">
        
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Picture Transitionizer (CRUD)</h1>

        <!-- MESSAGES -->
        <?php if (isset($msg)): ?>
            <div class="p-4 mb-4 text-white rounded <?php echo $msg_type == 'success' ? 'bg-green-500' : 'bg-red-500'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- SECTION 1: THE SLIDESHOW (READ) -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="slider-container" id="slider">
                <?php if (count($images) > 0): ?>
                    <?php foreach ($images as $index => $img): ?>
                        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <img src="<?php echo $uploadDir . $img['filename']; ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
                            <div class="slide-caption"><?php echo htmlspecialchars($img['title']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="flex items-center justify-center h-full text-white">
                        <p>No images uploaded yet. Use the form below.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SECTION 2: CREATE FORM -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Add New Picture</h2>
            <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="title" placeholder="Image Title" required class="border p-2 rounded w-full md:w-1/3">
                <input type="file" name="image" required class="border p-2 rounded w-full md:w-1/3">
                <button type="submit" name="upload" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full md:w-1/3">
                    Upload & Add to Slide
                </button>
            </form>
        </div>

        <!-- SECTION 3: MANAGE IMAGES (UPDATE & DELETE) -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Manage Gallery</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 text-left">Preview</th>
                            <th class="px-4 py-2 text-left">Title (Edit to Update)</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($images as $img): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2">
                                <img src="<?php echo $uploadDir . $img['filename']; ?>" class="h-16 w-16 object-cover rounded">
                            </td>
                            <td class="px-4 py-2">
                                <!-- UPDATE FORM INLINE -->
                                <form action="" method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($img['title']); ?>" class="border p-1 rounded">
                                    <button type="submit" name="update" class="text-sm bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded">
                                        Update
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-2">
                                <a href="?delete=<?php echo $img['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?');" class="text-sm bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- JAVASCRIPT FOR TRANSITIONS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            let currentSlide = 0;
            const slideInterval = 3000; // Transition every 3 seconds

            if (slides.length > 0) {
                setInterval(() => {
                    // Remove active class from current
                    slides[currentSlide].classList.remove('active');
                    
                    // Calculate next slide
                    currentSlide = (currentSlide + 1) % slides.length;
                    
                    // Add active class to next
                    slides[currentSlide].classList.add('active');
                }, slideInterval);
            }
        });
    </script>
</body>
</html>