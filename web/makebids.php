<?php
function makeBidInput($string, $tid) {
    return
        '
            <form action="index.php" method="POST" class="col s6">
                <input type="hidden" value="'. $tid .'" />
                <div class="input-field inline">
                 <input class="col s6" type="text" value="' . $string . '" name="new_bid" required>
                </div>
                 <button class="btn waves-effect wave-light" type="submit" name="make_bid" >Bid</button>
            </form>
        ';
}
?>
