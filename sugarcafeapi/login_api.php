<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$servername = "localhost";
$username = "root";
$password = " "; 
$dbname = "sugarcafepos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get POST data
$postData = json_decode(file_get_contents("php://input"), true);

if (isset($postData['action'])) {
    switch ($postData['action']) {
        case 'login':
            handleLogin($conn, $postData);
            break;
        case 'change_password':
            handleChangePassword($conn, $postData);
            break;
        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No action specified"]);
}

function handleLogin($conn, $data) {
    $role = $data['role'];
    $password = $data['password'];

    if ($role !== 'admin' && $role !== 'cashier') {
        echo json_encode(["success" => false, "message" => "Invalid role"]);
        return;
    }

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo json_encode(["success" => true, "message" => "Login successful", "data" => ["id" => $user['id'], "role" => $role]]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    $stmt->close();
}

function handleChangePassword($conn, $data) {
    $role = $data['role'];
    $currentPassword = $data['currentPassword'];
    $newPassword = $data['newPassword'];

    if ($role !== 'admin' && $role !== 'cashier') {
        echo json_encode(["success" => false, "message" => "Invalid role"]);
        return;
    }

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($currentPassword, $user['password'])) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE role = ?");
            $updateStmt->bind_param("ss", $hashedNewPassword, $role);
            
            if ($updateStmt->execute()) {
                echo json_encode(["success" => true, "message" => "Password changed successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to change password"]);
            }
            $updateStmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Current password is incorrect"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found"]);
    }

    $stmt->close();
}

$conn->close();
?>