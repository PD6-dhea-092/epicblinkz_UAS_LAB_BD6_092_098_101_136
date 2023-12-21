<?php

include 'connect.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_GET['logout'])){
   unset($user_id);
   session_destroy();
   header('location:login.php');
}

if(isset($_POST['add_to_cart'])){
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   // Establish a connection to PostgreSQL using PDO
   $pdo = new PDO("pgsql:host=localhost;dbname=db_epicblinkz", "postgres", "postgres");

   // Prepare and execute the SELECT query to check if the product is already in the cart
   $select_cart = $pdo->prepare("SELECT * FROM cart WHERE name = :product_name AND user_id = :user_id");
   $select_cart->bindParam(':product_name', $product_name, PDO::PARAM_STR);
   $select_cart->bindParam(':user_id', $user_id, PDO::PARAM_INT);
   $select_cart->execute();

   if($select_cart->rowCount() > 0){
      $message[] = 'Product already added to cart!';
   } else {
      // Prepare and execute the INSERT query to add the product to the cart
      $insert_cart = $pdo->prepare("INSERT INTO cart(user_id, name, price, image, quantity) VALUES(:user_id, :product_name, :product_price, :product_image, :product_quantity)");
      $insert_cart->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $insert_cart->bindParam(':product_name', $product_name, PDO::PARAM_STR);
      $insert_cart->bindParam(':product_price', $product_price, PDO::PARAM_STR);
      $insert_cart->bindParam(':product_image', $product_image, PDO::PARAM_STR);
      $insert_cart->bindParam(':product_quantity', $product_quantity, PDO::PARAM_INT);
      $insert_cart->execute();

      $message[] = 'Product added to cart!';
   }
}

$pdo = new PDO("pgsql:host=localhost;dbname=db_epicblinkz", "postgres", "postgres");

// Check if the connection is successful
if (!$pdo) {
    die("Connection failed.");
}

if (isset($_POST['update_cart'])) {
    $update_quantity = $_POST['cart_quantity'];
    $update_id = $_POST['cart_id'];

    // Prepare and execute the UPDATE query to update the cart quantity
    $update_cart = $pdo->prepare("UPDATE cart SET quantity = :update_quantity WHERE id = :update_id");
    $update_cart->bindParam(':update_quantity', $update_quantity, PDO::PARAM_INT);
    $update_cart->bindParam(':update_id', $update_id, PDO::PARAM_INT);
    $update_cart->execute();

    $message[] = 'Cart quantity updated successfully!';
}

if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];

    // Prepare and execute the DELETE query to remove a product from the cart
    $delete_cart = $pdo->prepare("DELETE FROM cart WHERE id = :remove_id");
    $delete_cart->bindParam(':remove_id', $remove_id, PDO::PARAM_INT);
    $delete_cart->execute();

    header('location:shop.php');
}

if (isset($_GET['delete_all'])) {
    // Prepare and execute the DELETE query to remove all products from the cart for a specific user
    $delete_all_cart = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $delete_all_cart->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_all_cart->execute();

    header('location:shop.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>EpicBlinkz | Shopping</title>
    <link rel="icon" href="img/favicon.png">

   <script src="https://unpkg.com/feather-icons"></script>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style_shop.css">

  


</head>
<body>
   
<div class="container">
        <nav class="navbar">
            <ul class="nav-links">
                <li class="nav-link">
                <a href="index.php" class="navbar-logo"><img src="img/favicon.png" alt="" style="width: 20px" height="20px"> Epic<span>Blinkz</span>.</a>
                </li>
                
                <li class="nav-link">
                <a href="#shopping-cart"><i data-feather="shopping-cart"></i></a>
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
                    if (isset($pdo)) {
                        $select_user = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
                        $select_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $select_user->execute();
                        
                        if ($select_user->rowCount() > 0) {
                            // Fetch the user information
                            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
                        }
                    } else {
                        echo "Database connection is not available.";
                    }
                    ?>
                    <ul class="drop-down">
                        <li>Username : <span><?php echo $fetch_user['username']; ?></span></li>
                        <li>Email : <span><?php echo $fetch_user['email']; ?></span> </li>
                        <li><a href="login.php?logout=<?php echo $user_id; ?>" onclick="return confirm('are your sure you want to logout?');" class="delete-btn-user">logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>          
        

<div class="container">



<div class="products">

   <h1 class="heading">latest products</h1>

   <div class="box-container">

  
   <?php
// Establish a connection to PostgreSQL using PDO
$pdo = new PDO("pgsql:host=localhost;dbname=db_epicblinkz", "postgres", "postgres");

// Prepare and execute the SELECT query to retrieve product information
$select_product = $pdo->query("SELECT * FROM products");

if ($select_product->rowCount() > 0) {
    // Fetch all product rows
    $fetch_product = $select_product->fetchAll(PDO::FETCH_ASSOC);

    // Loop through the results
    foreach ($fetch_product as $product) {
        ?>
        <form method="post" class="box" action="">
            <img src="images/<?php echo $product['image']; ?>" alt="">
            <div class="name"><?php echo $product['name']; ?></div>
            <div class="price">$<?php echo $product['price']; ?>/-</div>
            <input type="number" min="1" name="product_quantity" value="1">
            <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
            <input type="submit" value="add to cart" name="add_to_cart" class="btn">
        </form>
        <?php
    }
}
?>

   </div>

</div>

<div class="shopping-cart">

   <h1 class="heading" id="shopping-cart">shopping cart</h1>

   <table>
      <thead>
         <th>image</th>
         <th>name</th>
         <th>price</th>
         <th>quantity</th>
         <th>total price</th>
         <th>action</th>
      </thead>
      <tbody>
      <?php
         $cart_query = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id");
         $cart_query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
         $cart_query->execute();
         
         $grand_total = 0;
         
         if ($cart_query->rowCount() > 0) {
             // Fetch all cart rows
             $fetch_cart = $cart_query->fetchAll(PDO::FETCH_ASSOC);
         
             // Loop through the results
             foreach ($fetch_cart as $cart_item) {
                 ?>
                 <tr>
                     <td><img src="images/<?php echo $cart_item['image']; ?>" height="100" alt=""></td>
                     <td><?php echo $cart_item['name']; ?></td>
                     <td>$<?php echo $cart_item['price']; ?>/-</td>
                     <td>
                         <form action="" method="post">
                             <input type="hidden" name="cart_id" value="<?php echo $cart_item['id']; ?>">
                             <input type="number" min="1" name="cart_quantity" value="<?php echo $cart_item['quantity']; ?>">
                             <input type="submit" name="update_cart" value="update" class="option-btn">
                         </form>
                     </td>
                     <td>$<?php echo $sub_total = ($cart_item['price'] * $cart_item['quantity']); ?>/-</td>
                     <td><a href="shop.php?remove=<?php echo $cart_item['id']; ?>" class="delete-btn" onclick="return confirm('remove item from cart?');">remove</a></td>
                 </tr>
                 <?php
                 $grand_total += $sub_total;
             }
         } else {
             echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">no item added</td></tr>';
         }
      ?>
      <tr class="table-bottom">
         <td colspan="4">grand total :</td>
         <td>$<?php echo $grand_total; ?>/-</td>
         <td><a href="shop.php?delete_all" onclick="return confirm('delete all from cart?');" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">delete all</a></td>
      </tr>
      
   </tbody>
   </table>

   <div class="checkout-btn">
      <a href=checkout.php?user_id=<?php echo $user_id; ?> class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">procced to checkout</a>
   </div>

</div>

</div>
<!-- Feather Icons -->
<script>
      feather.replace();
    </script>
</body>
</html>