<?php
function traderoute_distance($type1, $type2, $start, $dest, $circuit, $sells = 'N')
{
    global $playerinfo, $shipinfo, $rs_difficulty, $galaxy_size;
    global $level_factor;
    global $db;

    $retvalue['triptime'] = 0;
    $retvalue['scooped1'] = 0;
    $retvalue['scooped2'] = 0;
    $retvalue['scooped'] = 0;

//  if ($type1 == 'L')
//  {
//    $query = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=$start");
//    $start = $query->fields;
//  }

//  if ($type2 == 'L')
//  {
//    $query = $db->Execute("SELECT * FROM {$db->prefix}universe WHERE sector_id=$dest");
//    $dest = $query->fields;
//  }

    if ($start == $dest)
    {
        if ($circuit == '1')
        {
            $retvalue['triptime'] = '1';
        }
        else
        {
            $retvalue['triptime'] = '2';
        }

        return $retvalue;
    }

    $distance = calc_dist($db, $start, $dest);
    $shipspeed = $shipinfo['engines'];
    if ($shipspeed < 1)
    {
        $shipspeed = 1;
    }

    // rs_difficulty is divided by the galaxy size to ensure that the equation is roughly the same regardless
    // of galaxy size. This ensures that low engines will get you nowhere, and high engines will be needed
    // for the longest trips.
    $triptime = abs(round(($rs_difficulty/$galaxy_size) * ($distance / $shipspeed)) -8); // 8 just makes sure at high levels that it levels out better.

    if (($triptime == 0 && $dest != $shipinfo['sector_id']) || ($shipinfo['engines'] == 100)) // 100 is the max for engine levels
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

    if ($shipinfo['dev_fuelscoop'] == "Y" && !$energyscooped && $triptime == 1)
    {
        $energyscooped = 100;
    }

    if ($sells == 'Y')
    {
        $free_power = (5 * num_level($shipinfo['power']));
    }
    else
    {
        $free_power = (5 * num_level($shipinfo['power'])) - $shipinfo['energy'];
    }

    if ($free_power < $energyscooped)
    {
        $energyscooped = $free_power;
    }

    if ($energyscooped < 1)
    {
        $energyscooped = 0;
    }

    $retvalue['scooped1'] = $energyscooped;

    if ($circuit == '2')
    {
        if ($sells == 'Y' && $shipinfo['dev_fuelscoop'] == 'Y' && $type2 == 'P' && $dest['port_type'] != 'energy')
        {
            $energyscooped = $distance * 100;
            $free_power = (5 * num_level($shipinfo['power']));
            if ($free_power < $energyscooped)
            {
                $energyscooped = $free_power;
            }

            $retvalue['scooped2'] = $energyscooped;
        }
        elseif ($shipinfo['dev_fuelscoop'] == 'Y')
        {
            $energyscooped = $distance * 100;
            $free_power = (5 * num_level($shipinfo['power'])) - $retvalue['scooped1'] - $shipinfo['energy'];
            if ($free_power < $energyscooped)
            {
                $energyscooped = $free_power;
            }

            $retvalue['scooped2'] = $energyscooped;
        }
    }

    if ($circuit == '2')
    {
        $triptime*=2;
        $triptime+=2;
    }
    else
    {
        $triptime+=1;
    }

    $retvalue['triptime'] = $triptime;
    $retvalue['scooped'] = round($retvalue['scooped1'] + $retvalue['scooped2']);

    return $retvalue;
}
?>
