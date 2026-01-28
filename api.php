<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "billing_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$conn->set_charset("utf8");

// Get request action
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if ($action === 'saveBill') {
        saveBill($conn, $data);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'getBills') {
        getBills($conn);
    } elseif ($action === 'getBillDetails') {
        getBillDetails($conn, isset($_GET['bill_id']) ? $_GET['bill_id'] : 0);
    }
}

// Save bill function
function saveBill($conn, $data) {
    $customer_name = $conn->real_escape_string($data['customerName']);
    $customer_phone = $conn->real_escape_string($data['customerPhone']);
    $items = $data['items'];
    $subtotal = $data['subtotal'];
    $tax_percent = $data['taxPercent'];
    $tax_amount = $data['taxAmount'];
    $grand_total = $data['grandTotal'];
    
    // Check if customer exists
    $customer_query = $conn->query("SELECT id FROM customers WHERE phone = '$customer_phone'");
    
    if ($customer_query->num_rows > 0) {
        $customer = $customer_query->fetch_assoc();
        $customer_id = $customer['id'];
    } else {
        // Create new customer
        $conn->query("INSERT INTO customers (name, phone) VALUES ('$customer_name', '$customer_phone')");
        $customer_id = $conn->insert_id;
    }
    
    // Insert bill
    $bill_query = "INSERT INTO bills (customer_id, subtotal, tax_percent, tax_amount, grand_total) 
                   VALUES ($customer_id, $subtotal, $tax_percent, $tax_amount, $grand_total)";
    
    if ($conn->query($bill_query)) {
        $bill_id = $conn->insert_id;
        
        // Insert bill items
        foreach ($items as $item) {
            $item_name = $conn->real_escape_string($item['name']);
            $quantity = $item['qty'];
            $price = $item['price'];
            $total = $item['total'];
            
            $conn->query("INSERT INTO bill_items (bill_id, item_name, quantity, price, total) 
                         VALUES ($bill_id, '$item_name', $quantity, $price, $total)");
        }
        
        echo json_encode(['success' => true, 'message' => 'Bill saved successfully!', 'bill_id' => $bill_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving bill: ' . $conn->error]);
    }
}

// Get all bills
function getBills($conn) {
    $query = "SELECT b.id, c.name, c.phone, b.grand_total, b.created_at 
              FROM bills b 
              JOIN customers c ON b.customer_id = c.id 
              ORDER BY b.created_at DESC";
    
    $result = $conn->query($query);
    $bills = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bills[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'bills' => $bills]);
}

// Get bill details
function getBillDetails($conn, $bill_id) {
    $bill_id = intval($bill_id);
    
    // Get bill header
    $query = "SELECT b.*, c.name, c.phone FROM bills b 
              JOIN customers c ON b.customer_id = c.id 
              WHERE b.id = $bill_id";
    
    $result = $conn->query($query);
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bill not found']);
        return;
    }
    
    $bill = $result->fetch_assoc();
    
    // Get bill items
    $items_query = "SELECT * FROM bill_items WHERE bill_id = $bill_id";
    $items_result = $conn->query($items_query);
    $items = [];
    
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    
    $bill['items'] = $items;
    
    echo json_encode(['success' => true, 'bill' => $bill]);
}

$conn->close();
?>
