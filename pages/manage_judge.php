<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "ajs_db1";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create judges table if it doesn't exist
// First, drop the existing table
// Remove these lines
// $drop_table = "DROP TABLE IF EXISTS judges";
// $conn->query($drop_table);

// Keep only the CREATE TABLE IF NOT EXISTS
$create_table = "CREATE TABLE IF NOT EXISTS judges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    judge_code VARCHAR(50) UNIQUE NOT NULL,
    event_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event(event_id)
)";
$conn->query($create_table);

// Fetch events for dropdown
$events_query = "SELECT event_id, event_name FROM event WHERE status = 'Active'";
$events_result = $conn->query($events_query);

// Update the insert logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_judge'])) {
    $firstName = trim($_POST['first_name']);
    $middleName = trim($_POST['middle_name']);
    $lastName = trim($_POST['last_name']);
    $eventId = $_POST['event_id'];
    $judgeCode = "JUDGE-" . rand(100000, 999999);

    if (!empty($firstName) && !empty($lastName)) {
        $stmt = $conn->prepare("INSERT INTO judges (first_name, middle_name, last_name, judge_code, event_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssi", $firstName, $middleName, $lastName, $judgeCode, $eventId);
            if ($stmt->execute()) {
                header("Location: manage_judge.php?success=1");
                exit();
            }
            $stmt->close();
        }
    }
}

// Update the edit logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_judge'])) {
    $judgeId = $_POST['judge_id'];
    $firstName = trim($_POST['first_name']);
    $middleName = trim($_POST['middle_name']);
    $lastName = trim($_POST['last_name']);
    $eventId = $_POST['event_id'];
    
    if (!empty($firstName) && !empty($lastName)) {
        $stmt = $conn->prepare("UPDATE judges SET first_name = ?, middle_name = ?, last_name = ?, event_id = ? WHERE id = ?");
        $stmt->bind_param("sssii", $firstName, $middleName, $lastName, $eventId, $judgeId);
        if ($stmt->execute()) {
            header("Location: manage_judge.php?success=2");
            exit();
        }
        $stmt->close();
    }
}

// Remove this duplicate section and keep only one query section
// Update the select query with event filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Main query
$query = "SELECT j.*, e.event_name 
          FROM judges j 
          LEFT JOIN event e ON j.event_id = e.event_id
          WHERE (j.first_name LIKE ? OR j.last_name LIKE ? OR j.judge_code LIKE ?)";
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    $event_id = (int)$_GET['event_id'];
    $query .= " AND j.event_id = $event_id";
}
$query .= " ORDER BY j.created_at DESC LIMIT ? OFFSET ?";

