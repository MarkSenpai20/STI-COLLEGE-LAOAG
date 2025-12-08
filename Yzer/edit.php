<?php
// 1. Get the key
include 'db.php';

// --- FETCH DATA ---
// Check if the link has an ID (edit.php?id=5)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get that specific person from the database
    $result = $conn->query("SELECT * FROM visitors WHERE id = $id");
    // Convert result to readable array
    $row = $result->fetch_assoc();
}

// --- UPDATE DATA ---
// Check if the "Update" button was clicked
if (isset($_POST['update'])) {
    $name = $_POST['visitor_name']; // New Name
    $purpose = $_POST['purpose'];   // New Purpose
    $id = $_POST['id'];             // The ID of the person to fix

    // Run SQL command: UPDATE (Overwrite old data)
    $conn->query("UPDATE visitors SET visitor_name='$name', purpose='$purpose' WHERE id=$id");
    
    // Go back to list
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Visitor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>✏️ Edit Visitor</h2>
    
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        
        <label>Name:</label>
        <input type="text" name="visitor_name" value="<?php echo $row['visitor_name']; ?>" required>
        
        <label>Purpose:</label>
        <input type="text" name="purpose" value="<?php echo $row['purpose']; ?>" required>
        
        <button type="submit" name="update" class="btn-update">Update Record</button>
    </form>
    <br>
    
    <div style="text-align: center;">
        <a href="index.php" class="link-cancel">Cancel</a>
    </div>
</div>

</body>
</html>