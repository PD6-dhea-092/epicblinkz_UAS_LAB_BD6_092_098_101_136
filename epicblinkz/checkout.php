<?php

@include 'connect.php';
session_start();
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;


if (isset($_POST['order_btn'])) {
   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $flat = $_POST['flat'];
   $street = $_POST['street'];
   $city = $_POST['city'];
   $province = $_POST['province'];
   $country = $_POST['country'];
   $pin_code = $_POST['pin_code'];


   try {
      $pdo = new PDO("pgsql:host=localhost;dbname=db_epicblinkz", "postgres", "postgres");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
      $user_id = $_SESSION['user_id'];

      $cart_query = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
      $cart_query->execute([$user_id]);

      $price_total = 0;
      $product_name = [];

      if ($cart_query->rowCount() > 0) {
          while ($product_item = $cart_query->fetch(PDO::FETCH_ASSOC)) {
              $product_name[] = $product_item['name'] . ' (' . $product_item['quantity'] . ') ';
              $product_price = number_format($product_item['price'] * $product_item['quantity']);
              $price_total += $product_price;
          }
      }

      $total_product = implode(', ', $product_name);

      $user_id = $_SESSION['user_id'];
      $detail_query = $pdo->prepare("INSERT INTO orders (name, number, email, method, flat, street, city, province, country, pin_code, total_product,  price_total) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $result = $detail_query->execute([$name, $number, $email, $method, $flat, $street, $city, $province, $country, $pin_code, $total_product, $price_total]);

      if ($result) {
          echo "
          <div class='order-message-container'>
          <div class='message-container'>
             <h3>Thank you for shopping!</h3>
             <div class='order-detail'>
                <span>" . $total_product . "</span>
                <span class='total'> Total: $" . $price_total . "/-</span>
             </div>
             <div class='customer-details'>
                <p>Your name: <span>" . $name . "</span></p>
                <p>Your number: <span>" . $number . "</span></p>
                <p>Your email: <span>" . $email . "</span></p>
                <p>Your address: <span>" . $flat . ", " . $street . ", " . $city . ", " . $province . ", " . $country . " - " . $pin_code . "</span></p>
                <p>Your payment mode: <span>" . $method . "</span></p>
                <p>(*Pay when the product arrives*)</p>
             </div>
                <a href='shop.php' class='btn'>Continue Shopping</a>
             </div>
          </div>
          ";
      } else {
          echo "Error inserting order details.";
      }
  } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
  }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>EpicBlinkz | checkout</title>
    <link rel="icon" href="img/favicon.png">

   <script src="https://unpkg.com/feather-icons"></script>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style_order.css">

</head>
<body>

<div class="container">
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-link">
                <a href="home.php" class="navbar-logo"><img src="img/favicon.png" alt="" style="width: 25px" height="25px"> Epic<span>Blinkz</span>.</a>
                </li>
                
                <li class="nav-link">
                <a href="shop.php#shopping-cart"><i data-feather="shopping-cart"></i></a>
                </li>
                <li class="nav-link services">
                    
                    <a><i data-feather="user"></i></a>
                    <?php
                    // Establish a connection to PostgreSQL using PDO
                    try {
                        $pdo = new PDO("pgsql:host=localhost;dbname=db_epicblinkz", "postgres", "postgres");
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    } catch (PDOException $e) {
                        die("Connection failed: " . $e->getMessage());
                    }
                    
                    // Your user selection code
                    $fetch_user = null; // Inisialisasi $fetch_user dengan null
                    
                    if (isset($pdo)) {
                    
                        $select_user = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
                        $select_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $select_user->execute();
                    
                        // Periksa apakah query mengembalikan hasil
                        if ($select_user->rowCount() > 0) {
                            // Fetch the user information
                            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                        }
                    } else {
                        echo "Database connection is not available.";
                    }
                    ?>
                    <ul class="drop-down">
                        <?php if ($fetch_user): ?>
                            <li>Username : <span><?php echo $fetch_user['username']; ?></span></li>
                            <li>Email : <span><?php echo $fetch_user['email']; ?></span> </li>
                            <li><a href="login.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Are you sure you want to logout?');" class="delete-btn-user">Logout</a></li>
                        <?php else: ?>
                            <li>User information not found</li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
</div>   

<div class="container">

<section class="checkout-form">

   <h1 class="heading">complete your order</h1>

   <form action="" method="post">

   <div class="display-order">
    <?php
try {
    $user_id = $_SESSION['user_id'];
    $select_cart = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
    $select_cart->execute([$user_id]);

    $total = 0;
    $grand_total = 0;

    if ($select_cart->rowCount() > 0) {
        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
            $total_price = number_format($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
            echo "<span>{$fetch_cart['name']} ({$fetch_cart['quantity']})</span>";
        }
    } else {
        echo "<div class='display-order'><span>Keranjang Anda kosong!</span></div>";
    }

    echo "<span class='grand-total'> Total Keseluruhan: $" . $grand_total . "/-</span>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

</div>



      <div class="flex">
         <div class="inputBox">
            <span>your name</span>
            <input type="text" placeholder="enter your name" name="name" required>
         </div>
         <div class="inputBox">
            <span>your number</span>
            <input type="number" placeholder="enter your number" name="number" required>
         </div>
         <div class="inputBox">
            <span>your email</span>
            <input type="email" placeholder="enter your email" name="email" required>
         </div>
         <div class="inputBox">
            <span>payment method</span>
            <select name="method">
               <option value="cash on delivery" selected>cash on devlivery</option>
               <option value="credit cart">credit cart</option>
               <option value="paypal">paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>address line 1</span>
            <input type="text" placeholder="e.g. flat no." name="flat" required>
         </div>
         <div class="inputBox">
            <span>address line 2</span>
            <input type="text" placeholder="e.g. street name" name="street" required>
         </div>
         <div class="inputBox">
            <span>city</span>
            <input type="text" placeholder="e.g. medan" name="city" required>
         </div>
         <div class="inputBox">
            <span>Province</span>
            <input type="text" placeholder="e.g. sumatera utara" name="province" required>
         </div>
         <div class="inputBox">
            <span>country</span>
            <input type="text" placeholder="e.g. Indonesia" name="country" required>
         </div>
         <div class="inputBox">
            <span>pin code</span>
            <input type="text" placeholder="e.g. 123456" name="pin_code" required>
         </div>
      </div>
      <input type="submit" value="order now" name="order_btn" class="btn">
   </form>

</section>

</div>


<script>
   feather.replace();
</script>
</body>
</html>