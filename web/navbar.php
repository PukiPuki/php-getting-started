<!-- Navbar -->
<nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper"><a id="logo-container" href="#" class="brand-logo left">Stuffsharing</a>
    <?php
        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
            echo '<ul class="right hide-on-med-and-down"> 
                    <li> <a href="admin.php" >Admin Panel</a></li>
                    </ul>';
        } 
        if (!isset($_SESSION['user'])) {
            echo '<ul class="right hide-on-med-and-down"> 
                    <li> <a href="login.php" >Login</a></li>
                    </ul>
                <ul class="right hide-on-med-and-down"> 
                    <li> <a href="signup.php"> Sign Up </a></li>
                    </ul>';
    } else {
            echo '<ul class="right hide-on-med-and-down"> 
                        <li> <a href="userloanout.php" >My Loans</a></li>
                    </ul>
                    <ul class="right hide-on-med-and-down"> 
                        <li> <a href="bids.php">My Bids</a></li>
                    </ul>
                    <ul class="right hide-on-med-and-down"> 
                        <li> <a href="logout.php">Logout</a></li>
                    </ul>';
    }
    ?>
    </div>
</nav>
