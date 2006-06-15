<?php
function linecolor()
{
    global $line_color, $color_line1, $color_line2;

    if ($line_color == $color_line1)
    {
        $line_color = $color_line2;
    }
    else
    {
        $line_color = $color_line1;
    }

    return $line_color;
}
?>
