<form action="admin.php" method="post" accept-charset="utf-8">
    <input type="hidden" name="menu" value="an">
    <table border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td>Add Admin News:</td>
            <td nowrap="nowrap"><input type="text" name="an_text" value="{$an_text}"></td>
            <td align="right"><input type="submit" name="command" value="add"></td>
        </tr>
        <tr>
            <td>Delete Last:</td>
            <td nowrap="nowrap">{$an_text}</td>
            <td align="right"><input type="submit" name="command" value="del"></td>
        </tr>
    </table>
</form>
