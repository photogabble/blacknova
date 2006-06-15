<?php
function shipyard_die($error_msg)
{
    global $l_footer_until_update;
    global $l_footer_players_on_1;
    global $l_footer_players_on_2;
    global $l_footer_one_player_on;
    global $sched_ticks;

    echo "<p>$error_msg<p>";
    global $l_global_mmenu;

    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
?>
