<?
// Separate userpass into username & password to support the legacy of multiple cookies for login.
if (preg_match("/global_funcs.php/i", $PHP_SELF)) {
      echo "You can not access this file directly!";
      die();
}


if ($userpass != '' and $userpass != '+') {
  $username = substr($userpass, 0, strpos($userpass, "+"));
  $password = substr($userpass, strpos($userpass, "+")+1);

}

// Ensure lang is set
$found = 0;
if(!empty($lang))
{
  if(!preg_match("/^[\w]+$/", $lang)) 
  {
     $lang = $default_lang;

  }
  foreach($avail_lang as $key => $value)
  {
    if($lang == $value[file])
    {
      SetCookie("lang",$lang,time()+(3600*2),$gamepath,$gamedomain);
      $found = 1;
      break;
    }
  }

  if($found == 0)
    $lang = $default_lang;
}

if (!isset($lang) || empty($lang))
  $lang = $default_lang;
$lang = $lang . ".inc";
//Planet log constants
define(PLOG_GENESIS_CREATE,1);
define(PLOG_GENESIS_DESTROY,2);
define(PLOG_CAPTURE,3);
define(PLOG_ATTACKED,4);
define(PLOG_SCANNED,5);
define(PLOG_OWNER_DEAD,6);
define(PLOG_DEFEATED,7);
define(PLOG_SOFA,8);
define(PLOG_PLANET_DESTRUCT,9);

//Log constants

define(LOG_LOGIN, 1);
define(LOG_LOGOUT, 2);
define(LOG_ATTACK_OUTMAN, 3);           //sent to target when better engines
define(LOG_ATTACK_OUTSCAN, 4);          //sent to target when better cloak
define(LOG_ATTACK_EWD, 5);              //sent to target when EWD engaged
define(LOG_ATTACK_EWDFAIL, 6);          //sent to target when EWD failed
define(LOG_ATTACK_LOSE, 7);             //sent to target when he lost
define(LOG_ATTACKED_WIN, 8);            //sent to target when he won
define(LOG_TOLL_PAID, 9);               //sent when paid a toll
define(LOG_HIT_MINES, 10);              //sent when hit mines
define(LOG_SHIP_DESTROYED_MINES, 11);   //sent when destroyed by mines
define(LOG_PLANET_DEFEATED_D, 12);      //sent when one of your defeated planets is destroyed instead of captured
define(LOG_PLANET_DEFEATED, 13);        //sent when a planet is defeated
define(LOG_PLANET_NOT_DEFEATED, 14);    //sent when a planet survives
define(LOG_RAW, 15);                    //this log is sent as-is
define(LOG_TOLL_RECV, 16);              //sent when you receive toll money
define(LOG_DEFS_DESTROYED, 17);         //sent for destroyed sector defenses
define(LOG_PLANET_EJECT, 18);           //sent when ejected from a planet due to alliance switch
define(LOG_BADLOGIN, 19);               //sent when bad login
define(LOG_PLANET_SCAN, 20);            //sent when a planet has been scanned
define(LOG_PLANET_SCAN_FAIL, 21);       //sent when a planet scan failed
define(LOG_PLANET_CAPTURE, 22);         //sent when a planet is captured
define(LOG_SHIP_SCAN, 23);              //sent when a ship is scanned
define(LOG_SHIP_SCAN_FAIL, 24);         //sent when a ship scan fails
define(LOG_FURANGEE_ATTACK, 25);        //furangees send this to themselves
define(LOG_STARVATION, 26);             //sent when colonists are starving... Is this actually used in the game?
define(LOG_TOW, 27);                    //sent when a player is towed
define(LOG_DEFS_DESTROYED_F, 28);       //sent when a player destroys fighters
define(LOG_DEFS_KABOOM, 29);            //sent when sector fighters destroy you
define(LOG_HARAKIRI, 30);               //sent when self-destructed
define(LOG_TEAM_REJECT, 31);            //sent when player refuses invitation
define(LOG_TEAM_RENAME, 32);            //sent when renaming a team
define(LOG_TEAM_M_RENAME, 33);          //sent to members on team rename
define(LOG_TEAM_KICK, 34);              //sent to booted player
define(LOG_TEAM_CREATE, 35);            //sent when created a team
define(LOG_TEAM_LEAVE, 36);             //sent when leaving a team
define(LOG_TEAM_NEWLEAD, 37);           //sent when leaving a team, appointing a new leader
define(LOG_TEAM_LEAD, 38);              //sent to the new team leader
define(LOG_TEAM_JOIN, 39);              //sent when joining a team
define(LOG_TEAM_NEWMEMBER, 40);         //sent to leader on join
define(LOG_TEAM_INVITE, 41);            //sent to invited player
define(LOG_TEAM_NOT_LEAVE, 42);         //sent to leader on leave
define(LOG_ADMIN_HARAKIRI, 43);         //sent to admin on self-destruct
define(LOG_ADMIN_PLANETDEL, 44);        //sent to admin on planet destruction instead of capture
define(LOG_DEFENCE_DEGRADE, 45);        //sent sector fighters have no supporting planet
define(LOG_PLANET_CAPTURED, 46);            //sent to player when he captures a planet
define(LOG_BOUNTY_CLAIMED,47);            //sent to player when they claim a bounty
define(LOG_BOUNTY_PAID,48);            //sent to player when their bounty on someone is paid
define(LOG_BOUNTY_CANCELLED,49);            //sent to player when their bounty is refunded
define(LOG_SPACE_PLAGUE,50);            // sent when space plague attacks a planet
define(LOG_PLASMA_STORM,51);           // sent when a plasma storm attacks a planet
define(LOG_BOUNTY_FEDBOUNTY,52);       // Sent when the federation places a bounty on a player
define(LOG_PLANET_BOMBED,53);     //Sent after bombing a planet
define(LOG_ADMIN_ILLEGVALUE, 54);        //sent to admin on planet destruction instead of capture
define(LOG_CHEAT_TEAM,55);            // Sent when someone attempts the kick any team member cheat
define(LOG_PLANET_YOUR_CAPTURED,56);  // Sent when your planet is captured


