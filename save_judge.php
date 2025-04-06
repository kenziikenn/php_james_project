if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'], $_POST['code'])) {
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);

        if (!empty($name) && !empty($code)) {
            // DEBUG: Print received values
            echo "Received Name: $name, Code: $code<br>";

            // Database Connection
            $conn = new mysqli("localhost", "root", "", "ajs_db1");
            if ($conn->connect_error) {
                die("Database Connection Failed: " . $conn->connect_error);
            }

            $query = "INSERT INTO judges (name, judge_code) VALUES (?, ?)";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("ss", $name, $code);

            if ($stmt->execute()) {
                echo "Judge added successfully!";
            } else {
                echo "SQL Error: " . $stmt->error;
            }
        } else {
            echo "Error: Name and code cannot be empty.";
        }
    } else {
        echo "Error: Invalid data received.";
    }
} else {
    echo "Error: Invalid request method.";
}
