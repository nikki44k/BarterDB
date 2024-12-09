<?php
session_start();
include 'db.php'; // Ensure db.php defines the connection as $pdo or $conn

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $value = $_POST['value'];
    $quantity = $_POST['quantity'];
    $status = 'available'; // Default status for newly posted items

    // Check for empty fields (basic validation)
    if (empty($name) || empty($description) || empty($quantity) || empty($value)) {
        echo "All fields are required.";
        exit();
    }

    // Use the correct connection variable from db.php
    $sql = "INSERT INTO items (user_id, name, description, quantity, value, status) 
            VALUES (:user_id, :name, :description, :quantity, :value, 'available')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':value', $value);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the user dashboard after successfully posting the item
        header("Location: user_dashboard.php");
        exit();
    } else {
        echo "Error posting item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarterDB Home</title>
    <link rel="stylesheet" href="styles.css"> 
	<meta content="" name="description">
	<meta content="" name="keywords">
	<!-- Favicons -->
	  <link href="http://localhost/barterdb/public/assets/img/dining_sets.jpg" rel="icon">
	  <link href="http://localhost/barterdb/public/assets/img/vintage_sofa.jpg" rel="icon">
	  <link href="http://localhost/barterdb/public/assets/img/vintage_cabinet.webp" rel="icon">

	  <!-- Google Fonts -->
	  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

	  <!-- Vendor CSS Files -->
	  <link href="http://localhost/barterdb/public/assets/vendor/animate.css/animate.min.css" rel="stylesheet">
	  <link href="http://localhost/barterdb/public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	  <link href="http://localhost/barterdb/public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
	  <link href="http://localhost/barterdb/public/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

	  <!-- Template Main CSS File -->
	  <link href="http://localhost/barterdb/public/assets/css/style.css" rel="stylesheet">

</head>


 <!-- ======= Header/Navbar ======= -->
  <nav class="navbar navbar-default navbar-trans navbar-expand-lg fixed-top">
    <div class="container">
      <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDefault" aria-controls="navbarDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <a class="navbar-brand text-brand" href="http://localhost/barterdb">Barter<span class="color-b">DB</span></a>

      <div class="navbar-collapse collapse justify-content-center" id="navbarDefault">
        <ul class="navbar-nav">

          <li class="nav-item">
            <a class="nav-link" href="http://localhost/barterdb/index.html">Home</a>
          </li>

          <li class="nav-item">
            <a class="nav-link " href="http://localhost/barterdb/about.html">About</a>
          </li>

          <li class="nav-item">
            <a class="nav-link " href="http://localhost/barterdb/user_dashboard.php">Dashboard</a>
          </li>

          <li class="nav-item">
            <a class="nav-link " href="http://localhost/barterdb/services.html">Services</a>
          </li>
		  <li class="nav-item">
            <a class="nav-link " href="http://localhost/barterdb/contact.html">Contact Us</a>
          </li>
		     <li class="nav-item">
            <a class="nav-link " href="http://localhost/barterdb/signout.php">Sign Out</a>
          </ul>
      </div>
	</div>
  </nav><!-- End Header/Navbar -->
  
  	<!-- End header -->
	
	<!-- Page Content -->
      <!-- Container-fluid starts -->
      <style type="text/css">
.message-box{
	padding:1px 0px !important;
}
hr.hr1{
	margin: 1px 0px !important;
}
/* ul, li, ol{
	list-style:inherit !important;
} */
</style>

  <main id="main">
    <!-- ======= Intro Single ======= -->
    <section class="intro-single">
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-lg-8">
            <div class="title-single-box">
              <h1 class="title-single"> Post New Item </h1>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Intro Single-->

   <section class="section-about">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 section-t8">
          <div class="row">
            <div class="col-md-07">
              <form action="http://localhost/barterdb/post_item.php" method="post" role="form">
                <input type="hidden" name="_token" value="8XrLjvBnOx6w3OcpNkaEbMbs5amz22vzyXBXXSgq">                                                
				<div class="row">
                    <div class="col-md-07 mb-3">
                    <div class="form-group">
                    <label for="name">Item Name</label>
					<input type="text" id="name" name="name" required>
                    </div>
                  </div>
				  
				  <div class="col-md-07 mb-3">
                    <div class="form-group">
                    <label for="description">Item Description</label>
					<textarea id="description" name="description" required></textarea>
                    </div>
                  </div>
        
              <!-- Add this input field to your form -->
              <div class="col-md-07 mb-3">
                  <div class="form-group">
                  <label for="value">Value (1-15):</label>
                  <input type="number" name="value" id="value" required>
              </div>
            </div>

		    <div class="col-md-07 mb-3">
                <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required>
		        </div>
            </div>

            <div class="col-md-07 text-center">
                <button type="submit" class="btn btn-a"> Post Item </button>
		    </div>
	               </div>
              </form>
			  <p><a href="user_dashboard.php">Go back to Dashboard</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  
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

