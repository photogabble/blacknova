<?php
// Recognizes only some (d, j, F, M, Y, H, i) format string components!
function simple_date($frmtstr, $full_year, $month_full, $month_short, $day, $hour, $min)
{
    $retvalue="";
    $temp_sizeof = strlen($frmtstr);
    for ($cntr=0; $cntr<$temp_sizeof; $cntr++)
    {
        switch (substr($frmtstr,$cntr,1))
        {
            case "d":
                if (strlen($day)==1)
                {
                    $retvalue .= "0$day";
                }
                else
                {
                    $retvalue .= $day;
                }
            break;

            case "j":
                $retvalue .= number_format($day, 0, $local_number_dec_point, $local_number_thousands_sep);
            break;

            case "F":
                $retvalue .= $month_full;
            break;

            case "M":
                $retvalue .= $month_short;
            break;

            case "Y":
                $retvalue .= $full_year;
            break;

            case "H":
                if (strlen($hour)==1)
                {
                    $retvalue .= "0$hour";
                }
                else
                {
                    $retvalue .= $hour;
                }
            break;

            case "i":
                if (strlen($min)==1)
                {
                    $retvalue .= "0$min";
                }
                else
                {
                    $retvalue .= $min;
                }
            break;

            default:
                $retvalue .= substr($frmtstr,$cntr,1);
            break;
        }
    }
    return $retvalue;
}
?>
