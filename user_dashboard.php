<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: signin.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch items posted by the user
$query = $conn->prepare("SELECT * FROM items WHERE user_id = :user_id AND status = 'available'");
$query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$query->execute();
$posted_items = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch active transactions for the logged-in user
$sql = "SELECT transactions.*, items.name as item_name, items.value 
        FROM transactions 
        JOIN items ON transactions.item_id = items.item_id
        WHERE (transactions.user_id = :user_id OR transactions.partner_id = :user_id) AND transactions.status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$active_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all available items (excluding user's own items)
$sql = "SELECT items.*, users.user_id AS partner_id 
        FROM items 
        JOIN users ON items.user_id = users.user_id 
        WHERE items.status = 'available' AND items.user_id != :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch completed transactions for the logged-in user
$sql = "SELECT transactions.*, items.name as item_name FROM transactions 
        JOIN items ON transactions.item_id = items.item_id
        WHERE (transactions.user_id = :user_id OR transactions.partner_id = :user_id) AND transactions.status = 'completed'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$completed_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
              <h1 class="title-single"> User Dashboard </h1>
            </div>
          </div>
        </div>
      </div>
    </section><!-- End Intro Single-->

    <h2>Your Posted Items</h2>
    <?php if ($posted_items): ?>
        <table>
            <tr>
                <th>Item Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Value</th>
                <th>Action</th>
            </tr>
            <?php foreach ($posted_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($item['value']); ?></td>
                    <td>
                        <form action="match_items.php" method="post">
                            <input type="hidden" name="item_id1" value="<?= $item['item_id'] ?>">
                            <button type="submit">Initiate Trade</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No posted items found.</p>
    <?php endif; ?>

    <!-- All Available Items -->
    <h2>All Available Items</h2>
    <table>
        <tr>
            <th>Item Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Value</th>
            <th>Partner ID</th>
        </tr>
        <?php foreach ($all_items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']); ?></td>
                <td><?= htmlspecialchars($item['description']); ?></td>
                <td><?= htmlspecialchars($item['quantity']); ?></td>
                <td><?= htmlspecialchars($item['value']); ?></td>
                <td><?= htmlspecialchars($item['partner_id']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Your Active Transactions</h2>
    <?php if ($active_items): ?>
    <table>
        <tr>
            <th>Trade with</th>
            <th>Status</th>
            <th>Partner ID</th>
            <th>Item Value</th>
            <th>Hash Key</th>
            <th>Action</th>
        </tr>
        <?php foreach ($active_items as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['item_name']); ?></td>
                <td>Active</td>
                <td><?php echo htmlspecialchars($transaction['partner_id']); ?></td>
                <td><?php echo htmlspecialchars($transaction['value']); ?></td>
                <td><?php echo htmlspecialchars($transaction['hash_key']); ?></td>
                <td>
                    <?php if ($transaction['partner_id'] == $user_id): // Show buttons for partner ?>
                        <form action="handle_transaction.php" method="post" style="display: inline;">
                            <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="btn btn-success">Accept</button>
                        </form>
                        <form action="handle_transaction.php" method="post" style="display: inline;">
                            <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                            <input type="hidden" name="action" value="decline">
                            <button type="submit" class="btn btn-danger">Decline</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No active transactions.</p>
<?php endif; ?>


    <h2>Your Completed Transactions</h2>
    <?php if ($completed_items): ?>
        <table>
            <tr>
                <th>Trade with</th>
                <th>Status</th>
                <th>Partner ID</th>
                <th>Hash Key</th>
            </tr>
            <?php foreach ($completed_items as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['item_name']); ?></td>
                    <td>Completed</td>
                    <td><?php echo htmlspecialchars($transaction['partner_id']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['hash_key']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No completed transactions.</p>
    <?php endif; ?> 

    <h2>Post a New Item</h2>
    <p><a href="post_item.php">Click this link to post a new item</a></p>
	
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
