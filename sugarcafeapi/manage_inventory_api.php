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

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        getInventory($conn);
        break;
    case 'POST':
        addItem($conn);
        break;
    case 'PUT':
        updateItem($conn);
        break;
    case 'DELETE':
        deleteItem($conn);
        break;
    default:
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        break;
}

function getInventory($conn) {
    $result = $conn->query("SELECT * FROM inventory");
    $inventory = [];
    while($row = $result->fetch_assoc()) {
        $inventory[] = $row;
    }
    echo json_encode($inventory);
}

function addItem($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO inventory (name, description, price, stock_quantity, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $data['name'], $data['description'], $data['price'], $data['stock_quantity'], $data['category']);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add item"]);
    }
    $stmt->close();
}

function updateItem($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE inventory SET name=?, description=?, price=?, stock_quantity=?, category=? WHERE id=?");
    $stmt->bind_param("ssdisi", $data['name'], $data['description'], $data['price'], $data['stock_quantity'], $data['category'], $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update item"]);
    }
    $stmt->close();
}

function deleteItem($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("DELETE FROM inventory WHERE id=?");
    $stmt->bind_param("i", $data['id']);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Item deleted successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete item"]);
    }
    $stmt->close();
}

$conn->close();
?>