{literal}
if (form.total_cost.value > {/literal}{$playerinfo_credits}{literal})
{
    form.total_needed.value = form.total_cost.value - {/literal}{$playerinfo_credits}{literal};
    form.total_cost.value = "{/literal}{$l_no_credits}{literal}";
}
else
{
    form.total_needed.value = "";
}
{/literal}

form.total_cost.length = form.total_cost.value.length;
form.engine_costper.value=changeDelta(form.engine_upgrade.value,{$shipinfo_engines});

{if $plasma_engines}
form.pengine_costper.value=changeDelta(form.pengine_upgrade.value,{$shipinfo_pengines});
{/if}
form.power_costper.value=changeDelta(form.power_upgrade.value,{$shipinfo_power});
form.computer_costper.value=changeDelta(form.computer_upgrade.value,{$shipinfo_computer});
form.sensors_costper.value=changeDelta(form.sensors_upgrade.value,{$shipinfo_sensors});
form.beams_costper.value=changeDelta(form.beams_upgrade.value,{$shipinfo_beams});
form.armor_costper.value=changeDelta(form.armor_upgrade.value,{$shipinfo_armor});
form.cloak_costper.value=changeDelta(form.cloak_upgrade.value,{$shipinfo_cloak});
form.torp_launchers_costper.value=changeDelta(form.torp_launchers_upgrade.value,{$shipinfo_torp_launchers});
form.hull_costper.value=changeDelta(form.hull_upgrade.value,{$shipinfo_hull});
form.shields_costper.value=changeDelta(form.shields_upgrade.value,{$shipinfo_shields});
}
 -->
</script>

<p>
{$l_creds_to_spend}<br>
{if $allow_ibank}
{$l_ifyouneedmore}<br>
{/if}
<a href="bounty.php">{$l_by_placebounty}</a><br>
 <form action="port2.php" method="post" accept-charset="utf-8">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr bgcolor="{$color_header}">
    <td><strong>{$l_ship_levels}</strong></td>
    <td><strong>{$l_cost}</strong></td>
    <td><strong>{$l_current_level}</strong></td>
    <td><strong>{$l_upgrade}</strong></td>
   </tr>
   <tr bgcolor="{$color_line1}">
    <td>{$l_hull}</td>
    <td><input type="text" readonly="readonly" class="portcosts1" name="hull_costper" value="0" disabled="disabled" {$onblur}></td>
    <td>{$number_shipinfo_hull}</td>
    <td>
<select size="1" name="hull_upgrade" onchange="counttotal()">
{html_options options=$hulloptions selected=$hullselected}</select>
    </td>
   </tr>
   <tr bgcolor="{$color_line2}">
    <td>{$l_engines}</td>
    <td><input type="text" readonly="readonly" class="portcosts2" name="engine_costper" value="0" disabled="disabled" {$onblur}></td>
    <td>{$number_shipinfo_engines}</td>
    <td>
<select size="1" name="engine_upgrade" onchange="counttotal()">
{html_options options=$engineoptions selected=$engineselected}</select>
    </td>
   </tr>

{if $plasma_engines}
   <tr bgcolor="{$color_line1}">
    <td>{$l_pengines}</td>
    <td><input type="text" readonly="readonly" class="portcosts1" name="pengine_costper" value="0" disabled="disabled" {$onblur}></td>
    <td>{$number_shipinfo_pengines}</td>
    <td>
<select size="1" name="pengine_upgrade" onchange="counttotal()">
{html_options options=$pengineoptions selected=$pengineselected}</select>
    </td>
   </tr>
{/if}
   <tr bgcolor="{$color_line2}">
    <td>{$l_power}</td>
    <td><input type="text" readonly="readonly" class="portcosts2" name="power_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_power}</td>
    <td>
<select size="1" name="power_upgrade" onchange="counttotal()">
{html_options options=$poweroptions selected=$powerselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line1}">
    <td>{$l_computer}</td>
    <td><input type="text" readonly="readonly" class="portcosts1" name="computer_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_computer}</td>
    <td>
<select size="1" name="computer_upgrade" onchange="counttotal()">
{html_options options=$computeroptions selected=$computerselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line2}">
    <td>{$l_sensors}</td>
    <td><input type="text" readonly="readonly" class="portcosts2" name="sensors_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_sensors}</td>
    <td>
<select size="1" name="sensors_upgrade" onchange="counttotal()">
{html_options options=$sensorsoptions selected=$sensorsselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line1}">
    <td>{$l_beams}</td>
    <td><input type="text" readonly="readonly" class="portcosts1" name="beams_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_beams}</td>
    <td>
<select size="1" name="beams_upgrade" onchange="counttotal()">
{html_options options=$beamsoptions selected=$beamsselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line2}">
    <td>{$l_armor}</td>
    <td><input type="text" readonly="readonly" class="portcosts2" name="armor_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_armor}</td>
    <td>
<select size="1" name="armor_upgrade" onchange="counttotal()">
{html_options options=$armoroptions selected=$armorselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line1}">
    <td>{$l_cloak}</td>
    <td><input type="text" readonly="readonly" class="portcosts1" name="cloak_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_cloak}</td>
    <td>
