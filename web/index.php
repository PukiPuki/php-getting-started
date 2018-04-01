<!DOCTYPE html>
<?php
session_start();
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
<?php
$DEBUG = false;
?>


<body>

<div>
    <?php
    include 'navbar.php';
    ?>
</div>

<div>
    <?php

    function sex()
    {
        echo '<h1>sex</h1>';
    }

    if ($DEBUG) {
        echo '<h1>sex</h1><h1>asd</h1>' . sex();
    }
    ?>
</div>

<div class="container" style="width:100%">
    <?php
    function pg_connection_string_from_database_url()
    {
        extract(parse_url($_ENV["DATABASE_URL"]));
        return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
    }

    $pg_conn = pg_connect(pg_connection_string_from_database_url())
    or die('Could not connect:' . pg_last_error());
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
                    </tr>';
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
                        </tr>';
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
    }
    ?>
</div>

<?php
#function pg_connection_string_from_database_url() {
#    extract(parse_url($_ENV["DATABASE_URL"]));
#    return "user=$user password=$pass host=$host dbname=" . substr($path, 1) . " sslmode=require"; # <- you may want to add sslmode=require there too
#}

#$pg_conn = pg_connect(pg_connection_string_from_database_url())
#or die('Could not connect:' . pg_last_error());

if (isset($_POST['bid'])) {
    echo "<script type='text/javascript'>alert('$_POST[bid]');</script>";
    echo "<script type='text/javascript'>alert('$_POST[bid]');</script>";
    $query = "SELECT * FROM filter_transactions('$_POST[category]')";
    $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
    if (!$result) {
        $message = ' <p>There are no transactions of that category</p> </div> </div> </div>';
        echo "<script type='text/javascript'>alert('$_POST[bid]');</script>";
        echo "<script type='text/javascript'>alert('$_POST[bid]');</script>";
    }
}

function makeBidInput($string) {
    return
        '
            <form action="index.php" method="POST">
                 <input type="text" placeholder="' . $string . '" name="bid" required>
                 <button type="submit" name="bid" value="' . $string . '">Bid</button>
            </form>
        ';
}

?>
</div>


<script>
    // Get the DIV with overlay effect
    var overlayBg = document.getElementById("myOverlay");
</script>

</body>
</html>

