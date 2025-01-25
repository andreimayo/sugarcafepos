<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
$servername = "localhost";
$username = "root";
$password = " ";
$dbname = "sugarcafepos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($method) {
    case 'GET':
        if ($action === 'get_orders') {
            getOrders($conn);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if ($data['action'] === 'submit_sales') {
            submitSales($conn, $data['order_ids']);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
        break;
    case 'PUT':
        // Implement edit functionality if needed
        break;
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        deleteOrder($conn, $data['id']);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        break;
}

function getOrders($conn) {
    $result = $conn->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY id DESC");
    $orders = [];
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
}

function submitSales($conn, $orderIds) {
    $conn->begin_transaction();
    try {
        foreach ($orderIds as $orderId) {
            $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();
        }
        $conn->commit();
        echo json_encode(["success" => true, "message" => "Sales report submitted successfully"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error submitting sales report: " . $e->getMessage()]);
    }
}

function deleteOrder($conn, $orderId) {
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete order"]);
    }
    $stmt->close();
}

$conn->close();
?>