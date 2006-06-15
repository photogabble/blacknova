<?php
function calc_ship_cleanup_cost($level_avg = 0, $type = 1)
{
    global $level_factor, $upgrade_cost;
  
    if ($type==1)
    {
        $c=1;
    }
    elseif ($type==2)
    {
        $c=2;
    }
    else
    {
        $c=4;
    }

    // You must check for upper boundary. Otherwise the typecast can cause it to flip to negative amounts.
    $cl_cost = (pow($level_factor, ($level_avg * 1.1)) * 80 * $upgrade_cost * $c);

    if ($cl_cost > 2000000000 || $cl_cost < 0)
    {
        $cl_cost = 2000000000;
    }
  
    return $cl_cost;
}
?>
