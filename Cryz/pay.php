<?php
session_start();

// If cart is empty or doesn't exist, redirect to main page
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

$cart = $_SESSION['cart'];
$total_to_pay = 0;

foreach ($cart as $item) {
    $total_to_pay += $item['qty'] * $item['price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="app-container">
        <header class="app-header"><br><br>
            <!-- <h1>Payment</h1> -->
        </header>
<hr id="hr_1"><br id="breakLine"><hr>
        <main class="app-main">
            <!-- 
                This form submits final transaction to api.php
                The 'total_to_pay' is passed via hidden input.
                The 'balance' (amount_paid) is set by JavaScript.
            -->
            <form action="api.php" method="POST" id="pay-form">
                <input type="hidden" name="action" value="complete_payment">
                <input type="hidden" name="total_amount" value="<?php echo $total_to_pay; ?>">
                
                <div class="pay-display">
                    <div class="pay-row">
                        <label>TO PAY:</label>
                        <!-- We add data-total attribute for JS to read -->
                        <input type="text" id="display-to-pay" value="P<?php echo number_format($total_to_pay, 2); ?>" data-total="<?php echo $total_to_pay; ?>" readonly>
                    </div>
                    <div class="pay-row">
                        <label>BALANCE:</label>
                        <input type="text" id="display-balance" value="P0.00" readonly>
                        <!-- This hidden input will store the actual number value for submission -->
                        <input type="hidden" name="amount_paid" id="input-balance" value="0">
                    </div>
                    <div class="pay-row">
                        <label>CHANGE:</label>
                        <input type="text" id="display-change" value="P<?php echo number_format(-$total_to_pay, 2); ?>" readonly>
                        <input type="hidden" name="change_given" id="input-change" value="<?php echo -$total_to_pay; ?>">
                    </div>
                </div>

                <div class="total-price-label">
                    Total Price: P<?php echo number_format($total_to_pay, 2); ?>
                </div>

                <!-- calculator Buttons -->
                <div class="denomination-grid">
                    <button type="button" class="btn denomination" data-value="1">1</button>
                    <button type="button" class="btn denomination" data-value="5">5</button>
                    <button type="button" class="btn denomination" data-value="10">10</button>
                    <button type="button" class="btn denomination" data-value="20">20</button>
                    <button type="button" class="btn denomination" data-value="50">50</button>
                    <button type="button" class="btn denomination" data-value="100">100</button>
                    <button type="button" class="btn denomination" data-value="200">200</button>
                    <button type="button" class="btn denomination" data-value="500">500</button>
                    <button type="button" class="btn denomination" data-value="1000">1000</button>
                </div>
                
                <button type="button" id="btn-clear-balance" class="btn btn-red btn-full">Clear Balance</button>
                
                <!-- This button submits form to save to database -->
                <button type="submit" id="btn-complete-payment" class="btn btn-green btn-full">Complete Payment</button>

                <!-- "BACK" button just a link back to main page -->
                <a href="index.php" class="btn btn-full">BACK</a>
            </form>
        </main>
    </div>
    
    <!-- Link the specific JS for this page -->
    <script src="pay.js"></script>
</body>
</html>