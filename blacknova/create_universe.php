<?
include("config.php");
include("languages/$lang");

/*
##############################################################################
# Create Universe Script                                                     #
#                                                                            #
# ChangeLog                                                                  #
#  Nov 2, 01 - Wandrer - Rewritten mostly from scratch                       #
##############################################################################
*/

/*
##############################################################################
# Define Functions for this script                                           #
##############################################################################
*/

### Description: Create Benchmark Class

class c_Timer {
   var $t_start = 0;
   var $t_stop = 0;
   var $t_elapsed = 0;

   function start() { $this->t_start = microtime(); }

   function stop()  { $this->t_stop  = microtime(); }

   function elapsed() {
      $start_u = substr($this->t_start,0,10); $start_s = substr($this->t_start,11,10);
      $stop_u  = substr($this->t_stop,0,10);  $stop_s  = substr($this->t_stop,11,10);
      $start_total = doubleval($start_u) + $start_s;
      $stop_total  = doubleval($stop_u) + $stop_s;
      $this->t_elapsed = $stop_total - $start_total;
      return $this->t_elapsed;
   }
}

function PrintFlush($Text="") {
print "$Text";
flush();
}

### End defining functions.

### Start Timer
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start();

### Set timelimit and randomize timer.

set_time_limit(0);
srand((double)microtime()*1000000);

### Include config files and db scheme.

include("includes/schema.php");

### Update cookie.
updatecookie();

$title="Create Universe";
include("header.php");

### Connect to the database.

connectdb();

### Print Title on Page.

bigtitle();

### Manually set step var if info isn't correct.

if($swordfish != $adminpass) {
$step="0";
}

if($swordfish == $adminpass && $engage == "") {
$step="1";
}

if($swordfish == $adminpass && $engage == "1") {
$step="2";
}

### Main switch statement.

