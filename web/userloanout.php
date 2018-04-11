<!DOCTYPE html>
<?php
session_start();
include "pgconnect.php";
include 'refresh.php';
include 'checklogin.php';
$username = $_SESSION[user];
?>
<html>
<title>Stuff Sharing (CS2102 Project)</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="./style/css/materialize.css">
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

<div class="container">
    <h1 class="w3-text-teal">Your Loan</h1>

    <?php
    $query = "SELECT * FROM all_current_loans_accepted('$username')";
    $result = pg_query($pg_conn, $query);

    echo '
    <div>
    <h2 class="w3-text-teal">Successful Loans</h2>
    </div>';

    if (!$result) {
        $message = '<p>You have nothing loaned out!</p>';
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        $index = 1;
        echo '
            <table class="striped responsive-table centered highlight", style="width:100%">
            <thead>
            <tr>
            <th>S/N</th>
            <th>Item</th>
            <th>Bidder</th>
            <th>Bidder Phone Number</th>
            <th>Location</th>
            <th>Pickup Date</th>
            <th>Return Date</th>
            <th>Action</th>
            </tr>
            </thead>';
        while ($row = pg_fetch_assoc($result)) {
            echo '<tr align = "center">
            <form name="display" action="userloanout.php" method="POST">
            <td>' . $index . '</td>
            <input type="hidden" name="itemid" value="' . $row["itemid"] . '" />
            <td>' . $row["itemname"] . '</td>
            <td>' . $row["biddername"] . '</td>
            <td>' . $row["phonenumber"] . '</td>
            <td><input type="text" name="location" value="' . $row["location"] . '" /></td>
            <td><input type="text" name="pickupdate" value="' . $row["pickupdate"] . '" /></td>
            <td><input type="text" name="returndate" value="' . $row["returndate"] . '" /></td>
            <td><button class="btn waves-effect wave-light" type="submit" name="reloan">Loan Again</button> </td>
            </form>
            </tr>';
            $index++;
        }
        echo '</table>';
    }

    echo '<div>
    <h2 class="w3-text-teal">Pending Loans</h2>
    </div>';

    $query = "SELECT * FROM all_current_loans_pending('$username')";
    $result = pg_query($pg_conn, $query);

    if (!$result) {
        $message = '<p>You have no pending loans!</p>';
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        $index = 1;
        echo '
            <table class="striped responsive-table centered highlight", style="width:120%">
            <thead>
            <tr>
            <th>S/N</th>
            <th>ItemID</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Min Bid</th>
            <th>Automatic Bid</th>
            <th>Location</th>
            <th>Loan Date</th>
            <th>Return Date</th>
            <th>Bidder Name</th>
            <th>Current Bid</th>
            <th>Action</th>
            <th>Action</th>
            <th>Action</th>
            <th>Action</th>
            </tr>
            </thead>';
        while ($row = pg_fetch_assoc($result)) {
            echo '<tr align = "center">
                <form name="display" action="userloanout.php" method="POST">
                <input type="hidden" name="newtransactionid" value="' . $row["transactionid"] . '">
                <td>' . $index . '</td>
                <td>' . $row["itemid"] . '</td>
                <td><input type="text" name = "newitemname" value="' . $row["itemname"] . '"/></td>
                <td><input type="text" name = "newcategory" value="' . $row["category"] . '"/></td>
                <td><input type="text" name = "newminbid" value="' . $row["minbid"] . '"/></td>
                <td><input type="text" name = "newautobuy" value="' . $row["autobuy"] . '"/></td>
                <td><input type="text" name = "newlocation" value="' . $row["location"] . '"/></td>
                <td><input type="text" name = "newpickupdate" value="' . $row["pickupdate"] . '"/></td>
                <td><input type="text" name = "newreturndate" value="' . $row["returndate"] . '"/></td>
                <td>' . $row["biddername"] . '</td>
                <td>' . $row["maxbid"] . '</td>
                <td><button class="btn waves-effect wave-light" type="submit" name="edititem" value="' . $row["itemid"] . '">Update</button></td>';

            if ($row[maxbid]) {
                echo '<td><button class="btn waves-effect wave-light" type="submit" name="biddername" value="' . $row["biddername"] . '">Accept</button></td>';
            } else {
                echo '<td><button class="btn waves-effect wave-light" type="button" disabled>Accept</button></td>';
            }
            if ($row[maxbid]) {
                echo ' <td><button class="btn waves-effect wave-light" type="submit" name="rejectbid" value="' . $row["biddername"] . '">Reject</button></td>';
            } else {
                echo '<td><button class="btn waves-effect wave-light" type="button" disabled>Reject</button></td>';
            }
            echo '<td><button class="btn waves-effect wave-light" type="submit" name="deleteitem" value="' . $row["itemid"] . '">Delete Item</button></td>
                </form>
                </tr>';
            $index++;
        }
        echo addBidUI($index);
        echo '</table>';
    }

    if (isset($_POST['edititem'])) {
        $query = "SELECT * FROM edit_transactions('$_POST[newtransactionid]', '$_POST[newlocation]','$_POST[newpickupdate]', '$_POST[newreturndate]')";
        $result = pg_query($pg_conn, $query);

        $query = "SELECT * FROM edit_items('$_POST[edititem]', '$_POST[newcategory]', '$_POST[newitemname]', '$_POST[newminbid]', '$_POST[newautobuy]')";
        $result = pg_query($pg_conn, $query);
        if ($result) {
            echo "<script type='text/javascript'>
                alert('Item edited!');
            </script>";
            refresh();
        } else {
            echo "<script type='text/javascript'>
                alert('Unable to edit item');
            </script>";
        }
    }

    if (isset($_POST['biddername'])) {
        $query = 'SELECT * FROM accept_loan(' . $_POST['newtransactionid'] . ',\'' . $_POST['biddername'] . '\')';
        $result = pg_send_query($pg_conn, $query);
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in accepting loan!');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Loan status modified!');
            </script>";
            refresh();
        }
    }

    if (isset($_POST['rejectbid'])) {
        $query = "SELECT * FROM reject_loan('$_POST[newtransactionid]', '$_POST[rejectbid]')";
        $result = pg_query($pg_conn, $query);
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in rejecting loan!');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Loan status modified!');
            </script>";
            refresh();
        }
    }

    if (isset($_POST['deleteitem'])) {
        $query = "SELECT * FROM delete_item('$_POST[deleteitem]')";
        $result = pg_query($pg_conn, $query);
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in deleting item!');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Item deleted!');
            </script>";
            refresh();
        }
    }

    if (isset($_POST['additem'])) {
        $query = "SELECT * FROM add_item_for_bidding('$username', '$_POST[newcategory]', '$_POST[newitemname]', '$_POST[newminbid]', '$_POST[newautobuy]', '$_POST[newlocation]', '$_POST[newpickupdate]', '$_POST[newreturndate]')";
        $result = pg_query($pg_conn, $query);
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in adding item!');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Item added!');
            </script>";
            refresh();
        }

    }

    if (isset($_POST['reloan'])) {
        $query = "SELECT * FROM add_transaction_on_existing_item('$_POST[location]', '$_POST[pickupdate]', '$_POST[returndate]', '$_POST[itemid]')";
        $result = pg_query($pg_conn, $query);
        if (!$result) {
            echo "<script type='text/javascript'>
                alert('Error in reloaning item!');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Item added for reloaning!');
            </script>";
            refresh();
        }

    }

    ?>

</div>
<?php
function addBidUI($index)
{
    return <<<END
        <tr align = "center">
            <form name="display" action="userloanout.php" method="POST">
                <input type="hidden" name="newtransactionid" value="$row[transactionid]">
                <td>$index</td>
                <td>$row[itemid]</td>
                <td><input type="text" name = "newitemname" placeholder="Item Name"</td>
                <td><input type="text" name = "newcategory" placeholder="Category"/></td>
                <td><input type="text" name = "newminbid" placeholder="0.0"/></td>
                <td><input type="text" name = "newautobuy" placeholder="$0.0"/></td>
                <td><input type="text" name = "newlocation" placeholder="Location"/></td>
                <td><input type="text" name = "newpickupdate" placeholder="YYYY-MM-DD"/></td>
                <td><input type="text" name = "newreturndate" placeholder="YYYY-MM-DD"/></td>
                <td></td>
                <td></td>
                <td><button class="btn waves-effect wave-light" type="submit" name="additem">Add</button></td>
            </form>
        </tr>
END;

}

?>


<script>

    // Get the DIV with overlay effect
    var overlayBg = document.getElementById("myOverlay");

</script>
</body>
</html>

