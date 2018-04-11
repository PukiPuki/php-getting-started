<?php
function makeBidInput($string) {
    return
        '
            <form action="index.php" method="POST" class="col s6">
                <div class="input-field inline">
                 <input class="col s6" type="text" placeholder="' . $string . '" name="new_bid" required>
                </div>
                 <button class="btn waves-effect wave-light" type="submit" name="tid" value="' . $string . '">Bid</button>
            </form>
        ';
}
?>
