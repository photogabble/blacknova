<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>
<h1>{$title}</h1>

{section name=sectors loop=$l_special_ports_array}
{$l_special_ports_array[sectors]}{$special_ports_results_array[sectors]}
{/section}
<br>
{section name=ports loop=$l_all_ports_array}
{$l_all_ports_array[ports]}{$all_ports_results_array[ports]}
{/section}
<br>

<form action="make_galaxy.php" name="make_galaxy" method="post">
<input type="hidden" name="autorun" value="{$autorun}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="nump" value="{$nump}">
<input type="hidden" name="linksper" value="{$linksper}">
<input type="hidden" name="twoways" value="{$twoways}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="sektors" value="{$sektors}">
<input type="hidden" name="admin_charname" value="{$admin_charname}">
<input type="hidden" name="gamenum" value="{$gamenum}">
<input type="submit" value="{$l_continue}">
</form>
<br><br><br><br>
<br><br><br><br>

{if $autorun}
<script type="text/javascript" src="backends/javascript/autorun.js"></script>
{/if}

