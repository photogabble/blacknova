<div style="text-align:center;">
<table border="0" cellspacing="0" cellpadding="0" width="90%">
    <tr>
        <td width="65%">
            <font style="font-size: 1.5em;" color="white"><strong>{$shipname}</strong></font><br>
            <font><strong>{$classname}</strong></font>
            <br>
            <font style="font-size: 0.8em;"><strong>{$classdescription}</strong></font>
            <br><br>
        </td>
        <td width="35%" align="center" valign="middle"><img src="templates/{$templateset}/images/{$classimage}" alt="shipclass image">
        </td>
    </tr>
</table>
<br>

<table border="0" cellspacing="0" cellpadding="0" width="90%">
    <tr>
        <td width="50%"><font color="white"><strong>{$l_ship_levels}</strong></font><br>
        </td>
    </tr>
    <tr>
        <td>
            <table border="0" cellspacing="0" cellpadding="3">
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_hull}&nbsp;<font color="white">({$shipinfo_hull} / {$classinfo_maxhull})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$hull_bars}
                    </td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_engines}&nbsp;<font color="white">({$shipinfo_engines} / {$classinfo_maxengines})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$engines_bars}
                    </td>
                </tr>
{if $plasma_engines}
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_pengines}&nbsp;<font color="white">({$shipinfo_pengines} / {$classinfo_maxpengines})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$pengines_bars}
                    </td>
                </tr>
{/if}
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_power}&nbsp;<font color="white">({$shipinfo_power} / {$classinfo_maxpower})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$power_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;" color="red"><strong>{$l_computer}&nbsp;({$shipinfo_computer} / {$classinfo_maxcomputer})&nbsp;&nbsp;</strong></font>
                    </td>
                    <td valign="bottom">{$computer_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_sensors}&nbsp;<font color="white">({$shipinfo_sensors} / {$classinfo_maxsensors})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$sensors_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;" color=yellow><strong>{$l_avg_stats}&nbsp;<font color="white">({$average_stats} / {$average_stats_max})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$average_bars}</td>
                </tr>
            </table>
        </td>
        <td width="50%">
            <table border="0" cellspacing="0" cellpadding=3>
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_armor}&nbsp;<font color="white">({$shipinfo_armor} / {$classinfo_maxarmor})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$armor_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_shields}&nbsp;<font color="white">({$shipinfo_shields} / {$classinfo_maxshields})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$shields_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;" color="red"><strong>{$l_beams}&nbsp;({$shipinfo_beams} / {$classinfo_maxbeams})&nbsp;&nbsp;</strong></font>
                    </td>
                    <td valign="bottom">{$beams_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;" color="red"><strong>{$l_torp_launch}&nbsp;({$shipinfo_torp_launchers} / {$classinfo_maxtorp_launchers})&nbsp;&nbsp;</strong></font>
                    </td>
                    <td valign="bottom">{$torp_launchers_bars}</td>
                </tr>
                <tr>
                    <td><font style="font-size: 0.8em;"><strong>{$l_cloak}&nbsp;<font color="white">({$shipinfo_cloak} / {$classinfo_maxcloak})&nbsp;&nbsp;</font></strong></font>
                    </td>
                    <td valign="bottom">{$cloak_bars}</td>
                </tr>
                <tr>
                    <td>&nbsp;<td valign="bottom"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" width="90%">
  <tr>
    <td width="33%"><font color="white"><strong>{$l_holds}</strong></font><br>
    </td>
    <td width="33%"><font color="white"><strong>{$l_arm_weap}</strong></font><br>
    </td>
    <td width="33%"><font color="white"><strong>{$l_devices}</strong></font><br>
    </td>
  </tr>   

  <tr>
    <td valign=top>

    <table border="0" cellspacing="0" cellpadding=3>
      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/credits.png" alt="credits">&nbsp;{$l_credits}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_credits}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        {$l_total_cargo}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$holds_used} / {$holds_max}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/ore.png" alt="ore">&nbsp;{$l_ore}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_ore}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/organics.png" alt="organics">&nbsp;{$l_organics}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_organics}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/goods.png" alt="goods">&nbsp;{$l_goods}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_goods}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/colonists.png" alt="colonists">&nbsp;{$l_colonists}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_colonists}
        </strong></font>
        </td>
      </tr>
      
{if $spy_success_factor}
      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/spy.png" alt="spy">&nbsp;{$l_spy}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$ship_spies}
        </strong></font>
        </td>
      </tr>
{/if}
      
    </table>

  </td><td valign=top>

    <table border="0" cellspacing="0" cellpadding=3>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/energy.png" alt="energy">&nbsp;{$l_energy}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_energy} / {$energy_max}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/tfighter.png" alt="fighters">&nbsp;<a 
href="mines.php?op=2">{$l_fighters}</a>&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_fighters} / {$ship_fighters_max}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img src="templates/{$templateset}/images/torp.png" alt="torp">&nbsp;<a 
href="mines.php?op=1">{$l_mines}/{$l_torps}</a>&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_torps} / {$torps_max}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        &nbsp;<img 
src="templates/{$templateset}/images/armor.png" alt="armor">&nbsp;{$l_armorpts}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_armor_pts} / {$armor_pts_max}
        </strong></font>
        </td>
      </tr>

    </table>

  <td valign=top>

    <table border="0" cellspacing="0" cellpadding=3>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        <a href=warpedit.php>{$l_warpedit}</a>&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_dev_warpedit}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        <a href=genesis.php>{$l_genesis}</a>&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_dev_genesis}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        {$l_deflect}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_dev_minedeflector}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        <a href=emerwarp.php>{$l_ewd}</a>&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>
        <font color="white"><strong>
        {$shipinfo_dev_emerwarp}
        </strong></font>
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        {$l_escape_pod}&nbsp;&nbsp;&nbsp;
        </strong></font> 
        <td>

{if $shipinfo_dev_escapepod == 'Y'}
        <font color="#00ff00"><strong>
        {$l_installed}
        </strong></font>
{else}
        <font color="#ff0000"><strong>
        {$l_not_installed}
        </strong></font>
{/if}
        </td>
      </tr>

      <tr>
        <td>
        <font style="font-size: 0.8em;"><strong>
        {$l_fuel_scoop}&nbsp;&nbsp;&nbsp;
        </strong></font>
        <td>

{if $shipinfo_dev_fuelscoop == 'Y'}
        <font color="#00FF00"><strong>
        {$l_installed}
        </strong></font>
{else}
        <font color="#FF0000"><strong>
        {$l_not_installed}
        </strong></font>
{/if}
        </td>
      </tr>

    </table>

  </td></tr>

</table>

</div>

{if $sid_isset}
<br><a href="spy.php">{$l_clickme}</a> {$l_spy_linkback}<br>
{/if}

<a href="main.php">{$l_global_mmenu}</a>

