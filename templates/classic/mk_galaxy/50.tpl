<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>
{$l_set_sector_max}{$set_sector_result}
{$l_build_zones}<br>
{section name=zones loop=$l_zone_array}
{$l_zone_array[zones]}{$zone_results_array[zones]}
{/section}
{$l_build_unique_sectors}<br>
{section name=sectors loop=$l_sector_array}
{$l_sector_array[sectors]}{$sector_results_array[sectors]}
{/section}
<br>
{$l_create_sectors}{$sector_build_result}
{$l_repair_collisions}{$collision_repair_result}
<br>
<form action="make_galaxy.php" name="make_galaxy" method="post" accept-charset="utf-8">
<input type="hidden" name="autorun" value="{$autorun}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="nump" value="{$nump}">
<input type="hidden" name="shp" value="{$shp}">
<input type="hidden" name="upp" value="{$upp}">
<input type="hidden" name="spp" value="{$spp}">
<input type="hidden" name="oep" value="{$oep}">
<input type="hidden" name="ogp" value="{$ogp}">
<input type="hidden" name="gop" value="{$gop}">
<input type="hidden" name="enp" value="{$enp}">
<input type="hidden" name="initscommod" value="{$initscommod}">
<input type="hidden" name="initbcommod" value="{$initbcommod}">
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

