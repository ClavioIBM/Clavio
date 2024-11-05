<?php
    require "amfcConx.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>AMFC Registration Page</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
    <script type="text/javascript">WebFont.load({ google: { families: ["Montserrat:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic", "Varela:400"] } });</script>
    <style>
        body {
            background-image: url('images/veges.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="container d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="card p-4 shadow-lg" style="max-width: 500px; width: 100%;">
            <div class="card-body">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <img src="images/ate maan.jpg" alt="AMFC Logo" style="max-width: 100px;">
                </div>
                <h2 class="card-title text-center mb-4">WELCOME TO ATE MAAN'S FOODCORNER</h2>
                <form id="loginForm" name="loginForm" action="regConx.php" method="POST" class="form">
                    <div class="form-group">
                        <label for="fname">First Name:</label>
                        <input class="form-control" name="fname" placeholder="First Name" type="text" id="fname" required pattern="[a-zA-Z\s.]+" title="Only alphabetic characters, spaces, and dots are allowed" />
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name:</label>
                        <input class="form-control" name="lname" placeholder="Last Name" type="text" id="lname" required pattern="[a-zA-Z\s.]+" title="Only alphabetic characters, spaces, and dots are allowed" />
                    </div>
                    <div class="form-group">
                        <label for="email-2">Email Address:</label>
                        <input class="form-control" name="email" placeholder="Sample@gmail.com" type="email" id="email-2" required />
                    </div>
                    <div class="form-group">
                        <label for="pass-2">Password:</label>
                        <input class="form-control" name="pass" placeholder="*********" type="password" id="pass-2" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long, containing at least one uppercase letter, one lowercase letter, and one number" />
                    </div>
                    <div class="form-group">
                        <label for="address">Delivery Address:</label>
                        <input class="form-control" name="address" placeholder="Delivery Address" type="text" id="address" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input class="form-control" name="phone" placeholder="09xxxxxxxxx" type="text" id="phone" required pattern="09\d{9}" title="Phone number must start with '09' followed by 9 digits" />
                    </div>
                    <button type="submit" id="subm" name="subm" class="btn btn-warning btn-block">REGISTER</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="logPage.php" class="link">Log in</a></p>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        &copy; 2024 Ate Maan Food Corner. All rights reserved.
    </footer>
</body>

</html>
