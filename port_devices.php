<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: port_devices.php

include_once ("./global_includes.php");
//direct_test(__FILE__, $_SERVER['PHP_SELF']);

    $title=$l_device_port_title;
    echo "<h1>" . $title. "</h1>\n";
    if (isLoanPending($playerinfo['player_id']))
    {
        echo "$l_port_loannotrade<p>";
        echo "<a href=\"igb_login.php\">$l_igb_term</a><p>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
    }

    $res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM {$db->prefix}bounty WHERE placed_by = 0 AND bounty_on = $playerinfo[player_id]");
    if ($res2)
    {
        $bty = $res2->fields;
        if ($bty['total_bounty'] > 0)
        {
            if ($pay != 1)
            {
                echo $l_port_bounty . "<br>";
                $l_port_bounty2 = str_replace("[amount]",number_format($bty['total_bounty'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_port_bounty2);
                echo "<a href=\"port.php?pay=1\">" . $l_port_bounty2 . "</a><br>";
                echo "<a href=\"bounty.php\">$l_by_placebounty</a><br><br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                die();
            }
            else
            {
                if ($playerinfo['credits'] < $bty['total_bounty'])
                {
                    $l_port_btynotenough = str_replace("[amount]",number_format($bty['total_bounty'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_port_btynotenough);
                    echo $l_port_btynotenough . "<br>";
                    global $l_global_mmenu;
                    echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                    die();
                }
                else
                {
                    $debug_query = $db->Execute("UPDATE {$db->prefix}players SET credits=credits-$bty[total_bounty] WHERE player_id = $playerinfo[player_id]");
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    $debug_query = $db->Execute("DELETE from {$db->prefix}bounty WHERE bounty_on = $playerinfo[player_id] AND placed_by = 0");
                    db_op_result($db,$debug_query,__LINE__,__FILE__);

                    echo $l_port_bountypaid . "<br><a href=\"port.php\">" . $l_port_bountypaid2 . "</a><br>";
                    die();
                }
            }
        }
    }

    $emerwarp_free = $max_emerwarp - $shipinfo['dev_emerwarp'];

    echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";
    echo "\n<script type=\"text/javascript\" defer=\"defer\">\n";
    echo "<!--\n";

    echo "function MakeMax(name, val)\n";
    echo "{\n";
    echo " if (document.forms[0].elements[name].value != val)\n";
    echo " {\n";
    echo "  if (val != 0)\n";
    echo "  {\n";
    echo "  document.forms[0].elements[name].value = val;\n";
    echo "  }\n";
    echo " }\n";
    echo "}\n";

    // changeDelta function //
    echo "function changeDelta(desiredvalue,currentvalue)\n";
    echo "{\n";
    echo "  Delta=0; DeltaCost=0;\n";
    echo "  Delta = desiredvalue - currentvalue;\n";
    echo "\n";
    echo "    while (Delta>0) \n";
    echo "    {\n";
    echo "     DeltaCost=DeltaCost + Math.pow($upgrade_factor,desiredvalue-Delta); \n";
    echo "     Delta=Delta-1;\n";
    echo "    }\n";
    echo "\n";
    echo "  DeltaCost=DeltaCost * $upgrade_cost\n";
    echo "  return Math.round(DeltaCost*Math.pow(10,0))/Math.pow(10,0);\n";
    echo "}\n";

    echo "function counttotal()\n";
    echo "{\n";
    echo "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n";
    echo "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n";
    echo "<script type=\"text/javascript\" defer=\"defer\" src=\"backends/javascript/clean_forms.js\"></script><noscript></noscript>";
    echo "var form = document.forms[0];\n";
    echo "var i = form.elements.length;\n";
    echo "while (i > 0)\n";
    echo "  {\n";
    echo " if (form.elements[i-1].value == '')\n";
    echo "  {\n";
    echo "  form.elements[i-1].value ='0';\n";
    echo "  }\n";
    echo " i--;\n";
    echo "}\n";
    echo "// Here we set all 'Max' items to 0 if they are over max - player amt.\n";
    echo "if (($emerwarp_free < form.dev_emerwarp_number.value) && (form.dev_emerwarp_number.value != 'Full'))\n";
    echo " {\n";
    echo " form.dev_emerwarp_number.value=0\n";
    echo " }\n";
    echo "// Done with the bounds checking\n";
    echo "// Pluses must be first, or if empty will produce a javascript error\n";
    echo "form.total_cost.value = form.dev_genesis_number.value * $dev_genesis_price \n";
    if ($emerwarp_free > 0)
    {
        echo "+ form.dev_emerwarp_number.value * $dev_emerwarp_price\n";
    }

    echo "+ form.dev_warpedit_number.value * $dev_warpedit_price\n";
    echo "+ form.elements['dev_minedeflector_number'].value * $dev_minedeflector_price\n";

    if ($spy_success_factor)
    {
        echo "+ form.elements['spy_number'].value * $spy_price\n";
    }

    if ($shipinfo['dev_escapepod'] == 'N')
    {
        echo "+ (form.escapepod_purchase.checked ?  $dev_escapepod_price : 0)\n";
    }

    if ($shipinfo['dev_fuelscoop'] == 'N')
    {
        echo "+ (form.fuelscoop_purchase.checked ?  $dev_fuelscoop_price : 0)\n";
    }

    echo ";\n";
    echo "  if (form.total_cost.value > $playerinfo[credits])\n";
    echo "  {\n";
    echo "    form.total_needed.value = form.total_cost.value - $playerinfo[credits];\n";
    echo "    form.total_cost.value = '$l_no_credits';\n";
//    echo "    form.total_cost.value = '$l_no_credits';\n";
    echo "  }\n";
    echo "  else\n";
    echo "  {\n";
    echo "    form.total_needed.value = '';\n";
    echo "  }\n";
    echo "  form.total_cost.length = form.total_cost.value.length;\n";
    echo "\n";
    echo "}";
    echo "\n// -->\n";
    echo "</script>\n";

    $onblur = "onblur=\"counttotal()\"";
    $onfocus =  "onfocus=\"counttotal()\"";
    $onchange =  "onchange=\"counttotal()\"";
    $onclick =  "onclick=\"counttotal()\"";

    echo "<p>\n";
    $l_creds_to_spend=str_replace("[credits]",number_format($playerinfo['credits'], 0, $local_number_dec_point, $local_number_thousands_sep),$l_creds_to_spend);
    echo "$l_creds_to_spend<br>\n";
    if ($allow_ibank)
    {
        $igblink = "\n<a href=\"igb_login.php\">$l_igb_term</a>";
        $l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);
        echo "$l_ifyouneedmore<br>";
    }

    echo "\n";
    echo "<a href=\"bounty.php\">$l_by_placebounty</a><br>\n";
    echo ' <form name="bntform" action="port2.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">\n';
    echo "  <table width=\"100%\" border=0 cellspacing=0 cellpadding=0>\n";
    echo "   <tr bgcolor=\"$color_header\">\n";
    echo "    <td><strong>$l_device</strong></td>\n";
    echo "    <td><strong>$l_cost</strong></td>\n";
    echo "    <td><strong>$l_current</strong></td>\n";
    echo "    <td><strong>$l_max</strong></td>\n";
    echo "    <td><strong>$l_qty</strong></td>\n";
    echo "    <td></td>\n";
    echo "    <td></td>\n";
    echo "    <td></td>\n";
    echo "    <td></td>\n";
    echo "   </tr>\n";
    echo "   <tr bgcolor=\"$color_line1\">\n";
    echo "    <td>$l_genesis</td>\n";
    echo "    <td>" . number_format($dev_genesis_price, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>" . number_format($shipinfo['dev_genesis'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>$l_unlimited</td>\n";
    echo "    <td><input type=text name=dev_genesis_number value=0 $onblur></td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "   </tr>\n";
    echo "   <tr bgcolor=\"$color_line2\">\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "   </tr>\n";
    echo "   <tr bgcolor=\"$color_line1\">\n";
    echo "    <td>$l_ewd</td>\n";
    echo "    <td>" . number_format($dev_emerwarp_price, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>" . number_format($shipinfo['dev_emerwarp'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>";
    if ($shipinfo['dev_emerwarp'] != $max_emerwarp)
    {
        echo"<a href='#' onclick=\"MakeMax('dev_emerwarp_number', $emerwarp_free);counttotal();return false;\">";
        echo number_format($emerwarp_free, 0, $local_number_dec_point, $local_number_thousands_sep) . "</a></td>\n";
        echo"    <td><input type=text name=dev_emerwarp_number value=0 $onblur>";
    }
    else
    {
        echo "0</td>\n";
        echo "    <td><input type=text readonly=readonly class='portcosts1' name=dev_emerwarp_number value=$l_full $onblur disabled='disabled'>";
    }

    echo "</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor=\"$color_line2\">\n";
    echo "    <td>$l_warpedit</td>\n";
    echo "    <td>" . number_format($dev_warpedit_price, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>" . number_format($shipinfo['dev_warpedit'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td><td>$l_unlimited</td><td><input type=text name=dev_warpedit_number value=0 $onblur></td>";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor=\"$color_line1\">\n";
    if ($spy_success_factor)
    {
        echo "    <td>$l_spy</td>\n";
        echo "    <td>". number_format($spy_price, 0, $local_number_dec_point, $local_number_thousands_sep) ."</td>\n";
        $res = $db->Execute("SELECT count(spy_id) as spy_num from {$db->prefix}spies WHERE owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id]");
        $spy_num = number_format($res->fields['spy_num'], 0, $local_number_dec_point, $local_number_thousands_sep);
        echo "    <td>$spy_num</td>\n";
        echo "    <td>$l_unlimited</td>\n";
        echo "    <td><input type=text name=spy_number value=0 $onblur></td>\n";
    }
    else
    {
        echo "    <td>&nbsp;</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td>&nbsp;</td>\n";
    }

    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "  </tr>";
    echo "  <tr bgcolor=\"$color_line2\">\n";
    echo "    <td>$l_deflect</td>\n";
    echo "    <td>" . number_format($dev_minedeflector_price, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>" . number_format($shipinfo['dev_minedeflector'], 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    echo "    <td>$l_unlimited</td>\n";
    echo "    <td><input type=text name=dev_minedeflector_number value=0 $onblur></td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor=\"$color_line1\">\n";
    echo "    <td>$l_escape_pod</td>\n";
    echo "    <td>" . number_format($dev_escapepod_price, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    if ($shipinfo['dev_escapepod'] == "N")
    {
        echo "    <td>$l_none</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td><input type=checkbox name=escapepod_purchase value=1 $onclick></td>\n";
    }
    else
    {
        echo "    <td>$l_equipped</td>\n";
        echo "    <td></td>\n";
        echo "    <td>$l_n_a</td>\n";
    }

    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo "  <tr bgcolor=\"$color_line2\">\n";
    echo "    <td>$l_fuel_scoop</td>\n";
    echo "    <td>" . number_format($dev_fuelscoop_price, 0, $local_number_dec_point, $local_number_thousands_sep) . "</td>\n";
    if ($shipinfo['dev_fuelscoop'] == "N")
    {
        echo "    <td>$l_none</td>\n";
        echo "    <td>&nbsp;</td>\n";
        echo "    <td><input type=checkbox name=fuelscoop_purchase value=1 $onclick></td>\n";
    }
    else
    {
        echo "    <td>$l_equipped</td>\n";
        echo "    <td></td>\n";
        echo "    <td>$l_n_a</td>\n";
    }

    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "    <td>&nbsp;</td>\n";
    echo "  </tr>\n";
    echo " </table>\n";
    echo " <br>\n";
    echo " <table width=\"100%\" border=0 cellspacing=0 cellpadding=0>\n";
    echo "  <tr>\n";
    echo "    <td><input type=submit name=submit_button value=$l_buy $onclick></td>\n";
//    echo "    <td align=right>$l_totalcost: <input type=text style=\"text-align:right\" name=total_cost SIZE=22 value=0 $onfocus $onblur $onchange $onclick></td>\n";
    echo "    <td align=right>Credits needed: <input type=text readonly=readonly tabindex='-1' style=\"text-align:right\" name=total_needed SIZE=25> &nbsp; ".
         "$l_totalcost: <input type=text style=\"text-align:right\" name=total_cost SIZE=25 value=0 $onfocus $onblur $onchange $onclick></td>\n";
    echo "  </tr>\n";
    echo " </table>\n";
    echo "</form>\n";
    echo "<a href=dump.php>$l_would_dump</a>.\n";
    if ($spy_success_factor)
    {
        echo "<br><br><a href=spy_cleanup_ship.php>$l_spy_cleanupship</a>.";
    }
?>
