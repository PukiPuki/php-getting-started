<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="./style/css/materialize.css"
<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

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
