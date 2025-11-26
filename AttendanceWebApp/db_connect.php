<?php
date_default_timezone_set('Asia/Manila'); 

// CONNECT TO USER HOST CREATED FROM THE PRIVILEGES
$host = "127.0.0.1";
$user = "teacher"; 
$pass = "sti123"; 
$dbname = "school_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Error Handling
if ($conn->connect_error) {
     die("Database Error: " . $conn->connect_error);
}

// Get server State JSON FILE for Students Perspective
$stateFile = 'server_state.json';

// Get Host Server IP
function getServerIP() {
    return gethostbyname(gethostname());
}

// Modify the server state mode
function setServerState($mode, $className) {
    global $stateFile;
    $data = ["mode" => $mode, "class" => $className];
    file_put_contents($stateFile, json_encode($data));
}

// Then reads the server state
function getServerState() {
    global $stateFile;
    if (!file_exists($stateFile)) return ["mode" => "IDLE", "class" => "None"];
    return json_decode(file_get_contents($stateFile), true);
}

// --- API HANDLING ---
if (isset($_GET['action'])) {
    if ($_GET['action'] !== 'export') {
        header('Content-Type: application/json');
    }
    
    $action = $_GET['action'];

    // 1. SET MODE
    if ($action == 'set_mode') {
        $mode = $_POST['mode']; 
        $class = $_POST['class_name'];
        setServerState($mode, $class);
        echo json_encode(["status" => "success", "mode" => $mode]);
        exit;
    }

    // 2. GET STATUS
    if ($action == 'get_status') {
        echo json_encode(getServerState());
        exit;
    }

    // 3. IMPORT CSV
    if ($action == 'import' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $class = $_POST['class_name'];
            $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
            $count = 0;
            while (($row = fgetcsv($file)) !== FALSE) {
                $name = $row[0] ?? '';
                $sid = $row[1] ?? '';
                if ($name && $sid) {
                    $check = $conn->query("SELECT id FROM students WHERE student_id = '$sid'");
                    if ($check->num_rows == 0) {
                        $stmt = $conn->prepare("INSERT INTO students (student_name, student_id, class_name) VALUES (?, ?, ?)");
                        $stmt->bind_param("sss", $name, $sid, $class);
                        $stmt->execute();
                        $count++;
                    }
                }
            }
            fclose($file);
            echo json_encode(["status" => "success", "count" => $count]);
        } else {
            echo json_encode(["status" => "error", "message" => "File upload failed"]);
        }
        exit;
    }

    // 4. SUBMIT FORM
    if ($action == 'submit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $state = getServerState();
        $sid = $_POST['student_id'];
        $name = $_POST['student_name'] ?? ''; 
        $class = $state['class'];
        $date = date('Y-m-d');
        $time = date("h:i:s A");

        if ($state['mode'] == 'REGISTER') {
            $check = $conn->query("SELECT id FROM students WHERE student_id = '$sid'");
            if ($check->num_rows > 0) {
                echo json_encode(["status" => "error", "message" => "ID Already Registered!"]);
            } else {
                $stmt = $conn->prepare("INSERT INTO students (student_name, student_id, class_name) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $sid, $class);
                $stmt->execute();
                echo json_encode(["status" => "success", "msg" => "Registered Successfully!"]);
            }
        } 
        elseif ($state['mode'] == 'ATTENDANCE') {
            $studentQ = $conn->query("SELECT student_name FROM students WHERE student_id = '$sid'");
            
            if ($studentQ->num_rows == 0) {
                echo json_encode(["status" => "error", "message" => "ID Not Found! Please Register first."]);
            } else {
                $studentRow = $studentQ->fetch_assoc();
                $realName = $studentRow['student_name'];

                $logCheck = $conn->query("SELECT id FROM attendance_logs WHERE student_id = '$sid' AND session_date = '$date' AND class_name = '$class'");
                
                if ($logCheck->num_rows > 0) {
                    echo json_encode(["status" => "error", "message" => "You are already marked present!"]);
                } else {
                    $stmt = $conn->prepare("INSERT INTO attendance_logs (student_name, student_id, class_name, session_date, timestamp) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $realName, $sid, $class, $date, $time);
                    $stmt->execute();
                    echo json_encode(["status" => "success", "msg" => "Welcome, $realName!"]);
                }
            }
        }
        exit;
    }

    // 5. LIVE LIST
    if ($action == 'live_list') {
        $state = getServerState();
        $class = $state['class'];
        $date = date('Y-m-d');
        $data = [];

        if ($state['mode'] == 'REGISTER') {
            $res = $conn->query("SELECT student_name, student_id, 'New Registration' as timestamp FROM students WHERE class_name = '$class' ORDER BY id DESC");
            while($row = $res->fetch_assoc()) $data[] = $row;
        } 
        elseif ($state['mode'] == 'ATTENDANCE') {
            $res = $conn->query("SELECT student_name, student_id, timestamp FROM attendance_logs WHERE class_name = '$class' AND session_date = '$date' ORDER BY id DESC");
            while($row = $res->fetch_assoc()) $data[] = $row;
        }
        echo json_encode($data);
        exit;
    }

    // 6. HISTORY SUMMARY
    if ($action == 'get_history') {
        $res = $conn->query("SELECT class_name, session_date, COUNT(*) as count FROM attendance_logs GROUP BY class_name, session_date ORDER BY session_date DESC");
        $data = [];
        while($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        exit;
    }

    // 7. HISTORY DETAILS
    if ($action == 'get_history_details') {
        $class = $_GET['class'];
        $date = $_GET['date'];
        $res = $conn->query("SELECT student_name, student_id, timestamp FROM attendance_logs WHERE class_name = '$class' AND session_date = '$date' ORDER BY timestamp DESC");
        $data = [];
        while($row = $res->fetch_assoc()) $data[] = $row;
        echo json_encode($data);
        exit;
    }

    // 8. EXPORT CSV
    if ($action == 'export') {
        $class = $_GET['class'];
        $date = $_GET['date'];
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Attendance_' . $class . '_' . $date . '.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Time / Status']);

        if ($date == 'REGISTER_MODE') {
             $res = $conn->query("SELECT student_name, student_id FROM students WHERE class_name = '$class'");
             while($row = $res->fetch_assoc()) fputcsv($output, [$row['student_name'], $row['student_id'], 'Registered Identity']);
        } else {
             $res = $conn->query("SELECT student_name, student_id, timestamp FROM attendance_logs WHERE class_name = '$class' AND session_date = '$date'");
             while($row = $res->fetch_assoc()) fputcsv($output, [$row['student_name'], $row['student_id'], $row['timestamp']]);
        }
        fclose($output);
        exit;
    }

    // 9. CLEAR LIVE LIST (NEW FEATURE) under development
    if ($action == 'clear_live') {
        $state = getServerState();
        $class = $state['class'];
        $date = date('Y-m-d');

        if ($state['mode'] == 'REGISTER') {
            // DANGER: This deletes the registered students for this class!
            $conn->query("DELETE FROM students WHERE class_name = '$class'");
        } 
        elseif ($state['mode'] == 'ATTENDANCE') {
            // This deletes today's attendance logs for this class
            $conn->query("DELETE FROM attendance_logs WHERE class_name = '$class' AND session_date = '$date'");
        }
        echo json_encode(["status" => "success"]);
        exit;
    }
}
?>