// Database tables variables
$dbtables['links'] = "${db_prefix}links";
$dbtables['planets'] = "${db_prefix}planets";
$dbtables['traderoutes'] = "${db_prefix}traderoutes";
$dbtables['players'] = "${db_prefix}players";                
$dbtables['universe'] = "${db_prefix}universe";
$dbtables['zones'] = "${db_prefix}zones";
$dbtables['ibank_accounts'] = "${db_prefix}ibank_accounts";             
$dbtables['IGB_transfers'] = "${db_prefix}IGB_transfers";            
$dbtables['teams'] = "${db_prefix}teams";
$dbtables['news'] = "${db_prefix}news";
$dbtables['messages'] = "${db_prefix}messages";
$dbtables['furangee'] = "${db_prefix}furangee";
$dbtables['sector_defence'] = "${db_prefix}sector_defence";
$dbtables['scheduler'] = "${db_prefix}scheduler";
$dbtables['ip_bans'] = "${db_prefix}ip_bans";
$dbtables['logs'] = "${db_prefix}logs";
//$dbtables['gen_id'] = "${db_prefix}gen_id";
$dbtables['bounty'] = "${db_prefix}bounty";
$dbtables['movement_log'] = "${db_prefix}movement_log";        
$dbtables['ship_types'] = "${db_prefix}ship_types";               
$dbtables['ships'] = "${db_prefix}ships";
$dbtables['planet_log'] = "${db_prefix}planet_log";
$dbtables['ip_log'] = "${db_prefix}ip_log";

$dbtables['email_log'] = "${db_prefix}email_log";

function mypw($one,$two)
{
   return pow($one*1,$two*1);
}

function bigtitle()
{
  global $title;
  echo "<H1>$title</H1>\n";
}

function TEXT_GOTOMAIN()
{
  global $l_global_mmenu;
  echo $l_global_mmenu;
}

function TEXT_GOTOLOGIN()
{
global $l_global_mlogin;
  echo $l_global_mlogin;
}

function TEXT_JAVASCRIPT_BEGIN()
{
  echo "\n<SCRIPT LANGUAGE=\"JavaScript\">\n";
  echo "<!--\n";
}

function TEXT_JAVASCRIPT_END()
{
  echo "\n// -->\n";
  echo "</SCRIPT>\n";
}

function checklogin()
{
  $flag = 0;

  global $username, $l_global_needlogin, $l_global_died;
  global $password, $l_login_died, $l_die_please;
  global $db, $dbtables;

  $result1 = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
  $playerinfo = $result1->fields;

  $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
  $shipinfo = $res->fields;

  /* Check the cookie to see if username/password are empty - check password against database */
  if($username == "" or $password == "" or $password != $playerinfo['password'])
  {
    echo $l_global_needlogin;
    $flag = 1;
  }

  /* Check for destroyed ship */
  if($shipinfo['destroyed'] == "Y")
  {
    /* if the player has an escapepod, set the player up with a new ship */
    if($shipinfo['dev_escapepod'] == "Y")
    {
      $result2 = $db->Execute("UPDATE $dbtables[ships] SET class=1, hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector_id=0, ore=0, organics=0, energy=1000, colonists=0, goods=0, fighters=100, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, destroyed='N',dev_lssd='N' where ship_id=$shipinfo[ship_id]");
      echo $l_login_died;
      $flag = 1;
    }
    else
    {
      /* if the player doesn't have an escapepod - they're dead, delete them. */
      /* uhhh  don't delete them to prevent self-distruct inherit*/
      echo $l_global_died;

      echo $l_die_please;
      $flag = 1;
    }
  }
  global $server_closed;
  global $l_login_closed_message;
  if($server_closed && $flag==0)
  {
    echo $l_login_closed_message;
    $flag=1;
  }

  return $flag;
}

