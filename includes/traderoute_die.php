<?php
function traderoute_die($error_msg)
{
    global $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $l_footer_one_player_on, $sched_ticks;
    if ($error_msg !='')
    {
        echo "<p>$error_msg<p>";
    }
    else
    {
        echo "<br><br>";
    }

    global $l_global_mmenu, $db;
    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
    include_once ("./footer.php");
    die();
}
?>
