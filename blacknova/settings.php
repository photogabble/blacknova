<? 

include("config.php");
include("languages/$lang");

$title="Game Configuration";
include("header.php");
connectdb();

bigtitle();

$MaxWidth = 600;
//-------------------------------------------------------------------------------------------------
$line_color = $color_line1;

function start_border($width)
{
  echo "<div align=\"center\"><center><table cellSpacing=\"0\" cellPadding=\"0\" width=\"$width\" border=\"0\"><tbody><tr><td nowrap>\n";
}

function end_border()
{
  echo "</td></tr></tbody></table></center></div>\n";
}

function tabimage($imgfile,$alt)
{
  echo "<div align=\"left\">\n";
  echo "  <center>\n";
  echo "  <table border=\"0\" width=\"600\" cellpadding=\"0\" cellspacing=\"0\">\n";
  echo "    <tr>\n";
  echo "      <td height=\"8\" nowrap></td>\n";
  echo "    </tr>\n";
  echo "    <tr>\n";
  echo "      <td nowrap><img border=\"0\" src=\"$imgfile\" alt=\"$alt\" width=\"75\" height=\"16\"></td>\n";
  echo "    </tr>\n";
  echo "  </table>\n";
  echo "  </center>\n";
  echo "</div>\n";
}

function start_line($bgcolor,$width)
{
  echo "<div align=\"center\"><table cellSpacing=\"1\" cellPadding=\"1\" border=\"0\" bgcolor=\"$bgcolor\" width=\"$width\"><tbody>\n";
}

function line($item, $value)
{
  global $line_color, $color_line1, $color_line2;

  echo "  <TR BGCOLOR=\"$line_color\">\n";
  echo "    <td noWrap align=\"left\" width=\"100%\">\n";
  echo "      <div align=\"left\"><table border=\"0\" cellspacing=\"0\" width=\"100%\" cellpadding=\"0\">\n";
  echo "        <tr>\n";
  echo "          <td align=\"left\" nowrap><font color=\"#ffffff\" size=\"2\">$item</font></td>\n";
  echo "          <td align=\"right\" nowrap><font color=\"#00ff00\" size=\"2\">$value</font></td>\n";
  echo "        </tr>\n";
  echo "      </table></div>\n";
  echo "    </td>\n";
  echo "  </TR>\n";

  if($line_color == $color_line1) 
    $line_color = $color_line2; 
  else
    $line_color = $color_line1; 
}

function end_line () 
{
  echo "</tbody></table></div>\n";
}

start_border($MaxWidth);

#################
## Version Tab ##
#################
tabimage("images/Info.gif","Displays the Game Information");
start_line("#E7C03D",$MaxWidth);
line("Game Server Title",$gamename);
line("Game Type, Version","$game_name, $game_version");
line("Game Codebase",$game_codebase);
line("Admin Info","<a href=\"mailto:$admin_mail\" class=\"nav\">$adminname</a>");
end_line();

#################
## Modules Tab ##
#################
tabimage("images/Modules.gif","Displays the Modules Information");
start_line("#E7C03D",$MaxWidth);
line("Modular Module Version",ModularVersion);

if ($modcnt >0)
{
  foreach ($modules as $modu => $moduleinfo)
  {
    $num++;
    list ($modfile,$modtag) = split ('/t', $moduleinfo,5);
    echo "      <tr>\n";
    echo "        <td width=100%>\n";
    echo "          <div align=center>\n";
    echo "            <table border=0 cellspacing=0 width=100% bgcolor=$line_color cellpadding=0>\n";
    echo "              <tr bgColor=$line_color>\n";
    echo "                <td width=15%><font color=#ffffff size=2>Module Name </font></td>\n";
    echo "                <td width=30%><font color=#00ff00 size=2><a target=\"_blank\" href=\"".constant("$modtag"."_Website")."\" class=\"nav\">".constant($modtag."_Name")." " .constant($modtag."_Version")."</a></font></td>\n";
    echo "                <td width=10%><font color=#ffffff size=2>Author </font></td>\n";
    echo "                <td width=20%><font color=#00ff00 size=2><a href=\"".constant($modtag."_Email")."\" class=\"nav\">".constant($modtag."_Author")."</a></font></td>\n";
    echo "                <td width=05%><font color=#ffffff size=2>Info </font></td>\n";
    echo "                <td width=20%><font color=#00ff00 size=2>".constant($modtag."_Info")."</font></td>\n";
    echo "              </tr>\n";
    echo "            </table>\n";
    echo "          </div>\n";
    echo "        </td>\n";
    echo "      </tr>\n";
    if($line_color == $color_line1){$line_color = $color_line2;}else{$line_color = $color_line1;}
  }
}
else
{
  echo "      <tr bgColor=$line_color>\n";
  echo "        <td width=100%><p align=center><font size=2 color=yellow>No Modules Loaded</font></td>\n";
  echo "      </tr>\n";
  if($line_color == $color_line1){$line_color = $color_line2;}else{$line_color = $color_line1;}
}
echo "    </tbody>\n";
echo "  </table>\n";
echo "</div>\n";

