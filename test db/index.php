<?php
session_start();
include "db.php"; // ensure this has your DB connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #fce4ec;
      color: #333;
    }

    .header {
      background-color: #fff;
      padding: 1rem 2rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header a {
      text-decoration: none;
      color: #c2185b;
      font-size: 1.5rem;
      font-weight: bold;
    }

    .header nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 1.5rem;
    }

    .header nav a {
      font-size: 1rem;
      color: #f06292;
      transition: color 0.3s;
    }

    .header nav a:hover {
      color: #c2185b;
    }

    .main {
      padding: 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 2rem;
    }

    .product {
      background-color: #fff;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .product:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .image-wrapper {
      position: relative;
      margin-bottom: 1rem;
      display: inline-block;
      line-height: 0;
    }

    .product img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 4px;
    }

    .stock-badge {
      position: absolute;
      bottom: 0;
      right: 0;
      padding: 8px 10px;
      border-radius: 5px 0 8px 0;
      font-size: 10px;
      font-weight: bold;
      color: white;
      text-transform: uppercase;
      z-index: 10;
    }

    .stock-low { background-color: #ffc107; }
    .stock-few { background-color: #fd7e14; }
    .stock-zero { background-color: #dc3545; }

    /* Limit product name to 3 lines */
    .product h2 {
      font-size: 1.25rem;
      margin: 0 0 0.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      transition: all 0.3s ease;
    }

    /* Expand full text when toggled */
    .product h2.expanded {
      -webkit-line-clamp: unset;
      overflow: visible;
    }

    .description {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: 5px 0;
      transition: all 0.3s ease;
    }

    .description.expanded {
      -webkit-line-clamp: unset;
      overflow: visible;
    }
.show-more {
  color: #e1628c;
  cursor: pointer;
  display: inline-block;
  font-weight: 500;
  font-size:15px;
  text-decoration: none; /* removed underline */
  transition: all 0.3s ease;
}

.show-more:hover {
  
  color: #c6285dff;
}

/* Buy Now button style */
.product a {
  display: inline-block;
  background-color: #e1628c;
  color: #fff;
  padding: 10px 20px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.product a:hover {
  background-color: #c2185b;
  transform: scale(1.05);
}

.product a[style*="cursor: not-allowed"] {
  background-color: #6c757d !important;
  color: #fff !important;
  transform: none !important;
}

    .footer {
      background-color: #f1a8c0;
      color: #fff;
      text-align: center;
      padding: 1rem;
      margin-top: 2rem;
    }

    #whatsappBtn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #25d366;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    #whatsappBtn img {
      width: 35px;
      height: 35px;
    }

    #whatsappBtn:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 15px rgba(0,0,0,0.4);
    }
  </style>
</head>
<body>

  <header class="header">
    <a href="index.php">Shop</a>
    <nav>
      <ul>
        <?php if (!isset($_SESSION['user_id'])) { ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Sign Up</a></li>
        <?php } else { ?>
          <?php if ($_SESSION['user_role'] === 'admin') { ?>
            <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
          <?php } else { ?>
            <li><a href="dashboard.php">User Dashboard</a></li>
          <?php } ?>
        <?php } ?>
      </ul>
    </nav>
  </header>

  <main class="main">
    <?php
    $sql = "SELECT * FROM products";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $image = !empty($row['image']) ? "image/" . $row['image'] : "default.png";
        $stock = (int)$row['stock'];

        $stock_text = '';
        $stock_class = '';

        if ($stock == 0) {
          $stock_text = 'Out of Stock';
          $stock_class = 'stock-zero';
        } elseif ($stock <= 5 && $stock > 0) {
          $stock_text = $stock . ' Only!';
          $stock_class = ($stock == 5) ? 'stock-few' : 'stock-low';
        }
    ?>
        <div class="product">
          <div class="image-wrapper">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="Product: <?php echo htmlspecialchars($row['name']); ?>">
            <?php if (!empty($stock_text)) { ?>
              <span class="stock-badge <?php echo $stock_class; ?>">
                <?php echo $stock_text; ?>
              </span>
            <?php } ?>
          </div>

          <h2><?php echo htmlspecialchars($row['name']); ?></h2>
          <span class="show-more" onclick="toggleText(this, 'h2')">Show more</span>

          <p class="description"><?php echo htmlspecialchars($row['description']); ?></p>
          <span class="show-more" onclick="toggleText(this, 'p')">Show more</span>

          <p>Price: ₹<?php echo number_format($row['price']); ?></p>

          <?php if (isset($_SESSION['user_id']) && $stock > 0) { ?>
            <a href="singleorder.php?user_id=<?php echo urlencode($_SESSION['user_id']); ?>&product_id=<?php echo urlencode($row['id']); ?>&product_price=<?php echo urlencode($row['price']); ?>">Buy Now</a>
          <?php } elseif ($stock <= 0) { ?>
            <a href="#" style="background-color: #6c757d; cursor: not-allowed;" onclick="return false;">Out of Stock</a>
          <?php } else { ?>
            <a href="login.php">Buy Now</a>
          <?php } ?>
        </div>
    <?php
      }
    } else {
      echo "<p>No products available</p>";
    }
    ?>
  </main>

  <footer class="footer">
    <a href="https://wa.me/919363587844" target="_blank" id="whatsappBtn" title="Chat with us on WhatsApp">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>
    <p>&copy; 2025 Softlogic</p>
  </footer>

  <script>
    // Common toggle function for both name & description
    function toggleText(btn, tagType) {
      const element = btn.previousElementSibling; // previous h2 or p
      element.classList.toggle('expanded');
      btn.textContent = element.classList.contains('expanded') ? 'Show less' : 'Show more';
    }
  </script>
</body>
</html>
