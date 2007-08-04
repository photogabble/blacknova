<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>
<strong>{$l_drop_all_tables}</strong>
<br>
{section name=dropped loop=$drop_table_names}
{$dropped}{$drop_table_names[dropped]}{$drop_table_results[dropped]}
{/section}
{if $final_drop_result}
<strong><font color="yellow">{$l_tabledrop_failure}</font></strong>
{else}
<strong>{$l_tabledrop_success}</strong>
{/if}
<br><br>

<strong>{$l_tablecreate}</strong><br>
{section name=created loop=$create_table_names}
{$created}{$create_table_names[created]}{$create_table_results[created]}
{/section}
{if $tablecreate_result == 0}
<strong>{$l_tablecreate_success}</strong>
{else}
<strong><font color="yellow">{$l_tablecreate_failure}</font></strong>
{/if}
<br><br>

<strong>{$l_store_values}</strong>
<br>
{$l_store_configs}{$config_result}
{$l_store_languages}{$lang_result}
{$set_ore_prodrate}{$ore_prodrate_results}
{$set_organics_prodrate}{$organics_prodrate_results}
{$set_goods_prodrate}{$goods_prodrate_results}
{$set_energy_prodrate}{$energy_prodrate_results}
{$set_fighters_prodrate}{$fighters_prodrate_results}
{$set_torps_prodrate}{$torps_prodrate_results}
<strong>{$l_store_complete}</strong>
<br><br>
<form action="make_galaxy.php" name="make_galaxy" method="post" accept-charset="utf-8">
<input type="submit" value="{$l_continue}">
<input type="hidden" name="gamenum" value="{$gamenum}">
<input type="hidden" name="admin_charname" value="{$admin_charname}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="autorun" value="{$autorun}">
<input type="hidden" name="total_elapsed" value="{$total_elapsed}">
</form>
<br><br><br><br>

{if $autorun}
<script type="text/javascript" src="backends/javascript/autorun.js"></script>
{/if}
