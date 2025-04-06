<?php
session_start();
include("../include/db_connect.php");

// Add events query here
$events_query = "SELECT event_id, event_name FROM event WHERE status = 'Active'";
$events_result = $conn->query($events_query);

// Update the contestant query to include event information
$result = $conn->query("SELECT c.candidate_id, c.first_name, c.middle_name, c.last_name, c.about, c.image, c.status, e.event_name 
                       FROM candidate c 
                       LEFT JOIN event e ON c.event_id = e.event_id");

// Update the form submission section
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addContestant"])) {
    $eventId = $_POST["eventId"];
    $contestantName = trim($_POST["contestantName"]);
    $nameParts = explode(" ", $contestantName, 2);
    $firstName = $nameParts[0] ?? '';
    $lastName = $nameParts[1] ?? '';
    $about = trim($_POST["about"]);
    $status = 'active';
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["contestantImage"]["name"]);
    move_uploaded_file($_FILES["contestantImage"]["tmp_name"], $target_file);
    
    if (!empty($firstName)) {
        $stmt = $conn->prepare("INSERT INTO candidate (first_name, middle_name, last_name, about, image, status, event_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $firstName, $middleName, $lastName, $about, $target_file, $status, $eventId);

        if ($stmt->execute()) {
            echo "<script>alert('Contestant added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding contestant');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please enter at least a first name.');</script>";
    }
}

if (isset($_GET['error'])) {
    header("Location: ../index.php?error");
}

// Replace these queries at the top of the file
$events_query = "SELECT event_id, event_name FROM event WHERE status = 'Active'";
$events_result = $conn->query($events_query);

// Modify this query to work with the current table structure
$result = $conn->query("SELECT candidate_id, first_name, middle_name, last_name, about, image, status FROM candidate");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contestants</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(-45deg, #2c3e50, #3498db, #2980b9, #34495e);
            background-size: 300% 300%;
            color: #fff;
            min-height: 100vh;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .navbar {
            background: rgba(44, 62, 80, 0.9);
            padding: 25px;
            text-align: center;
            font-size: 1.8rem;
            border-bottom: 2px solid rgba(52, 152, 219, 0.3);
        }

        .content {
            padding: 40px;
        }

        .contestants-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(44, 62, 80, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .contestants-table th, .contestants-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(52, 152, 219, 0.3);
            color: #fff;
        }

        .contestants-table th {
            background: rgba(52, 152, 219, 0.2);
            font-weight: 600;
        }

        .contestants-table tr:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        .btn-add {
            background: rgba(46, 204, 113, 0.3);
            border: 1px solid rgba(46, 204, 113, 0.3);
            padding: 12px 25px;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background: rgba(46, 204, 113, 0.5);
            transform: translateY(-2px);
        }

        .action-buttons button {
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #fff;
            margin-right: 5px;
        }

        .edit-btn {
            background: rgba(241, 196, 15, 0.3);
            border: 1px solid rgba(241, 196, 15, 0.3);
        }

        .edit-btn:hover {
            background: rgba(241, 196, 15, 0.5);
        }

        .delete-btn {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #ff4d4d;
        }

        .delete-btn:hover {
            background: rgba(231, 76, 60, 0.5);
        }

        .deactivate-btn {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .deactivate-btn:hover {
            background: rgba(231, 76, 60, 0.5);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background: rgba(44, 62, 80, 0.95);
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            border: 1px solid rgba(52, 152, 219, 0.3);
            color: #fff;
        }

        .modal input, .modal textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 8px;
            color: #fff;
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 28px;
            color: #fff;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 25px;
            background: rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(52, 152, 219, 0.5);
            transform: translateY(-2px);
        }

        .contestant-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="navbar">Manage Contestants</div>

    <div class="content">
        <div class="top-buttons">
            <a href="hp.php" class="back-btn">← Back to Homepage</a>
            <button class="btn-add" onclick="openModal()">Add New Contestant</button>
        </div>

        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Add New Contestant</h2>
                <form id="contestantForm" method="POST" enctype="multipart/form-data">
                    <select name="eventId" id="eventSelect" class="form-select" required>
                        <option value="">Select Event</option>
                        <?php while($event = mysqli_fetch_assoc($events_result)): ?>
                            <option value="<?php echo $event['event_id']; ?>">
                                <?php echo htmlspecialchars($event['event_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="firstName" id="firstName" placeholder="First Name" required>
                    <input type="text" name="middleName" id="middleName" placeholder="Middle Name">
                    <input type="text" name="lastName" id="lastName" placeholder="Last Name" required>
                    <textarea name="about" id="about" placeholder="Enter About Contestant" required></textarea>
                    <input type="file" name="contestantImage" id="contestantImage" accept="image/*" required>
                    <button type="submit" class="btn-add">Save Contestant</button>
                </form>
            </div>
        </div>

        <table class="contestants-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>About</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($row['image']); ?>" class="contestant-image" alt="Contestant"></td>
                        <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['about']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td class="action-buttons">
                            <button class="edit-btn">Edit</button>
                            <button class="delete-btn" onclick="deleteContestant(<?php echo $row['candidate_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center; padding: 30px;">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <div style="margin-bottom: 20px;">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#ff4d4d" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M15 9l-6 6M9 9l6 6"/>
                </svg>
            </div>
            <h2 style="color: #ff4d4d; margin-bottom: 15px;">Are you sure?</h2>
            <p style="color: #ccc; margin-bottom: 25px;">Do you really want to delete these records? This process cannot be undone.</p>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button onclick="closeDeleteModal()" style="padding: 10px 20px; background: #4a5568; border: none; border-radius: 6px; color: white; cursor: pointer;">Cancel</button>
                <button onclick="confirmDelete()" style="padding: 10px 20px; background: #ff4d4d; border: none; border-radius: 6px; color: white; cursor: pointer;">Delete</button>
            </div>
        </div>
    </div>

    <style>
        .modal select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 8px;
            color: #fff;
        }

        .modal select option {
            background: #2c3e50;
            color: #fff;
        }
    </style>

    <script>
        function openModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }

        document.getElementById('contestantForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('eventId', document.getElementById('eventSelect').value);
            formData.append('firstName', document.getElementById('firstName').value);
            formData.append('middleName', document.getElementById('middleName').value);
            formData.append('lastName', document.getElementById('lastName').value);
            formData.append('about', document.getElementById('about').value);
            formData.append('contestantImage', document.getElementById('contestantImage').files[0]);
            formData.append('addContestant', '1');

            fetch('save_contestant.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error adding contestant: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        });

        let contestantToDelete = null;

        function deleteContestant(id) {
            contestantToDelete = id;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            contestantToDelete = null;
        }

        function confirmDelete() {
            if (contestantToDelete) {
                fetch('delete_contestant.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + contestantToDelete
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error deleting contestant: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting contestant');
                });
            }
            closeDeleteModal();
        }

        // Update window click handler to include delete modal
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
                if (event.target.id === 'deleteConfirmModal') {
                    contestantToDelete = null;
                }
            }
        }
    </script>
</body>
</html>
