<?
include("config.php");
updatecookie();

include("languages/$lang");
$title=$l_gns_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------
$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo = $result2->fields;

$result3 = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE sector_id='$playerinfo[sector]'");
$num_planets = $result3->RecordCount();
bigtitle();

function createplanet()
{
   global $db, $dbtables, $playerinfo, $l_gns_pcreate;
   global $default_prod_organics, $default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, $max_planets_sector;
   
   $query1 = "INSERT INTO $dbtables[planets] VALUES('', $playerinfo[sector], NULL, 0, 0, 0, 0, 0, 0, 0, 0, $playerinfo[ship_id], 0, 'N', 'N', $default_prod_organics, $default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, 'N')";
   $update1 = $db->Execute($query1);
   $query2 = "UPDATE $dbtables[ships] SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id=$playerinfo[ship_id]";
   $update2 = $db->Execute($query2);
   
   echo $l_gns_pcreate."<BR>";
}

function printanddie($text = "")
{
   if ($text != "")
   echo $text."\n"."<BR><BR>";
   TEXT_GOTOMAIN();
   include("footer.php");
   die();
}

if($playerinfo[turns] < 1)
{
   printanddie($l_gns_turn);
}
elseif($playerinfo[on_planet]=='Y')
{
   printanddie(l_gns_onplanet);
}
elseif($num_planets >= $max_planets_sector)
{
   printanddie($l_gns_full);
}
elseif($playerinfo[dev_genesis] < 1)
{
   printanddie($l_gns_nogenesis);
}

if ($_POST['confirm'] == -1)
{
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface?id=" . $playerinfo[ship_id] . "\">";
}
elseif ($_POST['confirm'] != 1)
{
   echo "Are you sure you want to create a planet in this sector? ";
   echo "<FORM action=\"$PHP_SELF\" method=\"post\"><INPUT type=\"hidden\" name=\"confirm\" value=\"1\"><INPUT type=\"submit\" value=\"YES\"></FORM> ";
   echo "<FORM action=\"$PHP_SELF\" method=\"post\"><INPUT type=\"hidden\" name=\"confirm\" value=\"-1\"><INPUT type=\"submit\" value=\"No\"></FORM><BR>";
   printanddie();
}
elseif ($_POST['confirm'] == 1)
{
   $res = $db->Execute("SELECT allow_planet, corp_zone, owner FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
   $zoneinfo = $res->fields;
   
   if ($zoneinfo[allow_planet] == 'N')
   {
      echo $l_gns_forbid;
   }
   elseif ($zoneinfo[allow_planet] == 'L')
   {
      if($zoneinfo[corp_zone] == 'N')
      {
         if($playerinfo[team] == 0)
         {
            echo $l_gns_bforbid;
         }
         else
         {
            $res = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id=$zoneinfo[owner]");
            $ownerinfo = $res->fields;
            if($ownerinfo[team] != $playerinfo[team])
            {
               echo $l_gns_bforbid;
            }
            else
            {
               createplanet();
            }
         }
      }
    elseif($playerinfo[team] != $zoneinfo[owner])
    {
      echo $l_gns_bforbid;
    }
    else
    {
      createplanet();
    }
  }
  else
  {
      createplanet();
  }
}

TEXT_GOTOMAIN();
include("footer.php");
?>