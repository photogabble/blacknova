<script type="text/javascript" defer="defer" src="backends/javascript/focus.js"></script>
<script type="text/javascript" defer="defer" src="backends/javascript/sha256.js"></script>
<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>
<h1>{$title}</h1>

{$l_welcome_make}<br>
{$l_welcome_assist}<br>
{$l_welcome_defaults}<br>
<br>
{$l_welcome_limits}<br>
{$l_welcome_safe}
{$l_welcome_badidea}<br>
<br>
<font color="yellow">{$l_welcome_nosupport}</font>
<br><br>
<form method="post" action="make_galaxy.php" accept-charset="utf-8" onsubmit="encrypted_password.value=sha256_once(document.forms[0].password.value);password.value='';">

{if $badpass}
<font color="red">{$l_readmin_password}</font><br><br>
{/if}

{$l_admin_password}
<input type="password" id="Password" name="password" value="" size="32">
<input type="submit" value="{$l_submit}">
<input type="reset" value="{$l_reset}">
<input type="hidden" name="step" value="1">
<input type="hidden" name="encrypted_password" value="">
</form>
<br><br><br><br>
