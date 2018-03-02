<!-- Navbar -->
<div class="w3-top">
  <div class="w3-bar w3-theme w3-top w3-left-align w3-large">
    <a href="index.php" class="w3-bar-item w3-button w3-theme-l1">Stuffsharing</a>
    <!-- Right-sided navbar links -->
    <div class="w3-right w3-hide-small">
    <?php
        if (!isset($_SESSION['user'])) {
            echo '<a href="login.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Login</a>
    <a href="signup.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Sign Up</a>';
    } else {

    }
    ?>
    <a href="search.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Search</a>
    </div>
  </div>
</div>

