<?php
// ALWAYS start session at the top
session_start();

// Include the database connection file
require_once 'db.php';

// Determine the action
// We check GET for 'action' first, then POST
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    // --- ADD ITEM TO CART ---
    case 'add':
        if (
            isset($_POST['item_name']) && !empty($_POST['item_name']) &&
            isset($_POST['item_qty']) && is_numeric($_POST['item_qty']) &&
            isset($_POST['item_price']) && is_numeric($_POST['item_price'])
        ) {
            $item = [
                'name' => trim($_POST['item_name']),
                'qty' => (int)$_POST['item_qty'],
                'price' => (float)$_POST['item_price']
            ];

            // Add the new item to the session 'cart' array
            $_SESSION['cart'][] = $item;
        }
        // Redirect back to the main page
        header('Location: index.php');
        exit;

    // --- VOID (REMOVE) ITEM FROM CART ---
    case 'void':
        if (isset($_GET['id'])) {
            $index_to_remove = (int)$_GET['id'];
            
            // Check if that index exists in the cart
            if (isset($_SESSION['cart'][$index_to_remove])) {
                // Remove the item
                unset($_SESSION['cart'][$index_to_remove]);
                
                // Re-index the array to prevent holes
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                
                // Send a success JSON response for our JavaScript
                echo json_encode(['success' => true]);
                exit;
            }
        }
        // Send a failure response if 'id' was missing or invalid
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit;

    // --- CLEAR ENTIRE CART ---
    case 'clear':
        // Unset the session variable
        unset($_SESSION['cart']);
        
        // Redirect back to the main page
        header('Location: index.php');
        exit;

    // --- COMPLETE PAYMENT & SAVE TO SQL DATABASE ---
    case 'complete_payment':
        if (
            isset($_POST['total_amount']) && is_numeric($_POST['total_amount']) &&
            isset($_POST['amount_paid']) && is_numeric($_POST['amount_paid']) &&
            isset($_POST['change_given']) && is_numeric($_POST['change_given']) &&
            isset($_SESSION['cart']) && !empty($_SESSION['cart'])
        ) {
            $total = (float)$_POST['total_amount'];
            $paid = (float)$_POST['amount_paid'];
            $change = (float)$_POST['change_given'];
            $cart = $_SESSION['cart'];
            
            // Use a database transaction
            $conn->begin_transaction();
            
            try {
                // 1. Insert into 'transactions' table
                $stmt = $conn->prepare("INSERT INTO transactions (total_amount, amount_paid, change_given) VALUES (?, ?, ?)");
                $stmt->bind_param("ddd", $total, $paid, $change);
                $stmt->execute();
                
                // Get the ID of the transaction we just inserted
                $transaction_id = $conn->insert_id;
                
                // 2. Prepare statement for inserting items
                $item_stmt = $conn->prepare("INSERT INTO transaction_items (transaction_id, item_name, quantity, price_per_item) VALUES (?, ?, ?, ?)");
                
                // 3. Loop through cart and insert each item
                foreach ($cart as $item) {
                    $item_stmt->bind_param("isid", $transaction_id, $item['name'], $item['qty'], $item['price']);
                    $item_stmt->execute();
                }
                
                // If all went well, commit the transaction
                $conn->commit();
                
                // 4. Clear the cart
                unset($_SESSION['cart']);
                
                // 5. Redirect to main page (maybe with a success message)
                header('Location: index.php?payment=success');
                exit;
                
            } catch (mysqli_sql_exception $exception) {
                // If anything failed, roll back
                $conn->rollback();
                
                // Handle the error (e.g., redirect with error message)
                header('Location: pay.php?error=' . urlencode($exception->getMessage()));
                exit;
            }
            
        } else {
            // Invalid data
            header('Location: pay.php?error=invaliddata');
            exit;
        }

    // --- DEFAULT (if no action matches) ---
    default:
        // Just go to the main page
        header('Location: index.php');
        exit;
}
?>