<select size="1" name="cloak_upgrade" onchange="counttotal()">
{html_options options=$cloakoptions selected=$cloakselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line2}">
    <td>{$l_torp_launch}</td>
    <td><input type="text" readonly="readonly" class="portcosts2" name="torp_launchers_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_torp_launchers}</td>
    <td>
<select size="1" name="torp_launchers_upgrade" onchange="counttotal()">
{html_options options=$tloptions selected=$tlselected}</select>
    </td>
  </tr>
  <tr bgcolor="{$color_line1}">
    <td>{$l_shields}</td>
    <td><input type="text" readonly="readonly" class="portcosts1" name="shields_costper" value="0" disabled="disabled" onblur="counttotal()"></td>
    <td>{$number_shipinfo_shields}</td>
    <td>
<select size="1" name="shields_upgrade" onchange="counttotal()">
{html_options options=$shieldoptions selected=$shieldselected}</select>
    </td>
  </tr>
 </table>
 <br>
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr bgcolor="{$color_header}">
    <td><strong>{$l_item}</strong></td>
    <td><strong>{$l_cost}</strong></td>
    <td><strong>{$l_current}</strong></td>
    <td><strong>{$l_max}</strong></td>
    <td><strong>{$l_qty}</strong></td>
    <td><strong>{$l_item}</strong></td>
    <td><strong>{$l_cost}</strong></td>
    <td><strong>{$l_current}</strong></td>
    <td><strong>{$l_max}</strong></td>
    <td><strong>{$l_qty}</strong></td>
  </tr>
  <tr bgcolor="{$color_line1}">
    <td>{$l_fighters}</td>
    <td>{$number_fighter_price}</td>
    <td>{$number_shipinfo_fighters} / {$number_fighter_max}</td>
    <td>

{if $shipinfo_fighters != $fighter_max}
<a href="#" onclick="MakeMax('fighter_number', {$fighter_free});counttotal();return false;" onblur="counttotal()">{$number_fighter_free}</a></td>
<td><input type="text" name="fighter_number" size="6" maxlength="30" value="0" onblur="counttotal()">
{else}
0<td><input type="text" readonly="readonly" class="portcosts1" name="fighter_number" maxlength="30" value="{$l_full}" onblur="counttotal()" disabled="disabled">
{/if}

    </td>
    <td>{$l_torps}</td>
    <td>{$number_torpedo_price}</td>
    <td>{$number_shipinfo_torps} / {$number_torpedo_max}</td>
    <td>

{if $shipinfo_torps != $torpedo_max}
<a href="#" onclick="MakeMax('torpedo_number', {$torpedo_free});counttotal();return false;" onblur="counttotal()">{$number_torpedo_free}</a></td>
<td><input type="text" name="torpedo_number" size="6" maxlength="30" value="0" onblur="counttotal()">
{else}
0<td><input type="text" readonly="readonly" class="portcosts1" name="torpedo_number" maxlength="30" value="{$l_full}" onblur="counttotal()" disabled="disabled">
{/if}
</td>
  </tr>
<!-- Armor points -->
  <tr bgcolor="{$color_line2}">
    <td>{$l_armorpts}</td>
    <td>{$number_armor_price}</td>
    <td>{$number_shipinfo_armor_pts} / {$number_armor_max}</td>
    <td>
{if $shipinfo_armor_pts != $armor_max}
<a href="#" onclick="MakeMax('armor_number', {$armor_free});counttotal();return false;" onblur="counttotal()">{$number_armor_free}</a></td>
<td><input type="text" name="armor_number" size="6" maxlength="30" value="0" onblur="counttotal()">
{else}
0<td><input type="text" readonly="readonly" class="portcosts2" name="armor_number" maxlength="30" value="{$l_full}" disabled="disabled" onblur="counttotal()">
{/if}
    </td>
<!-- End armor points -->
<!-- Colonists -->
    <td>{$l_colonists}</td>
    <td>{$number_colonist_price}</td>
    <td>{$number_shipinfo_colonists} / {$number_colonist_max}</td>
    <td>
{if $shipinfo_colonists != $colonist_max}
<a href="#" onclick="MakeMax('colonist_number', {$colonist_free});counttotal();return false;" onblur="counttotal()">{$number_colonist_free}</a></td>
<td><input type="text" name="colonist_number" size="6" maxlength="30" value="0" onblur="counttotal()">
{else}
0<td><input type="text" readonly="readonly" class="portcosts2" name="colonist_number" maxlength="30" value="{$l_full}" disabled="disabled" onblur="counttotal()">
{/if}
    </td>
<!-- End colonists -->
  </tr>
 </table>
<br>
<!-- Submit section -->
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="submit" value="{$l_buy}" onclick="counttotal()"></td>
    <td align="right">
       {$l_credits_needed} <input type="text" readonly="readonly" style="text-align:right" name="total_needed" size="25" value="0"> &nbsp;
       {$l_totalcost}: <input type="text" style="text-align:right" name="total_cost" size="25" value="0" onblur="counttotal()" onfocus="counttotal()" onchange="counttotal()" onclick="counttotal()">
   </td>
  </tr>
 </table>
<!-- End submit section -->
</form>
<a href="dump.php">{$l_would_dump}</a>.