switch ($step) {
// Stage 1, Getting things started
   case "1":
      echo "<form action=create_universe.php method=post>";
      echo "<table>";
      echo "<tr><td><b><u>Base/Planet Setup</u></b></td><td></td></tr>";
      echo "<tr><td>Percent Special</td><td><input type=text name=special size=5 maxlength=5 value=1></td></tr>";
      echo "<tr><td>Percent Ore</td><td><input type=text name=ore size=5 maxlength=5 value=15></td></tr>";
      echo "<tr><td>Percent Organics</td><td><input type=text name=organics size=5 maxlength=5 value=10></td></tr>";
      echo "<tr><td>Percent Goods</td><td><input type=text name=goods size=5 maxlength=5 value=15></td></tr>";
      echo "<tr><td>Percent Energy</td><td><input type=text name=energy size=5 maxlength=5 value=10></td></tr>";
      echo "<tr><td>Percent Empty</td><td>Equal to 100 - total of above.</td></tr>";
      echo "<tr><td>Initial Commodities to Sell<br><td><input type=text name=initscommod size=6 maxlength=6 value=100.00> % of max</td></tr>";
      echo "<tr><td>Initial Commodities to Buy<br><td><input type=text name=initbcommod size=6 maxlength=6 value=100.00> % of max</td></tr>";
      echo "<tr><td><b><u>Sector/Link Setup</u></b></td><td></td></tr>";
      $fedsecs = intval($sector_max / 200);
      $loops = intval($sector_max / 500);
      echo "<tr><td>Number of sectors total (<b>overrides config.php</b>)</td><td><input type=text name=sektors size=5 maxlength=5 value=$sector_max></td></tr>";
      echo "<TR><TD>Number of Federation sectors</TD><TD><INPUT TYPE=TEXT NAME=fedsecs SIZE=6 MAXLENGTH=6 VALUE=$fedsecs></TD></TR>";
      echo "<tr><td>Number of loops</td><td><input type=text name=loops size=6 maxlength=6 value=$loops></td></tr>";
      echo "<tr><td>Percent of sectors with unowned planets</td><td><input type=text name=planets size=5 maxlength=5 value=10></td></tr>";
      echo "<tr><td></td><td><input type=hidden name=engage value=1><input type=hidden name=step value=2><input type=hidden name=swordfish value=$swordfish><input type=submit value=Submit><input type=reset value=Reset></td></tr>";
      echo "</table>";
      echo "</form>";
      break;

// Stage 2, Configuration
   case "2":
      $sector_max = round($sektors);
      if($fedsecs > $sector_max) {
         echo "The number of Federation sectors must be smaller than the size of the universe!";
         break;
      }
      $spp = round($sector_max*$special/100);
      $oep = round($sector_max*$ore/100);
      $ogp = round($sector_max*$organics/100);
      $gop = round($sector_max*$goods/100);
      $enp = round($sector_max*$energy/100);
      $empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
      $nump = round ($sector_max*$planets/100);
      echo "So you would like your $sector_max sector universe to have:<BR><BR>";
      echo "$spp special ports<BR>";
      echo "$oep ore ports<BR>";
      echo "$ogp organics ports<BR>";
      echo "$gop goods ports<BR>";
      echo "$enp energy ports<BR>";
      echo "$initscommod% initial commodities to sell<BR>";
      echo "$initbcommod% initial commodities to buy<BR>";
      echo "$empty empty sectors<BR>";
      echo "$fedsecs Federation sectors<BR>";
      echo "$loops loops<BR>";
      echo "$nump unowned planets<BR><BR>";
      echo "If this is correct, click confirm - otherwise go back.<BR>";
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=3>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=sektors value=$sector_max>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      echo "<BR><BR><FONT COLOR=RED>";
      echo "WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'CONFIRM'!</FONT>";
      break;

// Stage 3, Out with the old and in with the new
   case "3":
      $sector_max = round($sektors);
      create_schema();
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=4>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=sektors value=$sector_max>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      break;

// Stage 4, Galaxies-R-Us
   case "4":
      $sector_max = round($sektors);
// Build the zones table. Only four zones here. The rest are named after players for
// when they manage to dominate a sector.
      print("Building zone descriptions ");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('1', 'Unchartered space', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '0' )");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('2', 'Federation space', 0, 'N', 'N', 'N', 'N', 'N', 'N',  'Y', 'N', '$fed_max_hull')");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('3', 'Free-Trade space', 0, 'N', 'N', 'Y', 'N', 'N', 'N','Y', 'N', '0')");
      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('4', 'War Zone', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y','N', 'Y', '0')");
      $update = $db->Execute("UPDATE $dbtables[universe] SET zone_id='2' WHERE sector_id<$fedsecs");
      print("");
      PrintFlush("- completed successfully.<BR>");

// Setup some need values for product amounts
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

// Build Sector 0, Sol
      print("Creating sector 0 - Sol ");
      $sector = array();
      $sector[0] = array('sector_id' => '0',
                         'sector_name' => 'Sol',
                         'zone_id' => '2',
                         'port_type' => 'special',
                         'port_organics' => '0',
                         'port_ore' => '0',
                         'port_goods' => '0',
                         'port_energy' => '0',
                         'beacon' => 'Sol: Hub of the Universe',
                         'x' => '0',
                         'y' => '0',
                         'z' => '0');
      print("");
      PrintFlush("- completed successfully.<BR>");

// Build Sector 1, Alpha Centauri
      print("Creating sector 1 - Alpha Centauri ");
      $sector[1] = array('sector_id' => '1',
                         'sector_name' => 'Alpha Centari',
                         'zone_id' => '2',
                         'port_type' => 'energy',
                         'port_organics' => $initborganics,
                         'port_ore' => $initbore,
                         'port_goods' => $initbgoods,
                         'port_energy' => $initsenergy,
                         'beacon' => 'Alpha Centari: Gateway to the Galaxy',
                         'x' => '0',
                         'y' => '0',
                         'z' => '1');
      print("");
      PrintFlush("- completed successfully.<BR>");

// Here's where the remaining sectors get built
      print("Creating remaining ".($sector_max-2)." sectors ");
      $collisions=0;
      for($i=2; $i<$sector_max; $i++) {
        $sector[$i]= array('sector_id' => "$i");
        $collision = FALSE;
        while(TRUE) {
          // Lot of shortcuts here. Basically we generate a spherical coordinate and convert it to cartesian.
          // Why? Cause random spherical coordinates tend to be denser towards the center.
          // Should really be like a spiral arm galaxy but this'll do for now.
          $radius = rand(100,$universe_size*100)/100;

          $temp_a = deg2rad(rand(0,36000)/100-180);
          $temp_b = deg2rad(rand(0,18000)/100-90);
          $temp_c = $radius*sin($temp_b);

          $sector[$i]['x'] = round(cos($temp_a)*$temp_c);
          $sector[$i]['y'] = round(sin($temp_a)*$temp_c);
          $sector[$i]['z'] = round($radius*cos($temp_b));

          // Collision check
          if(isset($index[$sector[$i]['x'].','.$sector[$i]['y'].','.$sector[$i]['z']])) {
            $collisions++;
          } else {
            break;
         }
      }

        $index[$sector[$i]['x'].','.$sector[$i]['y'].','.$sector[$i]['z']]=&$sector[$i];

        // The Federation owns the first series of sectors. Logical because they
        // probably numbered them as they were found.
        if($i<$fedsecs) {
          $sector[$i]['zone_id'] = '2'; // Federation space
            } else {
          $sector[$i]['zone_id'] = '1'; // Uncharted
         }
      }
      if($collisions) {
        print("- $collisions sector collisions repaired ");
            } else {
        print("- no sector collisions detected ");
            }
      PrintFlush("- completed successfully.<BR>");


// Locations are mapped out so now we need ports.
      $shuffled = array();
      print "Preparing for port placement ";
      // Build up an array of references for conveniece
      for($i=0; $i<$sector_max; $i++) {
        $shuffled[$i] = &$sector[$i];
      }

      // Give it a really good shuffling. Once isn't enough, the sectors that get
      // ports will tend to be packed at the high end. Five seems to give a good,
      // even distribution.
      for($i=0;$i<5;$i++){
        shuffle($shuffled);
      }
      print("");
      PrintFlush("- preperations completed successfully.<br>");

      // Now we have two indexes, one normal and one referencing the array randomly.
      // This makes port placement easier because they can be added sequentually
      // using the shuffled reference array.

      // Place the special ports
      print "Placing $spp special ports ";
      for($i=0, $max = $spp; $i<$max; $i++) {
        if(isset($shuffled[$i]['port_type'])) {
          $max++;
          continue;
         }
        $shuffled[$i]['zone_id'] = '3';
        $shuffled[$i]['port_type'] = 'special';
      }
      print("");
      PrintFlush("- completed successfully.<br>");

      // Place the ore ports
      print "Placing $oep ore ports ";
      // $max += $oep-1; because Sol is an special port and counts towards the total.
      for($max += $oep-1; $i<$max; $i++) {
        if(isset($shuffled[$i]['port_type'])) {
          $max++;
          continue;
        }
        $shuffled[$i]['port_type'] = 'ore';
        $shuffled[$i]['port_ore'] = $initsore;
        $shuffled[$i]['port_organics'] = $initborganics;
        $shuffled[$i]['port_goods'] = $initbgoods;
        $shuffled[$i]['port_energy'] = $initbenergy;
      }
      print("");
      PrintFlush("- completed successfully.<br>");

      // Place the organics ports
      print "Placing $ogp organics ports ";
      for($max += $ogp; $i<$max; $i++) {
        if(isset($shuffled[$i]['port_type'])) {
          $max++;
          continue;
        }
        $shuffled[$i]['port_type'] = 'organics';
        $shuffled[$i]['port_ore'] = $initbore;
        $shuffled[$i]['port_organics'] = $initsorganics;
        $shuffled[$i]['port_goods'] = $initbgoods;
        $shuffled[$i]['port_energy'] = $initbenergy;
            }
      print("");
      PrintFlush("- completed successfully.<br>");

      // Place the goods ports
      print "Placing $gop goods ports ";
      for($max += $gop; $i<$max; $i++) {
        if(isset($shuffled[$i]['port_type'])) {
          $max++;
          continue;
        }
        $shuffled[$i]['port_type'] = 'goods';
        $shuffled[$i]['port_ore'] = $initbore;
        $shuffled[$i]['port_organics'] = $initborganics;
        $shuffled[$i]['port_goods'] = $initsgoods;
        $shuffled[$i]['port_energy'] = $initbenergy;
         }
      print("");
      PrintFlush("- completed successfully.<br>");

      // Place the energy ports
      print "Placing $enp energy ports ";
      // $max += $enp-1; because Alpha Centari is an energy port and counts towards the total.
      for($max += $enp-1; $i<$max; $i++) {
        if(isset($shuffled[$i]['port_type'])) {
          $max++;
          continue;
        }
        $shuffled[$i]['port_type'] = 'energy';
        $shuffled[$i]['port_ore'] = $initbore;
        $shuffled[$i]['port_organics'] = $initborganics;
        $shuffled[$i]['port_goods'] = $initbgoods;
        $shuffled[$i]['port_energy'] = $initsenergy;
      }
      print("");
      PrintFlush("- completed successfully.<br>");

      // Now we wrap the whole thing up and stuff it into the database.
      print "Transferring universe data to database ";
      for($i=0; $i<$sector_max; $i++){
        // Every 500 (and zero) we send it off to be processed.
        if($i%500 == 0) {
          // Don't want to handle zero here, we have to do something special
          // with it anyway
          if($i) {
            $insert = substr_replace($insert, ";", -2);
            $results = $db->Execute($insert);
            $insert=str_replace("\n","<br>",$insert);
//            print "<br>".$insert."<br>";
            PrintFlush($db->ErrorMsg());
            }
          // Set things up for the next batch
          $insert = "INSERT INTO $dbtables[universe] (sector_id,sector_name,zone_id,port_type,".
            "port_organics,port_ore,port_goods,port_energy,beacon,x,y,z) VALUES \n";
        }

        // Add a sector to the current batch
        $insert .= "('".$sector[$i]['sector_id']."',".
                   (isset($sector[$i]['sector_name'])?"'".$sector[$i]['sector_name']."'":"NULL").",".
                   (isset($sector[$i]['zone_id'])?$sector[$i]['zone_id']:"").",".
                   (isset($sector[$i]['port_type'])?"'".$sector[$i]['port_type']."'":"'none'").",".
                   (isset($sector[$i]['port_organics'])?(
                     $sector[$i]['port_organics'].",".
                     $sector[$i]['port_ore'].",".
                     $sector[$i]['port_goods'].",".
                     $sector[$i]['port_energy']):"0,0,0,0").",".
                   (isset($sector[$i]['beacon'])?"'".$sector[$i]['beacon']."'":"NULL").",".
                   $sector[$i]['x'].",".
                   $sector[$i]['y'].",".
                   $sector[$i]['z']."),\n";

        // Handle zero specially here
        if(!$i) {
          // Stick it in the database all by itself
          $insert = substr_replace($insert, ";", -2);
          $results = $db->Execute($insert);
          PrintFlush($db->ErrorMsg());

          // Darn it, MySQL insists on reindexing record zero to record one
          // so we change it back.
          $update = "UPDATE $dbtables[universe] SET sector_id=0 WHERE sector_id=1;";
          $results = $db->Execute($update);
          PrintFlush($db->ErrorMsg());

          // Set things up for the next batch
          $insert = "INSERT INTO $dbtables[universe] (sector_id,sector_name,zone_id,port_type,".
            "port_organics,port_ore,port_goods,port_energy,beacon,x,y,z) VALUES \n";
         }
      }
      // There will always be at least one sector left over so it's
      // taken care of here.
      $insert = substr_replace($insert, ";", -2);
      $results = $db->Execute($insert);
      PrintFlush($db->ErrorMsg());
      print("");
      PrintFlush("- completed successfully.<br>");


      // build a form for the next stage
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=5>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=sektors value=$sector_max>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      break;

// Stage 5, Planets-R-Us
   case "5":
      $sector_max = round($sektors);

      PrintFlush("Creating $nump planets ");

      $results = $db->Execute("SELECT $dbtables[universe].sector_id ".
                              "FROM $dbtables[universe], $dbtables[zones] ".
                              "WHERE $dbtables[zones].zone_id=$dbtables[universe].zone_id ".
                                "AND $dbtables[zones].allow_planet='N'");
      if(!$results) die("DB error while gathering 'No planet' zones");

      $blocked = array();

      while (!$results->EOF) {
        $blocked[$results->fields['sector_id']] = 1;
        $results->MoveNext();
      }

      for($i=0; $i<$nump; $i++) {
        $n = rand(0,$sector_max-1);
        if($blocked[$n]) {
          $i--;
          continue;
        }
        if(!$i%500) {
          if($i) {
            $insert = substr_replace($insert, ";", -2);
            $BenchmarkTimer->pause();
            $results = $db->Execute($insert);
            $BenchmarkTimer->resume();
            if(!$results) {
         PrintFlush($db->ErrorMsg());
              print "<pre>";
              print_r($insert);
              PrintFlush("</pre>");
              die("DB error while placing planets");
            }
          }
          $insert = "INSERT INTO $dbtables[planets] (colonists,owner,corp,prod_ore,prod_organics,prod_goods,".
                    "prod_energy,prod_fighters,prod_torp,sector_id) VALUES\n";
        }
        $insert .= "(2,0,0,$default_prod_ore,$default_prod_organics,$default_prod_goods,".
                   "$default_prod_energy,$default_prod_fighters,$default_prod_torp,$n),\n";
      }
      $insert = substr_replace($insert, ";", -2);
      $results = $db->Execute($insert);
      print "";
      if(!$results) {
         PrintFlush($db->ErrorMsg());
        print "<pre>";
        print_r($insert);
        PrintFlush("</pre>");
        die("DB error while placing planets");
      }
      PrintFlush("- completed.<br>");

      $links=array();
      $hi=-1;
      for($l = 0; $l<$loops; $l++) {
        $lo=$hi+1;
        $hi = round(($sector_max)*($l+1)/$loops)-1;
        echo"Creating warp loop ".($l+1)." of $loops (from sector $lo to $hi)\n";
        for($i=$lo; $i<$hi; $i++) {
          $links[$i][] = $i+1;
          $links[$i+1][] = $i;
        }
        $links[$lo][]=$hi;
        $links[$hi][]=$lo;
        echo "- completed.<br>";
      }


      PrintFlush("Randomly generating $sector_max two-way warps ");
      $dups = 0;
      for($i=0; $i<$sector_max; $i++) {
        do {
          do {
            $x = rand(1,$sector_max-1);
            $y = rand(1,$sector_max-1);
          } while ($x==$y);

          // Only need to check in one direction because only
          // two-way links exist so far.
          $duplicate=FALSE;
          if(isset($links[$x])) {
            foreach($links[$x] as $v) {
              if($y == $v) {
                $duplicate=TRUE;
                $dups++;
                break;
              }
            }
          }
        } while ($duplicate);
        $links[$x][]=$y;
        $links[$y][]=$x;
      }
      PrintFlush("- $dups duplicates prevented - completed.<br>");


      PrintFlush("Randomly generating $sector_max one-way warps ");
      $dups = 0;
      for($i=0; $i<$sector_max; $i++) {
        do {
          do {
            $x = rand(1,$sector_max-1);
            $y = rand(1,$sector_max-1);
          } while ($x==$y);

          $duplicate=FALSE;
          if(isset($links[$x])) {
            foreach($links[$x] as $v) {
              if($y == $v) {
                $duplicate=TRUE;
                $dups++;
                break;
              }
            }
          }
        } while ($duplicate);
        $links[$x][]=$y;
      }
      PrintFlush("- $dups duplicates prevented - completed.<br>");


      PrintFlush("Dumping warps to database ");
      $i = 0;
      foreach($links as $k1 => $v1) {
        foreach($links[$k1] as $k2 => $v2) {
          if(!($i%5000)) {
            if($i) {
              $insert = substr_replace($insert, ";", -2);
              $results = $db->Execute($insert);
              if(!$results) {
                PrintFlush($db->ErrorMsg());
                print "<pre>\n";
                print_r($insert);
                PrintFlush("</pre>");
                die("DB error while placing one-way warps");
              }
            }
            $insert = "INSERT INTO $dbtables[links] (link_start,link_dest) VALUES\n";
          }
          $insert .= "($k1,$v2),\n";
          $i++;
        }
      }
      $insert = substr_replace($insert, ";", -2);
      $results = $db->Execute($insert);
      print "";
      if(!$results) {
        PrintFlush($db->ErrorMsg());
        print "<pre>\n";
        print_r($insert);
        PrintFlush("</pre>");
        die("DB error while inserting links");
      }
      PrintFlush("- completed.<br>");


      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=7>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<input type=submit value=Confirm>";
      echo "</form>";
      break;

// Stage 7, Let there be life
   case "7":
      echo "<B><BR>Configuring game scheduler<BR></B>";

      echo "<BR>Update ticks will occur every $sched_ticks minutes<BR>";
 
      echo "Turns will occur every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_turns.php', '',unix_timestamp(now()))");

      echo "Defenses will be checked every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_defenses.php', '',unix_timestamp(now()))");

      echo "Furangees will play every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_furangee.php', '',unix_timestamp(now()))");

      echo "Interests on IGB accounts will be accumulated every $sched_IGB minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_IGB, 0, 'sched_IGB.php', '',unix_timestamp(now()))");

      echo "News will be generated every $sched_news minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_news, 0, 'sched_news.php', '',unix_timestamp(now()))");

      echo "Planets will generate production every $sched_planets minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_planets, 0, 'sched_planets.php', '',unix_timestamp(now()))");

      echo "Ports will regenerate every $sched_ports minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_ports, 0, 'sched_ports.php', '',unix_timestamp(now()))");

      echo "Ships will be towed from fed sectors every $sched_turns minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_turns, 0, 'sched_tow.php', '',unix_timestamp(now()))");

      echo "Rankings will be generated every $sched_ranking minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', '',unix_timestamp(now()))");

      echo "Sector Defences will degrade every $sched_degrade minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', '',unix_timestamp(now()))");

      echo "The planetary apocalypse will occur every $sched_apocalypse minutes.<br>";
      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES('', 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', '',unix_timestamp(now()))");

      echo "<B><BR>Configuring ship types<p></B>";

      echo "Inserting ship type: Spacewagon..."; //starting ship
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "1, " .                //type_id
                   "'Spacewagon', " .     //name
                   "'spacewagon.gif', " . //image
                   "'The Spacewagon ship class is often referred to as \"flying trash can\". The surname certainly befits its appearance, as well as functionnality. This is the standard ship issued by the Federation to new colonists departing Earth. This class of ship possesses minimum cargo and weapons space. In addition, its short range engines are not suited for long space travel. Hopping along warp lanes is the only viable way of moving through the universe using this ship.'," .
                   "'Y', " .              //buyable
                   "1000, " .             //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "10, " .               //turnstobuild
                   "0, " .                //minhull
                   "5, " .                //maxhull
                   "0, " .                //minengines
                   "4, " .                //maxengines
                   "0, " .                //minpower
                   "1, " .                //maxpower (that's MISTER power for you) ;)
                   "0, " .                //mincomputer
                   "1, " .                //maxcomputer
                   "0, " .                //minsensors
                   "2, " .                //maxsensors
                   "0, " .                //minbeams
                   "0, " .                //maxbeams
                   "0, " .                //mintorp_launchers
                   "0, " .                //maxtorp_launchers
                   "0, " .                //minshields
                   "0, " .                //maxshields
                   "0, " .                //minarmour
                   "2, " .                //maxarmour
                   "0, " .                //mincloak
                   "4  " .                //maxcloak
                   ")");
      echo "done<br>";

      echo "Inserting ship type: Stinger..."; //base attack ship
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "2, " .                //type_id
                   "'Stinger', " .        //name
                   "'stinger.gif', " .    //image
                   "'This small attack ship is a favorite among space pirates. Its small, lightweigth hull allows for tight turning and a good cruise speed. Since most of the ship\'s limited interior space is used by the weapons system and engines, this ship can carry minimal cargo. Ideal for small slave raids.'," .
                   "'Y', " .              //buyable
                   "80000, " .            //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "50, " .               //turnstobuild
                   "3, " .                //minhull
                   "4, " .                //maxhull
                   "5, " .                //minengines
                   "8, " .                //maxengines
                   "4, " .                //minpower
                   "8, " .                //maxpower 
                   "4, " .                //mincomputer
                   "8, " .                //maxcomputer
                   "4, " .                //minsensors
                   "8, " .                //maxsensors
                   "4, " .                //minbeams
                   "8, " .                //maxbeams
                   "0, " .                //mintorp_launchers
                   "1, " .                //maxtorp_launchers
                   "0, " .                //minshields
                   "2, " .                //maxshields
                   "0, " .                //minarmour
                   "2, " .                //maxarmour
                   "4, " .                //mincloak
                   "4  " .                //maxcloak
                   ")");
      echo "done<br>";

      echo "Inserting ship type: Marauder..."; //base trade ship
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "3, " .                //type_id
                   "'Marauder', " .       //name
                   "'marauder.gif', " .   //image
                   "'The marauder is the standard Feredation supply ship. It offers reasonable cargo space and can be decently outfitted with enough armour to resist attacks from pirates. It is well-liked by Federation officials, who turn its spacious cargo bay into luxuriant living quarters.'," .
                   "'Y', " .              //buyable
                   "120000, " .           //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "60, " .               //turnstobuild
                   "5, " .                //minhull
                   "12, " .               //maxhull
                   "2, " .                //minengines
                   "6, " .                //maxengines
                   "1, " .                //minpower
                   "4, " .                //maxpower 
                   "1, " .                //mincomputer
                   "4, " .                //maxcomputer
                   "2, " .                //minsensors
                   "4, " .                //maxsensors
                   "1, " .                //minbeams
                   "4, " .                //maxbeams
                   "1, " .                //mintorp_launchers
                   "4, " .                //maxtorp_launchers
                   "2, " .                //minshields
                   "6, " .                //maxshields
                   "2, " .                //minarmour
                   "6, " .                //maxarmour
                   "2, " .                //mincloak
                   "4  " .                //maxcloak
                   ")");
      echo "done<br>";

      echo "Inserting ship type: Katana..."; //base balanced
      $db->Execute("INSERT INTO $dbtables[ship_types] VALUES (" .
                   "4, " .                //type_id
                   "'Katana', " .         //name
                   "'katana.gif', " .     //image
                   "'The Katana is the latest technological wonder of the Federation. This ship combines most of the advantages of the Stinger class with those of the Marauder. Nice all-rounder, this ship is perfect for the successful space adventurer. It is also the strongest ship made available to the general population by the Federation.'," .
                   "'Y', " .              //buyable
                   "180000, " .           //cost_credits
                   "0, " .                //cost_ore
                   "0, " .                //cost_goods
                   "0, " .                //cost_energy
                   "0, " .                //cost_organics
                   "55, " .               //turnstobuild
                   "3, " .                //minhull
                   "8, " .                //maxhull
                   "3, " .                //minengines
                   "6, " .                //maxengines
                   "2, " .                //minpower
                   "6, " .                //maxpower 
                   "2, " .                //mincomputer
                   "6, " .                //maxcomputer
                   "1, " .                //minsensors
                   "6, " .                //maxsensors
                   "1, " .                //minbeams
                   "6, " .                //maxbeams
                   "1, " .                //mintorp_launchers
                   "6, " .                //maxtorp_launchers
                   "1, " .                //minshields
                   "6, " .                //maxshields
                   "3, " .                //minarmour
                   "6, " .                //maxarmour
                   "3, " .                //mincloak
                   "5  " .                //maxcloak
                   ")");
      echo "done<br>";

