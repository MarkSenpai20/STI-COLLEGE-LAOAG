<?php
// Start session just to be consistent, though we don't read from it here.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="app-container">
        <header class="app-header"><br><br>
            <!-- <h1>Add New Item</h1> -->
        </header>
<hr id="hr_1"><br id="breakLine"><hr>
        <main class="app-main">
            <!-- 
                This form POSTs the data to api.php.
                We include a hidden input 'action' to tell the API what to do.
            -->

            <!-- New Product Search -->
            <div class="product-search-container">
                <label for="product-search" class="form-label">Search Fixed Products:</label><br>
                <input type="search" id="product-search" placeholder="Type to search..." class="form-input" autocomplete="off">
                <div id="search-results" class="search-results-container">
                    <!-- Search results will be injected by add_item.js -->
                </div>
            </div>

            <hr class="form-divider">

            <form action="api.php" method="POST" class="add-item-form">
                <input type="hidden" name="action" value="add">
                
                <label for="input-item-name" class="form-label">Custom or Searched Item:</label>
                <input type="text" id="input-item-name" name="item_name" placeholder="ITEM" class="form-input" required>
                <input type="number" id="input-item-qty" name="item_qty" placeholder="QTY." class="form-input" min="1" required>
                <input type="number" id="input-item-price" name="item_price" placeholder="PRICE" class="form-input" min="0.01" step="0.01" required>
                
                <div class="button-grid-col-2">
                    <!-- "CANCEL" button is just a link back to the main page -->
                    <a href="index.php" class="btn">CANCEL</a>
                    <button type="submit" class="btn btn-green">ADD</button>
                </div>
            </form>
        </main>
    </div>

    <!-- Link the new JS file for this page -->
    <script src="add_item.js"></script>
</body>
</html>


