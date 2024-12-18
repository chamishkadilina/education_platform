<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
session_start();
// If user is already logged in, redirect to home
if(isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

// Include database connection
include 'db.php';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $confirm_password = $_POST['c_pass'];

    // Validate input
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Handle file upload
            $profile_pic = null;
            if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                $target_dir = "uploads/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                    $profile_pic = $target_file;
                }
            }

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_pic) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $profile_pic);

            if ($stmt->execute()) {
                // Redirect to login
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed: " . $stmt->error;
            }
        }
    }
}
?>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">Education.</a>
   </section>
</header>   

<section class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <?php if(isset($error)): ?>
         <p style="color: red; text-align: center;"><?php echo $error; ?></p>
      <?php endif; ?>
      <p>your name <span>*</span></p>
      <input type="text" name="name" placeholder="enter your name" required maxlength="50" class="box">
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" required maxlength="50" class="box">
      <p>your password <span>*</span></p>
      <input type="password" name="pass" placeholder="enter your password" required maxlength="20" class="box">
      <p>confirm password <span>*</span></p>
      <input type="password" name="c_pass" placeholder="confirm your password" required maxlength="20" class="box">
      <p>select profile picture <span>*</span></p>
      <input type="file" name="profile_pic" accept="image/*" required class="box">
      <input type="submit" value="register" name="submit" class="btn">
      <p>Already have an account? <a href="login.php">Login here</a></p>
   </form>
</section>

<script src="js/script.js"></script>
</body>
</html>