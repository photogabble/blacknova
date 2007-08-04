<?php
include_once ("./global_includes.php");

// Load language variables
load_languages($db, $raw_prefix, 'global_includes');

$no_body = 1;
include_once ("./header.php");

if (!isset($_GET['file']))
{
    $_GET['file'] = '';
}

dynamic_loader ($db, "syntax_highlighter.php");

$pos = strpos($_GET['file'], 'config');
$pos2 = strpos($_GET['file'], '..');
if (($pos === false) && ($pos2 === false))
{
    $file_title = basename($_GET['file']);
    $title = "Show sourcecode for " . $file_title;

    $HL = new highlighter();
    $HL->set_code(file_get_contents($_GET['file']));
    $output = $HL->process();
    $output = str_replace("<br />", "<br>\n", $output);
    $output = str_replace("</span>", "</span>\n", $output);
}
else
{
    $title = "An error occured";
    $output = "Illegal target entered.";
}

$smarty->assign("title", $title);
$smarty->assign("output", $output);
$smarty->display("$templateset/showsource.tpl");
include_once ("./footer.php");
?>