#####################
## Player Info Tab ##
#####################
$banres = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ip_bans],$dbtables[ships] WHERE ban_mask = ip_address");
$aliveres = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] WHERE ship_destroyed='N' AND email NOT LIKE '%@furangee'");
$regres = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] WHERE email NOT LIKE '%@furangee'");
$deadres = $db->Execute("SELECT COUNT(*) AS num_players FROM $dbtables[ships] WHERE ship_destroyed='Y' and email NOT LIKE '%@furangee'");
$row = $banres->fields; $banplayers = $row['num_players'];
$row = $aliveres->fields; $aliveplayers = $row['num_players'];
$row = $regres->fields; $regplayers = $row['num_players'];
$row = $deadres->fields; $deadplayers = $row['num_players'];

tabimage("images/PlayerInfo.gif","Displays the Player Information");
start_line("#E7C03D",$MaxWidth);
line("Registered Players","$regplayers");
line("Alive Players","$aliveplayers");
line("Dead Players","$deadplayers");
line("Banned Players","$banplayers");
end_line();

#####################
## Game Status Tab ##
#####################
tabimage("images/Status.gif","Displays the Status Information");
start_line("#E7C03D",$MaxWidth);
line("Allow Players",TRUEFALSE($server_closed,False,"Yes","<font color=red>No</font>"));
line("Allow New Players",TRUEFALSE($account_creation_closed,False,"Yes","<font color=red>No</font>"));
line("Maintenance Mode",TRUEFALSE(($account_creation_closed & $server_closed),True,"Yes","<font color=red>No</font>"));
line("Tournament Mode",TRUEFALSE(($account_creation_closed & !$server_closed),True,"Enabled","<font color=red>Disabled</font>"));
end_line();

######################
## Game Options Tab ##
######################
tabimage("images/Options.gif","Displays the Options Information");
start_line("#E7C03D",$MaxWidth);
line("Allow Corp Planet Credit Transfer",TRUEFALSE($corp_planet_transfers,1,"Yes","<font color=red>No</font>"));
line("Allow Full Long Range Scan",TRUEFALSE($allow_fullscan,True,"Yes","<font color=red>No</font>"));
line("Allow Sub-Orbital Fighter Attacks",TRUEFALSE($sofa_on,True,"Yes","<font color=red>No</font>"));
line("Display Password on Registering",TRUEFALSE($display_password,True,"Yes","<font color=red>No</font>"));
line("Genesis torps can destroy planets",TRUEFALSE($allow_genesis_destroy,True,"Yes","<font color=red>No</font>"));
line("Intergalactic Bank (IGB)",TRUEFALSE($allow_ibank,True,"Enabled","<font color=red>Disabled</font>"));
line("Known Space Maps",TRUEFALSE($ksm_allowed,True,"Enabled","<font color=red>Disabled</font>"));
line("Navigation Computer",TRUEFALSE($allow_navcomp,True,"Enabled","<font color=red>Disabled</font>"));
line("Newbie Nice",TRUEFALSE($newbie_nice,"YES","Enabled","<font color=red>Disabled</font>"));
line("Newbie Extra Nice",TRUEFALSE($newbie_extra_nice,"YES","Enabled","<font color=red>Disabled</font>"));
line("Enable Auto Tow",TRUEFALSE($Enable_AutoTow,True,"Enabled","<font color=red>Disabled</font>"));
end_line();

