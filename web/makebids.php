<?php
session_start();
include 'pgconnect.php';

function makeBidInput($string) {

if (isset($_POST['new_bid'])) {
#echo "<script type='text/javascript'>alert('$_POST[tid]');</script>";
#echo "<script type='text/javascript'>alert('$_POST[new_bid]');</script>";
#echo "<script type='text/javascript'>alert('$_SESSION[user]');</script>";

    $query = "SELECT * FROM make_bid('$_POST[new_bid]', '$_POST[tid]', '$_SESSION[user]')";
    $result = pg_query($pg_conn, $query) or die('Query failed: ' . pg_last_error());
    if (!$result) {
        $message = ' <p>There are no transactions of that category</p> </div> </div> </div>';
        echo "<script type='text/javascript'>alert('$_POST[tid]');</script>";
        echo "<script type='text/javascript'>alert('$_POST[new_bid]');</script>";
        echo "<script type='text/javascript'>alert('$_SESSION[user]');</script>";
    }
}

    return
        '
            <form action="makebids.php" method="POST">
                 <input type="text" placeholder="' . $string . '" name="new_bid" required>
                 <button type="submit" name="tid" value="' . $string . '">Bid</button>
            </form>
        ';
}

?>
