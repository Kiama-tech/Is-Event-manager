<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
include 'connect.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['admission_number'])) {
    header("Location: Login.html");
    exit();
}

// Open database connection
$conn = OpenCon();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch user details from the database using admission number
$admission_number = $_SESSION['admission_number']; // Use admission number to fetch details
$sql = "SELECT id, username, email, user_type FROM user WHERE admission_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admission_number); // Bind admission_number to query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    // Store user details in session
    $_SESSION['user_id'] = $user['id']; // Store user ID in session for future use
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_type'] = $user['user_type'];
} else {
    die("Error: User not found.");
}

// Close the connection
CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(135deg, var(--primary), #1a252f);
            color: white;
            padding: 1rem;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo h1 {
            background: linear-gradient(45deg, var(--secondary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 2.2rem;
            letter-spacing: 1px;
        }

        .top-nav {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem;
            position: fixed;
            top: 80px;
            width: 100%;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            z-index: 999;
        }

        .top-nav ul {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            list-style: none;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        .top-nav a {
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s;
            position: relative;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .top-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: width 0.3s;
        }

        .top-nav a:hover::after {
            width: 100%;
        }

        .profile-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            overflow: hidden;
            margin: 180px auto 20px auto;
            padding: 20px;
        }

        .profile-header {
            background: #007BFF;
            color: white;
            padding: 1rem;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .profile-header h2 {
            margin: 0;
        }

        .profile-section {
            padding: 2rem;
        }

        .profile-pic-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }

        #profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ccc;
        }

        .add-photo-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .add-photo-btn:hover {
            background-color: #0056b3;
        }

        .personal-info {
            margin-top: 2rem;
        }

        .personal-info h3 {
            margin-bottom: 1rem;
        }

        .edit-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .info-table th, .info-table td {
            padding: 0.75rem;
            border: 1px solid #ddd;
            text-align: left;
        }

        .info-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .info-table input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            pointer-events: none; /* Make input fields non-editable */
        }

        /* Hide the file input */
        #file-input {
            display: none;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>School Event Manager</h1>
        </div>
    </header>
    <nav class="top-nav">
        <ul>
            <li><a href="Home.html">Home</a></li>
            <li><a href="signup.html">Sign Up</a></li>
            <li><a href="Login.html">Login</a></li>
            <li><a href="Dashboard.html">Dashboard</a></li>
            <li><a href="calendar.html">Calendar</a></li>
            <li><a href="Account.html">Account</a></li>
        </ul>
    </nav>
    <div class="profile-container">
        <div class="profile-header">
            <h2>My Profile</h2>
        </div>
        <div class="profile-section">
            <div class="profile-pic-container">
                <img id="profile-pic" src="Default pic.jpg" alt="Profile Picture">
                <button id="add-photo" class="add-photo-btn">Add Profile Photo</button>
                <input type="file" id="file-input" accept="image/*">
            </div>
            <div class="personal-info">
                <h3>Personal Information</h3>
                <button class="edit-btn">Edit</button>
                <table class="info-table">
                    <tr>
                        <th>Full Name</th>
                        <td><input type="text" id="full-name" value="<?php echo $_SESSION['username']; ?>"></td>
                    </tr>
                    <tr>
                        <th>Admission</th>
                        <td><input type="text" id="admission" value="<?php echo $_SESSION['admission_number']; ?>"></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><input type="text" id="role" value="<?php echo $_SESSION['user_type']; ?>"></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- Logout Button -->
        <form action="logout.php" method="POST">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>

    <script>
        document.getElementById('add-photo').addEventListener('click', function() {
            document.getElementById('file-input').click();
        });

        document.getElementById('file-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-pic').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>