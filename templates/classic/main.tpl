{if $planetgone}
<br>{$l_nonexistant_pl}<br><br>
{/if}
{if $sectorinfo_star_size > 0}
<div><img src="templates/{$templateset}/images/stars/{$star_image}" alt="{$star_alt}" style="position: absolute; z-index:1; left: 50%; top: 50%; width: 200px; height: 200px; margin-left: -100px; margin-top: -100px;"></div>
{/if}

<div style="text-align:center;">
  <div style="border-width:1px; border-style:solid; margin:0 auto; position:relative; z-index: 2;" class="headingcolor mostwidth mainheader">
    <img src="templates/{$templateset}/images/rank/{$player_insignia}.png" alt="{$player_insignia_name}">
    <span class="textcolor">{$playerinfo_character_name}{$l_abord}<strong><a class="dis2" href="report.php">{$shipinfo_name}</a></strong></span>
  </div>
</div>

<div class="center" style="z-index: 2;">
<table class="center bigger" width="80%" cellpadding="0" cellspacing="1" border="0">

<tr><td align="left">
<span class="textcolor">{$l_turns_have}</span><strong class="highlightcolor">{$playerinfo_turns}</strong>
</td>
<td align="center">
<span class="textcolor">{$l_turns_used}</span><strong class="highlightcolor">{$playerinfo_turns_used}</strong>
</td>
<td align="right">
<span class="textcolor">{$l_score}</span><strong class="highlightcolor">
{if $score_link}
<a class="dis2" href="main.php?command=score">{$playerinfo_score}</a></strong>
{else}
{$playerinfo_score}</strong>
{/if}
</td>
<tr><td align="left">
<span class="textcolor">{$l_sector}: </span><strong class="highlightcolor">{$shipinfo_sector_id}</strong>

</td><td align="center">
{if $beaconhere}
    <strong class="highlightcolor">{$sectorinfo_beacon}</strong>
{/if}

</td><td align="right">

<a class="dis2" href="zoneinfo.php"><strong>{$zoneinfo_zone_name}</strong></a>
</td></tr>
</table><br>
<div class="center">
<table class="center" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>

<td valign="top">

{if $use_gravatar == "Y" && $override_gravatar == "N"}
<div class="center highlightcolor" style="white-space:nowrap;"><img src="templates/{$templateset}/images/lcorner.png" alt=""><strong class="headingcolor">Your Avatar</strong><img src="templates/{$templateset}/images/rcorner.png" alt=""></div>

<div class="mnu">
    <div style="text-align:center;"><img height="80" width="80" alt="Player Avatar" src="http://www.gravatar.com/avatar.php?gravatar_id={$gravatar_id}"></div>
</div>
<br>
{/if}

<div class="center highlightcolor" style="white-space:nowrap;"><img src="templates/{$templateset}/images/lcorner.png" alt=""><strong class="headingcolor">{$l_commands}</strong><img src="templates/{$templateset}/images/rcorner.png" alt=""></div>

<div class="mnu" style="white-space: nowrap;">
  <ul class="nostyle">
    <li><a class="mnu" href="planet_report_menu.php">{$l_planets}</a></li>
    <li><a class="mnu" href="log.php">{$l_log}</a></li>
    <li><a class="mnu" href="defense_report.php">{$l_sector_def}</a></li>
{if $gotmail > 0}
    <li><a class="mnu" href="readmail.php"><font color=red>{$l_read_msg} [{$gotmail}]</font></a></li>
{else}
    <li><a class="mnu" href="readmail.php">{$l_read_msg}</a></li>
{/if}
    <li><a class="mnu" href="mailto.php">{$l_send_msg}</a></li>
    <li><a class="mnu" href="settings.php">{$l_login_settings}</a></li>
    <li><a class="mnu" href="ranking.php">{$l_rankings}</a></li>
    <li><a class="mnu" href="teams.php">{$l_teams}</a></li>
    <li><a class="mnu" href="self_destruct.php">{$l_ohno}</a></li>
    <li><a class="mnu" href="options.php">{$l_options}</a></li>
    <li><a class="mnu" href="navcomp.php">{$l_navcomp}</a></li>
{if $ksm_allowed == true}
    <li><a class="mnu" href="galaxy2.php">{$l_map}</a></li>
{/if}
{if $spy_success_factor}
    <li><a class="mnu" href="spy.php">{$l_spy}</a></li>
{/if}
{if $playerinfo_acl > 127}
    <li><a class="mnu" href="admin.php">{$l_admin_title}</a></li>
{/if}
  </ul>
