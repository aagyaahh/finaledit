<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "speed";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$showRegistrationForm = false;

// Check if the login form is submitted
if (isset($_POST['loginSubmit'])) {
    // Validate and process login
    $loginUsername = $_POST['loginUsername'];
    $loginPassword = $_POST['loginPassword'];

    // Example: Check if username and password match
    $sql = "SELECT * FROM users WHERE username = '$loginUsername' AND password = '$loginPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Successful login, set a flag to show registration form
        // Set user_id in the session
        $row = $result->fetch_assoc();
        session_start();
        $_SESSION['user_id'] = $row['id']; // Assuming 'id' is the unique identifier for the user
        $showRegistrationForm = false;
        // Redirect to reserve.php
        header("Location: reserve.php");
        exit(); // Make sure to stop the script execution after the header is sent
    } else {
        echo "Invalid username or password!";
    }
}

// Check if the registration form is submitted
if (isset($_POST['signupSubmit'])) {
    // Validate and process signup
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $vehicleId = $_POST['vehicleId'];
    $signupUsername = $_POST['signupUsername'];
    $signupPassword = $_POST['signupPassword'];

    // Check if the username and phone combination already exists
    $checkDuplicateSql = "SELECT * FROM users WHERE username = '$signupUsername' OR phone = '$phone'";
    $duplicateResult = $conn->query($checkDuplicateSql);

    if ($duplicateResult->num_rows > 0) {
        echo "Error: Username or phone number already exists.";
    } else {
        // Insert user data into the database
        $sql = "INSERT INTO users (name, phone, vehicleId, username, password) VALUES ('$name', '$phone', '$vehicleId', '$signupUsername', '$signupPassword')";

        if ($conn->query($sql) === TRUE) {
            echo "Signup successful!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speedpark</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>
    <nav id="navbar">
        <div id="logo">
            <img src="logo.png" alt="OnlineParkingSystem.com">
        </div>
        <ul>
            <li class="item"><a href="#home">Home</a></li>
            <li class="item"><a href="#about-us">About Us</a></li>
            <li class="item"><a href="#pricing">Pricing</a></li>
            <li class="item"><a href="#contact-us">Contact Us</a></li>
            <li class="item"><a href="#signup" onclick="openSignupModal()">Sign up</a></li>
            <!-- Sign-up modal -->
            <div id="signupModal" class="modal fullscreen">
                <div class="modal-content">
                    <span class="close" onclick="closeSignupModal()">&times;</span>
                    <h2>Sign Up</h2>
                    <form id="signupForm" method="post">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" pattern="[A-Za-z]+" title="Only letters A-Z and a-z are allowed" required><br>

                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name ="phone" pattern="[0-9]{10}" title="Only 10-digit numbers allowed" required><br>

                        <label for="vehicleId">Vehicle ID:</label>
                        <input type="text" id="vehicleId" name="vehicleId" required><br>

                        <label for="signupUsername">Username:</label>
        <input type="text" id="signupUsername" name="signupUsername" pattern="[A-Za-z]+" title="Only the letter 'Aa-Zz' allowed" required><br>

        <label for="signupPassword">Password:</label>
        <input type="text" id="signupPassword" name="signupPassword" pattern="[A-Za-z]+" title="Only the letter 'Aa-Zz' allowed" required><br>
        <button type="submit" name="signupSubmit">Create Account</button>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    </form>
                </div>
            </div>

        </ul>
    </nav>

    <section id="home">
        <h1 class="h-primary">Welcome to SpeedPark</h1>
        <p>
            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Doloremque aut illum, dolorem, et vitae praesentium natus nemo maiores sint totam omnis molestias accusantium id. Soluta, nostrum amet? Facere, hic veniam.
        </p>
        <p>
            Lorem, ipsum dolor sit amet consectetur adipisicin
        </p>
    </section>

    <!-- login-->
    <div class="about__section">
        <div class="login-box">
            <h2>Login</h2>
            <form id="loginForm" method="post">
        <input type="text" name="loginUsername" placeholder="Username" required>
        <input type="password" name="loginPassword" placeholder="Password" required>
        <button type="submit" name="loginSubmit">Submit</button>
    </form>

            <div class="form" id="reservationForm">
                <h2>Reservation Form</h2>
                <form>
                    <label>Date:</label>
                    <input type="date" required>
                    <label for="startTime">Start Time:</label>
                    <input type="time" id="startTime" oninput="calculatePayment()" required>

                    <label for="startTime">End Time:</label>
                    <input type="time" id="endTime" oninput="calculatePayment()" required>
                    <p id="result"></p>

                    <script>
                        function calculatePayment() {
                            var startTime = document.getElementById('startTime').value;
                            var endTime = document.getElementById('endTime').value;
                            var startMinutes = parseInt(startTime.split(':')[0]) * 60 + parseInt(startTime.split(':')[1]);
                            var endMinutes = parseInt(endTime.split(':')[0]) * 60 + parseInt(endTime.split(':')[1]);

                            var timeGap = endMinutes - startMinutes;
                            var payment = timeGap * 0.167;
                            document.getElementById('result').innerHTML = 'Your Payment will be Rs.' + payment.toFixed(2);
                        }
                    </script>
                    <label class="centered-label">esewa id:**********</label>

                    <div class="dropdown">
                        <button class="dropbtn">Payment Options</button>
                        <div class="dropdown-content">
                            <a href="#">Cash Payment</a>
                            <a href="https://www.esewa.com.np/" target="_blank">eSewa Payment</a>
                        </div>
                    </div>
                    <label class="center">Book your parking slot:</label>
                    <div class="parking-container">
                        <div class="parking-slot vacant">1</div>
                        <div class="parking-slot vacant">2</div>
                        <div class="parking-slot vacant">3</div>
                        <div class="parking-slot vacant">4</div>
                        <div class="parking-slot vacant">5</div>
                        <label></label>

                    </div>
                    <h4>Note:*Red is already reserved</h4>
                    <h4>*Green is for vaccant</h4>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const parkingSlots = document.querySelectorAll('.parking-slot');

                            parkingSlots.forEach(slot => {
                                slot.addEventListener('click', function() {
                                    if (this.classList.contains('reserved')) {
                                        this.classList.remove('reserved');
                                        this.classList.add('vacant');
                                    } else {
                                        this.classList.remove('vacant');
                                        this.classList.add('reserved');
                                    }
                                });
                            });
                        });
                    </script>
                    <button type="button">Reserve</button>
                </form>
            </div>
        </div>


    </div>
    <!-- login-->

    <section id="services-container">
        <section id="about-us">
            <h1 class="h-primary center"> About Us </h1>
        </section>
        <div id="services">
            <div class="box">
                <img src="w3.png" alt="">
                <h2 class="h-secondary center"> Booking</h2>
                <p class="center"> Lorem ipsum dolor, sit amet consectetur adipisicing elit. Omnis, asperiores odio non animi cum natus earum fugit consequuntur excepturi eos. Accusamus magnam quis enim. Odio quos aliquam animi nesciunt reprehenderit.
                </p>
            </div>
            <div class="box">
                <img src="w2.png" alt="">
                <h2 class="h-secondary center"> Timing</h2>
                <p class="center"> Lorem ipsum dolor, sit amet consectetur adipisicing elit. Omnis, asperiores odio non animi cum natus earum fugit consequuntur excepturi eos. Accusamus magnam quis enim. Odio quos aliquam animi nesciunt reprehenderit.
                </p>
            </div>
            <div class="box">
                <img src="w1.png" alt="">
                <h2 class="h-secondary center"> Fare</h2>
                <p class="center"> Lorem ipsum dolor, sit amet consectetur adipisicing elit. Omnis, asperiores odio non animi cum natus earum fugit consequuntur excepturi eos. Accusamus magnam quis enim. Odio quos aliquam animi nesciunt reprehenderit.
                </p>
            </div>
        </div>
    </section>

    <!-- pricing section -->
    <section id="pricing" class="pricing_section layout_padding">
        <div class="container">
            <h1 class="h-primary center">Our Pricing </h1>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="box">
                        <h4 class="price">Rs.10/-</h4>
                        <h5 class="name">Basic</h5>
                        <p>Consequuntur iure, quam vero quidem minima obcaecati veniam, praesentium impedit quod repudiandae tempora amet deserunt rerum accusamus. Commodi qui, illum ad ipsa porro ipsum nostrum magni minus.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="box box-center">
                        <h4 class="price">Rs.20/-</h4>
                        <h5 class="name">Standard</h5>
                        <p>Consequuntur iure, quam vero quidem minima obcaecati veniam, praesentium impedit quod repudiandae tempora amet deserunt rerum accusamus. Commodi qui, illum ad ipsa porro ipsum nostrum magni minus.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="box">
                        <h4 class="price">Rs.30/-</h4>
                        <h5 class="name">Premium</h5>
                        <p>Consequuntur iure, quam vero quidem minima obcaecati veniam, praesentium impedit quod repudiandae tempora amet deserunt rerum accusamus. Commodi qui, illum ad ipsa porro ipsum nostrum magni minus.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end pricing section -->


    <footer id="contact-us">
        <div class="center">
            <h1 class="h-primary">Contact Us</h1>
            <div id="contact-info">
                <p><strong>Location:</strong>Location, City, Country</p>
                <p><strong>Phone:</strong> 9812345678</p>
                <p><strong>Email:</strong> speedpark@example.com</p>
            </div>
            <div id="social-media">
                <a href="#" target="_blank"><img src="facebook-icon.png" alt="Facebook"></a>
                <a href="#" target="_blank"><img src="twitter-icon.png" alt="Twitter"></a>
                <a href="#" target="_blank"><img src="instagram-icon.png" alt="Instagram"></a>

            </div>
        </div>
    </footer>
    <script src="script.js"></script>

    <script>
        //  links of js for scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>
