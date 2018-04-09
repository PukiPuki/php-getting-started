<!-- Navbar -->
<nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">Stuffsharing</a>
    <?php
        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
            echo '<ul class="right hide-on-med-and-down"> 
                    <li> <a href="admin.php" >Admin Panel</a></li>
                    /ul>';
        } 
        if (!isset($_SESSION['user'])) {
            echo '<ul class="right hide-on-med-and-down"> 
                    <li> <a href="login.php" >Login</a></li>
                    /ul>
                <ul class="right hide-on-med-and-down"> 
                    <li> <a href="signup.php"> Sign Up </a></li>
                    /ul>';
    } else {
            echo '<ul class="right hide-on-med-and-down"> 
                        <li> <a href="userloanout.php" >My Loans</a></li>
                    /ul>
                    <ul class="right hide-on-med-and-down"> 
                        <li> <a href="bids.php">My Bids</a></li>
                    /ul>
                    <ul class="right hide-on-med-and-down"> 
                        <li> <a href="logout.php">Logout</a></li>
                    /ul>';
    }
    ?>
      <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>

<div class="w3-top">
  <div class="w3-bar w3-theme w3-top w3-left-align w3-large">
    <a href="index.php" class="w3-bar-item w3-button w3-theme-l1">Stuffsharing</a>
    <!-- Right-sided navbar links -->
    <div class="w3-right w3-hide-small">
    <?php
        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
            echo '<a href="admin.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Admin Panel</a>';
        } else {

        }
        if (!isset($_SESSION['user'])) {
            echo '<a href="login.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Login</a>
    <a href="signup.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Sign Up</a>';
    } else {
        echo '<a href="userloanout.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">My Loans</a>';
        echo '<a href="bids.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">My Bids</a>';
        echo '<a href="logout.php" class="w3-bar-item w3-button w3-hide-small w3-hover-white">Logout</a>';
    }
    ?>
    </div>
  </div>
</div>

