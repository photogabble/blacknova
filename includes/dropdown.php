<?php
function dropdown($element_name,$current_value, $max_value, $onchange)
{
    // Create dropdowns when called
    $i = $current_value;
    $dropdownvar = "<select size='1' name='$element_name'";
    $dropdownvar = "$dropdownvar $onchange>\n";
    while ($i <= $max_value)
    {
        if ($current_value == $i)
        {
            $dropdownvar = "$dropdownvar        <option value='$i' selected>$i</option>\n";
        }
        else
        {
            $dropdownvar = "$dropdownvar        <option value='$i'>$i</option>\n";
        }

        $i++;
    }

    $dropdownvar = "$dropdownvar       </select>\n";
    return $dropdownvar;
}
?>
