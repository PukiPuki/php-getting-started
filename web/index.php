<!DOCTYPE html>
<?php
session_start();
include 'pgconnect.php';
include 'makebids.php';
?>
<html>
<title>Stuff Sharing (CS2102 Project)</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="./style/css/materialize.css"
<style>
    html, body, h1, h2, h3, h4, h5, h6 {
        font-family: "Roboto", sans-serif;
    }
</style>

<body>

<div>
    <?php
    include 'navbar.php';
    ?>
</div>

<div class="container" style="width:100%">
    <?php
    echo " <div style=margin-top:43px>
    <div class=\"w3-container\">
    </div>";

    if (isset($_SESSION[user])) {
        echo '<div>
            <div class="w3-container">
            <h1 class=\"w3-text-teal\">Welcome {$_SESSION[user]}</h1>
            <h2 class="w3-text-teal">Active transactions</h2>
            </div>';
        $query = "SELECT * FROM select_active_transactions()";
        $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
        if (!$result) {
            $message = ' <p>There are no transactions in the database!</p> </div> </div> </div>';
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else if (isset($_POST['filter'])) {
            $query = "SELECT * FROM filter_transactions('$_POST[category]')";
            $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
            if (!$result) {
                $message = ' <p>There are no transactions of that category</p> </div> </div> </div>';
                echo "<script type='text/javascript'>alert('$message');</script>";

            } else {
                $index = 1;
                echo '<div> 
            <form action="index.php" method="POST">
            <label for"filter"><b> Filter: </b></label>
            <input type="text" placeholder="Category" name=
            "category" required> 
            <button type="submit" name="filter">Filter</button>
            </form>';
                echo '
            <table style = "width:100%", align="center">
                    <thead>
                    <tr>
                    <th>S/N</th>
                    <th>tID</th>
                    <th>Item</th>
                    <th>Location</th>
                    <th>Loan Date</th>
                    <th>Return Date</th>
                    <th>Owner</th>
                    <th>Category</th>
                    <th>Minimum Bid</th>
                    <th>Automatic Bid</th>
                    <th>Current Bid</th>
                    <th>Make Bid</th>
                    </tr>
                    </thead>';
                while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                    echo '<tr align = "center">
                <td>' . $index . '</td>
                <td>' . $row["tid"] . '</td>
                <td>' . $row["itemname"] . '</td>
                <td>' . $row["location"] . '</td>
                <td>' . $row["pickupdate"] . '</td>
                <td>' . $row["returndate"] . '</td>
                <td>' . $row["owner"] . '</td>
                <td>' . $row["category"] . '</td>
                <td>' . $row["minbid"] . '</td>
                <td>' . $row["autobuy"] . '</td>
                <td>' . $row["highbid"] . '</td>
                <td>' . makeBidInput($row["tid"]) . '</td>
                </tr>';
                    $index++;
                }
                echo '</table>';
            }
        } else {
            $index = 1;
            echo '<div> 
        <form action="index.php" method="POST">
        <label for"filter"><b> Filter: </b></label>
        <input type="text" placeholder="Category" name=
        "category" required> 
        <button type="submit" name="filter">Filter</button>
        </form>
        </div>';
            echo '
                <table class="striped responsive-table centered highlight", style="width:100%">
                        <thead>
                        <tr>
                        <th>S/N</th>
                        <th>tID</th>
                        <th>Item</th>
                        <th>Location</th>
                        <th>Loan Date</th>
                        <th>Return Date</th>
                        <th>Owner</th>
                        <th>Category</th>
                        <th>Minimum Bid</th>
                        <th>Automatic Bid</th>
                        <th>Current Bid</th>
                        <th>Make Bid</th>
                        </tr>
                        </thead>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr align = "center">
                <td>' . $index . '</td>
                <td>' . $row["tid"] . '</td>
                <td>' . $row["itemname"] . '</td>
                <td>' . $row["location"] . '</td>
                <td>' . $row["pickupdate"] . '</td>
                <td>' . $row["returndate"] . '</td>
                <td>' . $row["owner"] . '</td>
                <td>' . $row["category"] . '</td>
                <td>' . $row["minbid"] . '</td>
                <td>' . $row["autobuy"] . '</td>
                <td>' . $row["highbid"] . '</td>
                <td>' . makeBidInput($row["tid"]) . '</td>
                </tr>';
                $index++;
            }
            echo '</table>';
        }
    } else {
        echo homeScreen();
    }
    ?>
</div>

<?php
if (isset($_POST['new_bid'])) {
    $query = "SELECT * FROM make_bid('$_POST[new_bid]', '$_POST[tid]', '$_SESSION[user]')";
    $result = pg_query($pg_conn, $query);
    if (!$result) {
        $message = ' <p>There are no transactions of that category</p> </div> </div> </div>';
        echo "<script type='text/javascript'>alert('$_POST[tid]');</script>";
        echo "<script type='text/javascript'>alert('$_POST[new_bid]');</script>";
        echo "<script type='text/javascript'>alert('$_SESSION[user]');</script>";
    }
}

function homeScreen () {
    return <<< END
     <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <br><br>
      <h1 class="header center orange-text">Starter Template</h1>
      <div class="row center">
        <h5 class="header col s12 light">Your next loan is just a click away</h5>
      </div>
      <div class="row center">
        <a href="https://radiant-forest-81050.herokuapp.com/signup.php" id="signup" class="btn-large waves-effect waves-light orange">Get Started/a>
        <a href="https://radiant-forest-81050.herokuapp.com/login.php" id="signup" class="btn-large waves-effect waves-light orange">Login</a>
      </div>
      <br><br>

    </div>
    </div>
    <div class="container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">flash_on</i></h2>
            <h5 class="center">Share stuff quickly</h5>

            <p class="light"> Our app allows you to quickly browse through available items, and easily share some of your own with no additional fees</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">group</i></h2>
            <h5 class="center">User Experience Focused</h5>

            <p class="light"> We value your experience and privacy, and therefore the only personal information you will have to provide is your phone number</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">settings</i></h2>
            <h5 class="center">Easy to work with</h5>

            <p class="light">Our consistent layout enables a smooth user experience, where users are able to add and find items quickly in real time with ease</p>
          </div>
        </div>
      </div>

    </div>
    <br><br>
  </div>
    ;
END;
}
?>

</div>


<script>
    // Get the DIV with overlay effect
    var overlayBg = document.getElementById("myOverlay");
</script>

 <footer class="page-footer orange">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Bio</h5>
          <p class="grey-text text-lighten-4">We are a group of NUS students, who did this app as part of a module project</p>


        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Connect</h5>
          <ul>
            <li><a class="white-text" href="https://github.com/pukipuki/php-getting-started">Github</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Credit to <a class="orange-text text-lighten-3" href="http://materializecss.com">Materialize</a> and <a class="orange-text text-lighten-3" href="https://www.w3schools.com/w3css/tryw3css_templates_webpage.htm">W3Schools</a>
      </div>
    </div>
  </footer>

</body>
</html>

