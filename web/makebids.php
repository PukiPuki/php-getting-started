<?php
function makeBidInput($string) {
    return
        '
            <form action="index.php" method="POST">
                 <input class="col s6" type="text" placeholder="' . $string . '" name="new_bid" required>
                 <button class="btn waves-effect wave-light" type="submit" name="tid" value="' . $string . '">Bid</button>
            </form>
        ';
}
?>
