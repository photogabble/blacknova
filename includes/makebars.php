<?php
function MakeBars($level, $max)
{
    global $l_n_a, $templateset;

    if ($max == 0)
    {
        $max = 1;
    }
    $heath = ($level / $max);
    $heath_bars = round($heath * 10);

    $image = '';

    for ($i=0; $i<$heath_bars; $i++)
    {
        $bright = floor($i / 2) + 1;
        if ($bright > 5)
        {
            $bright = 5;
        }

        $image .= "<img src=\"templates/$templateset/images/dialon$bright.png\" alt=\"\">";
    }

    for ($i=0; $i<(10-$heath_bars); $i++)
    {
        $image .= "<img src=\"templates/$templateset/images/dialoff.png\" alt=\"\">";
    }

    if ($image == '')
    {
        $image = "<font style=\"font-size: 0.8em;\"><strong>$l_n_a</strong></font>";
    }

    return $image;
}
?>