</div>
<div class="mnu" style="white-space: nowrap;">
  <ul class="nostyle">
    <li><a class="mnu" href="help.php">{$l_help}</a></li>
    <li><a class="mnu" href="faq/index.htm">{$l_faq}</a></li>
    <li><a class="mnu" href="feedback.php">{$l_feedback}</a></li>
{if $link_forums !=''}
    <li><a class="mnu" href="{$link_forums}" target="_blank">{$l_forums}</a></li>
{/if}
  </ul>
</div>

<div class="mnu">
  <ul class="nostyle"><li><a class="mnu" href="logout.php">{$l_logout}</a></li></ul>
</div>
<br>

<div class="center highlightcolor" style="white-space:nowrap;">
  <img src="templates/{$templateset}/images/lcorner.png" alt=""><span class="headingcolor"><strong>{$l_traderoutes}</strong></span><img src="templates/{$templateset}/images/rcorner.png" alt="">
</div>

<div class="mnu">
  <div class="center">
    <ul class="cargostyle">
{if $num_traderoutes == 0}
      <li><a class="mnu" href="traderoute.php">{$l_none}</a></li>
{else}
{section name=index start=0 loop=$traderoutes}
      <li><a class="mnu" href="traderoute.php?engage={$traderoutes[index].traderoute_id}">{$traderoutes[index].description}
{if $traderoutes[index].circuit == '1'}
=&gt;&nbsp;
{else}
&lt;=&gt;&nbsp;
{/if}
{$traderoutes[index].description2}</a></li>
{/section}
{/if}
    </ul>
  </div>
</div>
<div class="mnu">
  <div class="center">
    <ul class="cargostyle">
      <li><a class="mnu" href="traderoute.php">{$l_trade_control}</a></li>
    </ul>
  </div>
</div>

<br>

</td>
<td valign="top" class="maxwidth" style="font-size:1.5em;">
&nbsp;<br>

<div class="center" style="position:relative; z-index: 2;"><strong class="highlightcolor">

{if $portinfo_port_type == 'shipyard'}
<p><strong><a class="dis2" href="shipyard.php">{$l_main_shipyard2}</a><strong></p>
{elseif $portinfo_port_type == "none"}
{$l_tradingport|capitalize}: 
</strong>{$l_none|capitalize}<strong><br><br>
{else}
{$l_tradingport|capitalize}: 
<a class="dis2" href="port.php">{$portinfo_port_type|capitalize}</a>
<br><br>
{/if}
</strong></div>

<div class="center" style="position:relative; z-index: 2;"><strong class="highlightcolor">{$l_planet_in_sec} {$sectorinfo_sector_id} :</strong></div>

<table border="0" width="100%">
  <tr>
{if $num_planets == 0 || $successful_display == 0}
    <td align="center" valign="top">
      <br><div class="highlightcolor" style="position:relative; z-index:2;">{$l_none}</div><br>
   </td>
{else}
{section name=index start=0 loop=$planets}
    <td align="center" valign="top">
      <a href="planet.php?planet_id={$planets[index].planet_id}"><img src="templates/{$templateset}/images/planets/{$planets[index].planet_image}" alt="{$planets[index].planet_image}"></a>
      <div class="highlightcolor">{$planets[index].name}<br>({$planets[index].owner_name})</div><br>
    </td>
{/section}
{/if}
  </tr>
</table>

<div style="position:relative; z-index: 2;">
<div class="center"><strong class="highlightcolor">{$l_ships_in_sec} {$sectorinfo_sector_id} :<br></strong></div>
<table border=0 width="100%">
<tr>

<td align="center" colspan="99" valign="top">
<table width="100%" border="0">
<tr>

{if $shipinfo_sector_id != '0'}
{if $visible_count == 0}
<td align="center">
<br><div class="highlightcolor">{$l_none}</div><br>
</td></tr>
{else}
{section name=index start=0 loop=$visible_ship_array}
<td align="center" valign="top">
{if $visible_ship_array[index].team_name != ''}
<a href="ship.php?player_id={$visible_ship_array[index].player_id}&amp;ship_id={$visible_ship_array[index].ship_id}">
<img src="templates/{$templateset}/images/{$visible_ship_array[index].shipimage}" alt="{$visible_ship_array[index].shipimage_name}">
</a><br><div class="highlightcolor">{$visible_ship_array[index].name}<br>({$visible_ship_array[index].character_name})&nbsp;
({$visible_ship_array[index].team_name})</div>
{else}
<a href="ship.php?player_id={$visible_ship_array[index].player_id}&amp;ship_id={$visible_ship_array[index].ship_id}">
<img src="templates/{$templateset}/images/{$visible_ship_array[index].shipimage}" alt="{$visible_ship_array[index].shipimage_name}">
</a><br><div class="highlightcolor">{$visible_ship_array[index].name}<br>({$visible_ship_array[index].character_name})</div>
{/if}
</td>
{/section}
{/if}
{else}
<td align="center" valign="top">
<br><div class="highlightcolor">{$l_sector_0}</div><br><br></td>
{/if}

