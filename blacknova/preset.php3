<?


include("config.php3");
updatecookie();

include_once($gameroot . "/languages/$lang");
$title = "$l_pre_title";

include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

bigtitle();

if(!isset($change))
{
  echo "<FORM ACTION=preset.php3 METHOD=POST>";
  echo "Preset 1: <INPUT TYPE=TEXT NAME=preset1 SIZE=6 MAXLENGTH=6 VALUE=$playerinfo[preset1]><BR>";
  echo "Preset 2: <INPUT TYPE=TEXT NAME=preset2 SIZE=6 MAXLENGTH=6 VALUE=$playerinfo[preset2]><BR>";
  echo "Preset 3: <INPUT TYPE=TEXT NAME=preset3 SIZE=6 MAXLENGTH=6 VALUE=$playerinfo[preset3]><BR>";
  echo "<INPUT TYPE=HIDDEN NAME=change VALUE=1>";
  echo "<BR><INPUT TYPE=SUBMIT VALUE=$l_pre_save><BR><BR>";
  echo "</FORM>";
}
else
{
  $preset1 = round(abs($preset1));
  $preset2 = round(abs($preset2));
  $preset3 = round(abs($preset3));
  if($preset1 > $sector_max)
  {
    $l_pre_exceed = str_replace("[preset]", "1", $l_pre_exceed);
    $l_pre_exceed = str_replace("[sector_max]", $sector_max, $l_pre_exceed);
    echo $l_pre_exceed;
  }
  elseif($preset2 > $sector_max)
  {
    $l_pre_exceed = str_replace("[preset]", "2", $l_pre_exceed);
    $l_pre_exceed = str_replace("[sector_max]", $sector_max, $l_pre_exceed);
    echo $l_pre_exceed;
  }
  elseif($preset3 > $sector_max)
  {
    $l_pre_exceed = str_replace("[preset]", "3", $l_pre_exceed);
    $l_pre_exceed = str_replace("[sector_max]", $sector_max, $l_pre_exceed);
    echo $l_pre_exceed;
  }
  else
  {
    $update = mysql_query("UPDATE ships SET preset1=$preset1,preset2=$preset2,preset3=$preset3 WHERE ship_id=$playerinfo[ship_id]");
    $l_pre_set = str_replace("[preset1]", "$preset1", $l_pre_set);
    $l_pre_set = str_replace("[preset2]", "$preset2", $l_pre_set);
    $l_pre_set = str_replace("[preset3]", "$preset3", $l_pre_set);
    echo $l_pre_set;
  }
}

TEXT_GOTOMAIN();

include("footer.php3");

?> 
