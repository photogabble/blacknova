<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>
{$l_like_to_have}<br><br>
{if $ship_classes}
{$shp} {$l_shipyards}<br>
{/if}
{$spp} {$l_upgrade_ports}<br>
{$oep} {$l_ore_ports}<br>
{$ogp} {$l_organics_ports}<br>
{$gop} {$l_goods_ports}<br>
{$enp} {$l_energy_ports}<br>
{$initscommod}% {$l_initscommod}<br>
{$initbcommod}% {$l_initbcommod}<br>
{$empty} {$l_empty_sectors}<br>
{$fedsecs} {$l_fed_sectors}<br>
{$nump} {$l_unowned_planets}<br>
<br>
{$l_total_links}<br>
{$l_total_twoways}<br>
{$l_total_oneways}<br>
{$l_galaxy_size_change}<br>
<br><br>
<form action="make_galaxy.php" name="make_galaxy" method="post">
<input type="hidden" name="autorun" value="{$autorun}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="new_galaxy_size" value="{$new_galaxy_size}">
<input type="hidden" name="shp" value="{$shp}">
<input type="hidden" name="upp" value="{$upp}">
<input type="hidden" name="spp" value="{$spp}">
<input type="hidden" name="oep" value="{$oep}">
<input type="hidden" name="ogp" value="{$ogp}">
<input type="hidden" name="gop" value="{$gop}">
<input type="hidden" name="enp" value="{$enp}">
<input type="hidden" name="initscommod" value="{$initscommod}">
<input type="hidden" name="initbcommod" value="{$initbcommod}">
<input type="hidden" name="nump" value="{$nump}">
<input type="hidden" name="linksper" value="{$linksper}">
<input type="hidden" name="twoways" value="{$twoways}">
<input type="hidden" name="fedsecs" value="{$fedsecs}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="admin_charname" value="{$admin_charname}">
<input type="hidden" name="sektors" value="{$sektors}">
<input type="hidden" name="gamenum" value="{$gamenum}">
<input type="submit" value="{$l_continue}">
</form>
<br><br><br><br>

{if $autorun}
<script type="text/javascript" src="backends/javascript/autorun.js"></script>
{/if}

