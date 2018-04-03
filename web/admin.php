<!DOCTYPE html>
<?php
session_start();
include 'pgconnect.php';
include 'refresh.php';
?>

<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        form {
            border: 3px solid #f1f1f1;
        }

        /* Full-width inputs */
        input[type=text], input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Set a style for all buttons */
        button {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        /* Add a hover effect for buttons */
        button:hover {
            opacity: 0.8;
        }

        /* Extra style for the cancel button (red) */
        .cancelbtn {
            width: auto;
            padding: 10px 18px;
            background-color: #f44336;
        }

        /* Center the avatar image inside this container */
        .imgcontainer {
            text-align: center;
            margin: 24px 0 12px 0;
        }

        /* Avatar image */
        img.avatar {
            width: 40%;
            border-radius: 50%;
        }

        /* Add padding to containers */
        .container {
            padding: 16px;
        }

        /* The "Forgot password" text */
        span.psw {
            float: right;
            padding-top: 16px;
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.psw {
                display: block;
                float: none;
            }

            .cancelbtn {
                width: 100%;
            }
        }

    </style>
</head>
<body>
<?php
include 'navbar.php';

if (!$_SESSION[isAdmin]) {
    header('Location:index.php');
}

?>

<div style="margin-top:43px">
    <div>
        <h1 class="w3-text-teal"> User Control </h1>

        <div class="container">
            <h2 class="w3-text-teal"> Add User </h2>
            <form action="admin_users.php" method="POST">
                <div class="container">
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <label for="password"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <label for="phone"><b>Phone Number</b></label>
                    <input type="text" placeholder="Enter Phone Number" name="phone" required>
                    <label for="isAdmin"><b>Admin Privileges</b></label>
                    <input type="hidden" name="isAdmin" value="0"> </input>
                    <input type="checkbox" name="isAdmin" value="1"> Admin </input>
                    <button type="submit" name="add_user">Add User</button>
                </div>
            </form>
        </div>

        <form action="admin_users.php" method="POST">
            <div class="container">
                <h2 class="w3-text-teal"> Edit User </h2>
                <label for="username"><b>Username</b></label>
                <input type="text" placeholder="Enter User to Edit" name="username" required>

                <label for="newphone"><b>New Phone Number</b></label>
                <input type="text" placeholder="Enter New Phone Number" name="newphone" required>

                <label for="isAdmin"><b>Admin Privileges</b></label>
                <input type="hidden" name="isAdmin" value="0"> </input>
                <input type="checkbox" name="isAdmin" value="1"> Admin </input>
                <button type="submit" name="edit_user">Edit User</button>
            </div>
        </form>

        <div class="container">
            <h2 class="w3-text-teal"> Remove User </h2>
            <form action="admin_users.php" method="POST">
                <div class="container">
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter User to Remove" name="username" required>
                    <button type="submit" name="remove_user">Remove User</button>
                </div>
            </form>
        </div>
    </div>
<!-- For bids--> 
     
    <div class="container">
            <?php 
                $query = "SELECT * FROM admin_select_bids()";
               $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                $index = 1;
                echo '
                <h1 class="w3-text-teal"> Bids </h1>
                <table class="striped responsive-table centered highlight", style="width:100%">
                        <tr>
                        <th>S/N</th>
                        <th>tID</th>
                        <th>Bidder Name</th>
                        <th>Bid Price</th>
                        <th>Bidding status</th>
                        <th>Action</th>
                        <th>Action</th>
                        </tr>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr align = "center">
                    <form action="admin_bids.php" method="POST">
                    <input type="hidden" name="biddername" value="'.$row["biddername"].'"/>
                    <td>'.$index.'</td>
                    <td>'.$row["tid"].'</td>
                    <td>'.$row["biddername"].'</td>
                    <td><input type="text" name="bidprice" value="'.$row["biddingprice"].'"/></td>
                    <td><input type="text" name="bidstatus" value="'.$row["biddingstatus"].'"/></td>
                    <td><button type="submit" name="edit_bid" >Edit</button></td>
                    <td><button type="submit" name="delete_bid" >Delete</button></td>
                    </form>
                    </tr>';
                    $index++;
            }
                echo '<tr align = "center">
                    <form action="admin_bids.php" method="POST">
                    <input type="hidden" name="biddername" value="'.$row["biddername"].'"/>
                    <td>'.$index.'</td>
                    <td><input type="text" name="tid" placeholder="tID"/></td>
                    <td><input type="text" name="biddername" placeholder="Name"/></td>
                    <td><input type="text" name="bidprice" placeholder="0.0"/></td>
                    <td><input type="text" name="bidstatus" placeholder="STATUS"/></td>
                    <td><button type="submit" name="add_bid" >Add</button></td>
                    </form>
                    </tr>';
                    $index++;

