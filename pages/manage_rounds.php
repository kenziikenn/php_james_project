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
    <title>Manage Rounds</title>
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
            margin-left: 270px;
            padding: 40px;
        }

        .rounds-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(44, 62, 80, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .rounds-table th, .rounds-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(52, 152, 219, 0.3);
            color: #fff;
        }

        .rounds-table th {
            background: rgba(52, 152, 219, 0.2);
            font-weight: 600;
        }

        .rounds-table tr:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        .btn-custom {
            background: rgba(46, 204, 113, 0.3);
            border: 1px solid rgba(46, 204, 113, 0.3);
            padding: 12px 25px;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-custom:hover {
            background: rgba(46, 204, 113, 0.5);
            transform: translateY(-2px);
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

        .modal input {
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

        .top-buttons {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 25px;
            background: rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(52, 152, 219, 0.5);
            transform: translateY(-2px);
        }

        .edit-btn {
            background: rgba(241, 196, 15, 0.3);
            border: 1px solid rgba(241, 196, 15, 0.3);
        }

        .edit-btn:hover {
            background: rgba(241, 196, 15, 0.5);
        }

        .deactivate-btn {
            background: rgba(231, 76, 60, 0.3);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .deactivate-btn:hover {
            background: rgba(231, 76, 60, 0.5);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            animation: fadeOut 5s forwards;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.2);
            border: 1px solid rgba(46, 204, 113, 0.3);
            color: #fff;
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #fff;
        }

        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .page-link {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: rgba(52, 152, 219, 0.5);
            transform: translateY(-2px);
        }

        .page-link.active {
            background: rgba(52, 152, 219, 0.8);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="navbar">Manage Rounds</div>

    <div class="content">
        <div class="top-buttons">
            <a href="hp.php" class="back-btn">← Back to Homepage</a>
            <button class="btn-custom" onclick="openModal()">Add New Round</button>
        </div>

        <table class="rounds-table">
            <thead>
                <tr>
                    <th>Round Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Modify this query to work with existing structure
                $rounds_query = "SELECT round_id, round_name, description FROM round ORDER BY round_id";
                $rounds_result = mysqli_query($conn, $rounds_query);
                while ($row = mysqli_fetch_assoc($rounds_result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['round_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>
                            <button onclick='openEditModal(" . $row['round_id'] . ", \"" . htmlspecialchars($row['round_name']) . "\", \"" . htmlspecialchars($row['description']) . "\")' class='btn-custom' style='padding: 5px 10px; font-size: 0.8rem; margin-right: 5px;'>Edit</button>
                            <button onclick='deleteRound(" . $row['round_id'] . ")' class='btn-custom' style='padding: 5px 10px; font-size: 0.8rem; background-color: #dc3545;'>Delete</button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Add Round Modal -->
        <div id="roundModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Add New Round</h2>
                <input type="hidden" id="roundId">
                <input type="text" id="roundName" placeholder="Enter round name">
                <input type="text" id="roundDescription" placeholder="Enter round description">
                <button class="btn-custom" onclick="saveRound()">Save Round</button>
            </div>
        </div>
    </div>

    <script>
        let isEditing = false;

        function openModal(id = null) {
            document.getElementById('roundModal').style.display = 'block';
            if (id === null) {
                isEditing = false;
                document.getElementById('modalTitle').textContent = 'Add New Round';
                document.getElementById('roundId').value = '';
                document.getElementById('roundName').value = '';
                document.getElementById('roundDescription').value = '';
            }
        }

        function openEditModal(id, name, description) {
            isEditing = true;
            document.getElementById('modalTitle').textContent = 'Edit Round';
            document.getElementById('roundId').value = id;
            document.getElementById('roundName').value = name;
            document.getElementById('roundDescription').value = description;
            document.getElementById('roundModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('roundModal').style.display = 'none';
        }

        function saveRound() {
            const id = document.getElementById('roundId').value;
            const name = document.getElementById('roundName').value;
            const description = document.getElementById('roundDescription').value;

            if (!name) {
                alert('Please enter a round name');
                return;
            }

            const formData = new FormData();
            formData.append('round_name', name);
            formData.append('description', description);
            if (isEditing) {
                formData.append('round_id', id);
            }

            fetch(isEditing ? 'update_round.php' : 'save_round.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        }

        function deleteRound(button, id) {
            if (!confirm("Are you sure you want to delete this round? This will delete all associated scores.")) {
                return;
            }

            fetch('delete_round.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.parentElement.remove();
                } else {
                    alert("Error deleting round: " + data.message);
                }
            })
            .catch(error => {
                alert("Error deleting round: " + error);
            });
        }
    </script>
</body>
</html>