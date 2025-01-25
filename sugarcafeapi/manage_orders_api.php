<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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

// Get POST data
$postData = json_decode(file_get_contents("php://input"), true);

if (isset($postData['action']) && $postData['action'] === 'place_order') {
    placeOrder($conn, $postData);
} else {
    echo json_encode(["success" => false, "message" => "Invalid action"]);
}

function placeOrder($conn, $data) {
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (total_amount, customer_payment, change_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("ddd", $data['totalCost'], $data['customerPayment'], $data['change']);
        $stmt->execute();
        $orderId = $conn->insert_id;
        $stmt->close();

        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, size, price) VALUES (?, ?, ?, ?, ?)");
        foreach ($data['orders'] as $item) {
            $stmt->bind_param("iiiss", $orderId, $item['id'], $item['quantity'], $item['size'], $item['sizePrice']);
            $stmt->execute();
        }
        $stmt->close();

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Order placed successfully", "orderId" => $orderId]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error placing order: " . $e->getMessage()]);
    }
}

$conn->close();
?>