<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Special Awards</title>
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

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: rgba(44, 62, 80, 0.9);
            padding-top: 20px;
        }

        .sidebar a {
            padding: 15px 25px;
            color: #fff;
            text-decoration: none;
            display: block;
            font-size: 1rem;
            margin: 8px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: rgba(52, 152, 219, 0.1);
        }

        .sidebar a:hover {
            background: rgba(52, 152, 219, 0.3);
            transform: translateX(10px);
        }

        .content {
            margin-left: 270px;
            padding: 40px;
        }

        .form-container {
            background: rgba(44, 62, 80, 0.8);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
        }

        .form-container input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-custom {
            background: rgba(52, 152, 219, 0.3);
            border: 1px solid rgba(52, 152, 219, 0.3);
            padding: 12px 25px;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background: rgba(52, 152, 219, 0.5);
            transform: translateY(-2px);
        }

        .awards-list {
            margin-top: 20px;
            background: rgba(44, 62, 80, 0.8);
            padding: 20px;
            border-radius: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #fff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(52, 152, 219, 0.3);
        }

        th {
            background: rgba(52, 152, 219, 0.2);
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            background: rgba(46, 204, 113, 0.3);
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .edit-btn:hover {
            background: rgba(46, 204, 113, 0.5);
        }

        .delete-btn {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .delete-btn:hover {
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
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(44, 62, 80, 0.95);
            padding: 40px;
            border-radius: 15px;
            min-width: 500px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(52, 152, 219, 0.3);
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }

        .modal-content h2 {
            color: #fff;
            margin-bottom: 25px;
            font-size: 1.8rem;
            text-align: center;
        }

        .modal-content input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .modal-content select {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(52, 152, 219, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .modal-content select option {
            background: #2c3e50;
            color: #fff;
            padding: 10px;
        }

        .modal-content input:focus {
            outline: none;
            border-color: rgba(52, 152, 219, 0.8);
            background: rgba(255, 255, 255, 0.15);
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }

        .save-btn {
            background: rgba(46, 204, 113, 0.3);
            border: 1px solid rgba(46, 204, 113, 0.3);
            padding: 12px 30px;
        }

        .save-btn:hover {
            background: rgba(46, 204, 113, 0.5);
        }

        .cancel-btn {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(231, 76, 60, 0.3);
            padding: 12px 30px;
        }

        .cancel-btn:hover {
            background: rgba(231, 76, 60, 0.5);
        }
    </style>
</head>
<body>
    <div class="navbar">Manage Special Awards</div>
    <div class="sidebar">
        <a href="hp.php">Back to Homepage</a>
    </div>

    <div class="content">
        <div class="form-container" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 30px;">
            <h2 style="color: #fff; font-size: 24px; margin: 0;">Special Awards Management</h2>
            <button class="btn-custom" onclick="openAddModal()" id="addAwardBtn" 
                    style="background: #2980b9; border: none; padding: 10px 20px; border-radius: 5px;">
                Add New Special Award
            </button>
        </div>
        
        <div class="awards-list">
            <div style="background: rgba(44, 62, 80, 0.6); padding: 15px 20px; border-radius: 5px; margin-bottom: 20px;">
                <div style="margin-bottom: 10px; color: #fff;">Filter by Event:</div>
                <select id="eventFilter" class="event-select" 
                        style="width: 200px; padding: 8px; background: rgba(44, 62, 80, 0.9); color: #fff; border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 4px;" 
                        onchange="filterAwards()">
                    <option value="">All Events</option>
                    <?php
                    $events_query = "SELECT event_id, event_name FROM event WHERE status = 'active'";
                    $events_result = mysqli_query($conn, $events_query);
                    while ($event = mysqli_fetch_assoc($events_result)) {
                        echo "<option value='" . $event['event_id'] . "'>" . htmlspecialchars($event['event_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Award Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="awardsTableBody">
                    <?php
                    // Add error reporting at the top
                    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                    // Update the query
                    $query = "SELECT sa.*, e.event_name 
                              FROM special_awards sa 
                              LEFT JOIN event e ON sa.event_id = e.event_id 
                              WHERE sa.status = 'active' 
                              ORDER BY sa.id DESC";
                    
                    try {
                        $result = mysqli_query($conn, $query);
                        if (!$result) {
                            throw new Exception(mysqli_error($conn));
                        }
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr data-event-id='" . $row['event_id'] . "'>";
                            echo "<td>" . htmlspecialchars($row['award_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td class='action-buttons'>
                                    <button class='btn-custom edit-btn' onclick='openEditModal({
                                        id: " . $row['id'] . ",
                                        name: \"" . addslashes($row['award_name']) . "\",
                                        description: \"" . addslashes($row['description']) . "\",
                                        event_id: \"" . $row['event_id'] . "\"
                                    })'>Edit</button>
                                    <button class='btn-custom delete-btn' onclick='deleteAward(" . $row['id'] . ")'>Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } catch (Exception $e) {
                        echo "<tr><td colspan='3'>Error loading awards: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="awardModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Add Special Award</h2>
            <input type="hidden" id="awardId">
            <select id="modalEventSelect" class="event-select">
                <option value="">Select an Event</option>
                <?php
                $events_query = "SELECT event_id, event_name FROM event WHERE status = 'active'";
                $events_result = mysqli_query($conn, $events_query);
                while ($event = mysqli_fetch_assoc($events_result)) {
                    echo "<option value='" . $event['event_id'] . "'>" . htmlspecialchars($event['event_name']) . "</option>";
                }
                ?>
            </select>
            <input type="text" id="awardName" placeholder="Enter award name">
            <input type="text" id="awardDescription" placeholder="Enter award description">
            <div class="modal-buttons">
                <button class="btn-custom save-btn" onclick="saveAward()">Save</button>
                <button class="btn-custom cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('awardModal');
        let isEditing = false;

        function openAddModal() {
            isEditing = false;
            document.getElementById('modalTitle').textContent = 'Add Special Award';
            document.getElementById('awardId').value = '';
            document.getElementById('awardName').value = '';
            document.getElementById('awardDescription').value = '';
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        function saveAward() {
            const eventId = document.getElementById('modalEventSelect').value;
            const name = document.getElementById('awardName').value.trim();
            const description = document.getElementById('awardDescription').value.trim();

            if (!eventId) {
                alert('Please select an event');
                return;
            }

            if (!name) {
                alert('Please enter an award name');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('event_id', eventId);
            formData.append('award_name', name);
            formData.append('description', description);
            if (isEditing) {
                formData.append('id', document.getElementById('awardId').value);
            }

            const endpoint = isEditing ? 'update_special_award.php' : 'save_special_award.php';

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error saving award');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving award');
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        
        function openEditModal(award) {
            isEditing = true;
            document.getElementById('modalTitle').textContent = 'Edit Special Award';
            document.getElementById('awardId').value = award.id;
            document.getElementById('awardName').value = award.name;
            document.getElementById('awardDescription').value = award.description;
            document.getElementById('modalEventSelect').value = award.event_id;
            modal.style.display = 'block';
        }

        let deleteId = null;
        const deleteModal = document.getElementById('deleteConfirmModal');

        function deleteAward(id) {
            deleteId = id;
            document.getElementById('deleteConfirmModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId) {
                fetch('delete_special_award.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + deleteId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting award');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting award');
                });
            }
            closeDeleteModal();
        }

        // Update the window click handler to include the delete modal
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }
        function filterAwards() {
                const eventId = document.getElementById('eventFilter').value;
                const rows = document.querySelectorAll('#awardsTableBody tr');
                
                rows.forEach(row => {
                    const eventIdAttr = row.getAttribute('data-event-id');
                    if (!eventId || eventIdAttr === eventId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
    </script>

    <!-- Delete Confirmation Modal -->
        <div class="modal" id="deleteConfirmModal">
            <div class="modal-content" style="text-align: center; max-width: 400px; padding: 30px;">
                <div style="margin-bottom: 20px;">
                    <svg width="50" height="50" viewBox="0 0 24 24" style="margin: 0 auto;">
                        <circle cx="12" cy="12" r="11" stroke="#ff6b6b" stroke-width="2" fill="none"/>
                        <path d="M8 8L16 16M8 16L16 8" stroke="#ff6b6b" stroke-width="2"/>
                    </svg>
                </div>
                <h2 style="font-size: 24px; margin-bottom: 10px;">Are you sure?</h2>
                <p style="color: #999; margin-bottom: 25px;">Do you really want to delete these records? This process cannot be undone.</p>
                <div class="modal-buttons" style="gap: 10px;">
                    <button class="btn-custom cancel-btn" onclick="closeDeleteModal()" style="background: #eee; color: #666;">Cancel</button>
                    <button class="btn-custom delete-btn" onclick="confirmDelete()" style="background: #ff6b6b;">Delete</button>
                </div>
            </div>
        </div>
        
</body>
</html>