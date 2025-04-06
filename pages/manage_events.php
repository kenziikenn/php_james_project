<?php
$conn = mysqli_connect("localhost", "root", "", "ajs_db1");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Search parameter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Pagination settings
$records_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records with search
$total_query = "SELECT COUNT(*) as total FROM event";
if ($search) {
    $total_query .= " WHERE event_name LIKE '%$search%'";
}
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Fetch events with pagination and search
$events_query = "SELECT * FROM event";
if ($search) {
    $events_query .= " WHERE event_name LIKE '%$search%'";
}
$events_query .= " ORDER BY event_date DESC LIMIT $offset, $records_per_page";
$events = mysqli_query($conn, $events_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager</title>
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

        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(44, 62, 80, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .events-table th, .events-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(52, 152, 219, 0.3);
            color: #fff;
        }

        .events-table th {
            background: rgba(52, 152, 219, 0.2);
            font-weight: 600;
        }

        .events-table tr:hover {
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

        .sidebar {
        background: #1e2a3a;
        padding-top: 30px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        z-index: 100;
    }
    
  important; .sidebar {
            background: rgba(44, 62, 80, 0.9);
            padding-top: 90px;
            border-right: 2px solid rgba(52, 152, 219, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            z-index: 100;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 15px 25px;
            margin: 8px 15px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: rgba(52, 152, 219, 0.1);
        }

        .sidebar a:hover {
            background: rgba(52, 152, 219, 0.3);
            transform: translateX(10px);
        }
        /* Replace the existing animations with these new ones */
        @keyframes fadeInScale {
            from {
                transform: scale(0.95);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        @keyframes slideInBottom {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideInTop {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Update the element animations */
        .navbar {
            animation: slideInTop 0.5s ease-out;
        }
        
        .sidebar {
            animation: fadeInScale 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .content {
            animation: fadeInScale 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .events-table {
            animation: slideInBottom 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-add, .back-btn {
            animation: slideInBottom 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.2s backwards;
        }
        
        .pagination {
            animation: slideInBottom 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.3s backwards;
        }
        
        .sidebar a {
            animation: slideInBottom 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            animation-fill-mode: both;
        }
        
        /* Add staggered delay for sidebar links */
        .sidebar a:nth-child(1) { animation-delay: 0.1s; }
        .sidebar a:nth-child(2) { animation-delay: 0.2s; }
        .sidebar a:nth-child(3) { animation-delay: 0.3s; }
        .sidebar a:nth-child(4) { animation-delay: 0.4s; }
        .sidebar a:nth-child(5) { animation-delay: 0.5s; }
        .sidebar a:nth-child(6) { animation-delay: 0.6s; }
        .sidebar a:nth-child(7) { animation-delay: 0.7s; }
        .sidebar a:nth-child(8) { animation-delay: 0.8s; }
        .sidebar a:nth-child(9) { animation-delay: 0.9s; }
    }

        .sidebar a:hover {
            background: rgba(52, 152, 219, 0.3);
            transform: translateX(10px);
        }
        /* Add these new animations */
@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Update these existing elements */
.sidebar {
    animation: slideInLeft 0.6s ease-out;
}

.content {
    animation: slideInRight 0.6s ease-out;
}

.events-table {
    animation: fadeInUp 0.8s ease-out;
}

.navbar {
    animation: slideInLeft 0.5s ease-out;
}

.btn-add, .back-btn {
    animation: fadeInUp 0.6s ease-out;
}

.pagination {
    animation: fadeInUp 0.7s ease-out;
}
    
    </style>
</head>
<body>
    <div class="content">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    if (isset($_GET['action']) && $_GET['action'] === 'edit') {
                        echo "Event edited successfully!";
                    } else {
                        echo "Event created successfully!";
                    }
                ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">Error creating event. Please try again.</div>
        <?php endif; ?>
        <a href="hp.php" class="back-btn">← Back to Homepage</a>
        <button class="btn-add" onclick="openAddModal()">Add New Event</button>
        <select name="per_page" onchange="window.location.href='?per_page=' + this.value + '<?php echo $search ? '&search='.urlencode($search) : ''; ?>'" 
                style="padding: 10px; margin-left: 10px; border-radius: 8px; border: 1px solid rgba(52, 152, 219, 0.3); 
                       background: rgb(53, 76, 194); color: #fff;">
            <option value="3" <?php echo $records_per_page == 3 ? 'selected' : ''; ?>>Show 3</option>
            <option value="5" <?php echo $records_per_page == 5 ? 'selected' : ''; ?>>Show 5</option>
            <option value="10" <?php echo $records_per_page == 10 ? 'selected' : ''; ?>>Show 10</option>
            <option value="20" <?php echo $records_per_page == 20 ? 'selected' : ''; ?>>Show 20</option>
        </select>
        
        <!-- Add search bar -->
        <form method="GET" style="margin-bottom: 20px;">
            <input type="text" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>" 
                   style="padding: 10px; border-radius: 8px; border: 1px solid rgba(52, 152, 219, 0.3); 
                          background: rgba(255, 255, 255, 0.9); color: #000; width: 300px;">
            <button type="submit" class="btn-add">Search</button>
            <?php if ($search): ?>
                <a href="manage_events.php" class="btn-add">Clear</a>
            <?php endif; ?>
        </form>

        <table class="events-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date & Time</th>
                    <th>Venue</th> <!-- Added Venue column -->
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($event = mysqli_fetch_assoc($events)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($event['event_date'])); ?></td>
                        <td><?php echo htmlspecialchars($event['venue']); ?></td> <!-- Display Venue -->
                        <td><?php echo $event['status']; ?></td>
                        <td class="action-buttons">
                            <button class="edit-btn" onclick="openEditModal(<?php 
                                echo htmlspecialchars(json_encode([
                                    'id' => $event['event_id'],
                                    'name' => $event['event_name'],
                                    'date' => date('Y-m-d\TH:i', strtotime($event['event_date'])),
                                    'venue' => $event['venue'] // Include venue in modal data
                                ])); 
                            ?>)">Edit</button>
                            <button class="deactivate-btn" onclick="toggleStatus(<?php echo $event['event_id']; ?>)">
                                <?php echo $event['status'] == 'Active' ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <span style="color: #fff; margin-right: 15px;">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo ($page-1); ?>&per_page=<?php echo $records_per_page; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">&laquo; Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&per_page=<?php echo $records_per_page; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo ($page+1); ?>&per_page=<?php echo $records_per_page; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" class="page-link">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        </div>

    <!-- Add/Edit Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Add New Event</h2>
            <form id="eventForm" method="POST" action="save_event.php">
                <input type="hidden" id="eventId" name="eventId">
                <input type="text" id="eventName" name="eventName" placeholder="Event Name" required>
                <input type="datetime-local" id="eventTime" name="eventTime" required>
                <input type="text" id="venue" name="venue" placeholder="Venue" required> <!-- Added Venue input -->
                <button type="submit" class="btn-add">Save Event</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('eventModal');
        
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Event';
            document.getElementById('eventId').value = '';
            document.getElementById('eventName').value = '';
            document.getElementById('eventTime').value = '';
            document.getElementById('venue').value = ''; // Clear venue input
            modal.style.display = 'block';
        }

        function openEditModal(event) {
            document.getElementById('modalTitle').textContent = 'Edit Event';
            document.getElementById('eventId').value = event.id;
            document.getElementById('eventName').value = event.name;
            document.getElementById('eventTime').value = event.date;
            document.getElementById('venue').value = event.venue; // Set venue input
            document.getElementById('eventForm').action = 'save_event.php?action=edit';
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>

    <script>
        function toggleStatus(eventId) {
            fetch('toggle_event_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'event_id=' + eventId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.textContent = 'Error updating event status';
                    document.querySelector('.content').insertBefore(alertDiv, document.querySelector('.back-btn'));
                }
            });
        }
    </script>
</body>
</html>


