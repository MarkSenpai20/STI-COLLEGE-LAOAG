<?php
// 1. Get the key to the database
include 'db.php';

// 2. Check if the "Save" button was clicked
if (isset($_POST['submit'])) {
    // 3. Collect the data typed in the boxes
    $name = $_POST['visitor_name'];
    $purpose = $_POST['purpose'];
    
    // 4. Run SQL command: INSERT (Create new row)
    $conn->query("INSERT INTO visitors (visitor_name, purpose) VALUES ('$name', '$purpose')");
    
    // 5. Go back to the main list
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Visitor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>✍️ New Visitor Log</h2>
    
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="visitor_name" placeholder="Full Name" required>
        
        <label>Purpose:</label>
        <input type="text" name="purpose" placeholder="Reason for visit" required>
        
        <button type="submit" name="submit">Save Record</button>
    </form>
    <br>
    
    <div style="text-align: center;">
        <a href="index.php" class="link-cancel">View Logbook</a>
    </div>
</div>

</body>
</html>