function connectdb()
{
  /* connect to database - and if we can't stop right there */
  global $dbhost;
  global $dbport;
  global $dbuname;
  global $dbpass;
  global $dbname;
  global $default_lang;
  global $lang;
  global $gameroot;
  global $db_type;
  global $db_persistent;
  global $db;
  global $ADODB_FETCH_MODE;

  $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

  if(!empty($dbport))
    $dbhost.= ":$dbport";

  $db = ADONewConnection("$db_type");
  if($db_persistent == 1)
    $result = $db->PConnect("$dbhost", "$dbuname", "$dbpass", "$dbname");
  else
    $result = $db->Connect("$dbhost", "$dbuname", "$dbpass", "$dbname");

  if(!$result)
    die ("Unable to connect to the database");
}

function updatecookie()
{
  // refresh the cookie with username/password/id/res - times out after 60 mins, and player must login again.
  global $gamepath;
  global $gamedomain;
  global $userpass;
  global $username;
  global $password;
  global $id;
  global $res;
  // The new combined cookie login.
  $userpass = $username."+".$password;
  SetCookie("userpass",$userpass,time()+(3600*2),$gamepath,$gamedomain);
  if ($userpass != '' and $userpass != '+') {
      setcookie("username","",0); // Legacy support, delete the old login cookies.
      setcookie("password","",0); // Legacy support, delete the old login cookies.
    $username = substr($userpass, 0, strpos($userpass, "+"));
    $password = substr($userpass, strpos($userpass, "+")+1);
  }
  setcookie("id", $id);
  setcookie("res", $res);
}


function playerlog($sid, $log_type, $data = "")
{
  global $db, $dbtables;
  /* write log_entry to the player's log - identified by player's player_id - sid. */
  if ($sid != "" && !empty($log_type))
  {
    $db->Execute("INSERT INTO $dbtables[logs] VALUES('', $sid, $log_type, NOW(), '$data')");
  }
}

function adminlog($log_type, $data = "")
{
  global $db, $dbtables;
  /* write log_entry to the admin log  */
  if (!empty($log_type))
  {
    $db->Execute("INSERT INTO $dbtables[logs] VALUES('', 0, $log_type, NOW(), '$data')");
  }
}

function gen_score($sid)
{
  global $ore_price;
  global $organics_price;
  global $goods_price;
  global $energy_price;
  global $upgrade_cost;
  global $upgrade_factor;
  global $dev_genesis_price;
  global $dev_beacon_price;
  global $dev_emerwarp_price;
  global $dev_warpedit_price;
  global $dev_minedeflector_price;
  global $dev_escapepod_price;
  global $dev_fuelscoop_price;
  global $dev_lssd_price;
  global $fighter_price;
  global $torpedo_price;
  global $armour_price;
  global $colonist_price;
  global $base_ore;
  global $base_goods;
  global $base_organics;
  global $base_credits;
  global $db, $dbtables;

  $calc_hull = "ROUND(pow($upgrade_factor,hull))";
  $calc_engines = "ROUND(pow($upgrade_factor,engines))";
  $calc_power = "ROUND(pow($upgrade_factor,power))";
  $calc_computer = "ROUND(pow($upgrade_factor,computer))";
  $calc_sensors = "ROUND(pow($upgrade_factor,sensors))";
  $calc_beams = "ROUND(pow($upgrade_factor,beams))";
  $calc_torp_launchers = "ROUND(pow($upgrade_factor,torp_launchers))";
  $calc_shields = "ROUND(pow($upgrade_factor,shields))";
  $calc_armour = "ROUND(pow($upgrade_factor,armour))";
  $calc_cloak = "ROUND(pow($upgrade_factor,cloak))";
  $calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak)*$upgrade_cost";

  $calc_torps = "$dbtables[ships].torps*$torpedo_price";
  $calc_armour_pts = "armour_pts*$armour_price";
  $calc_ship_ore = "$dbtables[ships].ore*$ore_price";
  $calc_ship_organics = "$dbtables[ships].organics*$organics_price";
  $calc_ship_goods = "$dbtables[ships].goods*$goods_price";
  $calc_ship_energy = "$dbtables[ships].energy*$energy_price";
  $calc_ship_colonists = "$dbtables[ships].colonists*$colonist_price";
  $calc_ship_fighters = "$dbtables[ships].fighters*$fighter_price";
  $calc_equip = "$calc_torps+$calc_armour_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";

  $calc_dev_warpedit = "dev_warpedit*$dev_warpedit_price";
  $calc_dev_genesis = "dev_genesis*$dev_genesis_price";
  $calc_dev_beacon = "dev_beacon*$dev_beacon_price";
  $calc_dev_emerwarp = "dev_emerwarp*$dev_emerwarp_price";
  $calc_dev_escapepod = "IF(dev_escapepod='Y', $dev_escapepod_price, 0)";
  $calc_dev_fuelscoop = "IF(dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
  $calc_dev_lssd = "IF(dev_lssd='Y', $dev_lssd_price, 0)";
  $calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
  $calc_dev = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_beacon+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_minedeflector+$calc_dev_lssd";

  $calc_planet_goods = "SUM($dbtables[planets].organics)*$organics_price+SUM($dbtables[planets].ore)*$ore_price+SUM($dbtables[planets].goods)*$goods_price+SUM($dbtables[planets].energy)*$energy_price";
  $calc_planet_colonists = "SUM($dbtables[planets].colonists)*$colonist_price";
  $calc_planet_defence = "SUM($dbtables[planets].fighters)*$fighter_price+IF($dbtables[planets].base='Y', $base_credits+SUM($dbtables[planets].torps)*$torpedo_price, 0)";
  $calc_planet_credits = "SUM($dbtables[planets].credits)";

  $res = $db->Execute("SELECT $calc_levels+$calc_equip+$calc_dev+$dbtables[players].credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits AS score FROM $dbtables[players] LEFT JOIN $dbtables[planets] ON $dbtables[planets].owner=$dbtables[players].player_id LEFT JOIN $dbtables[ships] ON $dbtables[players].player_id=$dbtables[ships].player_id WHERE $dbtables[players].player_id=$sid AND destroyed='N'");
  $row = $res->fields;
  $score = $row[score];
  $res = $db->Execute("SELECT balance, loan FROM $dbtables[ibank_accounts] where player_id = $sid");
  if($res)
  {
     $row = $res->fields;
     $score += ($row[balance] - $row[loan]);
  }
  $score = ROUND(SQRT($score));
  $db->Execute("UPDATE $dbtables[players] SET score=$score WHERE player_id=$sid");

  return $score;
}

