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
$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$shipinfo[sector_id]'");
$sectorinfo = $result2->fields;

$result3 = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE sector_id='$shipinfo[sector_id]'");
$num_planets = $result3->RecordCount();
bigtitle();

if($playerinfo[turns] < 1)
{
  echo "$l_gns_turn";
}
elseif($shipinfo[on_planet]=='Y')
{
  echo $l_gns_onplanet;
}
elseif($num_planets >= $max_planets_sector)
{
  echo $l_gns_full;
}
elseif($shipinfo[dev_genesis] < 1)
{
  echo "$l_gns_nogenesis";
}
else
{
  $res = $db->Execute("SELECT allow_planet, corp_zone, owner FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
  $zoneinfo = $res->fields;
  if($zoneinfo[allow_planet] == 'N')
  {
    echo "$l_gns_forbid";
  }
  elseif($zoneinfo[allow_planet] == 'L')
  {
    if($zoneinfo[corp_zone] == 'N')
    {
      if($playerinfo[team] == 0)
      {
        echo $l_gns_bforbid;
      }
      else
      {
        $res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$zoneinfo[owner]");
        $ownerinfo = $res->fields;
        if($ownerinfo[team] != $playerinfo[team])
        {
          echo $l_gns_bforbid;
        }
        else
        {
          $query1 = "INSERT INTO $dbtables[planets] VALUES('', $shipinfo[sector_id], NULL, 0, 0, 0, 0, 0, 0, 0, 0, $playerinfo[player_id], 0, 'N', 'N', $default_prod_organics, $default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, 'N')";
          $update1 = $db->Execute($query1);
          $query2 = "UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id]";
          $update2 = $db->Execute($query2);
          $query2 = "UPDATE $dbtables[ships] SET dev_genesis=dev_genesis-1 WHERE ship_id=$shipinfo[ship_id]";
          $update2 = $db->Execute($query2);
          echo $l_gns_pcreate;
        }
      }
    }
    elseif($playerinfo[team] != $zoneinfo[owner])
    {
      echo $l_gns_bforbid;
    }
    else
    {
      $query1 = "INSERT INTO $dbtables[planets] VALUES('', $shipinfo[sector_id], NULL, 0, 0, 0, 0, 0, 0, 0, 0, $playerinfo[player_id], 0, 'N', 'N', $default_prod_organics, $default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, 'N')";
      $update1 = $db->Execute($query1);
      $query2 = "UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id]";
      $update2 = $db->Execute($query2);
      $query2 = "UPDATE $dbtables[ships] SET dev_genesis=dev_genesis-1 WHERE ship_id=$shipinfo[ship_id]";
      $update2 = $db->Execute($query2);
      echo $l_gns_pcreate;
    }
  }
  else
  {
    $query1 = "INSERT INTO $dbtables[planets] VALUES('', $shipinfo[sector_id], NULL, 0, 0, 0, 0, 0, 0, 0, 0, $playerinfo[player_id], 0, 'N', 'N', $default_prod_organics, $default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp, 'N')";
    $update1 = $db->Execute($query1);
    $query2 = "UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id]";
    $update2 = $db->Execute($query2);
    $query2 = "UPDATE $dbtables[ships] SET dev_genesis=dev_genesis-1 WHERE ship_id=$shipinfo[ship_id]";
    $update2 = $db->Execute($query2);
    echo $l_gns_pcreate;
  }
}

//-------------------------------------------------------------------------------------------------

echo "<BR><BR>";
TEXT_GOTOMAIN();

include("footer.php");

?>
