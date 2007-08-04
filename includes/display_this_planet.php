<?php
// This function breaks almost every coding rule we have, but its better to have it here than duplicated in main and lrscan
function display_this_planet($db, $this_planet, $planettypes, $basefontsize, $l_unowned, $l_unnamed, $colonist_limit)
{
    global $general_highlight_color;
    global $templateset, $shipinfo;

    $totalcount = 0;
    $curcount = 0;
    $i = 0;
    $planetlevel = 0;
    if ($this_planet['owner'] != 0)
    {
        $result5 = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=". $this_planet['owner'] . "");
        $planet_owner = $result5->fields;

        $planetavg = $this_planet['computer'] + $this_planet['sensors'] + $this_planet['beams'] + $this_planet['torp_launchers'] + $this_planet['shields'] + $this_planet['cloak'] + ($this_planet['colonists'] / ($colonist_limit / 54));
        $planetavg = round($planetavg/94.5); // Divide by (54 levels * 7 categories / 4) to get 1-4.
        if ($planetavg > 4)
        {
            $planetavg = 4;
        }

        if ($planetavg < 0)
        {
            $planetavg = 0;
        }

        $planetlevel = $planetavg;
    }
    $output = "<td align=\"center\" valign=\"top\">";
    if ($shipinfo['sector_id'] == $this_planet['sector_id'])
    {
        $output = $output . "<a href=\"planet.php?planet_id=" . @$this_planet['planet_id'] . "\">";
    }
    $output = $output . "<img src=\"templates/$templateset/images/planets/$planettypes[$planetlevel].png\" alt=\"" . $planettypes[$planetlevel]. "\">";
    if ($shipinfo['sector_id'] == $this_planet['sector_id'])
    {
        $output = $output . "</a>";
    }
    $output = $output . "<br><font color=\"";
    $output = $output . $general_highlight_color; 
    $output = $output . "\">";
    if (empty($this_planet['name']))
    {
        $output = $output . $l_unnamed;
    }
    else
    {
        $output = $output . $this_planet['name'];
    }

    if (@$this_planet['owner'] == 0)
    {
        $output = $output . "<br>($l_unowned)";
    }
    else
    {
        $output = $output . "<br>($planet_owner[character_name])";
    }

    $output = $output . "</font></td>";
    return $output;
}
?>
