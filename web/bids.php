<!DOCTYPE html>
<?php
session_start();
include 'pgconnect.php';
include 'makebids.php';
include 'checklogin.php'
?>
<html>
<title>Stuff Sharing (CS2102 Project)</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Compiled and minified CSS -->
<link rel="stylesheet" href="./style/css/materialize.css">
<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<body>
<div>
    <?php
    include 'navbar.php';
    ?>
</div>

<div>
    <?php
    echo '<div class="container" style="width:100%">';

    if (isset($_SESSION[user])) {
        echo '<h2 class="header">Active transactions</h2>';
        $query = "SELECT * FROM check_bid_status('$_SESSION[user]')";
        $result = pg_query($pg_conn, $query);
        if (!$result) {
            $message = ' <p>You have no active bids!</p> </div> </div> </div>';
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else if (isset($_POST['retract'])) {
            $query = "SELECT * FROM retract_bid('$_SESSION[user]','$_POST[retract]')";
            $result = pg_query($pg_conn, $query);
            if (!$result) {
                $message = 'Error retracting bid';
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
            header('Location:bids.php');
        } else {
            $index = 1;
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
                        <th>My Bid</th>
                        <th>Highest Bid</th>
                        <th>Bid Status</th>
                        <th>Retract Bid</th>
                        </tr>
                        </thead>
                        <tbody>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr>
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
                    <td>' . $row["userbiddingprice"] . '</td>
                    <td>' . $row["maxbiddingprice"] . '</td>
                    <td>' . $row["biddingstatus"] . '</td>';
                    if ($row["biddingstatus"] == "ACCEPTED") {
                        echo '<td>' . noRetractBid() . '</td></tr>';
                    } else {
                        echo '<td>' . retractBid($row["tid"]) . '</td></tr>';
                    }
                $index++;
            }
            echo '</tbody></table></div>';
        }
    }
    ?>
</div>

<div class="container" style="width:100%">
    <?php

    if (isset($_SESSION[user])) {
        echo '<h2 class="header">Successful transactions</h2>';
        $query = "SELECT * FROM all_current_items_borrowed('$_SESSION[user]')";
        $result = pg_query($pg_conn, $query);
        if (!$result) {
            $message = ' <p>You have no successful bids!</p> </div> </div> </div>';
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else {
            $index = 1;
            echo '
                <table class="striped responsive-table centered highlight", style="width:100%">
                        <thead>
                        <tr>
                        <th>S/N</th>
                        <th>Item</th>
                        <th>Owner</th>
                        <th>Phone Number</th>
                        <th>Return Date</th>
                        </tr>
                        </thead>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr align = "center">
                    <td>' . $index . '</td>
                    <td>' . $row["itemname"] . '</td>
					<td>' . $row["owner"] . '</td>
					<td>' . $row["phonenumber"] . '</td>
                    <td>' . $row["returndate"] . '</td>
                    </tr>';
                $index++;
            }
            echo '</table>';
        }
    }
    ?>
</div>

<?php
function retractBid($string)
{
    return '
        <form action="bids.php" method="POST">
            <button class="btn waves-effect waves-light" type="submit" name="retract" value="' . $string . '">Retract</button>
        </form>';
}
function noRetractBid()
{
    return '<button class="btn waves-effect waves-light" type="button" disabled>Retract</button>';
}
?>


</div>

<script>

    // Get the DIV with overlay effect
    var overlayBg = document.getElementById("myOverlay");

</script>

</body>
</html>

