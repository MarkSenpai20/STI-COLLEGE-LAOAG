<?php
// FILE: lan_file_share.php
// A simple tool to upload and download files over WiFi.

// Configure the upload directory
$uploadDir = __DIR__ . '/uploads/';

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// 1. HANDLE UPLOADS
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $file = $_FILES['fileToUpload'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetFile = $uploadDir . basename($file['name']);
        
        // Check if file already exists
        if (file_exists($targetFile)) {
            $message = "Error: File already exists.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $message = "Success: " . htmlspecialchars(basename($file['name'])) . " uploaded!";
            } else {
                $message = "Error: There was an error uploading your file.";
            }
        }
    } else {
        $message = "Error: Upload failed with code " . $file['error'];
    }
}

// 2. HANDLE DELETION
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $targetPath = $uploadDir . $fileToDelete;
    if (file_exists($targetPath)) {
        unlink($targetPath);
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to clear query param
        exit;
    }
}

// 3. GET LIST OF FILES
$files = array_diff(scandir($uploadDir), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi File Drop</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f4f4f9; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-top: 0; }
        
        /* Upload Area */
        .upload-box { border: 2px dashed #ccc; padding: 30px; text-align: center; border-radius: 8px; margin-bottom: 20px; transition: 0.2s; background: #fafafa; }
        .upload-box:hover { border-color: #0084ff; background: #f0f8ff; }
        input[type="file"] { display: none; }
        .custom-file-upload { display: inline-block; padding: 10px 20px; cursor: pointer; background: #0084ff; color: white; border-radius: 5px; font-weight: bold; }
        .upload-btn { margin-top: 15px; background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 1rem; display: none; }
        
        /* File List */
        ul { list-style: none; padding: 0; }
        li { background: white; border-bottom: 1px solid #eee; padding: 15px; display: flex; align-items: center; justify-content: space-between; }
        li:last-child { border-bottom: none; }
        
        .file-info { display: flex; flex-direction: column; overflow: hidden; }
        .file-name { font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
        .file-size { font-size: 0.8em; color: #888; }
        
        .actions { display: flex; gap: 10px; }
        .btn { text-decoration: none; padding: 5px 10px; border-radius: 4px; font-size: 0.85em; }
        .btn-dl { background: #e2e6ea; color: #333; }
        .btn-del { background: #fee2e2; color: #dc3545; }

        .alert { padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }

        @media (min-width: 500px) {
            .file-name { max-width: 350px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📂 WiFi File Drop</h2>

    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="upload-box">
            <label for="fileToUpload" class="custom-file-upload">
                Select File
            </label>
            <input type="file" name="fileToUpload" id="fileToUpload" onchange="showSubmitBtn()">
            <div id="file-chosen" style="margin-top: 10px; font-size: 0.9em; color: #666;">No file chosen</div>
            <button type="submit" class="upload-btn" id="submit-btn">Upload Now</button>
        </div>
    </form>

    <h3>Available Files</h3>
    <?php if (empty($files)): ?>
        <p style="text-align: center; color: #888;">No files uploaded yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($files as $file): 
                $path = 'uploads/' . $file;
                $size = filesize($uploadDir . $file);
                $sizeStr = $size > 1048576 ? round($size/1048576, 2) . ' MB' : round($size/1024, 2) . ' KB';
            ?>
                <li>
                    <div class="file-info">
                        <span class="file-name" title="<?php echo $file; ?>"><?php echo $file; ?></span>
                        <span class="file-size"><?php echo $sizeStr; ?></span>
                    </div>
                    <div class="actions">
                        <a href="<?php echo $path; ?>" class="btn btn-dl" download>Download</a>
                        <a href="?delete=<?php echo urlencode($file); ?>" class="btn btn-del" onclick="return confirm('Delete this file?')">×</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<script>
    function showSubmitBtn() {
        const input = document.getElementById('fileToUpload');
        const fileName = input.files[0] ? input.files[0].name : 'No file chosen';
        document.getElementById('file-chosen').textContent = fileName;
        document.getElementById('submit-btn').style.display = 'inline-block';
    }
</script>

</body>
</html>