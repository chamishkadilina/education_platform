<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "No user found!";
    }
}
?>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">Education.</a>
   </section>
</header>   

<section class="form-container">
   <form action="" method="post">
      <h3>login now</h3>
      <?php if(isset($error)): ?>
         <p style="color: red; text-align: center;"><?php echo $error; ?></p>
      <?php endif; ?>
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" required maxlength="50" class="box">
      <p>your password <span>*</span></p>
      <input type="password" name="pass" placeholder="enter your password" required maxlength="20" class="box">
      <input type="submit" value="login" name="submit" class="btn">
      <p>Don't have an account? <a href="register.php">Register here</a></p>
   </form>
</section>

<script src="js/script.js"></script>
</body>
</html>