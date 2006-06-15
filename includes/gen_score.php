<?php
function gen_score($db,$sid)
{
    if ($sid != '')
    {
        global $dev_genesis_price, $dev_emerwarp_price, $dev_warpedit_price;
        global $dev_minedeflector_price, $dev_escapepod_price, $dev_fuelscoop_price;
        global $fighter_price, $torpedo_price, $armor_price, $colonist_price;
        global $base_ore, $base_goods, $base_organics, $base_credits;
        global $ore_price, $organics_price, $goods_price, $energy_price;
        global $upgrade_cost, $upgrade_factor;
        global $spy_price;

        // Dynamic functions
        dynamic_loader ($db, "sign.php");

        static $base_value;  // No need to calculate it more than once
        if (!$base_value)
        {
            $base_value = $base_ore*$ore_price + $base_goods*$goods_price + $base_organics*$organics_price + $base_credits;
        }
        
        //  SQL query gets ship levels, cargo, equipment, and devices.
        //  Add ship levels, multiply by upgrade cost, store the total in $calc_levels .
        //  Add cargo and equipment, store the total in $calc_equip .
        //  Add devices, store the total in $calc_dev .

        // 1st query - Player's current ship
        $debug_query = $db->SelectLimit("SELECT * FROM {$db->prefix}ships WHERE {$db->prefix}ships.player_id=$sid AND destroyed='N'",1);
        db_op_result($db,$debug_query, __LINE__, __FILE__);
        $row = $debug_query->fields;
        $calc_hull = round(pow($upgrade_factor,$row['hull']));
        $calc_engines = round(pow($upgrade_factor,$row['engines']));
//        $calc_pengines = round(pow($upgrade_factor,$row['pengines']));
        $calc_power = round(pow($upgrade_factor,$row['power']));
        $calc_computer = round(pow($upgrade_factor,$row['computer']));
        $calc_sensors = round(pow($upgrade_factor,$row['sensors']));
        $calc_beams = round(pow($upgrade_factor,$row['beams']));
        $calc_torp_launchers = round(pow($upgrade_factor,$row['torp_launchers']));
        $calc_shields = round(pow($upgrade_factor,$row['shields']));
        $calc_armor = round(pow($upgrade_factor,$row['armor']));
        $calc_cloak = round(pow($upgrade_factor,$row['cloak']));
//        $calc_levels = ($calc_hull+$calc_engines+$calc_pengines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armor+$calc_cloak)*$upgrade_cost;
        $calc_levels = ($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armor+$calc_cloak)*$upgrade_cost;
    
        $calc_torps = $row['torps'] * $torpedo_price;
        $calc_armor_pts = $row['armor_pts'] * $armor_price;
        $calc_ship_ore = $row['ore'] * $ore_price;
        $calc_ship_organics = $row['organics'] * $organics_price;
        $calc_ship_goods = $row['goods'] * $goods_price;
        $calc_ship_energy = $row['energy'] * $energy_price;
        $calc_ship_colonists = $row['colonists'] * $colonist_price;
        $calc_ship_fighters = $row['fighters'] * $fighter_price;
        $calc_equip = $calc_torps+$calc_armor_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters;
    
        $calc_dev_warpedit = $row['dev_warpedit'] * $dev_warpedit_price;
        $calc_dev_genesis = $row['dev_genesis'] * $dev_genesis_price;
        $calc_dev_emerwarp = $row['dev_emerwarp'] * $dev_emerwarp_price;
        $calc_dev_minedeflector = $row['dev_minedeflector'] * $dev_minedeflector_price;

        if ($row['dev_escapepod'] = "Y")
        {
            $calc_dev_escapepod = $dev_escapepod_price;
        }
        else
        {
            $calc_dev_escapepod = 0;
        }

//        if ($row['dev_fuelscoop'] == "Y")
//        {
//            $calc_dev_fuelscoop = $dev_fuelscoop_price;
//        }
//        else
//        {
//            $calc_dev_fuelscoop = 0;
//        }

//        $calc_dev = $calc_dev_warpedit+$calc_dev_genesis+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_minedeflector;
        $calc_dev = $calc_dev_warpedit+$calc_dev_genesis+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_minedeflector;

        //  SQL query gets planet defense levels, cargo, and credits.
        //  Add cargo and store in $calc_planet_cargo .

        // 2nd query - planets owned by player and their contents
        $debug_query = $db->Execute("Select * FROM {$db->prefix}planets WHERE {$db->prefix}planets.owner=$sid");
        db_op_result($db,$debug_query, __LINE__, __FILE__);

        $calc_planet_cargo = 0;
        $calc_planet_colonists = 0;
        $calc_planet_credits = 0;
        $calc_planet_defense = 0;
        $calc_planet_def_levels = 0;
        $calc_planet_base = 0;
        
        while (!$debug_query->EOF)
        {
            $row = $debug_query->fields;
            $calc_planet_organics = $row['organics'] * $organics_price;
            $calc_planet_ore = $row['ore'] * $ore_price;
            $calc_planet_goods = $row['goods'] * $goods_price;
            $calc_planet_energy = $row['energy'] * $energy_price;
            $calc_planet_cargo += $calc_planet_organics + $calc_planet_ore + $calc_planet_goods + $calc_planet_energy;
    
            $calc_planet_colonists += $row['colonists']*$colonist_price;
            $calc_planet_fighters = $row['fighters'] * $fighter_price;
            $calc_planet_torps = $row['torps'] * $torpedo_price;
            $calc_planet_credits += $row['credits'];
            $calc_planet_defense += $calc_planet_fighters + $calc_planet_torps;
    
            $calc_planet_computer = round(pow($upgrade_factor,$row['computer']));
            $calc_planet_sensors = round(pow($upgrade_factor,$row['sensors']));
            $calc_planet_beams = round(pow($upgrade_factor,$row['beams']));
            $calc_planet_torp_launchers = round(pow($upgrade_factor,$row['torp_launchers']));
            $calc_planet_shields = round(pow($upgrade_factor,$row['shields']));
            $calc_planet_cloak = round(pow($upgrade_factor,$row['cloak']));
            $calc_planet_armor = 0; // This will be needed later, when we add planet armor for humans
            $calc_planet_def_levels += ($calc_planet_armor+$calc_planet_computer+$calc_planet_sensors+$calc_planet_beams+$calc_planet_torp_launchers+$calc_planet_shields+$calc_planet_cloak)*$upgrade_cost;
            
            if ($row['base'] == 'Y')
            {
                $calc_planet_base += $base_value;
            }

            $debug_query->movenext();
        }
        
        //  SQL query to find Player currently has in Credits on him/her.
        //  Playet Credits: $calc_player_credits 

        // 3rd query
        $debug_query = $db->Execute("SELECT {$db->prefix}players.credits as player_credits ".
                                    "FROM {$db->prefix}players WHERE {$db->prefix}players.player_id=$sid");
        db_op_result($db,$debug_query, __LINE__, __FILE__);
        $row = $debug_query->fields;
        $calc_player_credits = $row['player_credits'];

        //  Add up all of the previous main values, store this 'subtotal' in $score.
        $score = $calc_levels+$calc_equip+$calc_dev+$calc_player_credits+$calc_planet_cargo+$calc_planet_colonists+$calc_planet_defense+$calc_planet_credits+$calc_planet_def_levels + $calc_planet_base;

        //  SQL query - if the player has any loans, add them to the subtotal raw score: $score 

        // 4th query
        $debug_query = $db->Execute("SELECT balance, loan FROM {$db->prefix}ibank_accounts WHERE player_id = $sid");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    
        if ($debug_query)
        {
            $row = $debug_query->fields;
            $score += ($row['balance'] - $row['loan']);
        }
    
        //  This function checks the number of spies the player has, and adds their cost to the score calculation.

        //  5th query
        $debug_query = $db->Execute("SELECT * FROM {$db->prefix}spies WHERE owner_id = $sid");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    
        if ($debug_query)
        {
            $spies = $debug_query->RecordCount();
            $score += ($spies * $spy_price);
        }
    
        // Get the value of all deployed sector fighters
        // 6th query
        $debug_query = $db->Execute("SELECT sum(quantity) as quantity FROM {$db->prefix}sector_defense WHERE player_id = $sid " .
                                    "and defense_type ='F'");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $row = $debug_query->fields;
        $score += ($row['quantity'] * $fighter_price);

        // Get the value of all deployed sector mines
        // 7th query
        $debug_query = $db->Execute("SELECT sum(quantity) as quantity FROM {$db->prefix}sector_defense WHERE player_id = $sid " .
                                    "and defense_type ='M'");
        db_op_result($db,$debug_query,__LINE__,__FILE__);

        $row = $debug_query->fields;
        $score += ($row['quantity'] * $torpedo_price);

        // Clean score up to a nice clean, positive number.
        $score = sign($score) * ROUND(SQRT(abs($score)));
    
        //  Update the player's score in the db.

        //  8th query
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET score=$score WHERE player_id=$sid");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
    
        return $score;
    }
    else
    {
        return 0;
    }
}
?>
