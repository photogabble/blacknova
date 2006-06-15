<?php
function port_trade($price, $delta, $max, $limit, $factor, $port_type, $origin)
{
    global $price_array, $portinfo;

    if ($portinfo['port_type'] ==  $port_type)
    {
        $price_array[$port_type] = $price - $delta * $max / $limit * $factor;
    }
    else
    {
        $price_array[$port_type] = $price + $delta * $max / $limit * $factor;
        if ($origin != "0")
        {
            $origin = "-" . $origin;
        }
    }

    return $origin;
}
?>
