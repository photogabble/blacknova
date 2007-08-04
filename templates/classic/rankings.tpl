<div align="center">

{if $dbprefix == $raw_prefix}
<form action="ranking.php" method="post" accept-charset="utf-8">
<select name=gamenum>
{html_options options=$game_instances}
</select>
<input type="submit" value="{$l_submit}">
</form>
<br><br>
{/if}
</div>