</table>
</td>

</tr>
</table>
</div>

<div style="position:relative; z-index: 2;">
{if $shipinfo_sector_id != '0'}
<div class="center"><strong class="highlightcolor">
{$l_lss}:</strong></div>
<table border="0" width="100%">
<tr>
{/if}

{if $shipinfo_sector_id != '0'}
{if !$shipseen}
<td align="center"><br><div class="highlightcolor">{$l_none}</div><br></td>
{elseif $shipinfo_sensors >= $lssd_level_three}
<td align="center"><br><div class="highlightcolor">Player {$shipseen_playername} on board a {$shipseen_classname} class ship traveled to sector <a href="move.php?move_method=real&amp;destination={$shipseen_destination}">{$shipseen_destination}</a></div><br></td>
{elseif $shipinfo_sensors >= $lssd_level_two}
<td align="center"><br><div class="highlightcolor">Player {$shipseen_playername} on board a {$shipseen_classname} class ship. </div><br></td>
{else}
<td align="center"><br><div class="highlightcolor">An unknown {$shipseen_classname} class ship. </div><br></td>
{/if}
</tr>
</table>
{/if}
</div>

<div style="position:relative; z-index: 2;">
{if $num_defense}
<div style="text-align:center;"><strong class="highlightcolor">{$l_sector_def}:<br></strong></div>
<table border="0" width="100%"><tr><td>
{else}
<table border="0" width="100%"><tr><td align="center" valign="top">
{section name=index start=0 loop=$defenses}
<td align="center" valign="top">
{if $defenses[index].defense_type == 'F'}
<a href="modify_defenses.php?defense_id={$defenses[index].defense_id}"><img src="templates/{$templateset}/images/fighters.png" alt="fighters"></a><br>
<div class="highlightcolor">
{$defenses[index].character_name} ({$defenses[index].quantity} {$defenses[index].mode})</div>
{elseif  $defenses[index].defense_type == 'M'}
<a href="modify_defenses.php?defense_id={$defenses[index].defense_id}"><img src="templates/{$templateset}/images/mines.png" alt="mines"></a><br>
<div class="highlightcolor">
{$defenses[index].character_name} ({$defenses[index].quantity} {$defenses[index].mode})</div>
{/if}
</td>
{/section}
{/if}
</tr></table>
<br>
</div>

<!-- foo -->
<td valign="top">

<div class="center highlightcolor" style="white-space:nowrap;">
  <img src="templates/{$templateset}/images/lcorner.png" alt=""><span class="headingcolor"><strong>{$l_cargo}</strong></span><img src="templates/{$templateset}/images/rcorner.png" alt="">
</div>

<div class="mnu">
  <ul class="cargostyle">
    <li><img height="12" width="12" alt="{$l_ore}" src="templates/{$templateset}/images/ore.png">&nbsp;{$l_ore}</li>
    <li class="rightalign">{$shipinfo_ore}</li>
    <li><img height="12" width="12" alt="{$l_organics}" src="templates/{$templateset}/images/organics.png">&nbsp;{$l_organics}</li>
    <li class="rightalign">{$shipinfo_organics}</li>
    <li><img height="12" width="12" alt="{$l_goods}" src="templates/{$templateset}/images/goods.png">&nbsp;{$l_goods}</li>
    <li class="rightalign">{$shipinfo_goods}</li>
    <li><img height="12" width="12" alt="{$l_energy}" src="templates/{$templateset}/images/energy.png">&nbsp;{$l_energy}</li>
    <li class="rightalign">{$shipinfo_energy}</li>
    <li><img height="12" width="12" alt="{$l_colonists}" src="templates/{$templateset}/images/colonists.png">&nbsp;{$l_colonists}</li>
    <li class="rightalign">{$shipinfo_colonists}</li>
    <li><img height="12" width="12" alt="{$l_credits}" src="templates/{$templateset}/images/credits.png">&nbsp;{$l_credits}</li>
    <li class="rightalign">{$playerinfo_credits}</li>
  </ul>
</div>

<br>

