<?php
function sign( $data )
{
    if ($data > 0)
    {
        return 1;
    }
    elseif ($data < 0)
    {
        return -1;
    }
    else
    {
        return 0;
    }
}
?>
