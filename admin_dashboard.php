<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");  // Redirect to login if not admin
    exit();
}

// Securely fetch all users
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Securely fetch all transactions
$stmt = $conn->prepare("SELECT transactions.*, items.name AS item_name, users.name AS user_name, partner.name AS partner_name
                        FROM transactions
                        JOIN items ON transactions.item_id = items.item_id
                        JOIN users AS partner ON transactions.partner_id = partner.user_id
                        JOIN users ON transactions.user_id = users.user_id");
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
              <h1 class="title-single"> Admin Dashboard </h1>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Intro Single-->

<!-- Users Management -->
<h2>Users</h2>
<table border="1">
    <tr>
		<th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
			<td><?php echo htmlspecialchars($user['user_id']); ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['phone']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td><?= htmlspecialchars($user['status']) ?></td>
            <td>
                <!-- Action buttons: suspend or delete users -->
                <?php if ($user['status'] === 'active'): ?>
                    <form action="admin_actions.php" method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <button type="submit" name="action" value="suspend">Suspend</button>
                    </form>
                <?php else: ?>
                    <form action="admin_actions.php" method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <button type="submit" name="action" value="activate">Activate</button>
                    </form>
                <?php endif; ?>
                <form action="admin_actions.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                    <button type="submit" name="action" value="delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Transactions Management -->
<h2>Transactions</h2>
<table border="1">
    <tr>
        <th>Item Name</th>
        <th>User</th>
        <th>Partner</th>
        <th>Hash Key</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?= htmlspecialchars($transaction['item_name']) ?></td>
            <td><?= htmlspecialchars($transaction['user_name']) ?></td>
            <td><?= htmlspecialchars($transaction['partner_name']) ?></td>
            <td><?= htmlspecialchars($transaction['hash_key']) ?></td>
            <td><?= htmlspecialchars($transaction['status']) ?></td>
            <td>
                <!-- Action buttons: change transaction status -->
                <?php if ($transaction['status'] === 'active'): ?>
                    <form action="admin_actions.php" method="POST">
                        <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                        <button type="submit" name="action" value="complete">Mark as Completed</button>
                    </form>
                    <form action="admin_actions.php" method="POST">
                        <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                        <button type="submit" name="action" value="cancel">Cancel Transaction</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
	
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
