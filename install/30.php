<?php
        clearstatcache();
        $new_time = filemtime('config/db_config.php');
        if ($_POST['time_error'] == $new_time)   // The file is not uploaded properly -- error
        {
            docheck($_POST['time_error']);
        }
        else
        {
            docheck(0);
        }

// If the file config/db_config.php does not allow writing, admin gets the download form
function docheck($time_error)
{
    global $rawdata;

    include ("./config/db_config.php");   // Checking just created config file

    $game_installed = isset($ADODB_CRYPT_KEY);
    if ($game_installed && !$time_error)
    {
        echo "<font color=lime>Local settings successfully saved.</font><br><br>";
        echo "Please change the permissions on your db_config.php right away!<br>";
        echo "Set it to '0404', or user = R, group = ---, other = R--<br>";
        echo "<font color=\"red\">If you do not, you risk having your server compromised!!</font><br>";
        echo "make_galaxy.php will not run until you change that permission.<br><br>";
        echo "Everything looks great! Feel free to run the <a href=\"./make_galaxy.php\">Create Universe</a> script now!";
        echo "<br><br>If you have already run the make galaxy script, <a href=\"index.php\">Login now</a>.<br><br>";
    }
    else
    {
        echo "<script type=\"text/javascript\" defer=\"defer\">";
        echo "function enablebutton()";
        echo "{";
        echo "    document.forms[1].secondbutton.disabled=false;";
        echo "}";
        echo "</script>";

        echo "<font color=red><strong>ERROR:</strong><br>Local settings are <strong>NOT</strong> successfully saved.</font>";

        echo "<br><br><form action=\"install.php\" method=\"post\" accept-charset=\"utf-8\"><input type=\"hidden\" name=\"rawdata\" value=\"$rawdata\">";
        echo "<input type=hidden name=\"step\" value=\"3\"><input type=submit value=\"Download\" onclick=\"enablebutton()\"> ";
        echo "the file 'db_config.php' and upload it manually to <strong>/config</strong> subdir (overwrite the current one).</form>";

        echo "<form action=\"install.php\" method=\"post\" accept-charset=\"utf-8\"><strong>AFTER</strong> that click <input type=\"hidden\" name=\"rawdata\" value=\"$rawdata\">";
        echo "<input type=\"hidden\" name=\"step\" value=\"4\"><input type=\"hidden\" name=\"_adminpass\" value=\"$_POST[_adminpass]\">";
        echo "<input type=\"hidden\" name=\"time_error\" value=\"$time_error\"><input type=\"submit\" name=\"secondbutton\" value=\"here.\" disabled=\"disabl$
    }
}

?>
