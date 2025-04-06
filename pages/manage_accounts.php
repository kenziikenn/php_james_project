<?php
session_start();
include_once '../function/config.php';

// Check if user is admin
if (!isset($_SESSION['type_id']) || $_SESSION['type_id'] != 1) {
    header("Location: ../index.php?error");
    exit();
}

$data = new Databases;

// Handle account activation
if (isset($_POST['activate_account'])) {
    $account_id = $_POST['account_id'];
    $updateQuery = "UPDATE `account` SET `status` = 1 WHERE `account_id` = ?";
    $stmt = mysqli_prepare($data->con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $account_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Account activated successfully!";
    } else {
        $error_message = "Error activating account.";
    }
}

// Add these handlers after the existing activation handler
if (isset($_POST['edit_account'])) {
    $account_id = $_POST['account_id'];
    $new_username = mysqli_real_escape_string($data->con, $_POST['new_username']);
    $new_password = mysqli_real_escape_string($data->con, $_POST['new_password']);
    
    $updateQuery = "UPDATE `account` SET `uname` = ?";
    $params = array($new_username);
    $types = "s";
    
    if (!empty($new_password)) {
        $updateQuery .= ", `pword` = ?";
        $params[] = md5($new_password);
        $types .= "s";
    }
    
    $updateQuery .= " WHERE `account_id` = ?";
    $params[] = $account_id;
    $types .= "i";
    
    $stmt = mysqli_prepare($data->con, $updateQuery);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Account updated successfully!";
    } else {
        $error_message = "Error updating account.";
    }
}

if (isset($_POST['deactivate_account'])) {
    $account_id = $_POST['account_id'];
    $updateQuery = "UPDATE `account` SET `status` = 0 WHERE `account_id` = ?";
    $stmt = mysqli_prepare($data->con, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $account_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Account deactivated successfully!";
    } else {
        $error_message = "Error deactivating account.";
    }
}

// Update the query to fetch all accounts, not just pending ones
$query = "SELECT * FROM `account` WHERE account_id != " . $_SESSION['account_id'];
$accounts = mysqli_query($data->con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts - Admin Dashboard</title>
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
            margin: 85px auto 25px;
            padding: 25px;
            max-width: 1200px;
        }

        .account-table {
            width: 100%;
            border-collapse: collapse;
            background: #132f4c;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(102, 178, 255, 0.1);
        }

        .account-table th, .account-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid rgba(102, 178, 255, 0.1);
        }

        .account-table th {
            background: #1e4976;
            color: #66b2ff;
            font-weight: 500;
        }

        .account-table tr:hover {
            background: #1e4976;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 25px;
            background: #132f4c;
            color: #66b2ff;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: 1px solid rgba(102, 178, 255, 0.1);
        }

        .back-btn:hover {
            background: #1e4976;
            transform: translateY(-2px);
            border-color: #66b2ff;
            box-shadow: 0 4px 15px rgba(102, 178, 255, 0.2);
        }

        .activate-btn, .edit-btn, .deactivate-btn {
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #fff;
            margin-right: 5px;
            border: 1px solid rgba(102, 178, 255, 0.1);
        }

        .activate-btn {
            background: #1e4976;
            color: #66b2ff;
        }

        .activate-btn:hover {
            background: #275d94;
            transform: translateY(-2px);
        }

        .edit-btn {
            background: #132f4c;
            color: #66b2ff;
        }

        .edit-btn:hover {
            background: #1e4976;
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

        /* Update modal styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 25, 41, 0.95);
            z-index: 1100;
            align-items: center;
            justify-content: center;
            display: none;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: linear-gradient(145deg, #132f4c, #1a3b5c);
            padding: 40px;
            border-radius: 16px;
            border: 1px solid rgba(102, 178, 255, 0.15);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 450px;
            position: relative;
        }

        /* Add smooth page transition */
        .content {
            margin: 85px auto 25px;
            padding: 25px;
            max-width: 1200px;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Update navbar animation */
        .navbar {
            background: #132f4c;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
            backdrop-filter: blur(8px);
        }

        .modal-content {
            background: linear-gradient(145deg, #132f4c, #1a3b5c);
            padding: 40px;
            border-radius: 16px;
            border: 1px solid rgba(102, 178, 255, 0.15);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                0 4px 8px rgba(102, 178, 255, 0.1);
            width: 90%;
            max-width: 450px;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .modal h2 {
            color: #66b2ff;
            margin-bottom: 25px;
            font-size: 26px;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .modal label {
            display: block;
            color: #b2bac2;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .modal input {
            width: 100%;
            padding: 14px;
            margin: 8px 0 20px;
            background: rgba(10, 25, 41, 0.5);
            border: 1px solid rgba(102, 178, 255, 0.2);
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .modal input:focus {
            border-color: #66b2ff;
            background: rgba(10, 25, 41, 0.7);
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 178, 255, 0.15);
        }

        .modal .edit-btn {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background: linear-gradient(135deg, #1e88e5, #1976d2);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .modal .edit-btn:hover {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 28px;
            color: #b2bac2;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(10, 25, 41, 0.3);
        }

        .close:hover {
            color: #fff;
            background: rgba(102, 178, 255, 0.2);
            transform: rotate(90deg);
        }
    }

        .modal input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #0a1929;
            border: 1px solid rgba(102, 178, 255, 0.1);
            border-radius: 8px;
            color: #fff;
        }

        .modal input:focus {
            border-color: #66b2ff;
            outline: none;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #132f4c;
            border: 1px solid rgba(102, 178, 255, 0.1);
        }

        .alert-success {
            background: #1e4976;
            color: #66b2ff;
        }

        .alert-danger {
            background: #d32f2f;
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <span>Manage Accounts</span>
    </nav>

    <div class="content">
        <a href="hp.php" class="back-btn">← Back to Dashboard</a>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table class="account-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
          
            <tbody>
                <?php while ($account = mysqli_fetch_assoc($accounts)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($account['fn'] . ' ' . $account['ln']); ?></td>
                        <td><?php echo htmlspecialchars($account['uname']); ?></td>
                        <td><?php echo $account['type_id'] == 2 ? 'Tabulator' : 'Admin'; ?></td>
                        <td>
                            <?php if ($account['status'] == 0): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="account_id" value="<?php echo $account['account_id']; ?>">
                                    <button type="submit" name="activate_account" class="activate-btn">Activate</button>
                                </form>
                            <?php else: ?>
                                <button onclick="openEditModal(<?php echo $account['account_id']; ?>, '<?php echo htmlspecialchars($account['uname']); ?>')" class="edit-btn">Edit</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="account_id" value="<?php echo $account['account_id']; ?>">
                                    <button type="submit" name="deactivate_account" class="deactivate-btn">Deactivate</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add the modal dialog -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Account</h2>
            <form method="POST">
                <input type="hidden" id="edit_account_id" name="account_id">
                <label>Username:</label>
                <input type="text" id="new_username" name="new_username" required>
                <label>New Password (leave blank to keep current):</label>
                <input type="password" name="new_password">
                <button type="submit" name="edit_account" class="edit-btn">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(accountId, username) {
            const modal = document.getElementById('editModal');
            document.getElementById('edit_account_id').value = accountId;
            document.getElementById('new_username').value = username;
            modal.style.display = 'flex';
        }

        function closeEditModal() {
                const modal = document.getElementById('editModal');
            modal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>