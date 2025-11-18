<?php
// Start the session to manage the "cart"
session_start();

// Initialize the cart in the session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaheraApp</title>
    <!-- Link the external CSS file -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="app-container">
        <header class="app-header">
            <h1>KaheraApp</h1>
        </header>

        <main class="app-main">
            <!-- Item List Display -->
            <div class="item-list-container">
                <div class="item-list-header">ITEM LIST:</div>
                <div class="item-list-display" id="item-list">
                    <?php if (empty($cart)): ?>
                        <div class="item-row empty">No items added.</div>
                    <?php else: ?>
                        <?php foreach ($cart as $index => $item): ?>
                            <?php
                                $item_total = $item['qty'] * $item['price'];
                                $total += $item_total;
                            ?>
                            <!-- 
                                We add data-index="<?php echo $index; ?>"
                                so our JavaScript knows which item to void.
                            -->
                            <div class="item-row" data-index="<?php echo $index; ?>">
                                <?php
                                echo htmlspecialchars($item['name']) . " x" . 
                                     htmlspecialchars($item['qty']) . " - P" . 
                                     number_format($item_total, 2);
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Main Buttons -->
            <div class="button-grid-col-2">
                <!-- "NEW" button clears the session via api.php -->
                <a href="api.php?action=clear" class="btn">NEW</a>
                <!-- "ADD" button links to the add_item.php page -->
                <a href="add_item.php" class="btn">ADD</a>
            </div>
            
            <a href="products.php" class="btn btn-full">MANAGE PRODUCTS</a>
            <a href="api.php?action=clear" class="btn btn-full">CLEAR ITEM LIST</a>

            <!-- Status Message Area -->
            <div id="status-message" class="status-message"></div>

            <div class="button-grid-col-2">
                <!-- "PAY" button links to pay.php -->
                <a href="pay.php" class="btn <?php echo empty($cart) ? 'disabled' : ''; ?>">PAY</a>
                <button id="btn-void" class="btn <?php echo empty($cart) ? 'disabled' : ''; ?>">VOID</button>
            </div>

            <!-- Cancel Void Button (hidden by default) -->
            <button id="btn-cancel-void" class="btn btn-red btn-full hidden">CANCEL VOID</button>
        </main>
    </div>

    <!-- Link the external JavaScript file -->
    <script src="main.js"></script>
</body>
</html>