function db_kill_player($player_id)
{
  global $default_prod_ore;
  global $default_prod_organics;
  global $default_prod_goods;
  global $default_prod_energy;
  global $default_prod_fighters;
  global $default_prod_torp;
  global $gameroot;
  global $db,$dbtables;

  include("languages/english.inc");

  $db->Execute("UPDATE $dbtables[ships] SET destroyed='Y',on_planet='N',sector_id=0,cleared_defences=' ' WHERE player_id=$player_id");
  $db->Execute("DELETE from $dbtables[bounty] WHERE placed_by = $player_id");

  $res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner='$player_id' AND base='Y'");
  $i=0;

  while(!$res->EOF && $res)
  {
    $sectors[$i] = $res->fields[sector_id];
    $i++;
    $res->MoveNext();
  }

  $res = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE owner='$player_id'");
  while(!$res->EOF && $res)
  {
     planet_log($res->fields[sector_id],$player_id,$player_id,PLOG_OWNER_DEAD);   
     $res->MoveNext();
  }

  $db->Execute("UPDATE $dbtables[planets] SET owner=0,fighters=0, base='N' WHERE owner=$player_id");

  if(!empty($sectors))
  {
    foreach($sectors as $sector)
    {
      calc_ownership($sector);
    }
  }
  $db->Execute("DELETE FROM $dbtables[sector_defence] where player_id=$player_id");

  $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='N' AND owner=$player_id");
  $zone = $res->fields;

  $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE zone_id=$zone[zone_id]");

  $query = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id='$player_id'");
  $name = $query->fields;

  $headline = $name[character_name] . $l_killheadline;

  $newstext=str_replace("[name]",$name[character_name],$l_news_killed);

  $news = $db->Execute("INSERT INTO $dbtables[news] (headline, newstext, user_id, date, news_type) VALUES ('$headline','$newstext','$player_id',NOW(), 'killed')");
}

function NUMBER($number, $decimals = 0)
{
  global $local_number_dec_point;
  global $local_number_thousands_sep;
  return number_format($number, $decimals, $local_number_dec_point, $local_number_thousands_sep);
}

function NUM_HOLDS($level_hull)
{
  global $level_factor;
  return round(mypw($level_factor, $level_hull) * 100);
}

function NUM_ENERGY($level_power)
{
  global $level_factor;
  return round(mypw($level_factor, $level_power) * 500);
}

function NUM_FIGHTERS($level_computer)
{
  global $level_factor;
  return round(mypw($level_factor, $level_computer) * 100);
}

function NUM_TORPEDOES($level_torp_launchers)
{
  global $level_factor;
  return round(mypw($level_factor, $level_torp_launchers) * 100);
}

function NUM_ARMOUR($level_armour)
{
  global $level_factor;
  return round(mypw($level_factor, $level_armour) * 100);
}

function NUM_BEAMS($level_beams)
{
  global $level_factor;
  return round(mypw($level_factor, $level_beams) * 100);
}

function NUM_SHIELDS($level_shields)
{
  global $level_factor;
  return round(mypw($level_factor, $level_shields) * 100);
}

function SCAN_SUCCESS($level_scan, $level_cloak)
{
  return (5 + $level_scan - $level_cloak) * 5;
}