$search_param = "%$search%";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssii", $search_param, $search_param, $search_param, $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM judges j WHERE (first_name LIKE ? OR last_name LIKE ? OR judge_code LIKE ?)";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);
  // Handle judge deletion
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_judge'])) {
    $judgeId = $_POST['judge_id'];
    $stmt = $conn->prepare("DELETE FROM judges WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $judgeId);
        if ($stmt->execute()) {
            header("Location: manage_judge.php?success=3");
            exit();
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Manager</title>
    <style>
        /* Keep only the necessary styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

            body {
                background: linear-gradient(135deg, #0a1929, #132f4c);
                color: #fff;
                min-height: 100vh;
            }

            .navbar {
                background: rgba(19, 47, 76, 0.95);
                text-align: center;
                padding: 10px;
                position: fixed;
                width: 100%;
                top: 0;
                z-index: 1000;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                border-bottom: 1px solid rgba(102, 178, 255, 0.1);
                height: 40px;
                font-size: 16px;
            }

            .content {
                position: relative;
                max-width: 1200px;
                margin: 80px auto 30px;
                padding: 20px;
                z-index: 999;
            }

            .top-buttons {
                display: flex;
                gap: 15px;
                margin-bottom: 25px;
                position: relative;
                z-index: 999;
                padding-top: 20px;
                justify-content: space-between;
                align-items: center;
            }

            .search-bar {
                background: rgba(19, 47, 76, 0.8);
                border: 1px solid rgba(102, 178, 255, 0.2);
                border-radius: 6px;
                padding: 8px 15px;
                color: #fff;
                width: 300px;
                font-size: 13px;
            }

            .search-bar:focus {
                outline: none;
                border-color: #66b2ff;
                box-shadow: 0 0 0 2px rgba(102, 178, 255, 0.1);
            }

            .pagination {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 20px;
            }

            .pagination a {
                background: rgba(19, 47, 76, 0.8);
                color: #66b2ff;
                padding: 8px 12px;
                border-radius: 6px;
                text-decoration: none;
                border: 1px solid rgba(102, 178, 255, 0.2);
                transition: all 0.3s ease;
            }

            .pagination a:hover, .pagination .active {
                background: #1e4976;
                transform: translateY(-2px);
            }
            .back-btn, .add-btn {
                padding: 10px 20px;
                font-size: 14px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .back-btn {
                background: rgba(19, 47, 76, 0.8);
                color: #66b2ff;
                padding: 6px 12px;
                border-radius: 6px;
                text-decoration: none;
                border: 1px solid rgba(102, 178, 255, 0.2);
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                font-size: 12px;
                height: 30px;
            }

            .back-btn:hover {
                background: #1e4976;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 178, 255, 0.2);
            }

            .add-btn {
                background: linear-gradient(135deg, #1e88e5, #1976d2);
                color: #fff;
                padding: 12px 25px;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .add-btn:hover {
                background: linear-gradient(135deg, #1976d2, #1565c0);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
            }

            .judges-table {
                width: 100%;
                border-collapse: collapse;
                background: rgba(19, 47, 76, 0.8);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                border: 1px solid rgba(102, 178, 255, 0.1);
            }

            .judges-table th, .judges-table td {
                padding: 16px;
                text-align: left;
                border-bottom: 1px solid rgba(102, 178, 255, 0.1);
            }

            .judges-table th {
                background: rgba(30, 73, 118, 0.5);
                color: #66b2ff;
                font-weight: 500;
            }

            .judges-table tr:hover {
                background: rgba(30, 73, 118, 0.3);
            }

            .action-buttons {
                display: flex;
                gap: 10px;
            }

            .edit-btn {
                background: #1e4976;
                color: #66b2ff;
                padding: 8px 20px;
                border: 1px solid rgba(102, 178, 255, 0.2);
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .edit-btn:hover {
                background: #275d94;
                transform: translateY(-2px);
            }

            .deactivate-btn {
                background: #d32f2f;
                color: #fff;
                padding: 8px 20px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .deactivate-btn:hover {
                background: #b71c1c;
                transform: translateY(-2px);
            }

            .modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(10, 25, 41, 0.95);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 1100;
            }

            .modal-content {
                background: #132f4c;
                padding: 30px;
                border-radius: 16px;
                border: 1px solid rgba(102, 178, 255, 0.15);
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
                width: 90%;
                max-width: 500px;
                position: relative;
                transform: translateY(0);
            }

            .modal-content h2 {
                color: #66b2ff;
                margin-bottom: 20px;
                font-size: 24px;
            }

            .modal-content input, .modal-content select {
                width: 100%;
                padding: 12px;
                margin: 8px 0;
                background: rgba(10, 25, 41, 0.5);
                border: 1px solid rgba(102, 178, 255, 0.2);
                border-radius: 8px;
                color: #fff;
                font-size: 14px;
            }

            .modal-content input:focus, .modal-content select:focus {
                border-color: #66b2ff;
                outline: none;
                box-shadow: 0 0 0 3px rgba(102, 178, 255, 0.15);
            }

            .btn-add {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #1e88e5, #1976d2);
                color: #fff;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 15px;
                margin-top: 15px;
                transition: all 0.3s ease;
            }

            .btn-add:hover {
                background: linear-gradient(135deg, #1976d2, #1565c0);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
            }

            .close {
                color: #b2bac2;
                float: right;
                font-size: 28px;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .close:hover {
                color: #fff;
            }
        </style>
    </div>
</head>
<body>
    <div class="navbar">Judge Manager</div>
    <div class="content">
        <div class="top-buttons">
            <div class="left-buttons" style="display: flex; gap: 15px; align-items: center;">
                <button class="add-btn" onclick="openAddModal()">Add New Judge</button>
                <a href="hp.php" class="back-btn">← Back to Homepage</a>
            </div>
            <input type="text" class="search-bar" placeholder="Search judges..." value="<?php echo htmlspecialchars($search); ?>" onkeyup="searchJudges(this.value)">
        </div>

        <table class="judges-table">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Judge Code</th>
                    <th>Event</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($judge = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($judge['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($judge['middle_name']); ?></td>
                        <td><?php echo htmlspecialchars($judge['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($judge['judge_code']); ?></td>
                        <td><?php echo htmlspecialchars($judge['event_name']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($judge['created_at'])); ?></td>
                        <td class="action-buttons">
                            <button class="edit-btn" onclick="openEditModal(<?php 
                                echo htmlspecialchars(json_encode([
                                    'id' => $judge['id'],
                                    'firstName' => $judge['first_name'],
                                    'middleName' => $judge['middle_name'],
                                    'lastName' => $judge['last_name'],
                                    'eventId' => $judge['event_id']
                                ])); 
                            ?>)">Edit</button>
                            <button class="deactivate-btn" onclick="deleteJudge(<?php echo $judge['id']; ?>, '<?php echo htmlspecialchars($judge['first_name'] . ' ' . $judge['last_name']); ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; 
                ?>
                </tbody>
            </table>
            
            <div id="addJudgeModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>Add New Judge</h2>
                    <form method="POST" action="">
                        <select name="event_id" required>
                            <option value="">Select Event</option>
                            <?php 
                            mysqli_data_seek($events_result, 0);
                            while($event = $events_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $event['event_id']; ?>">
                                    <?php echo htmlspecialchars($event['event_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="middle_name" placeholder="Middle Name">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="hidden" name="add_judge" value="1">
                        <button type="submit" class="btn-add">Add Judge</button>
                    </form>
                </div>
            </div>
            
            <!-- Edit Judge Modal -->
            <div id="editJudgeModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Judge</h2>
                    <form method="POST" action="manage_judge.php">
                        <select name="event_id" id="editEventId" required style="width: 100%; padding: 12px; margin: 10px 0; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(52, 152, 219, 0.3); border-radius: 8px; color: #fff;">
                            <option value="">Select Event</option>
                            <?php 
                            mysqli_data_seek($events_result, 0);
                            while($event = $events_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $event['event_id']; ?>">
                                    <?php echo htmlspecialchars($event['event_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="text" name="first_name" id="editFirstName" placeholder="First Name" required>
                        <input type="text" name="middle_name" id="editMiddleName" placeholder="Middle Name">
                        <input type="text" name="last_name" id="editLastName" placeholder="Last Name" required>
                        <input type="hidden" name="judge_id" id="editJudgeId">
                        <input type="hidden" name="edit_judge" value="1">
                        <button type="submit" class="btn-add">Save Changes</button>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteConfirmModal" class="modal">
                <div class="modal-content" style="max-width: 400px;">
                    <div style="text-align: center; padding: 20px;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 20px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#ff4d4d" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M15 9l-6 6M9 9l6 6"/>
                            </svg>
                        </div>
                        <h2 style="color: #ff4d4d; margin-bottom: 10px;">Are you sure?</h2>
                        <p style="color: #ccc; margin-bottom: 20px;">Do you really want to delete these records? This process cannot be undone.</p>
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button onclick="closeDeleteModal()" style="padding: 10px 20px; background: #4a5568; border: none; border-radius: 6px; color: white; cursor: pointer;">Cancel</button>
                            <form id="deleteForm" method="POST" style="margin: 0;">
                                <input type="hidden" name="judge_id" id="deleteJudgeId">
                                <input type="hidden" name="delete_judge" value="1">
                                <button type="submit" style="padding: 10px 20px; background: #ff4d4d; border: none; border-radius: 6px; color: white; cursor: pointer;">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                function deleteJudge(id, name) {
                    const modal = document.getElementById('deleteConfirmModal');
                    document.getElementById('deleteJudgeId').value = id;
                    modal.style.display = 'flex';
                }

                function closeDeleteModal() {
                    document.getElementById('deleteConfirmModal').style.display = 'none';
                }

                // Close modals when clicking outside
                window.onclick = function(event) {
                    const deleteModal = document.getElementById('deleteConfirmModal');
                    const addModal = document.getElementById('addJudgeModal');
                    const editModal = document.getElementById('editJudgeModal');
                    
                    if (event.target === deleteModal) {
                        closeDeleteModal();
                    } else if (event.target === addModal) {
                        closeModal();
                    } else if (event.target === editModal) {
                        closeEditModal();
                    }
                }
            </script>
            <script>
                function openAddModal() {
                    document.getElementById('addJudgeModal').style.display = 'flex';
                }

                function closeModal() {
                    document.getElementById('addJudgeModal').style.display = 'none';
                }

                // Add this to your existing window.onclick function
                window.onclick = function(event) {
                    const addModal = document.getElementById('addJudgeModal');
                    if (event.target === addModal) {
                        closeModal();
                    }
                }
            </script>
            
          