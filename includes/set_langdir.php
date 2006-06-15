<?php
function set_langdir($db, $badfile)
{
    global $default_lang, $avail_lang, $raw_prefix;

    if (isset($_POST['newlang']))
    {
        $_SESSION['langdir'] = $_POST['newlang'];
    }

    $default_lang = 'english';
    if (!$badfile)
    {
        $debug_query = $db->Execute("SELECT name, value from {$raw_prefix}inst_languages");
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        $i = 0;
        while ($debug_query && !$debug_query->EOF)
        {
            $row = $debug_query->fields;
            $avail_lang[$i]['name'] = $row['name'];
            $avail_lang[$i]['value'] = $row['value'];
            $i++;
            $debug_query->MoveNext();
        }
    }

    $maxval = count($avail_lang);

    // If langdir is set on the session, check to see if it is an available language in the game.
    if (isset($_SESSION['langdir']))
    {
        for ($i=0; $i<$maxval; $i++)
        {
            if ($avail_lang[$i]['value'] == $_SESSION['langdir'])
            {
                $templangdir = $_SESSION['langdir'];
                break;
            }
            else
            {
                $templangdir = $default_lang;
            }
        }
    }
    else
    {
        $templangdir = $default_lang;
    }

    return $templangdir;
}
?>
