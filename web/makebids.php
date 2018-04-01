<?php
function makeBidInput($string)
{
    return
        '
            <form action="index.php" method="POST">
                     <input type="text" placeholder="' . $string . '" name="bid" required>
                     <button type="submit" name="bid" value="' . $string . '">Bid</button>
                </form>
            ';
}

?>
