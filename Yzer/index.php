<?php
// 1. Get the key to the database
include 'db.php';

// --- DELETE LOGIC ---
// Check if the URL has "?delete=ID" (Example: index.php?delete=5)
if (isset($_GET['delete'])) {
    $id = $_GET['delete']; // Get the ID number
    
    // Run the SQL command to DELETE that specific row
    $conn->query("DELETE FROM visitors WHERE id = $id");
    
    // Refresh the page so the name disappears from the list
    header("Location: index.php");
    exit; // Stop the code here
}

// --- READ LOGIC ---
// Run SQL command to SELECT all visitors, showing the newest ones first (DESC)
$result = $conn->query("SELECT * FROM visitors ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor List</title>
    <link rel="stylesheet" href="style.css">
    <script src="delete.js"></script>
</head>
<body>

<div class="container">
    <h2>📋 Barangay Logbook</h2>
    
    <div style="text-align: center;">
        <a href="add_visitor.php" class="btn-add">+ Add Visitor</a>
    </div>

    <table>
        <tr>
            <th>Name</th>
            <th>Purpose</th>
            <th>Action</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['visitor_name']; ?></td>
            <td><?php echo $row['purpose']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">✏️</a>
                
                <button class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">X</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>