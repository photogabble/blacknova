<? 

include("config.php");
include("languages/$lang");

include("header.php");

//-------------------------------------------------------------------------------------------------
$line_color = $color_line1;

function TRUEFALSE($truefalse,$Stat,$True,$False)
{
  return(($truefalse == $Stat) ? $True : $False);
}

function line($item, $value)
{
  global $line_color, $color_line1, $color_line2;
  global $sofa_on;

  echo "<TR BGCOLOR=\"$line_color\"><TD width=\"450\"><font size=\"2\" color=\"#FFFFFF\">$item</font></TD><TD align=\"right\" width=\"150\"><font size=\"2\" color=\"#00FF00\">$value</font></TD></TR>\n";

  if($line_color == $color_line1)
  { 
    $line_color = $color_line2; 
  }
  else
  { 
    $line_color = $color_line1; 
  }
}

echo "<div align=\"center\"><table cellSpacing=\"0\" cellPadding=\"0\" width=\"600\" border=\"0\"><tbody><tr><td>";  
$modcnt = count($modules);
$num=0;
echo "<div align=\"left\"><table cellSpacing=\"0\" cellPadding=\"2\" border=\"0\" width=\"100%\"><tbody>\n";

# Modules Section

$title="Modules";
bigtitle();

echo "<div align=center>\n";
echo "  <table cellSpacing=0 cellPadding=2 width=100% border=0>\n";
echo "    <tbody>\n";
if ($modcnt >0)
{
  foreach ($modules as $modu => $moduleinfo)
  {
    $num++;
    list ($modfile,$modtag) = split ('/t', $moduleinfo,5);
    echo "      <tr bgColor=$line_color>\n";
    echo "        <td width=15%><font color=#ffffff size=2>Module Name </font><font color=#00ff00 size=2></font></td>\n";
    echo "        <td width=20%><font color=#00ff00 size=2><a class=nav href=".constant("$modtag"."_Website").">".constant($modtag."_Name")."</a></font></td>\n";
    echo "        <td width=10%><font color=#00ff00 size=2>".constant($modtag."_Version")."</font></td>\n";
    echo "        <td width=10%><font color=#ffffff size=2>Author </font><font color=#00ff00 size=2></font></td>\n";
    echo "        <td width=20%><font color=#00ff00 size=2><a href=\"".constant($modtag."_Email")."\" class=\"nav\">".constant($modtag."_Author")."</a></font></td>\n";
    echo "        <td width=5%><font color=#ffffff size=2>Info </font></td>\n";
    echo "        <td width=20%><font color=#00ff00 size=2>".constant($modtag."_Info")."</font></td>\n";
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

#Game Status
$title="Game Status";
bigtitle();

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
line("Allow Players",TRUEFALSE($server_closed,False,"Yes","<font color=red>No</font>"));
line("Allow New Players",TRUEFALSE($account_creation_closed,False,"Yes","<font color=red>No</font>"));
echo "</TABLE>\n";

#Game Options

$title="Game Options";
bigtitle();

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
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

echo "</TABLE>\n";

#Game Settings

$title="Game Settings";
bigtitle();

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
line("Game version:",$game_name);
line("Average tech level needed to hit mines",$mine_hullsize);
line("Averaged Tech level When Emergency Warp Degrades",$ewd_maxhullsize);
   
$num = NUMBER($sector_max);
line("Number of Sectors",$num);
line("Maximum Links per sector",$link_max);
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
line("Colonists Limit","&nbsp;".$num);
   
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
echo "</TABLE>\n";

#Scheduler Settings

$title="Game Scheduler Settings";
bigtitle();
 
$line_color = $color_line1;
  
echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
global $sched_type;
line("Scheduler Type",TRUEFALSE($sched_type,0,"Cron Based","Player Triggered"));
line("Ticks happen every",$sched_ticks ."&nbsp;minutes");
line("Turns will happen every",$sched_turns ."&nbsp;minutes");
line("Defenses will be checked every",$sched_turns ."&nbsp;minutes");
line("Furangees will play every",$sched_turns ."&nbsp;minutes");  
   
if($allow_ibank)
line("Interests on IGB accounts will be accumulated every&nbsp;", $sched_IGB ."&nbsp;minutes");

line("News will be generated every",$sched_news ."&nbsp;minutes");
line("Planets will generate production every",$sched_planets ."&nbsp;minutes");
line("Ports will regenerate every",$sched_ports ."&nbsp;minutes");
line("Ships will be towed from fed sectors every",$sched_turns ."&nbsp;minutes");
line("Rankings will be generated every",$sched_ranking ."&nbsp;minutes");
line("Sector Defences will degrade every",$sched_degrade ."&nbsp;minutes");
line("The planetary apocalypse will occur every&nbsp;",$sched_apocalypse ."&nbsp;minutes");

echo "</TABLE>";
echo "</td></tr></tbody></table></div>";
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
