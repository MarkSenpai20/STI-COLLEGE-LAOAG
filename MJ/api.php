<?php
// api.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow any origin (for development)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// --- DATABASE CONFIGURATION ---
// !! IMPORTANT: Change these to match your MySQL setup !!
$DB_HOST = "127.0.0.1"; // or "localhost"
$DB_USER = "root";       // default XAMPP user
$DB_PASS = "";           // default XAMPP pass
$DB_NAME = "game_db";
// -----------------------------

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Get the request body
$input = json_decode(file_get_contents("php://input"), true);

// Get action from query string, default to 'default'
$action = $_GET['action'] ?? 'default';

// --- API ROUTER ---
switch ($action) {
    case 'register':
        handleRegister($conn, $input);
        break;
    case 'login':
        handleLogin($conn, $input);
        break;
    case 'save':
        handleSave($conn, $input);
        break;
    case 'leaderboard':
        getLeaderboard($conn);
        break;
    case 'request_coins':
        requestCoins($conn, $input);
        break;
    case 'admin_get_requests':
        getCoinRequests($conn, $input);
        break;
    case 'admin_approve_request':
        approveCoinRequest($conn, $input);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action specified.']);
        break;
}

// Close connection
$conn->close();

// --- FUNCTION DEFINITIONS ---

function handleRegister($conn, $data) {
    if (empty($data['username']) || empty($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Username and password required.']);
        return;
    }

    $username = $conn->real_escape_string($data['username']);
    $password_hash = password_hash($conn->real_escape_string($data['password']), PASSWORD_DEFAULT);
    
    // Check if user exists
    $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
    } else {
        // Insert new user
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password_hash, coins, current_stage) VALUES (?, ?, 500, 1)");
        $stmt_insert->bind_param("ss", $username, $password_hash);
        
        if ($stmt_insert->execute()) {
            // Also create leaderboard entry
            $stmt_lb = $conn->prepare("INSERT INTO leaderboard (username) VALUES (?) ON DUPLICATE KEY UPDATE username=username");
            $stmt_lb->bind_param("s", $username);
            $stmt_lb->execute();
            
            echo json_encode(['success' => true, 'message' => 'Registration successful.']);
            $stmt_lb->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt_insert->error]);
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}

function handleLogin($conn, $data) {
    if (empty($data['username']) || empty($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Username and password required.']);
        return;
    }
    
    $username = $conn->real_escape_string($data['username']);
    $password = $conn->real_escape_string($data['password']);

    $stmt = $conn->prepare("SELECT password_hash, coins, current_stage, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct!
            echo json_encode([
                'success' => true,
                'user' => [
                    'username' => $username,
                    'coins' => $user['coins'],
                    'stage' => $user['current_stage'],
                    'isAdmin' => $user['is_admin']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    }
    $stmt->close();
}

function handleSave($conn, $data) {
    if (empty($data['username']) || !isset($data['coins']) || !isset($data['stage'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
        return;
    }

    $username = $conn->real_escape_string($data['username']);
    $coins = intval($data['coins']);
    $stage = intval($data['stage']);

    // Update user stats
    $stmt = $conn->prepare("UPDATE users SET coins = ?, current_stage = ? WHERE username = ?");
    $stmt->bind_param("iis", $coins, $stage, $username);
    $stmt->execute();

    // Update leaderboard (only if new score is higher)
    $stmt_lb = $conn->prepare("UPDATE leaderboard SET highest_stage = GREATEST(highest_stage, ?) WHERE username = ?");
    $stmt_lb->bind_param("is", $stage, $username);
    $stmt_lb->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Data saved.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save data or user not found.']);
    }
    $stmt->close();
    $stmt_lb->close();
}

function getLeaderboard($conn) {
    // Simple leaderboard: top 10 by highest stage
    $result = $conn->query("SELECT username, highest_stage, highest_dmg, highest_combo FROM leaderboard ORDER BY highest_stage DESC, highest_dmg DESC LIMIT 10");
    
    $leaderboard = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $leaderboard[] = $row;
        }
    }
    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
}

function requestCoins($conn, $data) {
    if (empty($data['username'])) {
        echo json_encode(['success' => false, 'message' => 'Username required.']);
        return;
    }
    $username = $conn->real_escape_string($data['username']);

    // Check for existing pending request
    $stmt_check = $conn->prepare("SELECT request_id FROM coin_requests WHERE username = ? AND status = 'pending'");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You already have a pending request.']);
    } else {
        $stmt = $conn->prepare("INSERT INTO coin_requests (username) VALUES (?)");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Coin request sent to admin.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send request.']);
        }
        $stmt->close();
    }
    $stmt_check->close();
}

function getCoinRequests($conn, $data) {
    // Admin only - simple check, ideally needs token auth
    if (empty($data['username']) || $data['username'] !== 'admin') {
         echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
         return;
    }
    
    $result = $conn->query("SELECT request_id, username, request_time FROM coin_requests WHERE status = 'pending' ORDER BY request_time ASC");
    $requests = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
    echo json_encode(['success' => true, 'requests' => $requests]);
}

function approveCoinRequest($conn, $data) {
    if (empty($data['admin_user']) || $data['admin_user'] !== 'admin' || empty($data['request_id'])) {
         echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid data.']);
         return;
    }
    
    $request_id = intval($data['request_id']);
    
    // Get username from request
    $stmt_get = $conn->prepare("SELECT username FROM coin_requests WHERE request_id = ? AND status = 'pending'");
    $stmt_get->bind_param("i", $request_id);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    
    if ($result_get->num_rows === 1) {
        $row = $result_get->fetch_assoc();
        $username_to_refill = $row['username'];
        
        // Start transaction
        $conn->begin_transaction();
        
        // 1. Update user's coins (set to 500)
        $stmt_update_user = $conn->prepare("UPDATE users SET coins = 500 WHERE username = ?");
        $stmt_update_user->bind_param("s", $username_to_refill);
        $exec1 = $stmt_update_user->execute();
        
        // 2. Update request status
        $stmt_update_req = $conn->prepare("UPDATE coin_requests SET status = 'approved' WHERE request_id = ?");
        $stmt_update_req->bind_param("i", $request_id);
        $exec2 = $stmt_update_req->execute();
        
        if ($exec1 && $exec2) {
            $conn->commit();
            echo json_encode(['success' => true, 'message' => "Refilled $username_to_refill's coins."]);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
        }
        $stmt_update_user->close();
        $stmt_update_req->close();
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Request not found or already approved.']);
    }
    $stmt_get->close();
}
?>