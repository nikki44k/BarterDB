<?php
session_start();
include 'db.php'; // Include database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = isset($_POST['role']) ? 'admin' : 'user'; // Default to user

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $query = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)");
    $query->bindParam(':name', $name);
    $query->bindParam(':email', $email);
    $query->bindParam(':password_hash', $password_hash);
    $query->bindParam(':role', $role);

    if ($query->execute()) {
        // Registration successful
        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['role'] = $role;
        header("Location: admin_dashboard.php"); // Redirect to admin dashboard if admin
    } else {
        echo "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Registration</h1>
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Register as Admin?</label>
        <input type="checkbox" id="role" name="role"> <!-- Checkbox to register as admin -->

        <button type="submit">Register</button>
    </form>
    <a href="signin.html">Already have an account? Sign In</a>
	
  </main><!-- End #main -->
  <!-- ======= Footer ======= -->
  <section class="section-footer">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12">
          <div class="widget-a">
            <div class="w-header-a">
              <h3 class="w-title-a text-brand">BarterDB</h3>
            </div>
            <div class="w-body-a">
              <p class="w-text-a color-text-a">
              Trade 
              </p>
            </div>
            <div class="w-footer-a">
              <ul class="list-unstyled">
                <li class="color-a">
                  <span class="color-text-a">Phone .</span> +1(986)-229-6200
                </li>
                <li class="color-a">
                  <span class="color-text-a">Email .</span> nikhithakilari@gmail.com
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <nav class="nav-footer">
            <ul class="list-inline">
              <li class="list-inline-item">
                <a href="http://localhost/barterdb/index.html">Home</a>
              </li>
              <li class="list-inline-item">
                <a href="http://localhost/barterdb/about.html">About</a>
              </li>
              <li class="list-inline-item">
                <a href="http://localhost/barterdb/services.html">Services</a>
              </li>
              <li class="list-inline-item">
                <a href="http://localhost/barterdb/signin.html">Dashboard</a>
              </li>
              <li class="list-inline-item">
                <a href="http://localhost/barterdb/contact.html">Contact</a>
              </li>
            </ul>
          </nav>
          <div class="socials-a">
            <ul class="list-inline">
              <li class="list-inline-item">
                <a href="#">
                  <i class="bi bi-facebook" aria-hidden="true"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="bi bi-twitter" aria-hidden="true"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="bi bi-instagram" aria-hidden="true"></i>
                </a>
              </li>
              <li class="list-inline-item">
                <a href="#">
                  <i class="bi bi-linkedin" aria-hidden="true"></i>
                </a>
              </li>
            </ul>
          </div>
          <div class="copyright-footer">
            <p class="copyright color-text-a">
              &copy; Copyright
              <span class="color-a">BarterDB</span> All Rights Reserved.
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer><!-- End  Footer -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="http://localhost/barterdb/public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="http://localhost/barterdb/public/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="http://localhost/barterdb/public/assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="http://localhost/barterdb/public/assets/js/main.js"></script>

</body>
</html>

</body>
</html>
