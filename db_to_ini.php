<?php
include_once ("./global_includes.php");
include_once ("./header.php");

$debug_query = $db->Execute("SELECT DISTINCT category FROM {$raw_prefix}languages order by category asc");

$j = 1;
while (!$debug_query->EOF)
{
    $categories[$j] = $debug_query->fields['category'];
    $j++;
    $debug_query->MoveNext();
}

$inifile = fopen("languages/english.ini","w+");
for ($i=1; $i<count($categories); $i++)
{
    $line =  "[" . $categories[$i] . "]\n";
    $inires = fwrite($inifile,$line); //write the line to the file
    $debug_query = $db->Execute("SELECT * FROM {$raw_prefix}languages where category='$categories[$i]' order by name asc");
    while (!$debug_query->EOF)
    {
        $line = $debug_query->fields['name'] . " = \"" . addslashes($debug_query->fields['value']) . "\";\n";
        $inires = fwrite($inifile,$line); //write the line to the file
        $debug_query->MoveNext();
    }
}

fclose($inifile);
include_once ("./footer.php");
?>
