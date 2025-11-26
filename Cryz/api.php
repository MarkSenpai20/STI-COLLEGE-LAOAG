<?php
// ALWAYS start session at the top
session_start();

// Include database connection file
require_once 'db.php';

// Determine the action
// We check GET for 'action' first then POST
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
            
            // Check if index exists in cart
            if (isset($_SESSION['cart'][$index_to_remove])) {
                // Remove item
                unset($_SESSION['cart'][$index_to_remove]);
                
                // Re-index array to prevent holes
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                
                // Send success JSON response for our JavaScript
                echo json_encode(['success' => true]);
                exit;
            }
        }
        // Send failure response if 'id' missing or invalid
        echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
        exit;

    // --- CLEAR ENTIRE CART ---
    case 'clear':
        // Unset the session variable
        unset($_SESSION['cart']);
        
        // Redirect back to the main page
        header('Location: index.php');
        exit;

    // --- SEARCH FOR PRODUCTS (AJAX) ---
    case 'search_products':
        header('Content-Type: application/json');
        $term = $_GET['term'] ?? '';
        
        if (empty($term)) {
            echo json_encode([]);
            exit;
        }

        try {
            // Use a wildcard search
            $search_term = "%" . $term . "%";
            $stmt = $conn->prepare("SELECT id, item_name, price_per_item FROM products WHERE item_name LIKE ? LIMIT 10");
            $stmt->bind_param("s", $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result->fetch_all(MYSQLI_ASSOC);
            
            echo json_encode($products);

        } catch (mysqli_sql_exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;

    // --- MANAGE PRODUCTS (Create, Read, Delete - AJAX) ---
    case 'manage_products':
        header('Content-Type: application/json');
        
        // GET: Fetch all products
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $result = $conn->query("SELECT id, item_name, price_per_item FROM products");
                $products = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($products);
            } catch (mysqli_sql_exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
        
        // POST: Add a new product
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['item_name']) && isset($_POST['item_price'])) {
                $name = trim($_POST['item_name']);
                $price = (float)$_POST['item_price'];
                
                try {
                    $stmt = $conn->prepare("INSERT INTO products (item_name, price_per_item) VALUES (?, ?)");
                    $stmt->bind_param("sd", $name, $price);
                    $stmt->execute();
                    
                    $new_id = $conn->insert_id;
                    echo json_encode([
                        'id' => $new_id,
                        'item_name' => $name,
                        'price_per_item' => $price
                    ]);
                } catch (mysqli_sql_exception $e) {
                    echo json_encode(['error' => 'Failed to add product. Is the name unique?']);
                }
            } else {
                echo json_encode(['error' => 'Invalid data']);
            }
        }
        
        // DELETE: Remove a product
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $_GET['id'] ?? 0;
            if ($id > 0) {
                try {
                    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Product not found']);
                    }
                } catch (mysqli_sql_exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                }
            }
        }
        exit;

    // --- EXPORT PRODUCTS ---
    case 'export_products':
        try {
            $result = $conn->query("SELECT item_name, price_per_item FROM products");
            $products = $result->fetch_all(MYSQLI_ASSOC);
            
            $filename = "kahera_products_" . date("Y-m-d") . ".json";
            
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            echo json_encode($products, JSON_PRETTY_PRINT);

        } catch (mysqli_sql_exception $e) {
            echo "Error: " . $e->getMessage();
        }
        exit;

    // --- IMPORT PRODUCTS ---
    case 'import_products':
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
            $tmp_name = $_FILES['import_file']['tmp_name'];
            $file_content = file_get_contents($tmp_name);
            $products = json_decode($file_content, true);

            if (is_array($products)) {
                $conn->begin_transaction();
                try {
                    $stmt = $conn->prepare("INSERT INTO products (item_name, price_per_item) VALUES (?, ?) ON DUPLICATE KEY UPDATE price_per_item = VALUES(price_per_item)");
                    
                    foreach ($products as $product) {
                        if (isset($product['item_name']) && isset($product['price_per_item'])) {
                            $stmt->bind_param("sd", $product['item_name'], $product['price_per_item']);
                            $stmt->execute();
                        }
                    }
                    $conn->commit();
                    header('Location: products.php?import=success');
                    exit;

                } catch (mysqli_sql_exception $e) {
                    $conn->rollback();
                    header('Location: products.php?import=error');
                    exit;
                }
            }
        }
        // Fallback for error
        header('Location: products.php?import=error');
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
            
            // Use database transaction
            $conn->begin_transaction();
            
            try {
                // 1. Insert into transactions table
                $stmt = $conn->prepare("INSERT INTO transactions (total_amount, amount_paid, change_given) VALUES (?, ?, ?)");
                $stmt->bind_param("ddd", $total, $paid, $change);
                $stmt->execute();
                
                // Get ID of the transaction we inserted
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
                
                // Handle error (e.g., redirect with error message)
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


