<?php
session_start();
// You can add logic here to check if user is admin, etc.
// For now, we'll just show the page.

// Check for import status messages
$message = '';
if (isset($_GET['import'])) {
    if ($_GET['import'] === 'success') {
        $message = '<div class="status-message success">Products imported successfully!</div>';
    } else if ($_GET['import'] === 'error') {
        $message = '<div class="status-message">Error importing products. Please check file format.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="app-container">
        <header class="app-header">
            <h1>Manage Products</h1>
        </header>

        <main class="app-main">
            <!-- Back to Main App -->
            <a href="index.php" class="btn btn-full" style="margin-bottom: 1rem;">Back to POS</a>

            <?php echo $message; ?>

            <!-- Add New Product Form -->
            <form id="add-product-form" class="add-item-form">
                <h2>Add New Fixed Product</h2>
                <input type="text" id="new-product-name" name="item_name" placeholder="Product Name" class="form-input" required>
                <input type="number" id="new-product-price" name="item_price" placeholder="Price" class="form-input" min="0.01" step="0.01" required>
                <button type="submit" class="btn btn-green">Save Product</button>
            </form>

            <hr class="form-divider">

            <!-- Import/Export -->
            <div class="import-export-container">
                <h2>Import / Export</h2>
                
                <!-- Export Form (simple link) -->
                <a href="api.php?action=export_products" class="btn btn-full">Export Products as JSON</a>
                
                <!-- Import Form -->
                <form action="api.php" method="POST" enctype="multipart/form-data" class="import-form">
                    <input type="hidden" name="action" value="import_products">
                    <label for="import-file">Import from JSON:</label>
                    <input type="file" name="import_file" id="import-file" class="form-input" accept=".json" required>
                    <button type="submit" class="btn btn-full">Upload and Import</button>
                </form>
            </div>

            <hr class="form-divider">

            <!-- Existing Product List -->
            <h2>Existing Products</h2>
            <div id="product-list-loading">Loading products...</div>
            <div id="product-list-container" class="product-manage-list">
                <!-- Products will be loaded here by products.js -->
            </div>
        </main>
    </div>
    
    <script src="products.js"></script>
</body>
</html>