<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "transitionizer_db";
$uploadDir = "uploads/";


$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}



// Upload Image
if (isset($_POST['upload'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $uploadDir . time() . "_" . $fileName; 
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
        header("Location: index.php"); // Refresh clear URL parameters
        exit();
    }
}

// UPDATE Edit Title
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $conn->query("UPDATE images SET title = '$title' WHERE id = $id");
    $msg = "Title updated successfully.";
    $msg_type = "success";
}

// READ Fetch all images
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
    <style>
    
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        /* Center content max-width */
        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #444;
            margin-bottom: 30px;
        }

        
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            margin-bottom: 25px;
        }

        .card-header {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        /*   FORMS & INPUTS   */
        input[type="text"], input[type="file"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%; 
            box-sizing: border-box; 
            margin-bottom: 10px;
        }

        
        .form-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .form-input {
            flex: 1;
            min-width: 200px;
        }

        /*   BUTTONS   */
        button, .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }

        .btn-update { background-color: #ffc107; color: #333; }
        .btn-update:hover { background-color: #e0a800; }

        .btn-delete { background-color: #dc3545; color: white; }
        .btn-delete:hover { background-color: #c82333; }

        /*   ALERTS   */
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: white;
            text-align: center;
        }
        .alert-success { background-color: #28a745; }
        .alert-error { background-color: #dc3545; }

        /*   TABLE STYLES  */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        
        .inline-form {
            display: flex;
            gap: 5px;
        }

     
        .slider-container {
            position: relative;
            height: 400px;
            overflow: hidden;
            background-color: #000;
            border-radius: 4px;
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
        .slide.active { opacity: 1; }
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
        .empty-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            color: #ccc;
        }

        /* RESPONSIVE*/
        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
            .slider-container {
                height: 250px;
            }
            th, td {
                padding: 8px 4px;
                font-size: 0.9rem;
            }
            .btn {
                width: 100%;
                margin-top: 2px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <h1>Picture Transitionizer (CRUD)</h1>

        <!-- MESSAGES -->
        <?php if (isset($msg)): ?>
            <div class="alert <?php echo $msg_type == 'success' ? 'alert-success' : 'alert-error'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <!-- SLIDESHOW -->
        <div class="card">
            <div class="slider-container" id="slider">
                <?php if (count($images) > 0): ?>
                    <?php foreach ($images as $index => $img): ?>
                        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                            <img src="<?php echo $uploadDir . $img['filename']; ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
                            <div class="slide-caption"><?php echo htmlspecialchars($img['title']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-slide">
                        <p>No images uploaded yet. Use the form below.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!--FORM -->
        <div class="card">
            <div class="card-header">Add New Picture</div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-input">
                        <input type="text" name="title" placeholder="Image Title" required>
                    </div>
                    <div class="form-input">
                        <input type="file" name="image" required>
                    </div>
                    <div class="form-input" style="flex: 0 0 auto;">
                        <button type="submit" name="upload" class="btn btn-primary">Upload & Add</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- MANAGE IMAGES (UPDATE & DELETE) -->
        <div class="card">
            <div class="card-header">Manage Gallery</div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Title (Edit to Update)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($images as $img): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $uploadDir . $img['filename']; ?>" class="img-thumbnail">
                            </td>
                            <td>
                                <!-- UPDATE FORM INLINE -->
                                <form action="" method="POST" class="inline-form">
                                    <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($img['title']); ?>" style="margin-bottom:0;">
                                    <button type="submit" name="update" class="btn btn-update">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $img['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?');" class="btn btn-delete">
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

    <!-- TRANSITIONS -->
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