function SCAN_ERROR($level_scan, $level_cloak)
{
  global $scan_error_factor;

  $sc_error = (4 + $level_scan / 2 - $level_cloak / 2) * $scan_error_factor;

  if($sc_error<1)
  {
    $sc_error=1;
  }
  if($sc_error>99)
  {
    $sc_error=99;
  }

  return $sc_error;
}

function explode_mines($sector, $num_mines)
{
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='M' order by quantity ASC");
    echo $db->ErrorMsg();
    
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF && $num_mines > 0)
       {
          $row = $result3->fields;
          if($row[quantity] > $num_mines)
          {
             $update = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity - $num_mines where defence_id = $row[defence_id]");
             $num_mines = 0;
          }
          else
          {
             $update = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
             $num_mines -= $row[quantity];
          }
          $result3->MoveNext();
       }
    }

}

function destroy_fighters($sector, $num_fighters)
{
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='F' order by quantity ASC");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF && $num_fighters > 0)
       {
          $row=$result3->fields;
          if($row[quantity] > $num_fighters)
          {
             $update = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity - $num_fighters where defence_id = $row[defence_id]");
             $num_fighters = 0;
          }
          else
          {
             $update = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
             $num_fighters -= $row[quantity];
          }
          $result3->MoveNext();
       }
    }

}

function message_defence_owner($sector, $message)
{
    global $db, $dbtables;
    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' ");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF)
       {

          playerlog($result3->fields[player_id],LOG_RAW, $message);
          $result3->MoveNext();
       }
    }
}

function distribute_toll($sector, $toll, $total_fighters)
{
    global $db, $dbtables;

    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' AND defence_type ='F' ");
    echo $db->ErrorMsg();
    //Put the defence information into the array "defenceinfo"
    if($result3 > 0)
    {
       while(!$result3->EOF)
       {
          $row = $result3->fields;
          $toll_amount = ROUND(($row['quantity'] / $total_fighters) * $toll);
          $db->Execute("UPDATE $dbtables[players] set credits=credits + $toll_amount WHERE player_id = $row[player_id]");
          playerlog($row[player_id], LOG_TOLL_RECV, "$toll_amount|$sector");
          $result3->MoveNext();
       }
    }

}

function defence_vs_defence($player_id)
{
   global $db, $dbtables;

   $result1 = $db->Execute("SELECT * from $dbtables[sector_defence] where player_id = $player_id");
   if($result1 > 0)
   {
      while(!$result1->EOF)
      {
         $row=$result1->fields;
         $deftype = $row[defence_type] == 'F' ? 'Fighters' : 'Mines';
         $qty = $row['quantity'];
         $result2 = $db->Execute("SELECT * from $dbtables[sector_defence] where sector_id = $row[sector_id] and player_id <> $player_id ORDER BY quantity DESC");
         if($result2 > 0)
         {
            while(!$result2->EOF && $qty > 0)
            {
               $cur = $result2->fields;
               $targetdeftype = $cur[defence_type] == 'F' ? $l_fighters : $l_mines;
               if($qty > $cur['quantity'])
               {
                  $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $cur[defence_id]");
                  $qty -= $cur['quantity'];
                  $db->Execute("UPDATE $dbtables[sector_defence] SET quantity = $qty where defence_id = $row[defence_id]");
                  playerlog($cur[player_id], LOG_DEFS_DESTROYED, "$cur[quantity]|$targetdeftype|$row[sector_id]");
                  playerlog($row[player_id], LOG_DEFS_DESTROYED, "$cur[quantity]|$deftype|$row[sector_id]");
               }
               else
               {
                  $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
                  $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=quantity - $qty WHERE defence_id = $cur[defence_id]");
                  playerlog($cur[player_id], LOG_DEFS_DESTROYED, "$qty|$targetdeftype|$row[sector_id]");
                  playerlog($row[player_id], LOG_DEFS_DESTROYED, "$qty|$deftype|$row[sector_id]");
                  $qty = 0;
               }
               $result2->MoveNext();
            }
         }
         $result1->MoveNext();
      }
      $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE quantity <= 0");
   }
}

