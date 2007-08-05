<script type="text/javascript" defer="defer" src="backends/javascript/focus.js"></script>
<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>
<form action="make_galaxy.php" method="post">
{$l_mk_adminname}
<input type="text" name="admin_charname" size="20" maxlength="20" value="Webmaster">
<br><br>
{$l_configset_which}
<select name="mode">
{section name=index start=0 loop=$configsets}
{if $configsets[index].selected}
<option selected="selected">{$configsets[index].name}</option>
{else}
<option>{$configsets[index].name}</option>
{/if}
{/section}
</select>
<br><br>
<input type="checkbox" name="autorun" value="on" checked>
{$l_autorun}
<br>
<input type="checkbox" name="persist" value="on" checked>
{$l_persist}
<br><br>
<select name=gamenum>
{html_options options=$game_instances selected=$newgame}
</select>
<br><br>
<font color="yellow">
{$l_welcome_warning}<br></font>
<br>
<input type="submit" value="{$l_continue}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="total_elapsed" value="{$total_elapsed}">
</form>
<br><br><br><br>
