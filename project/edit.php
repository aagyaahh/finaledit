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

// Check if the form is submitted for reservation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['updatedDate'];
    $startTime = $_POST['updatedStartTime'];
    $endTime = $_POST['updatedEndTime'];
    $slot = $_POST['updatedSlot'];

    // Check if the selected slot is vacant
    $checkSlotSql = "SELECT * FROM reservations WHERE slot = '$slot' AND date = '$date' AND ((start_time <= '$startTime' AND end_time > '$startTime') OR (start_time < '$endTime' AND end_time >= '$endTime')) AND user_id = '$userId'";
    $checkSlotResult = $conn->query($checkSlotSql);

    if ($checkSlotResult->num_rows == 0) {
        // The slot is vacant, proceed with the reservation update
        $updateId = intval($_GET['id']); // Assign the correct value to $updateId
        $updateSql = "UPDATE reservations SET date='$date', start_time='$startTime', end_time='$endTime', slot='$slot' WHERE id=$updateId AND user_id = '$userId'"; // Include user_id in the update statement

        if ($conn->query($updateSql) === TRUE) {
            // Redirect to display.php after successful reservation update
            header("Location: display.php");
            exit(); // Make sure to stop the script execution after the header is sent
        } else {
            echo "Error updating reservation: " . $conn->error;
        }
    } else {
        // The slot is already reserved by the same user
        echo "Error: You have already reserved this slot for the specified time.";
    }
}

// Retrieve reservation data for editing
$editId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$editSql = "SELECT * FROM reservations WHERE id = $editId AND user_id = '$userId'";
$editResult = $conn->query($editSql);

if ($editResult->num_rows == 1) {
    $editData = $editResult->fetch_assoc();
} else {
    // Redirect if the reservation does not exist or does not belong to the user
    header("Location: display.php");
    exit();
}

// Retrieve all reservations for displaying slots
$sql = "SELECT * FROM reservations";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <div class="center">
        <h2>Edit Reservation</h2>
        <form action="edit.php?id=<?php echo $editId; ?>" method="post">
        <label for ="date-input" >Date:</label>
<input type="date"id="updatedDate" name="updatedDate" min=""/>

<script>
var today= new Date().toISOString().split('T')[0];
document.getElementById("updatedDate").setAttribute("min", today);
</script>

<label for="updatedStartTime">Start Time:</label>
<input type="time" id="updatedStartTime" name="updatedStartTime" value="<?php echo $editData['start_time']; ?>" required>

            <label for="updatedEndTime">End Time:</label>
            <input type="time" id="updatedEndTime" name="updatedEndTime" value="<?php echo $editData['end_time']; ?>" required>

            <input type="hidden" name="updatedSlot" value="<?php echo $editData['slot']; ?>"> <!-- Include hidden input for the slot -->
            

            <label for="updatedSlot">Slot:</label>
            <div class="parking-container">
    <?php
    $parkingSlots = array("1", "2", "3", "4", "5");
    foreach ($parkingSlots as $slotOption) {
        $reservedClass = '';
        foreach ($result as $reservation) {
            if ($reservation['slot'] == $slotOption && $reservation['date'] == $editData['date']) {
                $reservedClass = 'reserved';
                break;
            }
        }

        echo "<div class='parking-slot $reservedClass' data-slot='$slotOption'>$slotOption</div>";
    }
    ?>
</div>

<script>
    // Add JavaScript to handle slot selection
    document.querySelectorAll('.parking-slot').forEach(function (slot) {
        slot.addEventListener('click', function () {
            // Remove the 'reserved' class from all slots
            document.querySelectorAll('.parking-slot').forEach(function (otherSlot) {
                otherSlot.classList.remove('reserved');
            });

            // Add the 'reserved' class to the clicked slot
            slot.classList.add('reserved');

            // Update the hidden input value with the selected slot
            document.querySelector('input[name="updatedSlot"]').value = slot.getAttribute('data-slot');

            // Show the update button when a slot is clicked
            document.querySelector('button[name="updateReservation"]').style.display = 'block';
        });
    });
</script>

<button type="submit" name="updateReservation" style="display: none;">Update</button>
        </form>
    </div>
</body>

</html>