<div class="center highlightcolor" style="white-space:nowrap;">
  <img src="templates/{$templateset}/images/lcorner.png" alt=""><span class="headingcolor"><strong>{$l_realspace}</strong></span><img src="templates/{$templateset}/images/rcorner.png" alt="">
</div>

<div class="mnu">
  <div class="center">
    <ul class="nostyle">
      <li>
{if ($shipinfo_sector_id -1) >= 1}
        <a class="mnu" href="move.php?move_method=real&amp;engage=1&amp;destination={$rslink_sector_back}">{$rslink_sector_back}&nbsp;&lt;=</a>
{/if}
{if ($shipinfo_sector_id) < $sector_max}
        <a class="mnu" href="move.php?move_method=real&amp;engage=1&amp;destination={$rslink_sector_forward}">=&gt;&nbsp;{$rslink_sector_forward}</a>
{/if}
      </li>
    </ul>
  </div>
</div>

<div class="mnu">
  <div class="center">
    <form method="post" action="move.php" accept-charset="utf-8" style="margin: 0; padding: 2; white-space:nowrap">
{section name=index start=1 loop=$presetinfo}
      <div style="font-size:1.5em;"><a class="mnu" href="move.php?move_method=real&amp;engage=1&amp;destination={$presetinfo[index]}">=&gt;&nbsp;{$presetinfo[index]}</a>&nbsp;<a class="dis2" href="preset.php">[{$l_set}]</a></div>
{/section}
      <p class="forms">
      <input type="hidden" name="move_method" value="real">
      <input type="text" name="destination" maxlength="7" size="3" class="rsform">
      <input type="submit" name="explore" value="Verify" class="rsform">
      <input type="submit" name="go" value="Go" class="rsform">
      </p>
    </form>
  </div>
</div>
<br>

<div class="center highlightcolor" style="white-space:nowrap;">
  <img src="templates/{$templateset}/images/lcorner.png" alt=""><span class="headingcolor"><strong>{$l_main_warpto}</strong></span><img src="templates/{$templateset}/images/rcorner.png" alt="">
</div>

<div class="center" style="white-space: nowrap;">
  <div class="mnu">
    <div class="center">
      <ul class="nostyle">
        <li><a class="dis" href="lrscan.php?sector=*">[{$l_fullscan}]</a></li>
      </ul>
    </div>
    <ul class="nostyle">
{section name=index start=0 loop=$links}
{if $links[index].dest > $sectorinfo_sector_id}
      <li><a class="mnu" href="move.php?move_method=warp&amp;destination={$links[index].dest}">{$links[index].ways}&gt;&nbsp;{$links[index].dest}</a>
          <a class="dis2" href="lrscan.php?sector={$links[index].dest}">[{$l_scan}]{$links[index].known}</a></li>
{else}
      <li><a class="mnu" href="move.php?move_method=warp&amp;destination={$links[index].dest}">&lt;{$links[index].ways}&nbsp;{$links[index].dest}</a>
          <a class="dis2" href="lrscan.php?sector={$links[index].dest}">[{$l_scan}]{$links[index].known}</a></li>
{/if}
{/section}
{if $num_links==0}
      <li><a class="dis2">{$l_no_warplink}</a></li><br>
{/if}
    </ul>
  </div>
  <div class="mnu">
    <div class="center">
      <form method="post" action="move.php" accept-charset="utf-8" style="margin: 0; padding: 2; white-space:nowrap">
      <p class="forms">
      <input type="hidden" name="move_method" value="navcomp">
      <input type="text" name="destination" maxlength="7" size="3" class="rsform">
      <input type="submit" name="explore" value="Verify" class="rsform">
      <input type="submit" name="go" value="Go" class="rsform">
      <input name="state" value="1" type="hidden">
      </p>
      </form>
    </div>
  </div>
</div>
<br>

{if $plasma_engines}
<div class="center smalltext highlightcolor" style="white-space:nowrap;">
  <img src="templates/{$templateset}/images/lcorner.png" alt=""><span class="headingcolor"><strong>{$l_plasma}</strong></span><img src="templates/{$templateset}/images/rcorner.png" alt="">
</div>

<div class="mnu">
  <form method="post" action="move.php" accept-charset="utf-8" style="margin: 0; padding: 2; white-space:nowrap">
      <p class="forms">
      <input type="hidden" name="move_method" value="plasma">
      <input type="text" name="destination" class="rsform" maxlength="7" size="3">&nbsp;
      <input type="submit" name="explore" value="Verify" class="rsform">&nbsp;
      <input type="submit" name="go" value="Go" class="rsform">
      </p>
  </form>
</div>
{/if}
</table>
</div>
</div>

