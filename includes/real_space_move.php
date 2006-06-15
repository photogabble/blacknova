<?php
function real_space_move($destination)
{
    dynamic_loader ($db, "number.php");

    global $db;
    global $l_pr_hostile;
    global $l_rs_ready;
    global $l_unnamed;
    global $level_factor;
    global $playerinfo, $shipinfo;

    $distance = calc_dist($db, $shipinfo['sector_id'],$destination);
    $shipspeed = $shipinfo['engines'];
    $triptime = abs(round(($rs_difficulty/$galaxy_size) * ($distance / $shipspeed)) -8); // 8 just makes sure at high levels that it levels out better.

    if ($triptime == 0 && $destination != $shipinfo['sector_id'])
    {
        $triptime = 1;
    }

    if ($shipinfo['dev_fuelscoop'] == "Y")
    {
        $energyscooped = $distance * 100;
    }
    else
    {
        $energyscooped = 0;
    }

    if ($shipinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1)
    {
        $energyscooped = 100;
    }

    $free_power = (num_level($shipinfo['power']) * 5) - $shipinfo['energy']; // Energy is level *5.

    // amount of energy that can be stored is less than amount scooped amount scooped is set to what can be stored
    if ($free_power < $energyscooped)
    {
        $energyscooped = $free_power;
    }

    // make sure energyscooped is not null
    if (!isset($energyscooped))
    {
        $energyscooped = "0";
    }

    // make sure energyscooped not negative, or decimal
    if ($energyscooped < 1)
    {
        $energyscooped = 0;
    }

    // check to see if already in that sector
    if ($destination == $shipinfo['sector_id'])
    {
        $triptime = 0;
        $energyscooped = 0;
    }

    if ($triptime > $playerinfo['turns'])
    {
        $l_rs_movetime=str_replace("[triptime]",number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_movetime);
        echo "$l_rs_movetime<br><br>";
        echo "$l_rs_noturns";
        $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET cleared_defenses=' ' WHERE ship_id=$shipinfo[ship_id]");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $retval = "BREAK-TURNS";
    }
    else
    {
        // modified from traderoute.php
        // Sector Defense Check

        $hostile = 0;
        $result98 = $db->Execute("SELECT * FROM {$db->prefix}sector_defense WHERE sector_id = $destination AND player_id != $playerinfo[player_id]");
        if (!$result98->EOF)
        {
            $fighters_owner = $result98->fields;
            $nsresult = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=$fighters_owner[player_id]");
            $nsfighters = $nsresult->fields;
            if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
            {
                $hostile = 1;
            }
        }

        if ($hostile > 0)
        {
            $retval = "HOSTILE";
            $l_pr_hostile2 = str_replace("[destination]", $destination, $l_pr_hostile);
            echo "$l_pr_hostile2<br>";
        }
        else
        {
            $stamp = date("Y-m-d H:i:s");
            $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-$triptime,turns_used=turns_used+$triptime WHERE player_id=$playerinfo[player_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $debug_query = $db->Execute("UPDATE {$db->prefix}ships SET sector_id=$destination,energy=energy+$energyscooped WHERE ship_id=$shipinfo[ship_id]");
            db_op_result($db,$debug_query,__LINE__,__FILE__);

            $l_rs_ready2=str_replace("[sector]",$destination,$l_rs_ready);
            $l_rs_ready2=str_replace("[triptime]",number_format($triptime, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_ready2);
            $l_rs_ready2=str_replace("[energy]",number_format($energyscooped, 0, $local_number_dec_point, $local_number_thousands_sep),$l_rs_ready2);
            echo "$l_rs_ready2<br>";
            $retval = "GO";
        }
    }

    return $retval;
}
?>
