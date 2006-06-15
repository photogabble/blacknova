<?php
function selling_checkboxes($planet, $i)
{
  if ($planet[$i]['sells'] != 'Y')
    return "<input type=checkbox name=\"sells[]\" value=\"" . $planet[$i]['planet_id'] . "\">";
  elseif ($planet[$i]['sells'] == 'Y')
    return "<input type=checkbox name=\"sells[]\" value=\"" . $planet[$i]['planet_id'] . "\" checked>";
}
?>
