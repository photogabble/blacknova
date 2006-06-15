<?php
function base_build_check($planet, $i)
{
    global $l_yes, $l_no;
    global $base_ore, $base_organics, $base_goods, $base_credits, $l_pr_build;

    if ($planet[$i]['base'] == 'Y')
    {
        return $l_yes;
    }
    elseif ($planet[$i]['ore'] >= $base_ore && $planet[$i]['organics'] >= $base_organics && $planet[$i]['goods'] >= $base_goods && $planet[$i]['credits'] >=$base_credits)
    {
        return "<a href=\"planet_report_ce.php?buildp=" . $planet[$i]['planet_id'] . "&builds=" . $planet[$i]['sector_id'] . "\">$l_pr_build</a>";
    }
    else
    {
        return $l_no;
    }
}
?>
