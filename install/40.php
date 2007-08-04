<?php
    header("Pragma: no-cache");
    header("Content-disposition: attachment; filename=db_config.php");
    header("Content-type: text/php");
    echo rawurldecode($_POST['rawdata']);
    die();
?>
