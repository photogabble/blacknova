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
// File: bounty.php
include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "gen_score.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'ports');
load_languages($db, $raw_prefix, 'bounty');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_by_title;
updatecookie($db);
include_once ("./header.php");

if (!isset($response))
{
    $response = '';
}

//-------------------------------------------------------------------------------------------------

switch ($response) 
{
    case '1': // Display
        echo "<h1>" . $title. "</h1>\n";
        $res5 = $db->Execute("SELECT * FROM {$db->prefix}players,{$db->prefix}bounty WHERE bounty_on = player_id AND bounty_on=?", array($bounty_on));
        $j = 0;
        if ($res5)
        {
            while (!$res5->EOF)
            {
                $bounty_details[$j] = $res5->fields;
                $j++;
                $res5->MoveNext();
            }
        }

        $num_details = $j;
        if ($num_details < 1)
        {
            echo "$l_by_nobounties<br>";
        }
        else
        {
            echo "$l_by_bountyon " . $bounty_details[0]['character_name'];
            echo '<table border=1 cellspacing=1 cellpadding=2 width="50%" align=center>';
            echo "<tr bgcolor=\"$color_header\">";
            echo "<td><strong>$l_amount</td>";
            echo "<td><strong>$l_by_placedby</td>";
            echo "<td><strong>$l_by_action</td>";
            echo "<td><strong>$l_by_reason</td>";
            echo "</tr>";
            $color = $color_line1;
            for ($j=0; $j<$num_details; $j++)
            {
                $someres = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id =?", array($bounty_details[$j]['placed_by']));
                $details = $someres->fields;
                echo "<tr bgcolor=\"$color\">";
                echo "<td>&nbsp;" . number_format($bounty_details[$j]['amount'], 0, $local_number_dec_point, $local_number_thousands_sep) . "&nbsp;</td>";
                if ($bounty_details[$j]['placed_by'] == 0)
                {
                    echo "<td>$l_by_thefeds</td>";
                }
                else
                {
                    echo "<td>" . $details['character_name'] . "</td>";
                }
                if ($bounty_details[$j]['placed_by'] == $playerinfo['player_id'])
                {
                    echo "<td><a href=bounty.php?bid=" . $bounty_details[$j]['bounty_id'] . "&response=2>$l_by_cancel</a></td>";
                }
                else
                {
                    echo "<td> </td>";
                }
                echo "<td>" . $bounty_details[$j]['bounty_reason'] . "</td>";
                echo "</tr>";

                if ($color == $color_line1)
                {
                    $color = $color_line2;
                }
                else
                {
                    $color = $color_line1;
                }
            }
            echo "</table>";
        }
        break;

    case '2': // Cancel
        echo "<h1>" . $title. "</h1>\n";
        if ($playerinfo['turns'] <1 )
        {
            echo "$l_by_noturn<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        $res = $db->Execute("SELECT * from {$db->prefix}bounty WHERE bounty_id=?", array($bid));
        if (!$res)
        {
            echo "$l_by_nobounty<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }
        $bty = $res->fields;

        if ($bty['placed_by'] != $playerinfo['player_id'])
        {
            echo "$l_by_notyours<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        $del = $db->Execute("DELETE FROM {$db->prefix}bounty WHERE bounty_id=?", array($bid));
        $refund = $bty['amount'];
        $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1, credits=credits+? WHERE player_id=?", array($refund, $playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        echo "$l_by_canceled<br>";
        global $l_global_mmenu;
        echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
        include_once ("./footer.php");
        die();
        break;

    case '3': // Place
        echo "<h1>" . $title. "</h1>\n";
        $bounty_on = preg_replace('/[^0-9]/','',$bounty_on);
        $ex = $db->Execute("SELECT * from {$db->prefix}players LEFT JOIN {$db->prefix}ships " .
                           "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id " .
                           "WHERE {$db->prefix}ships.destroyed='N' AND {$db->prefix}players.player_id=?", array($bounty_on));
        if (!$ex)
        {
            echo "$l_by_notexists<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        $bty = $ex->fields;
        if ($bty['destroyed'] == "Y")
        {
            echo "$l_by_destroyed<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        if ($playerinfo['turns']<1 )
        {
            echo "$l_by_noturn<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        $amount = preg_replace('/[^0-9]/','',$amount);
        if ($amount <= 0)
        {
            echo "$l_by_zeroamount<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        if ($bounty_on == $playerinfo['player_id'])
        {
            echo "$l_by_yourself<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

        if ($amount > $playerinfo['credits'])
        {
            echo "$l_by_notenough<br><br>";
            global $l_global_mmenu;
            echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
            include_once ("./footer.php");
            die();
        }

       if ($bounty_maxvalue != 0)
       {
            $percent = $bounty_maxvalue * 100;
            $score = gen_score($db,$playerinfo['player_id']);
            $maxtrans = floor($score * $score * $bounty_maxvalue);
            echo "Maximum bounty available to place would be: ". number_format($maxtrans, 0, $local_number_dec_point, $local_number_thousands_sep) ."<br>";
            $previous_bounty = 0;
            $pb = $db->Execute("SELECT SUM(amount) AS totalbounty FROM {$db->prefix}bounty WHERE bounty_on=? AND placed_by=?", array($bounty_on, $playerinfo['player_id']));
            if ($pb)
            {
                $prev = $pb->fields;
                $previous_bounty = $prev['totalbounty'];
            }
            if ($amount + $previous_bounty > $maxtrans)
            {
                $l_by_toomuch = str_replace("[percent]", $percent, $l_by_toomuch);
                echo "$l_by_toomuch<br><br>";
                global $l_global_mmenu;
                echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
                include_once ("./footer.php");
                die();
            }

      }

      $debug_query = $db->Execute("INSERT INTO {$db->prefix}bounty (bounty_on, placed_by, amount, bounty_reason) values " .
                                  "(?,?,?,?)", array($bounty_on, $playerinfo['player_id'] ,$amount, $reason));
      db_op_result($db,$debug_query,__LINE__,__FILE__);
      $debug_query = $db->Execute("UPDATE {$db->prefix}players SET turns=turns-1, turns_used=turns_used+1, credits=credits-? WHERE player_id=?", array($amount, $playerinfo['player_id']));
      db_op_result($db,$debug_query,__LINE__,__FILE__);
      echo "$l_by_placed<br>";
      global $l_global_mmenu;
      echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";
      include_once ("./footer.php");
      die();
      break;

    default:
        echo "<h1>" . $title. "</h1>\n";
        $debug_query = $db->Execute("SELECT DISTINCT {$db->prefix}players.* FROM {$db->prefix}ships LEFT JOIN {$db->prefix}players " .
                                    "ON {$db->prefix}players.player_id = {$db->prefix}ships.player_id WHERE destroyed='N' AND " .
                                    "{$db->prefix}players.player_id !=? ORDER BY character_name ASC", array($playerinfo['player_id']));
        db_op_result($db,$debug_query,__LINE__,__FILE__);
        echo '<form name="bntform" action="bounty.php" method="post" onsubmit="document.bntform.submit_button.disabled=true;">';
        echo "<table>";
        echo "<tr><td>$l_by_bountyon</td><td><select name=bounty_on>";
        while (!$debug_query->EOF)
        {
            if (isset($bounty_on) && $bounty_on == $debug_query->fields[player_id])
            {
                $selected = "selected";
            }
            else
            {
                $selected = "";
            }

            $charname = $debug_query->fields[character_name];
            $player_id = $debug_query->fields[player_id];
            echo "<option value=$player_id $selected>$charname</option>";
            $debug_query->MoveNext();
        }

        echo "</select></td></tr>";
        echo "<tr><td>$l_by_reason:</td>";
        echo "<td><input type=text name=reason size=20 maxlength=20></td></tr>";
        echo "<tr><td>$l_by_amount:</td>";
        echo "<td><input type=text name=amount size=20 maxlength=20></td></tr>";
        echo "<tr><td></td><td><input type=submit name='submit_button' value='$l_by_place'><input type=reset value=Clear></td>";
        echo "</table>";
        echo "<input type=hidden name=response value=3>";
        echo "</form>";

        $result3 = $db->Execute ("SELECT bounty_reason, bounty_on, SUM(amount) as total_bounty FROM {$db->prefix}bounty GROUP BY bounty_on");

        $i = 0;
        if ($result3)
        {
            while (!$result3->EOF)
            {
                $bounties[$i] = $result3->fields;
                $i++;
                $result3->MoveNext();
            }
        }

        $num_bounties = $i;
        if ($num_bounties < 1)
        {
            echo "$l_by_nobounties<br>";
        }
        else
        {
            echo $l_by_moredetails . "<br><br>";
            echo "<table width=\"100%\" border=0 cellspacing=0 cellpadding=2>";
            echo "<tr bgcolor=\"$color_header\">";
            echo "<td><strong>$l_by_bountyon</strong></td>";
            echo "<td><strong>$l_amount</td>";
            echo "<td><strong>$l_by_reason</td>";
            echo "</tr>";
            $color = $color_line1;
            for ($i=0; $i<$num_bounties; $i++)
            {
                $someres = $db->Execute("SELECT character_name FROM {$db->prefix}players WHERE player_id=?", array($bounties[$i]['bounty_on']));
                $details = $someres->fields;
                echo "<tr bgcolor=\"$color\">";
                echo "<td><a href=bounty.php?bounty_on=" . $bounties[$i]['bounty_on'] . "&response=1>". $details['character_name'] ."</a></td>";
                echo "<td>&nbsp;" . number_format($bounties[$i]['total_bounty'], 0, $local_number_dec_point, $local_number_thousands_sep) . "&nbsp;</td>";
                echo "<td>" . $bounties[$i]['bounty_reason'] . "</td>";
                echo "</tr>";

                if ($color == $color_line1)
                {
                   $color = $color_line2;
                }
                else
                {
                   $color = $color_line1;
                }
            }
        echo "</table>";
        }

    echo "<br><br>";
    break;
}

//-------------------------------------------------------------------------------------------------

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");

?>