function kick_off_planet($player_id,$whichteam)
{
   global $db, $dbtables;

   $result1 = $db->Execute("SELECT * from $dbtables[planets] where owner = '$player_id' ");

   if($result1 > 0)
   {
      while(!$result1->EOF)
      {
         $row = $result1->fields;
         $result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and planet_id = '$row[planet_id]' and player_id <> '$player_id' ");
         if($result2 > 0)
         {
            while(!$result2->EOF )
            {
               $cur = $result2->fields;
               $db->Execute("UPDATE $dbtables[ships] SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
               playerlog($cur[player_id], LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
               $result2->MoveNext();
            }
         }
         $result1->MoveNext();
      }
   }
}


function calc_ownership($sector)
{
  global $min_bases_to_own, $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;
  global $db, $dbtables;

  $res = $db->Execute("SELECT owner, corp FROM $dbtables[planets] WHERE sector_id=$sector AND base='Y'");
  $num_bases = $res->RecordCount();

  $i=0;
  if($num_bases > 0)
  {

   while(!$res->EOF)
    {
      $bases[$i] = $res->fields;
      $i++;
      $res->MoveNext();
    }
  }
  else
    return "Sector ownership didn't change";

  $owner_num = 0;

  foreach($bases as $curbase)
  {
    $curcorp=-1;
    $curship=-1;
    $loop = 0;
    while ($loop < $owner_num)
    {
      if($curbase[corp] != 0)
      {
        if($owners[$loop][type] == 'C')
        {
          if($owners[$loop][id] == $curbase[corp])
          {
            $curcorp=$loop;
            $owners[$loop][num]++;
          }
        }
      }

      if($owners[$loop][type] == 'S')
      {
        if($owners[$loop][id] == $curbase[owner])
        {
          $curship=$loop;
          $owners[$loop][num]++;
        }
      }

      $loop++;
    }

    if($curcorp == -1)
    {
      if($curbase[corp] != 0)
      {
         $curcorp=$owner_num;
         $owner_num++;
         $owners[$curcorp][type] = 'C';
         $owners[$curcorp][num] = 1;
         $owners[$curcorp][id] = $curbase[corp];
      }
    }

    if($curship == -1)
    {
      if($curbase[owner] != 0)
      {
        $curship=$owner_num;
        $owner_num++;
        $owners[$curship][type] = 'S';
        $owners[$curship][num] = 1;
        $owners[$curship][id] = $curbase[owner];
      }
    }
  }

  // We've got all the contenders with their bases.
  // Time to test for conflict

  $loop=0;
  $nbcorps=0;
  $nbships=0;
  while($loop < $owner_num)
  {
    if($owners[$loop][type] == 'C')
      $nbcorps++;
    else
    {
      $res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=" . $owners[$loop][id]);
      if($res && $res->RecordCount() != 0)
      {
        $curship = $res->fields;
        $ships[$nbships]=$owners[$loop][id];
        $scorps[$nbships]=$curship[team];
        $nbships++;
      }
    }

    $loop++;
  }

  //More than one corp, war
  if($nbcorps > 1)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
    return $l_global_warzone;
  }

  //More than one unallied ship, war
  $numunallied = 0;
  foreach($scorps as $corp)
  {
    if($corp == 0)
      $numunallied++;
  }
  if($numunallied > 1)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
    return $l_global_warzone;
  }

  //Unallied ship, another corp present, war
  if($numunallied > 0 && $nbcorps > 0)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
    return $l_global_warzone;
  }

  //Unallied ship, another ship in a corp, war
  if($numunallied > 0)
  {
    $query = "SELECT team FROM $dbtables[players] WHERE (";
    $i=0;
    foreach($ships as $ship)
    {
      $query = $query . "player_id=$ship";
      $i++;
      if($i!=$nbships)
        $query = $query . " OR ";
      else
        $query = $query . ")";
    }
    $query = $query . " AND team!=0";
    $res = $db->Execute($query);
    if($res->RecordCount() != 0)
    {
      $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
      return $l_global_warzone;
    }
  }


  //Ok, all bases are allied at this point. Let's make a winner.
  $winner = 0;
  $i = 1;
  while ($i < $owner_num)
  {
    if($owners[$i][num] > $owners[$winner][num])
      $winner = $i;
    elseif($owners[$i][num] == $owners[$winner][num])
    {
      if($owners[$i][type] == 'C')
        $winner = $i;
    }
    $i++;
  }

  if($owners[$winner][num] < $min_bases_to_own)
  {
    $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
    return $l_global_nzone;
  }


  if($owners[$winner][type] == 'C')
  {
    $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='Y' && owner=" . $owners[$winner][id]);
    $zone = $res->fields;

    $res = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=" . $owners[$winner][id]);
    $corp = $res->fields;

    $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
    return "$l_global_team $corp[team_name]!";
  }
  else
  {
    $onpar = 0;
    foreach($owners as $curowner)
    {
      if($curowner[type] == 'S' && $curowner[id] != $owners[$winner][id] && $curowner[num] == $owners[winners][num])
        $onpar = 1;
        break;
    }

    //Two allies have the same number of bases
    if($onpar == 1)
    {
      $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
      return $l_global_nzone;
    }
    else
    {
      $res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE corp_zone='N' && owner=" . $owners[$winner][id]);
      $zone = $res->fields;

      $res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=" . $owners[$winner][id]);
      $ship = $res->fields;

      $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
      return "$l_global_player $ship[character_name]!";
    }
  }
}

function player_insignia_name($a_username) {

global $db, $dbtables, $username, $player_insignia;
global $l_insignia;

$res = $db->Execute("SELECT score FROM $dbtables[players] WHERE email='$a_username'");
$playerinfo = $res->fields;  
$score_array = array('1000', '3000', '6000', '9000', '12000', '15000', '20000', '40000', '60000', '80000', '100000', '120000', '160000', '200000', '250000', '300000', '350000', '400000', '450000', '500000');

for ( $i=0; $i<count($score_array); $i++)
{           
    if ( $playerinfo[score] < $score_array[$i])
     {
       $player_insignia = $l_insignia[$i];
       break;    
     }
}

if(!isset($player_insignia))
  $player_insignia = end($l_insignia);

return $player_insignia;

}

