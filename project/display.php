<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Connect to your MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "speed";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Retrieve reservations for the currently logged-in user
$sql = "SELECT * FROM reservations WHERE user_id = '$userId'";
$result = $conn->query($sql);

// Check if the user wants to delete a reservation
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);

    // Delete reservation from the database
    $deleteSql = "DELETE FROM reservations WHERE id = $deleteId";
    
    if ($conn->query($deleteSql) === TRUE) {
        echo "Reservation deleted successfully!";
    } else {
        echo "Error deleting reservation: " . $conn->error;
    }
}
if (isset($_POST['logout'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Display</title>
    <link href="style.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 71px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="center">
        <h2>Reservation Information</h2>

        <?php
        // Display reservation information in a table
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>Date</th>";
            echo "<th>Start Time</th>";
            echo "<th>End Time</th>";
            echo "<th>Slot</th>";
            echo "<th>Action</th>";
            echo "</tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['start_time'] . "</td>";
                echo "<td>" . $row['end_time'] . "</td>";
                echo "<td>" . $row['slot'] . "</td>";
                echo "<td><a href='edit.php?id=" . $row['id'] . "'>Edit</a> | <a href='display.php?delete=" . $row['id'] . "'>Delete</a></td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "No reservations found.";
        }

        ?>
          <!-- Logout form -->
          <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</body>

</html>
