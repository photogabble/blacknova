<?php
function db_output($db,$status,$served_line,$served_page)
{
    global $langdir, $raw_prefix;
    global $l_db_success, $l_db_failure;

    // Dynamic functions
    dynamic_loader ($db, "load_languages.php");

    if (!isset($_POST['step']) || $_POST['step'] >2) // Prevent loading languages before db loads.
    {
        // Load language variables
        load_languages($db, $raw_prefix, 'global_includes');
    }

    if ($status)
    {
        $return = "<font color=\"lime\"> - " . $l_db_success . "</font><br>";
    }
    else
    {
        $output_result=str_replace("[served_page]", $served_page, $l_db_failure);
        $output_result=str_replace("[served_line]", $served_line-1, $output_result);
        $output_result=str_replace("[dberror]", $status, $output_result);
//        $output_result=str_replace("[dberror]", $db->ErrorMsg(), $output_result);
        $return = "<font color=\"red\"> - " . $output_result . "<hr>\n</font><br>\n";
    }

    $status ='';
    $output_result = '';
    return $return;
}
?>
