<!DOCTYPE html>
<?php
session_start();
include 'pgconnect.php';
include 'refresh.php';
include 'checkadmin.php';
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Compiled and minified CSS -->
    <link type="text/css" rel="stylesheet" href="./style/css/materialize.min.css" media="screen,projection">
    <link href="https://fonts.googleapis.com/icon?family=Inconsolata" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>
<?php
include 'navbar.php';

?>

<div class="container">
    <h1 class="header"> User Control </h1>

        <ul class="collapsible">
            <li>
                <div class="collapsible-header"><i class="material-icons">add</i>Add User</div>
                <div class="collapsible-body">
                <form action="admin_users.php" class="col s12" method="POST">
                <div class="row input-field col s6">
                    <input type="text" id="username" name="username" required>
                    <label for="username"> Username</label>
                </div>
                <div class="row input-field col s6">
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="row input-field col s6">
                    <input type="text" id="phone" name="phone" required>
                    <label for="phone">Phone Number</label>
                </div>
                <div class="row">
                    <input type="hidden" name="isAdmin" value="0"> </input>
                    <label for="isAdmin">
                    <input type="checkbox" id="isAdmin" name="isAdmin" value="1" />
                    <span> Admin Privileges</span>
                    </label>
                </div>
                <div class="row input-field">
                    <button class="right btn waves-effect wave-light" type="submit" name="add_user">Add User</button>
                </div>
            </form>
            </div>
            </li>
            <li>
                <div class="collapsible-header"><i class="material-icons">edit</i>Edit User</div>
                <div class="collapsible-body">
                    <form action="admin_users.php" method="POST">
                        <div class="row input-field col s6">
                            <input type="text" id="username"  name="username" required>
                            <label for="username">Username</label>
                        </div>
                        <div class="row input-field col s6">
                            <input type="text" id="newphone" name="newphone" required>
                            <label for="newphone">New Phone Number</label>
                        </div>
                        <div class="row input-field col s6">
                              <input type="hidden" name="isAdmin" value="0"> </input>
                              <label for="isAdmin_edit">
                              <input type="checkbox" id="isAdmin_edit" name="isAdmin" value="1" />
                              <span> Admin Privileges</span>
                              </label>
                          </div>
                        <div class="row input-field col s6">
                            <button class="right btn waves-effect wave-light" type="submit" name="edit_user">Edit User</button>
                        </div>
                   </form>
                </div>
            </li>
            <li>
                <div class="collapsible-header"><i class="material-icons">delete</i>Remove User</div>
                <div class="collapsible-body">
                   <form action="admin_users.php" method="POST">
                        <div class="row input-field col s6">
                           <input type="text" id="username"  name="username" required>
                           <label for="username"><b>Username</b></label>
                        </div>
                        <div class="row input-field col s6">
                           <button class="right btn waves-effect wave-light" type="submit" name="remove_user">Remove User</button>
                       </div>
                   </form>
                </div>
            </li>
        </ul>
</div>
    <!-- For bids-->

<div class="container">
<h1 class="header"> Bids </h1>
<ul class="collapsible">
    <li>
        <div class="collapsible-header"><i class="material-icons">arrow_drop_down</i>Show Bids</div>
        <div class="collapsible-body">
<?php
$query = "SELECT * FROM admin_select_bids()";
$result = pg_query($pg_conn, $query);
$index = 1;
echo '
                <table class="striped responsive-table centered highlight", style="width:100%">
                        <thead>
                        <tr>
                        <th>S/N</th>
                        <th>tID</th>
                        <th>Bidder Name</th>
                        <th>Bid Price</th>
                        <th>Bidding status</th>
                        <th>Action</th>
                        <th>Action</th>
                        </tr>
                        </thead>';
while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
    echo '<tr align = "center">
        <form action="admin_bids.php" method="POST">
        <input type="hidden" name="biddername" value="' . $row["biddername"] . '"/>
        <input type="hidden" name="tid" value="' . $row["tid"] . '"/>
        <td>' . $index . '</td>
        <td>' . $row["tid"] . '</td>
        <td>' . $row["biddername"] . '</td>
        <td><input type="text" name="bidprice" value="' . $row["biddingprice"] . '"/></td>
        <td><input type="text" name="bidstatus" value="' . $row["biddingstatus"] . '"/></td>
        <td><button class="btn waves-effect wave-light" type="submit" name="edit_bid" >Edit</button></td>
        <td><button class="btn waves-effect wave-light" type="submit" name="delete_bid" >Delete</button></td>
        </form>
        </tr>';
$index++;
}
echo '<tr align = "center">
    <form action="admin_bids.php" method="POST">
    <input type="hidden" name="biddername" value="' . $row["biddername"] . '"/>
    <td>' . $index . '</td>
    <td><input type="text" name="tid" placeholder="tID"/></td>
    <td><input type="text" name="biddername" placeholder="Name"/></td>
    <td><input type="text" name="bidprice" placeholder="0.0"/></td>
    <td><input type="text" name="bidstatus" placeholder="STATUS"/></td>
    <td><button class="btn waves-effect wave-light" type="submit" name="add_bid" >Add</button></td>
    </form>
    </tr>
    </table>';
$index++;

