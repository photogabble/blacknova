<?php
function checkdead($db)
{
    global $playerinfo, $shipinfo; 
    global $spy_success_factor;
    global $l_global_died1, $l_global_died2, $l_login_died1, $l_login_died2, $l_die_please1, $l_die_please2;
    global $start_fighters, $start_armor, $start_energy;
    global $start_pod, $start_scoop;
    global $langdir, $local_lang;
    global $boom_armor, $boom_energy, $boom_fighters, $boom_pod, $boom_scoop;

    if ($shipinfo['destroyed'] == "Y") // Check for destroyed ship
    {
        if ($shipinfo['dev_escapepod'] == "Y") // If the player has an escapepod, set the player up with a new ship.
        {
            $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET class=1, hull=0, engines=0, pengines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=$boom_armor, cloak=0, shields=0, sector_id=1, ore=0, organics=0, energy=$boom_energy, colonists=0, goods=0, fighters=$boom_fighters, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_emerwarp=0, dev_escapepod='$boom_pod', dev_fuelscoop='$boom_scoop', dev_minedeflector=0, destroyed='N' WHERE ship_id=$shipinfo[ship_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);
            
            if ($spy_success_factor) // If there was a spy onboard, make sure its destroyed.
            {
                spy_ship_destroyed($db,$shipinfo['ship_id'],0);
            }

            echo $l_login_died1 . "<a href=\"main.php\">" . $l_login_died2 . "</a>";
        }
        else
        {
            // if the player doesn't have an escapepod - they're dead, delete them.
            // uhhh  don't delete them to prevent self-distruct inherit
            // I really don't know what the above comments reference, so I'm leaving them here in case I figure it out - iamsure
            global $l_error_occured, $l_login_closed_message, $title, $l_global_mlogin;
            $title = $l_error_occured;
            echo "<h1>" . $title. "</h1>\n";

            echo $l_global_died1 . "<a href=\"log.php\">" . $l_global_died2 . "</a>";
            echo "<br><br>";
            echo "<a href=\"index.php\">" . $l_global_mlogin . "</a>";

            // Dynamic functions
            dynamic_loader ($db, "db_kill_player.php");
            dynamic_loader ($db, "cancel_bounty.php");

            db_kill_player($db, $playerinfo['player_id']);
            cancel_bounty($db, $playerinfo['player_id']);

            include_once ("./footer.php");
            die();
        }
    }
}
?>