function t_port($ptype) {

global $l_ore, $l_none, $l_energy, $l_organics, $l_goods, $l_special;

switch ($ptype) {
    case "ore":
        $ret=$l_ore;
        break;
    case "none":
        $ret=$l_none;
        break;
    case "energy":
        $ret=$l_energy;
        break;
    case "organics":
        $ret=$l_organics;
        break;
    case "goods":
        $ret=$l_goods;
        break;
    case "special":
        $ret=$l_special;
        break;
}

return $ret;
}

/*
function GenNextID($id)
{
  global $db, $dbtables;

  $db->Execute("LOCK TABLES $dbtables[gen_id] write");
  $res = $db->Execute("SELECT count FROM $dbtables[gen_id] WHERE name='$id'");
  if(!$res)
    return(-1);
  $count = $res->fields[count];
  $res = $db->Execute("UPDATE $dbtables[gen_id] SET count=count+1 WHERE name='$id'");
  $count++;
  $db->Execute("UNLOCK TABLES");
  return($count);
}
*/

function stripnum($str)
{
  $str=(string)$str;
  $output = ereg_replace("[^0-9.]","",$str);
  return $output;
}

function collect_bounty($attacker,$bounty_on)
{
   global $db,$dbtables,$l_by_thefeds;
   $res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[players] WHERE bounty_on = $bounty_on AND bounty_on = player_id and placed_by <> 0");
   if($res)
   {
      while(!$res->EOF)
      {
         $bountydetails = $res->fields;
         if($res->fields[placed_by] == 0)
         {
            $placed = $l_by_thefeds;
         }
         else
         {
            $res2 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id = $bountydetails[placed_by]");
            $placed = $res2->fields[character_name];
         }
         $update = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $bountydetails[amount] WHERE player_id = $attacker");
         $delete = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");

         playerlog($attacker, LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
         playerlog($bountydetails[placed_by],LOG_BOUNTY_PAID,"$bountydetails[amount]|$bountydetails[character_name]");

         $res->MoveNext();
      }
   }
   $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_on = $bounty_on");
}

function cancel_bounty($bounty_on)
{
   global $db,$dbtables;
   $res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[players] WHERE bounty_on = $bounty_on AND bounty_on = player_id");
   if($res)
   {
      while(!$res->EOF)
      {
         $bountydetails = $res->fields;
         if($bountydetails[placed_by] <> 0)
         {
            $update = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $bountydetails[amount] WHERE player_id = $bountydetails[placed_by]");
    
            playerlog($bountydetails[placed_by],LOG_BOUNTY_CANCELLED,"$bountydetails[amount]|$bountydetails[character_name]");
         }
         $delete = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
         $res->MoveNext();
      }
   }
}

function get_player($player_id)
{
   global $db,$dbtables;
   $res = $db->Execute("SELECT character_name from $dbtables[players] where player_id = $player_id");
   if($res)
   {
      $row = $res->fields;
      $character_name = $row[character_name];
      return $character_name;
   }
   else
   {
      return "Unknown";
   }
}

function get_player_from_ship($ship_id)
{
   global $db,$dbtables;
   $res = $db->Execute("SELECT character_name from $dbtables[ships] LEFT JOIN $dbtables[players] USING(player_id) WHERE ship_id = $ship_id");
   if($res)
   {
      $row = $res->fields;
      $character_name = $row[character_name];
      return $character_name;
   }
   else
   {
      return "Unknown";
   }
}

function log_move($ship_id,$sector_id)
{
   global $db,$dbtables;
   $res = $db->Execute("INSERT INTO $dbtables[movement_log] (ship_id,sector_id,time) VALUES ($ship_id,$sector_id,NOW())");
}

function isLoanPending($player_id)
{
  global $db, $dbtables;
  global $IGB_lrate;

  $res = $db->Execute("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM $dbtables[ibank_accounts] WHERE player_id=$player_id");
  if($res)
  {
    $account=$res->fields;

    if($account[loan] == 0)
      return false;

    $curtime=time();
    $difftime = ($curtime - $account[time]) / 60;
    if($difftime > $IGB_lrate)
      return true;
    else
      return false;
  }
  else
    return false;

}

function newplayer($email, $char, $pass, $ship_name)
{
  global $db, $dbtables;
  global $start_credits, $start_turns, $default_lang;
  global $start_armour, $start_energy, $start_fighters, $max_turns;

  $stamp=date("Y-m-d H:i:s");

  $query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM $dbtables[players]");
  $res = $query->fields;

  $mturns = $res[mturns];

  if($mturns > $max_turns)
    $mturns = $max_turns;

  if($mturns < $start_turns)
    $mturns = $start_turns;

  //Create player
  $db->Execute("INSERT INTO $dbtables[players] VALUES(" .
               "''," .             //player_id
               "0," .              //currentship
               "'$char'," .        //character_name
               "'$pass'," .        //password
               "'$email'," .       //email
               "$start_credits," . //credits
               "$mturns," .        //turns
               "0," .              //turns_used
               "'$stamp'," .       //last_login
               "0," .              //rating
               "0," .              //score
               "0," .              //team
               "0," .              //team_invite
               "'N'," .            //interface
               "'1.1.1.1'," .      //ip_address
               "0," .              //preset1
               "0," .              //preset2
               "0," .              //preset3
               "'Y'," .            //trade_colonists
               "'N'," .            //trade_fighters
               "'N'," .            //trade_torps
               "'Y'," .            //trade_energy
               "'$default_lang'," .//lang
               "'Y'" .             //dhtml
               ")");
  
  //Get the new player's id
  $res = $db->Execute("SELECT player_id from $dbtables[players] WHERE email='$email'");
  $player_id = $res->fields[player_id];
  
  //Create player's ship
  $db->Execute("INSERT INTO $dbtables[ships] VALUES(" .
               "''," .             //ship_id
               "$player_id," .     //player_id
               "'1'," .            //class
               "'$ship_name'," .   //name
               "'N'," .            //destroyed
               "0," .              //hull
               "0," .              //engines
               "0," .              //power
               "0," .              //computer
               "0," .              //sensors
               "0," .              //beams
               "0," .              //torp_launchers
               "0," .              //torps
               "0," .              //shields
               "0," .              //armour
               "$start_armour," .  //armour_pts
               "0," .              //cloak
               "0," .              //sector_id
               "0," .              //ore
               "0," .              //organics
               "0," .              //goods
               "$start_energy," .  //energy
               "0," .              //colonists
               "$start_fighters," .//fighters
               "'N'," .            //on_planet
               "0," .              //dev_warpedit
               "0," .              //dev_genesis
               "0," .              //dev_beacon
               "0," .              //dev_emerwarp
               "'Y'," .            //dev_escapepod
               "'N'," .            //dev_fuelscoop
               "0," .              //dev_minedeflector
               "0," .              //planet_id
               "''," .             //cleared_defences
               "'N'" .            //dev_lssd
               ")");

  echo $db->ErrorMsg();
  //Get the new ship's id
  $res = $db->Execute("SELECT ship_id from $dbtables[ships] WHERE player_id=$player_id");
  $ship_id = $res->fields[ship_id];
  
  //Insert current ship in players table
  $db->Execute("UPDATE $dbtables[players] SET currentship=$ship_id WHERE player_id=$player_id");

  //Create player's zone
  $zone_name = "$char" . "\'s Territory";
  $db->Execute("INSERT INTO $dbtables[zones] VALUES(" .
               "''," .             //zone_id
               "'$zone_name'," .   //zone_name
               "$player_id," .     //owner
               "'N'," .            //corp_zone
               "'Y'," .            //allow_beacon
               "'Y'," .            //allow_attack
               "'Y'," .            //allow_planetattack
               "'Y'," .            //allow_warpedit
               "'Y'," .            //allow_planet
               "'Y'," .            //allow_trade
               "'Y'," .            //allow_defenses
               "0" .               //max_hull
               ")");

  //Create the IGB account
  $db->Execute("INSERT INTO $dbtables[ibank_accounts] (player_id,balance,loan) VALUES ($player_id,0,0)");

  return $player_id;
}
function planet_log($planet,$owner,$player_id,$action)
{
   global $db, $dbtables,$enhanced_logging;
   if($enhanced_logging)
   {
      $res = $db->Execute("SELECT ip_address from $dbtables[players] WHERE player_id=$player_id");
      $ip = $res->fields[ip_address];
      $db->Execute("INSERT INTO $dbtables[planet_log] (planet_id,player_id,owner_id,ip_address,action,time) VALUES ($planet,$player_id,$owner,'$ip',$action,NOW())");
   }
   
}

function ip_log($player_id,$ip_address)
{
   global $db, $dbtables,$enhanced_logging;
   if($enhanced_logging)
   {
      $res = $db->Execute("INSERT INTO $dbtables[ip_log] (player_id,ip_address,time) VALUES ($player_id,'$ip_address',NOW())");
   }
}

// calculate the distance between two sectors.
// We even run the queries ourselves.
function calc_dist($src,$dst) {
  global $db;
  global $dbtables;

  $results = $db->Execute("SELECT x,y,z FROM ".$dbtables['universe'].
                          " WHERE sector_id=$src OR sector_id=$dst");

// Make sure you check for this when calling this function.
  if(!$results) return 0;


  $x = $results->fields['x'];
  $y = $results->fields['y'];
  $z = $results->fields['z'];

  $results->MoveNext();

  $x -= $results->fields['x'];
  $y -= $results->fields['y'];
  $z -= $results->fields['z'];

  $x = sqrt($x*$x + $y*$y + $z*$z);

// Make sure it's never less than 1.
//  if($x > 1) return 1;

  return $x;
}
?>
