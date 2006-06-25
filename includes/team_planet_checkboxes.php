<?php
function team_planet_checkboxes($planet, $i)
{
    if ($planet[$i]['team'] <= 0)
    {
        return "<input type=checkbox name=\"team[]\" value=\"" . $planet[$i]['planet_id'] . "\">";
    }
    elseif ($planet[$i]['team'] > 0)
    {
        return "<input type=checkbox name=\"team[]\" value=\"" . $planet[$i]['planet_id'] . "\" checked>";
    }
}
?>
