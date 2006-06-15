<?php
function direct_test($file, $phpself)
{
echo "direct test";
die();
    // Soon, we can template this!
    global $langdir, $l_error_occured, $l_cannot_access, $raw_prefix;
    $phpfile = substr($file, (strrpos($file, "/") +1));
    $selffile = substr($phpself, (strrpos($phpself, "/") +1));

    if ($phpfile == $selffile)
    {
        include_once ("./global_includes.php");
        dynamic_loader ($db, "load_languages.php");

        // Load language variables
        load_languages($db, $raw_prefix, 'common');

        $title = $l_error_occured;
        echo $l_cannot_access;
        include_once ("./footer.php");
        die();
    }
}
?>