/************************************************************
Ships from here will have to be built, and the stats above
will probably all be changed when testing, so no use defining
these right now.
************************************************************/

      echo "Inserting ship type: Wraith...";
      echo "done<br>";

      echo "Inserting ship type: Raven...";
      echo "done<br>";

      echo "Inserting ship type: Triton...";
      echo "done<br>";

      echo "Inserting ship type: Phoenix...";
      echo "done<br>";

      echo "Inserting ship type: Sequoia...";
      echo "done<br>";

      echo "Inserting ship type: Valkyrie...";
      echo "done<br>";

      echo "Inserting ship type: Nemesis...";
      echo "done<br>";

      echo "Inserting ship type: Golem...";
      echo "done<br>";

      echo "Inserting ship type: Behemoth...";
      echo "done<br>";

      $password = substr($admin_mail, 0, $maxlen_password);
      echo "<BR><BR><center><B>Your admin login is: <BR>";
      echo "<BR>Username: $admin_mail";
      echo "<BR>Password: $password<BR></B></center>";
      newplayer($admin_mail, "WebMaster", $password, "WebMaster\'s Ship");
  
      PrintFlush("<BR><BR><center><BR><B>Congratulations! Universe created successfully.<BR>");
      PrintFlush("Click <A HREF=login.php>here</A> to return to the login screen.</B></center>");
      break;

// Pre-stage, What's the password?
   default:
      echo "<form action=create_universe.php method=post>";
      echo "Password: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
      echo "<input type=submit value=Submit><input type=hidden name=step value=1>";
      echo "<input type=reset value=Reset>";
      echo "</form>";
      break;
}

// Done, And it took God seven days
$StopTime=$BenchmarkTimer->stop();
$Elapsed=$BenchmarkTimer->elapsed();
PrintFlush("<br>Elapsed Time - $Elapsed");
include("footer.php");
?>