?>

    </div>
<!-- For transactions--> 
        <div class="container">
    
            <?php 
                $query = "SELECT * FROM admin_select_transaction()";
               $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                $index = 1;
                echo '
                <h1 class="w3-text-teal"> Transactions </h1>
                <table class="striped responsive-table centered highlight", style="width:100%">
                        <tr>
                        <th>S/N</th>
                        <th>tID</th>
                        <th>Location</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Item ID</th>
                        <th>Action</th>
                        <th>Action</th>
                        </tr>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr align = "center">
                    <form action="admin_transactions.php" method="POST">
                    <td>'.$index.'</td>
                    <td><input type=text name=tid value="'.$row["tid"].'"/></td>
                    <td><input type="text" name="location" value="'.$row["location"].'"/></td>
                    <td><input type="text" name="pickupdate" value="'.$row["pickupdate"].'"/></td>
                    <td><input type="text" name="returndate" value="'.$row["returndate"].'"/></td>
                    <td><input type="text" name="itemid" value="'.$row["itemid"].'"/></td>
                    <td><button type="submit" name="edit_transaction" >Edit</button></td>
                    <td><button type="submit" name="delete_transaction" >Delete</button></td>
                    </form>
                    </tr>';
                    $index++;
            }
                echo '<tr align = "center">
                    <form action="admin_transactions.php" method="POST">
                    <input type="hidden" name="biddername" value="'.$row["biddername"].'"/>
                    <td>'.$index.'</td>
                    <td></td>
                    <td><input type="text" name="location" placeholder="Location"/></td>
                    <td><input type="text" name="pickupdate" placeholder="YYYY-MM-DD" /></td>
                    <td><input type="text" name="returndate" placeholder="YYYY-MM-DD" /></td>
                    <td><input type="text" name="itemid" placeholder="Item ID"/></td>
                    <td><button type="submit" name="add_transaction" >Add</button></td>
                    </form>
                    </tr>';
                    $index++;
?>
            </div>
            
<!-- For itemss--> 
        <div class="container">
    
            <?php 
                $query = "SELECT * FROM admin_select_items()";
               $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
                $index = 1;
                echo '
                <h1 class="w3-text-teal"> Items </h1>
                <table class="striped responsive-table centered highlight", style="width:100%">
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
                        </tr>';
            while ($row = pg_fetch_assoc($result)) {   //Creates a loop to loop through results
                echo '<tr align = "center">
                    <form action="admin_items.php" method="POST">
                    <td>'.$index.'</td>
                    <td><input type=text name=itemid value="'.$row["itemid"].'"/></td>
                    <td><input type="text" name="owner" value="'.$row["owner"].'"/></td>
                    <td><input type="text" name="category" value="'.$row["category"].'"/></td>
                    <td><input type="text" name="itemname" value="'.$row["itemname"].'"/></td>
                    <td><input type="text" name="minbid" value="'.$row["minbid"].'"/></td>
                    <td><input type="text" name="autobuy" value="'.$row["autobuy"].'"/></td>
                    <td><button type="submit" name="edit_item" >Edit</button></td>
                    <td><button type="submit" name="delete_item" >Delete</button></td>
                    </form>
                    </tr>';
                    $index++;
            }
                echo '<tr align = "center">
                    <form action="admin_items.php" method="POST">
                    <input type="hidden" name="biddername" value="'.$row["biddername"].'"/>
                    <td>'.$index.'</td>
                    <td></td>
                    <td><input type="text" name="itemid" placeholder="Item ID"/></td>
                    <td><input type="text" name="owner" placeholder="Owner"/></td>
                    <td><input type="text" name="category" placeholder="Category" /></td>
                    <td><input type="text" name="itemname" placeholder="Item Name" /></td>
                    <td><input type="text" name="minbid" placeholder="0.0" /></td>
                    <td><input type="text" name="autobuy" placeholder="0.0" /></td>
                    <td><button type="submit" name="add_item" >Add</button></td>
                    </form>
                    </tr>';
                    $index++;
?>
            </div>
</div>
</body>
</html>
