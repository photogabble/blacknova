<?

  if (preg_match("/sched_ports.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
  }

  echo "<B>PORTS</B><BR><BR>";
  echo "Adding commodities to all ports...";
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore+($ore_rate*$multiplier),port_organics=port_organics+($organics_rate*$multiplier),port_goods=port_goods+($goods_rate*$multiplier),port_energy=port_energy+($energy_rate*$multiplier) WHERE port_type <> 'special' AND port_type <> 'none'"));
  echo "Ensuring minimum ore levels are 0...";
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=0 WHERE port_ore<0"));
  echo "<BR>";
  echo "Ensuring minimum organics levels are 0...";
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_organics=0 WHERE port_organics<0"));
  echo "<BR>";
  echo "Ensuring minimum goods levels are 0...";
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_goods=0 WHERE port_goods<0"));
  echo "<BR>";
  echo "Ensuring minimum energy levels are 0...";
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_energy=0 WHERE port_energy<0"));
  echo "<BR>";
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_energy=$energy_limit WHERE port_energy > $energy_limit"));
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_goods=$goods_limit WHERE  port_goods > $goods_limit"));
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_organics=$organics_limit WHERE port_organics > $organics_limit"));
  QUERYOK($db->Execute("UPDATE $dbtables[universe] SET port_ore=$ore_limit WHERE port_ore > $ore_limit"));
  $multiplier = 0;
?>
