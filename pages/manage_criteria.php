
<?php
// Update database connection to use the existing database
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add pagination code
$records_per_page = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records
$total_query = "SELECT COUNT(*) as total FROM criterion";
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get criteria with pagination
$query = "SELECT criterion_id, description, weight FROM criterion LIMIT $offset, $records_per_page";
$criteria_result = mysqli_query($conn, $query);

// Calculate total percentage
$total_query = "SELECT SUM(weight) as total_weight FROM criterion";
$total_weight_result = mysqli_query($conn, $total_query);
$total_weight = mysqli_fetch_assoc($total_weight_result)['total_weight'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Criteria</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #0a1929;
            color: #fff;
            min-height: 100vh;
        }

        .navbar {
            background: #132f4c;
            padding: 18px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.3);
        }

        .content {
            margin-top: 70px;
            padding: 25px;
        }

        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #132f4c;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(102, 178, 255, 0.1);
        }

        .criteria-table th, .criteria-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(102, 178, 255, 0.1);
        }

        .criteria-table th {
            background: #1e4976;
            color: #66b2ff;
            font-weight: 500;
        }

        .criteria-table tr:hover {
            background: #1e4976;
        }

        .action-buttons button {
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            margin-right: 8px;
        }

        .edit-btn {
            background: #1e4976;
            color: #66b2ff;
            border: 1px solid rgba(102, 178, 255, 0.2);
        }

        .edit-btn:hover {
            background: #275d94;
            transform: translateY(-2px);
        }

        .deactivate-btn {
            background: #d32f2f;
            color: #fff;
        }

        .deactivate-btn:hover {
            background: #b71c1c;
            transform: translateY(-2px);
        }

        .btn-add {
            background: linear-gradient(135deg, #1e88e5, #1976d2);
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }

        .back-btn {
            background: #1e4976;
            color: #66b2ff;
            padding: 12px 25px;
            border: 1px solid rgba(102, 178, 255, 0.2);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #275d94;
            transform: translateY(-2px);
        }

        .form-select {
            width: 100%;
            padding: 12px;
            background: #132f4c;
            border: 1px solid rgba(102, 178, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            margin: 10px 0;
        }

        .form-select:focus {
            border-color: #66b2ff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 178, 255, 0.15);
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

        .modal-content .btn-add {
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

        .modal-content .btn-add:hover {
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

        .modal-content h2 {
            color: #66b2ff;
            margin-bottom: 20px;
        }

        .pagination {
            margin-top: 20px;
            gap: 10px;
        }

        .pagination a {
            background: #1e4976;
            color: #66b2ff;
            padding: 8px 16px;
            border: 1px solid rgba(102, 178, 255, 0.2);
            border-radius: 8px;
            text-decoration: none;
        }

        .pagination a:hover, .pagination a.active {
            background: #275d94;
            transform: translateY(-2px);
        }
        
        .modal.fade-in {
                            animation: fadeIn 0.3s ease-in-out;
                        }

                        .modal-content.slide-up {
                            animation: slideUp 0.3s ease-in-out;
                        }

                        @keyframes fadeIn {
                            from {
                                opacity: 0;
                            }
                            to {
                                opacity: 1;
                            }
                        }

                        @keyframes slideUp {
                            from {
                                opacity: 0;
                                transform: translate(-50%, 20%);
                            }
                            to {
                                opacity: 1;
                                transform: translate(-50%, -50%);
                            }
                        }

                        /* Update the delete modal styles */
                        #deleteModal .modal-content {
                            background: white;
                            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                            transition: all 0.3s ease;
                        }
    </style>
</head>
<body>
    <div class="navbar">Manage Criteria</div>

    <div class="content">
        <div class="top-buttons">
            <a href="hp.php" class="back-btn">← Back to Homepage</a>
            <button class="btn-add" onclick="openModal()" style="padding: 6px 12px; font-size: 12px;">Add New Criteria</button>
        </div>

        <!-- Add event selection -->
        <div class="event-selection">
            <select id="eventSelect" class="form-select" onchange="loadEventCriteria()">
                <option value="">Select Event</option>
                <?php
                $events_query = "SELECT event_id, event_name FROM event WHERE status = 'Active'";
                $events_result = mysqli_query($conn, $events_query);
                while($event = mysqli_fetch_assoc($events_result)): 
                ?>
                    <option value="<?php echo $event['event_id']; ?>">
                        <?php echo htmlspecialchars($event['event_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Modify the add modal -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Add New Criteria</h2>
                <select id="modalEventSelect" class="form-select" required>
                    <option value="">Select Event</option>
                    <?php
                    mysqli_data_seek($events_result, 0);
                    while($event = mysqli_fetch_assoc($events_result)): 
                    ?>
                        <option value="<?php echo $event['event_id']; ?>">
                            <?php echo htmlspecialchars($event['event_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="text" id="criteriaInput" placeholder="Enter criteria">
                <input type="number" id="percentageInput" placeholder="Enter percentage" min="0" max="100">
                <button class="btn-add" onclick="addCriteria()">Save Criteria</button>
            </div>
        </div>

        <!-- Rest of your table code -->
        <table class="criteria-table">
            <thead>
                <tr>
                    <th>Criteria</th>
                    <th>Percentage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($criteria_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['weight']); ?>%</td>
                        <td>
                            <div class="action-buttons">
                                <button onclick="openEditModal('<?php echo htmlspecialchars($row['description']); ?>', <?php echo $row['weight']; ?>, <?php echo $row['criterion_id']; ?>)" 
                                        class="edit-btn">Edit</button>
                                <button onclick="deleteCriteria(this, '<?php echo htmlspecialchars($row['description']); ?>', <?php echo $row['weight']; ?>, <?php echo $row['criterion_id']; ?>)" 
                                        class="deactivate-btn">Delete</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add edit modal after the add modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2>Edit Criteria</h2>
                <input type="text" id="editCriteriaInput" placeholder="Enter criteria">
                <input type="number" id="editPercentageInput" placeholder="Enter percentage" min="0" max="100">
                <input type="hidden" id="editCriteriaId">
                <button class="btn-add" onclick="updateCriteria()">Update Criteria</button>
            </div>
        </div>

        <!-- Add these JavaScript functions -->
        <script>
            // Single, unified modal handling functions
            function openModal() {
                document.getElementById('addModal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('addModal').style.display = 'none';
                document.getElementById('criteriaInput').value = '';
                document.getElementById('percentageInput').value = '';
                document.getElementById('modalEventSelect').value = '';
            }

            function openEditModal(description, weight, id) {
                const modal = document.getElementById('editModal');
                modal.style.display = 'flex';
                document.getElementById('editCriteriaInput').value = description;
                document.getElementById('editPercentageInput').value = weight;
                document.getElementById('editCriteriaId').value = id;
            }

            function closeEditModal() {
                const modal = document.getElementById('editModal');
                modal.style.display = 'none';
                document.getElementById('editCriteriaInput').value = '';
                document.getElementById('editPercentageInput').value = '';
                document.getElementById('editCriteriaId').value = '';
            }

            // Close modals when clicking outside
            window.onclick = function(event) {
                const addModal = document.getElementById('addModal');
                const editModal = document.getElementById('editModal');
                if (event.target === addModal) {
                    closeModal();
                }
                if (event.target === editModal) {
                    closeEditModal();
                }
            };

            function updateCriteria() {
                let input = document.getElementById("editCriteriaInput");
                let percentageInput = document.getElementById("editPercentageInput");
                let id = document.getElementById("editCriteriaId").value;
                let criteriaText = input.value.trim();
                let percentage = parseInt(percentageInput.value.trim(), 10);
                
                if (criteriaText === "" || isNaN(percentage) || percentage < 0) {
                    alert("Please enter a valid criteria and percentage.");
                    return;
                }
                
                if (percentage > 100) {
                    alert("Must be BELOW 100%.");
                    return;
                }
                
                fetch('update_criteria.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&description=${encodeURIComponent(criteriaText)}&weight=${percentage}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert("Error updating criteria: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Error updating criteria: " + error);
                });
            }

            // Initialize totalPercentage with the value from PHP
            let totalPercentage = <?php echo $total_weight ?: 0; ?>;
            let criteriaSet = new Set();

            function loadEventCriteria() {
                const eventId = document.getElementById('eventSelect').value;
                if (!eventId) {
                    const tbody = document.querySelector('.criteria-table tbody');
                    tbody.innerHTML = '';
                    return;
                }

                fetch(`get_criteria_by_event.php?event_id=${eventId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        
                        const tbody = document.querySelector('.criteria-table tbody');
                        tbody.innerHTML = '';
                        let totalWeight = 0;

                        data.forEach(row => {
                            totalWeight += parseFloat(row.weight);
                            const escapedDescription = row.description.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                            tbody.innerHTML += `
                                <tr>
                                    <td>${escapedDescription}</td>
                                    <td>${row.weight}%</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="openEditModal('${escapedDescription}', ${row.weight}, ${row.criterion_id})" 
                                                    class="edit-btn">Edit</button>
                                            <button onclick="deleteCriteria(this, '${escapedDescription}', ${row.weight}, ${row.criterion_id})" 
                                                    class="deactivate-btn">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading criteria: ' + error.message);
                    });
            }

            function addCriteria() {
                const eventId = document.getElementById('modalEventSelect').value;
                const criteriaInput = document.getElementById('criteriaInput');
                const percentageInput = document.getElementById('percentageInput');
                const criteriaText = criteriaInput.value.trim();
                const percentage = parseInt(percentageInput.value.trim(), 10);

                if (!eventId) {
                    alert("Please select an event");
                    return;
                }

                if (criteriaText === "" || isNaN(percentage) || percentage < 0) {
                    alert("Please enter valid criteria and percentage");
                    return;
                }

                if (percentage > 100) {
                    alert("Percentage must be below 100%");
                    return;
                }

                fetch('save_criteria.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `description=${encodeURIComponent(criteriaText)}&weight=${percentage}&event_id=${eventId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert("Error saving criteria: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Error saving criteria: " + error);
                });

                closeModal();
            }
            let deleteItemId = null;

            // In the JavaScript deleteCriteria function, update to:
            function deleteCriteria(button, criteriaText, percentage, id) {
                if (confirm(`Are you sure you want to delete "${criteriaText}"?`)) {
                    fetch('delete_criteria.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            button.closest('tr').remove();
                            // Update total percentage display if needed
                        } else {
                            alert("Error deleting criteria: " + data.message);
                        }
                    })
                    .catch(error => {
                        alert("Error deleting criteria: " + error);
                    });
                }
            }
            
        </script>

        <!-- Delete Confirmation Modal -->
                <div id="deleteModal" class="modal">
                    <div class="modal-content" style="text-align: center; max-width: 400px; padding: 30px; background: white;">
                        <div style="margin-bottom: 20px;">
                            <svg width="50" height="50" viewBox="0 0 24 24" style="margin: 0 auto; display: block;">
                                <circle cx="12" cy="12" r="11" stroke="#ff6b6b" stroke-width="2" fill="none"/>
                                <path d="M8 8L16 16M8 16L16 8" stroke="#ff6b6b" stroke-width="2"/>
                            </svg>
                        </div>
                        <h2 style="font-size: 24px; margin-bottom: 10px; color: #333;">Are you sure?</h2>
                        <p style="color: #666; margin-bottom: 25px;">Do you really want to delete these records? This process cannot be undone.</p>
                        <div style="display: flex; justify-content: center; gap: 15px;">
                            <button id="cancelDelete" type="button" 
                                    style="background: #eee; color: #666; border: none; padding: 10px 30px; border-radius: 4px; cursor: pointer; font-size: 14px; min-width: 120px;">
                                Cancel
                            </button>
                            <button id="confirmDelete" type="button" 
                                    style="background: #ff6b6b; color: #fff; border: none; padding: 10px 30px; border-radius: 4px; cursor: pointer; font-size: 14px; min-width: 120px;">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

           
</body>
</html>

