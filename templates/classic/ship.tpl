<h1>{$title}</h1>

{if $otherplayer_sector_id != $shipinfo_sector_id}
{$l_ship_the} <font color="white"> {$otherplayer_name}</font>, {$l_ship_nolonger} {$shipinfo_sector_id} <br>
{else}
{$l_ship_youc} <font color="white"> {$otherplayer_name}</font>, {$l_ship_owned} <font color="white"> {$otherplayer_character_name}. </font><br><br>
{$l_ship_perform}<br><br>
<a href="scan.php?player_id={$player_id}">{$l_planet_scn_link}</a><br>
<a href="attack.php?player_id={$player_id}&ship_id={$ship_id}">{$l_planet_att_link}</a><br>
<a href="mailto.php?to={$player_id}">{$l_send_msg}</a><br>
{/if}
<br>

<a href="main.php">{$l_global_mmenu}</a>