#######################
## Game Settings Tab ##
#######################
tabimage("images/Settings.gif","Displays the Settings Information");
start_line("#E7C03D",$MaxWidth);
line("Average tech level needed to hit mines",$mine_hullsize);
line("Averaged Tech level When Emergency Warp Degrades",$ewd_maxhullsize);
$num = NUMBER($sector_max);
line("Number of Sectors",$num);
line("Maximum Links per sector",$link_max);
line("Universe Size",$universe_size);
line("Maximum average tech level for Federation Sectors",$fed_max_hull);
$bank_enabled = $allow_ibank ? "Yes" : "No";
line("Intergalactic Bank Enabled",$bank_enabled);
if($allow_ibank)
{
  $rate = $ibank_interest * 100;
  line("IGB Interest rate per update",$rate);
  $rate = $ibank_loaninterest * 100;
  line("IGB Loan rate per update",$rate);
}  
line("Tech Level upgrade for Bases",$basedefense);
$num = NUMBER($colonist_limit);
line("Colonists Limit",$num);
$num = NUMBER($doomsday_value);
line("Colonists Limit before Apocalypse",$num);
$num = NUMBER($max_turns);
line("Maximum number of accumulated turns",$num);
line("Maximum number of planets per sector",$max_planets_sector);
line("Maximum number of traderoutes per player",$max_traderoutes_player);
line("Colonist Production Rate",$colonist_production_rate);
line("Unit of Energy used per sector fighter",$energy_per_fighter);
$rate = $defence_degrade_rate * 100;
line("Sector fighter degradation percentage rate",$rate);
line("Number of planets with bases need for sector ownership&nbsp;",$min_bases_to_own);
$rate = NUMBER(($interest_rate - 1) * 100 , 3);
line("Planet interest rate",$rate);
line("Maximum Password length",NUMBER($maxlen_password));
$rate = 1 / $colonist_production_rate;
$num = NUMBER($rate/$fighter_prate);
line("Colonists needed to produce 1 Fighter each turn",$num);
$num = NUMBER($rate/$torpedo_prate);
line("Colonists needed to produce 1 Torpedo each turn",$num);
$num = NUMBER($rate/$ore_prate);
line("Colonists needed to produce 1 Ore each turn",$num);
$num = NUMBER($rate/$organics_prate);
line("Colonists needed to produce 1 Organics each turn",$num);
$num = NUMBER($rate/$goods_prate);
line("Colonists needed to produce 1 Goods each turn",$num);
$num = NUMBER($rate/$energy_prate);
line("Colonists needed to produce 1 Energy each turn",$num);
$num = NUMBER($rate/$credits_prate);
line("Colonists needed to produce 1 Credits each turn",$num);
end_line();

########################
## Game Scheduler Tab ##
########################
tabimage("images/Scheduler.gif","Displays the Scheduler Information");
start_line("#E7C03D",$MaxWidth);
line("Scheduler Type",TRUEFALSE($sched_type,0,"Cron based","Player triggered"));
line("Ticks happen every",$sched_ticks ." minutes");
line("Turns will happen every",$sched_turns ." minutes");
line("Defenses will be checked every",$sched_turns ." minutes");
line("Furangees will play every",$sched_turns ." minutes");  
if($allow_ibank) line("Interests on IGB accounts will be accumulated every&nbsp;", $sched_IGB ." minutes");
line("News will be generated every",$sched_news ." minutes");
line("Planets will generate production every",$sched_planets ." minutes");
line("Ports will regenerate every",$sched_ports ." minutes");
line("Ships will be towed from fed sectors every",$sched_turns ." minutes");
line("Rankings will be generated every",$sched_ranking ." minutes");
line("Sector Defences will degrade every",$sched_degrade ." minutes");
line("The planetary apocalypse will occur every&nbsp;",$sched_apocalypse ." minutes");
end_line();

end_border();
echo "<BR><BR>";



if(empty($username))
{
  TEXT_GOTOLOGIN();
}
else
{
  TEXT_GOTOMAIN();
}

include("footer.php");

?>
