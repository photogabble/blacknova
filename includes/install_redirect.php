<?php
function install_redirect($ADODB_SESSION_DRIVER)
{
    if ($_SERVER['SERVER_PORT'] == '443')
    {
        $server_type = 'https';
    }
    else
    {
        $server_type = 'http';
    }

    if (empty($ADODB_SESSION_DRIVER))
    {
        if (strlen(dirname($_SERVER['PHP_SELF'])) > 1)
        {
            $add_slash_to_url = '/';
        }

        $server_port = '';
        if ($_SERVER['SERVER_PORT'] != '80' || $_SERVER['SERVER_PORT'] != '443')
        {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }

        echo $server_type;
        // Much smoother - no broken header/footer issues, and seamless for user.
//        header("Location: " . $server_type . "://" . $_SERVER['SERVER_NAME'] .$server_port . dirname($_SERVER['PHP_SELF']) . $add_slash_to_url . "install.php");
        exit();
    }
}
?>