?>
</div>
</div>
    <!-- For transactions-->

<div class="container">
<h1 class="header"> Transactions </h1>
<ul class="collapsible">
    <li>
        <div class="collapsible-header"><i class="material-icons">arrow_drop_down</i>Show Transactions</div>
        <div class="collapsible-body">
<?php
    $query = "SELECT * FROM admin_select_transaction()";
    $result = pg_query($pg_conn, $query);
    $index = 1;
    echo '
                    <table class="striped responsive-table centered highlight", style="width:100%">
                            <thead>
                            <tr>
                            <th>S/N</th>
                            <th>tID</th>
                            <th>Location</th>
                            <th>Pickup Date</th>
                            <th>Return Date</th>
                            <th>Item ID</th>
                            <th>Action</th>
                            <th>Action</th>
                            </tr>
                            </thead>';
    while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
        echo '<tr align = "center">
            <form action="admin_transactions.php" method="POST">
            <td>' . $index . '</td>
            <td><input type=text name=tid value="' . $row["tid"] . '"/></td>
            <td><input type="text" name="location" value="' . $row["location"] . '"/></td>
            <td><input type="text" name="pickupdate" value="' . $row["pickupdate"] . '"/></td>
            <td><input type="text" name="returndate" value="' . $row["returndate"] . '"/></td>
            <td><input type="text" name="itemid" value="' . $row["itemid"] . '"/></td>
            <td><button class="btn waves-effect wave-light" type="submit" name="edit_transaction" >Edit</button></td>
            <td><button class="btn waves-effect wave-light" type="submit" name="delete_transaction" >Delete</button></td>
            </form>
            </tr>';
    $index++;
    }
    echo '<tr align = "center">
        <form action="admin_transactions.php" method="POST">
        <input type="hidden" name="biddername" value="' . $row["biddername"] . '"/>
        <td>' . $index . '</td>
        <td></td>
        <td><input type="text" name="location" placeholder="Location"/></td>
        <td><input type="text" name="pickupdate" placeholder="YYYY-MM-DD" /></td>
        <td><input type="text" name="returndate" placeholder="YYYY-MM-DD" /></td>
        <td><input type="text" name="itemid" placeholder="Item ID"/></td>
        <td><button class="btn waves-effect wave-light" type="submit" name="add_transaction" >Add</button></td>
        </form>
        </tr>
        </table>';
    $index++;
    ?>
</div>
</div>

    <!-- For items-->
<div class="container">
<h1 class="header">Items</h1>
<ul class="collapsible">
    <li>
        <div class="collapsible-header"><i class="material-icons">arrow_drop_down</i>Show Items</div>
        <div class="collapsible-body">
<?php
    $query = "SELECT * FROM admin_select_items()";
    $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
    $index = 1;
    echo '
                    <table class="striped responsive-table centered highlight", style="width:100%">
                            <thead>
                            <tr>
                            <th>S/N</th>
                            <th>Item ID</th>
                            <th>Owner</th>
                            <th>Category</th>
                            <th>Item Name</th>
                            <th>Minimum Bid </th>
                            <th>Auto Buy </th>
                            <th>Action</th>
                            <th>Action</th>
                            </tr>
                            </thead>';
    while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
        echo '<tr align = "center">
            <form action="admin_items.php" method="POST">
            <td>' . $index . '</td>
            <td><input type=text name=itemid value="' . $row["itemid"] . '"/></td>
            <td><input type="text" name="owner" value="' . $row["owner"] . '"/></td>
            <td><input type="text" name="category" value="' . $row["category"] . '"/></td>
            <td><input type="text" name="itemname" value="' . $row["itemname"] . '"/></td>
            <td><input type="text" name="minbid" value="' . $row["minbid"] . '"/></td>
            <td><input type="text" name="autobuy" value="' . $row["autobuy"] . '"/></td>
            <td><button class="btn waves-effect wave-light" type="submit" name="edit_item" >Edit</button></td>
            <td><button class="btn waves-effect wave-light" type="submit" name="delete_item" >Delete</button></td>
            </form>
            </tr>
    ';
    $index++;
    }
    echo '<tr align = "center">
        <form action="admin_items.php" method="POST">
        <input type="hidden" name="biddername" value="' . $row["biddername"] . '"/>
        <td>' . $index . '</td>
        <td><input type="text" name="itemid" placeholder="Item ID"/></td>
        <td><input type="text" name="owner" placeholder="Owner"/></td>
        <td><input type="text" name="category" placeholder="Category" /></td>
        <td><input type="text" name="itemname" placeholder="Item Name" /></td>
        <td><input type="text" name="minbid" placeholder="0.0" /></td>
        <td><input type="text" name="autobuy" placeholder="0.0" /></td>
        <td><button class="btn waves-effect wave-light" type="submit" name="add_item" >Add</button></td>
        </form>
        </tr>
        </table>
        ';
    $index++;
?>
    </div>
    </div>
</div>

<!--JavaScript at end of body for optimized loading-->
      <script type = "text/javascript" src = "https://code.jquery.com/jquery-2.1.1.min.js"></script>           
      <script type="text/javascript" src="./style/js/materialize.min.js"></script>
    <script>$('.collapsible').collapsible();</script>

</body>
</html>
