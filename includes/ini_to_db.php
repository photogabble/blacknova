<?php
// Function for placing values in the db.
function ini_to_db ($db, $ini_file, $ini_table, $storelang)
{
    global $l_store_values, $raw_prefix;
    global $cumulative;
    $cumulative = 0;
    if (substr($ini_file,0,17) == "config/configset-")
    {
        dynamic_loader ($db, "load_languages.php");
        load_languages($db, $raw_prefix, 'config');
    }

    // Store the ini values the admin set into the DB.

    // This is a loop, that reads a ini file, of the type variable = value.
    // It will loop thru the list of the ini variables, and push them into the db.
    $ini_keys = parse_ini_file("$ini_file",true);

    foreach ($ini_keys as $config_category=>$whatever2)
    {
        foreach ($whatever2 as $config_key=>$config_value)
        {
            // Initialize an array to hold the record data to store
            $record = array();

            // Set the values for the fields in the record
            $record['name'] = $config_key;
            $record['value'] = $config_value;
            $record['category'] = $config_category;
            if ($ini_file == "config/config.php")
            {
                $config_description = 'l_config_'. $config_key;
                $record['description'] = $$config_description;
            }

            if (!isset($config_description))
            {
                $config_description = '';
            }

            if (!isset($$config_description))
            {
                $$config_description = '';
            }

            $debug_query_insert = "INSERT into $ini_table (name, category, value, description) VALUES (?,?,?,?)";
            $debug_query = $db->Execute($debug_query_insert, array($config_key, $config_category, $config_value, $$config_description));
            $current_status = db_op_result($db, $debug_query,__LINE__,__FILE__);
            cumulative_error($cumulative, $current_status);
        }
    }

    return db_output($db, !$cumulative,__LINE__,__FILE__);
}
?>
