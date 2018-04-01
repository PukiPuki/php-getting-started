<?php
function makeBidInput($string)
{
    return
        '
            <form action="index.php" method="POST">
                 <input type="text" placeholder="' . $string . '" name="new_bid" required>
                 <button type="submit" name="tid" value="' . $string . '">Bid</button>
            </form>
        ';
}

?>
