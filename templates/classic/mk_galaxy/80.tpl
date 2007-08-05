<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>
{$l_gen_links} {$gen_links_result}
<br><br>
<form action="make_galaxy.php" name="make_galaxy" method="post">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="autorun" value="{$autorun}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="admin_charname" value="{$admin_charname}">
<input type="hidden" name="gamenum" value="{$gamenum}">
<input type="submit" value="{$l_continue}">
</form>

{if $autorun}
<script type="text/javascript" src="backends/javascript/autorun.js"></script>
{/if}

