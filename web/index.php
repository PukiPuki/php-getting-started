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
    <h1 class=\"w3-text-teal\">Welcome {$_SESSION[user]}</h1>
    </div>";

    if (isset($_SESSION[user])) {
        echo '<div>
            <div class="w3-container">
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
        <h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
      </div>
      <div class="row center">
        <a href="http://materializecss.com/getting-started.html" id="download-button" class="btn-large waves-effect waves-light orange">Get Started</a>
      </div>
      <br><br>

    </div>
    </div>;
END;
}
?>

</div>


<script>
    // Get the DIV with overlay effect
    var overlayBg = document.getElementById("myOverlay");
</script>

</body>
</html>

