<?php
function go_build_base($planet_id, $sector_id)
{
    // Dynamic functions
    dynamic_loader ($db, "calc_ownership.php");

    global $base_ore, $base_organics, $base_goods, $base_credits;
    global $db, $l_clickme, $l_planet_bbuild, $l_toplanetmenu;
    global $shipinfo, $spy_success_factor, $planet_detect_success1, $playerinfo;

    echo "<br><a href=standard_report.php>$l_pr_planetstatus</a><br><br>";

    $result3 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=$planet_id");
    if ($result3)
    {
        $planetinfo = $result3->fields;
    }

    real_space_move($sector_id);

    if ($spy_success_factor)
    {
        spy_detect_planet($db,$shipinfo['ship_id'], $planet_id, $planet_detect_success1);
    }

    echo "<br><a href=planet.php?planet_id=$planet_id>$l_clickme</a> $l_toplanetmenu<br><br>";


    // build a base
    if ($planetinfo['ore'] >= $base_ore && $planetinfo['organics'] >= $base_organics && $planetinfo['goods'] >= $base_goods && $planetinfo['credits'] >= $base_credits)
    {
        // Create The Base
        $debug_query = $db->Execute("UPDATE {$db->prefix}planets SET base='Y', ore=$planetinfo[ore]-$base_ore, organics=$planetinfo[organics]-$base_organics, goods=$planetinfo[goods]-$base_goods, credits=$planetinfo[credits]-$base_credits WHERE planet_id=$planet_id");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        // Update User Turns
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        // Refresh Planet Info
        $result3 = $db->Execute("SELECT * FROM {$db->prefix}planets WHERE planet_id=$planet_id");
        $planetinfo=$result3->fields;

        // Notify User Of Base Results
        echo "$l_planet_bbuild<br><br>";

        // Calc Ownership and Notify User Of Results
        $ownership = calc_ownership($db,$planetinfo['sector_id']);
        if (!empty($ownership))
        {
            echo "$ownership<p>";
        }
    }
echo "<br><br>";
global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

}
?>