if (empty($operation))
{
    echo "<br>";
    echo "<h2><font color=\"red\">Are You Sure?</font></h2><br>";
    echo "<input type=\"hidden\" name=\"operation\" value=\"clear_ai_log\">";
    echo "<input type=\"hidden\" name=\"menu\" value=\"ai\">";
    echo "<input type=\"submit\" value=\"Clear\">";
}
elseif ($operation == "clear_ai_log")
{
    $res = $db->Execute("SELECT email,player_id FROM {$db_prefix}players WHERE email LIKE '%@aiplayer'");
    while (!$res->EOF)
    {
        $row = $res->fields;
        $debug_query = $db->Execute("DELETE FROM {$db_prefix}logs WHERE player_id=$row[player_id]");
        db_op_result($debug_query,__LINE__,__FILE__);
        echo "Log for player_id $row[player_id] cleared.<br>";
        $res->MoveNext();
    }
}
else
{
    echo "Invalid operation";
}

<input type="hidden" name="module" value="clearlog">
</form>

