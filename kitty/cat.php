<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password if required
$dbname = "cat_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("error" => "Connection failed: " . $conn->connect_error)));
}

// Function to handle CRUD operations
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        if (!empty($_GET["id"])) {
            // Retrieve a single cat
            $id = intval($_GET["id"]);
            get_cat($id); 
        } else {
            // Retrieve all cats
            get_cats();
        }
        break;
        
    case 'POST':
        // Insert a new cat
        insert_cat();
        break;
        
    case 'PUT':
        // Update an existing cat
        $id = intval($_GET["id"]);
        update_cat($id);
        break;
        
    case 'DELETE':
        // Delete a cat
        $id = intval($_GET["id"]);
        delete_cat($id);
        break;
        
    default:
        // Invalid request method
        http_response_code(405);
        echo json_encode(array("error" => "Method Not Allowed"));
        break;
}

$conn->close();

// Retrieve all cats
function get_cats() {
    global $conn;
    $sql = "SELECT * FROM chicken";
    $result = $conn->query($sql);
    $cats = array();

    while($row = $result->fetch_assoc()) {
        $cats[] = $row;
    }

    echo json_encode($cats);
}

// Retrieve a single cat
function get_cat($id) {
    global $conn;
    $sql = "SELECT * FROM chicken WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        http_response_code(404);
        echo json_encode(array("error" => "Cat not found"));
    }
}

// Insert a new cat
function insert_cat() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data["name"];
    $age = $data["age"];
    $year = $data["year"];

    if (!empty($name) && !empty($age) && !empty($year)) {
        $sql = "INSERT INTO chicken (name, age, year) VALUES ('$name', '$age', '$year')";
        if ($conn->query($sql) === TRUE) {
            http_response_code(201);
            echo json_encode(array("message" => "Cat added successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("error" => "Error adding cat: " . $conn->error));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("error" => "Invalid input"));
    }
}

// Update an existing cat
function update_cat($id) {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data["name"];
    $age = $data["age"];
    $year = $data["year"];

    if (!empty($name) && !empty($age) && !empty($year)) {
        $sql = "UPDATE chicken SET name='$name', age='$age', year='$year' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Cat updated successfully"));
        } else {
            http_response_code(500);
            echo json_encode(array("error" => "Error updating cat: " . $conn->error));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("error" => "Invalid input"));
    }
}

// Delete a cat
function delete_cat($id) {
    global $conn;
    $sql = "DELETE FROM chicken WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("message" => "Cat deleted successfully"));
    } else {
        http_response_code(500);
        echo json_encode(array("error" => "Error deleting cat: " . $conn->error));
    }
}
?>
