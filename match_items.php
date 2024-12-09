<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the current user ID
$user_id = $_SESSION['user_id'];

// Fetch all items posted by the current user
$stmt = $conn->prepare("SELECT item_id, name FROM items WHERE user_id = :user_id AND status = 'available'");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$my_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all active users (excluding the current user) to select a partner
$stmt = $conn->prepare("SELECT user_id, name FROM users WHERE user_id != :user_id AND status = 'active'");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Check if 'item_a' and 'item_b' are set in POST request
    if (isset($_POST['item_a']) && isset($_POST['item_b'])) {
        $item_a_id = $_POST['item_a'];
        $partner_id = $_POST['partner_id'];
        $item_b_id = $_POST['item_b'];

        // Verify that the partner is active
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = :partner_id AND status = 'active'");
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->execute();
        $partner_valid = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$partner_valid) {
            die('Selected partner is not active or does not exist.');
        }

        // Fetch the partner's item details
        $stmt = $conn->prepare("SELECT name FROM items WHERE item_id = :item_b_id AND user_id = :partner_id");
        $stmt->bindParam(':item_b_id', $item_b_id);
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->execute();
        $partner_item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify that the partner's item exists and is available
        $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = :item_b_id AND user_id = :partner_id AND status = 'available'");
        $stmt->bindParam(':item_b_id', $item_b_id, PDO::PARAM_INT);
        $stmt->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $stmt->execute();
        $item_valid = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item_valid) {
            die('Error: Partner item is not available.');
        }

        // Create a unique hash key for the trade
        $hash_key = substr(md5(uniqid(rand(), true)), 0, 16); // Generate 16-character hash key

        // Fetch values of both items
        $stmt = $conn->prepare("SELECT value FROM items WHERE item_id = :item_id");
        $stmt->bindParam(':item_id', $item_a_id);
        $stmt->execute();
        $item_a_value = $stmt->fetchColumn();

        $stmt->bindParam(':item_id', $item_b_id);
        $stmt->execute();
        $item_b_value = $stmt->fetchColumn();

        // Validate the values
        if ($item_a_value !== $item_b_value) {
          echo "<p style='color: red;'>Error: The values of the items do not match.</p>";
          echo "<a href='user_dashboard.php' style='color: blue; text-decoration: underline;'>Back to Dashboard</a>";
          exit(); // Stop further execution
        }

        // Proceed to insert the transaction only if values match
        $stmt = $conn->prepare("INSERT INTO transactions (item_id, user_id, partner_id, hash_key, status) 
                                VALUES (:item_a_id, :user_id, :partner_id, :hash_key, 'active')");
        $stmt->bindParam(':item_a_id', $item_a_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':partner_id', $partner_id);
        $stmt->bindParam(':hash_key', $hash_key);
        $stmt->execute();

        // Update item statuses only after successful transaction insertion
        $stmt = $conn->prepare("UPDATE items SET status = 'traded' WHERE item_id = :item_id");
        $stmt->bindParam(':item_id', $item_a_id);
        $stmt->execute();

        $stmt->bindParam(':item_id', $item_b_id);
        $stmt->execute();

        // Redirect to the user's dashboard or a confirmation page
        header("Location: user_dashboard.php");
        exit();
    }
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
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
              <h1 class="title-single"> Intiate Trade </h1>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Intro Single-->
	
		<div class="container mt-5">
        <form action="match_items.php" method="POST">
            <div class="form-group">
                <label for="item_a">Select Your Item to trade:</label>
                <select class="form-control" name="item_a" id="item_a" required>
                    <option value="">Select Item</option>
                    <?php foreach ($my_items as $item): ?>
                        <option value="<?= $item['item_id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="partner_id">Select Partner ID:</label>
                <select class="form-control" name="partner_id" id="partner_id" required>
                    <option value="">Select Partner ID</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['user_id']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="item_b">Select Partner's Item:</label>
                <select class="form-control" name="item_b" id="item_b" required>
                    <option value="">Select Item</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Initiate Trade</button>
        </form>
    </div>

    <script>
        // JavaScript to dynamically load partner's items
        document.getElementById('partner_id').addEventListener('change', function() {
        var partner_id = this.value;
        var itemSelect = document.getElementById('item_b');

        // Clear existing options
        itemSelect.innerHTML = '<option value="">-- Select Item --</option>';

        if (partner_id) {
            fetch('get_partner_items.php?partner_id=' + partner_id)
                .then(response => response.json())
                .then(data => {
                    data.forEach(item => {
                        var option = document.createElement('option');
                        option.value = item.item_id;
                        option.textContent = item.name;
                        itemSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching partner items:', error));
        }
    });
   </script>
	
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


