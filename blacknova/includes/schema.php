<?

function create_schema()
{
/*********************************************************
If you add/remove a table, don't forget to update the
table name variables in the global_func file.
*********************************************************/

global $maxlen_password;
global $dbtables;
global $db;

function db_create_result()
{
global $db;

 if ($db)
 {
  echo "<td><font color=\"lime\">- created successfully.</font></td></tr><br>\n";
 }
 else
 {
  echo "<td><font color=\"red\">- failed to create. error code: $db.</font></td></tr><br>\n";
 }

}

// Delete all tables in the database
echo "<b>Dropping all tables </b><BR>";
foreach ($dbtables as $table => $tablename)
{
  echo "Dropping $table ";
//  $db->debug = true;
  $query = $db->Execute("DROP TABLE $tablename");
  if ($query)
  {
   echo "<td><font color=\"lime\">- dropped successfully.</font></td></tr><br>\n";
  }
  else
  {
   echo "<td><font color=\"red\">- failed to drop. error code: $query.</font></td></tr><br>\n";
  }
}
echo "<b>All tables have been successfully dropped.</b><p>";

// Create database schema
echo "<b>Creating tables </b><BR>";
echo "Creating table: links ";
$db->Execute("CREATE TABLE $dbtables[links] (" .
             "link_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "link_start int unsigned DEFAULT '0' NOT NULL," .
             "link_dest int unsigned DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (link_id)," .
             "KEY link_start (link_start)," .
             "KEY link_dest (link_dest)" .
             ")");
db_create_result();

echo "Creating table: planets ";
$db->Execute("CREATE TABLE $dbtables[planets](" .
             "planet_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "sector_id int unsigned DEFAULT '0' NOT NULL," .
             "name tinytext," .
             "organics bigint(20) DEFAULT '0' NOT NULL," .
             "ore bigint(20) DEFAULT '0' NOT NULL," .
             "goods bigint(20) DEFAULT '0' NOT NULL," .
             "energy bigint(20) DEFAULT '0' NOT NULL," .
             "colonists bigint(20) DEFAULT '0' NOT NULL," .
             "credits bigint(20) DEFAULT '0' NOT NULL," .
             "fighters bigint(20) DEFAULT '0' NOT NULL," .
             "torps bigint(20) DEFAULT '0' NOT NULL," .
             "owner int unsigned DEFAULT '0' NOT NULL," .
             "corp int unsigned DEFAULT '0' NOT NULL," .
             "base enum('Y','N') DEFAULT 'N' NOT NULL," .
             "sells enum('Y','N') DEFAULT 'N' NOT NULL," .
             "prod_organics float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
             "prod_ore float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
             "prod_goods float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
             "prod_energy float(5,2) unsigned DEFAULT '20.0' NOT NULL," .
             "prod_fighters float(5,2) unsigned DEFAULT '10.0' NOT NULL," .
             "prod_torp float(5,2) unsigned DEFAULT '10.0' NOT NULL," .
             "defeated enum('Y','N') DEFAULT 'N' NOT NULL," .
             "PRIMARY KEY (planet_id)," .
             "KEY owner (owner)," .
             "KEY corp (corp)" .
             ")");
db_create_result();

echo "Creating table: traderoutes ";
$db->Execute("CREATE TABLE $dbtables[traderoutes](" .
             "traderoute_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "source_id int unsigned DEFAULT '0' NOT NULL," .
             "dest_id int unsigned DEFAULT '0' NOT NULL," .
             "source_type enum('P','L','C','D') DEFAULT 'P' NOT NULL," .
             "dest_type enum('P','L','C','D') DEFAULT 'P' NOT NULL," .
             "move_type enum('R','W') DEFAULT 'W' NOT NULL," .
             "owner int unsigned DEFAULT '0' NOT NULL," .
             "circuit enum('1','2') DEFAULT '2' NOT NULL," .
             "PRIMARY KEY (traderoute_id)," .
             "KEY owner (owner)" .
             ")");
db_create_result();

echo "Creating table: players ";
$db->Execute("CREATE TABLE $dbtables[players](" .
             "player_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "currentship int unsigned DEFAULT '0' NOT NULL," .
             "character_name char(20) NOT NULL," .
             "password char($maxlen_password) NOT NULL," .
             "email char(60) NOT NULL," .
             "credits bigint(20) DEFAULT '0' NOT NULL," .
             "turns smallint(4) DEFAULT '0' NOT NULL," .
             "turns_used int unsigned DEFAULT '0' NOT NULL," .
             "last_login datetime," .
             "rating int DEFAULT '0' NOT NULL," .
             "score int DEFAULT '0' NOT NULL," .
             "team int DEFAULT '0' NOT NULL," .
             "team_invite int DEFAULT '0' NOT NULL," .
             "interface enum('N','O') DEFAULT 'N' NOT NULL," .
             "ip_address tinytext NOT NULL," .
             "preset1 int DEFAULT '0' NOT NULL," .
             "preset2 int DEFAULT '0' NOT NULL," .
             "preset3 int DEFAULT '0' NOT NULL," .
             "trade_colonists enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
             "trade_fighters enum('Y', 'N') DEFAULT 'N' NOT NULL," .
             "trade_torps enum('Y', 'N') DEFAULT 'N' NOT NULL," .
             "trade_energy enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
             "lang varchar(30) DEFAULT 'english.inc' NOT NULL," .
             "dhtml enum('Y', 'N') DEFAULT 'Y' NOT NULL," .
             "PRIMARY KEY (email)," .
             "KEY email (email)," .
             "KEY team (team)," .
             "KEY player_id (player_id)" .
             ")");
db_create_result();

echo "Creating table: universe ";
$db->Execute("CREATE TABLE $dbtables[universe](" .
             "sector_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "sector_name tinytext," .
             "zone_id int DEFAULT '0' NOT NULL," .
             "port_type enum('ore','organics','goods','energy','special','none') DEFAULT 'none' NOT NULL," .
             "port_organics bigint(20) DEFAULT '0' NOT NULL," .
             "port_ore bigint(20) DEFAULT '0' NOT NULL," .
             "port_goods bigint(20) DEFAULT '0' NOT NULL," .
             "port_energy bigint(20) DEFAULT '0' NOT NULL," .
             "KEY zone_id (zone_id)," .
             "KEY port_type (port_type)," .
             "beacon tinytext," .

#             "angle1 float(10,2) DEFAULT '0.00' NOT NULL," .
#             "angle2 float(10,2) DEFAULT '0.00' NOT NULL," .
#             "distance bigint(20) unsigned DEFAULT '0' NOT NULL," .
             "x bigint(20) DEFAULT '0' NOT NULL," .
             "y bigint(20) DEFAULT '0' NOT NULL," .
             "z bigint(20) DEFAULT '0' NOT NULL," .

             "fighters bigint(20) DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (sector_id)" .
             ")");
db_create_result();

echo "Creating table: zones ";
$db->execute("CREATE TABLE $dbtables[zones](" .
             "zone_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "zone_name tinytext," .
             "owner int unsigned DEFAULT '0' NOT NULL," .
             "corp_zone enum('Y', 'N') DEFAULT 'N' NOT NULL," .
             "allow_beacon enum('Y','N','L') DEFAULT 'Y' NOT NULL," .
             "allow_attack enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "allow_planetattack enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "allow_warpedit enum('Y','N','L') DEFAULT 'Y' NOT NULL," .
             "allow_planet enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
             "allow_trade enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
             "allow_defenses enum('Y','L','N') DEFAULT 'Y' NOT NULL," .
             "max_hull int DEFAULT '0' NOT NULL," .
             "PRIMARY KEY(zone_id)," .
             "KEY zone_id(zone_id)" .
             ")");
db_create_result();

echo "Creating table: ibank_accounts ";
$db->Execute("CREATE TABLE $dbtables[ibank_accounts](" .
             "player_id int DEFAULT '0' NOT NULL," .
             "balance bigint(20) DEFAULT '0'," .
             "loan bigint(20)  DEFAULT '0'," .
             "loantime TIMESTAMP(14)," .
			 "PRIMARY KEY(player_id)" .
             ")");
db_create_result();

echo "Creating table: IGB_transfers ";
$db->Execute("CREATE TABLE $dbtables[IGB_transfers](" .
             "transfer_id int DEFAULT '0' NOT NULL auto_increment," .
             "source_id int DEFAULT '0' NOT NULL," .
             "dest_id int DEFAULT '0' NOT NULL," .
             "time TIMESTAMP(14)," .
             "PRIMARY KEY(transfer_id)" .
             ")");
db_create_result();

echo "Creating table: teams ";
$db->Execute("CREATE TABLE $dbtables[teams](" .
             "id int DEFAULT '0' NOT NULL," .
             "creator int DEFAULT '0'," .
             "team_name tinytext," .
             "description tinytext," .
             "number_of_members tinyint(3) DEFAULT '0' NOT NULL," .
             "PRIMARY KEY(id)" .
             ")");
db_create_result();

echo "Creating table: news ";
$db->Execute("CREATE TABLE $dbtables[news] (" .
             "news_id int(11) DEFAULT '0' NOT NULL auto_increment," .
             "headline varchar(100) NOT NULL," .
             "newstext text NOT NULL," .
             "user_id int(11)," .
             "date timestamp(8)," .
             "news_type varchar(10)," .
             "PRIMARY KEY (news_id)," .
             "KEY news_id (news_id)," .
             "UNIQUE news_id_2 (news_id)" .
             ")");
db_create_result();

echo "Creating table: internal messaging ";
$db->Execute("CREATE TABLE $dbtables[messages] (" .
             "ID int NOT NULL auto_increment," .
             "sender_id int NOT NULL default '0'," .
             "recp_id int NOT NULL default '0'," .
             "subject varchar(250) NOT NULL default ''," .
             "sent varchar(19) NULL," .
             "message longtext NOT NULL," .
             "notified enum('Y','N') NOT NULL default 'N'," .
             "PRIMARY KEY  (ID) " .
             ") TYPE=MyISAM");
db_create_result();

echo "Creating table: furangee ";
$db->Execute("CREATE TABLE $dbtables[furangee](" .
             "furangee_id char(40) NOT NULL," .
             "active enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "aggression smallint(5) DEFAULT '0' NOT NULL," .
             "orders smallint(5) DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (furangee_id)," .
             "KEY furangee_id (furangee_id)" .
             ")");
db_create_result();

echo "Creating table: sector_defence ";
$db->Execute("CREATE TABLE $dbtables[sector_defence](" .
             "defence_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "player_id int DEFAULT '0' NOT NULL," .
             "sector_id int unsigned DEFAULT '0' NOT NULL," .
             "defence_type enum('M','F') DEFAULT 'M' NOT NULL," .
             "quantity bigint(20) DEFAULT '0' NOT NULL," .
             "fm_setting enum('attack','toll') DEFAULT 'toll' NOT NULL," .
             "PRIMARY KEY (defence_id)," .
             "KEY sector_id (sector_id)," .
             "KEY player_id (player_id)" .
             ")");
db_create_result();

echo "Creating table: scheduler ";
$db->Execute("CREATE TABLE $dbtables[scheduler](" .
             "sched_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "loop enum('Y','N') DEFAULT 'N' NOT NULL," .
             "ticks_left int unsigned DEFAULT '0' NOT NULL," .
             "ticks_full int unsigned DEFAULT '0' NOT NULL," .
             "spawn int unsigned DEFAULT '0' NOT NULL," .
             "file varchar(30) NOT NULL," .
             "extra_info varchar(50) NOT NULL," .
             "last_run BIGINT(20)," .
             "PRIMARY KEY (sched_id)" .
             ")");
db_create_result();

echo "Creating table: ip_bans ";
$db->Execute("CREATE TABLE $dbtables[ip_bans](" .
             "ban_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "ban_mask varchar(16) NOT NULL," .
             "PRIMARY KEY (ban_id)" .
             ")");
db_create_result();

echo "Creating table: logs ";
$db->Execute("CREATE TABLE $dbtables[logs](" .
             "log_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "player_id int DEFAULT '0' NOT NULL," .
             "type mediumint(5) DEFAULT '0' NOT NULL," .
             "time TIMESTAMP(14)," .
             "data varchar(255)," .
             "PRIMARY KEY (log_id)," .
             "KEY idate (player_id,time)" .
             ")");
db_create_result();

echo "Creating table: bounty ";
$db->Execute("CREATE TABLE $dbtables[bounty] (" .
             "bounty_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "amount bigint(20) unsigned DEFAULT '0' NOT NULL," . 
             "bounty_on int unsigned DEFAULT '0' NOT NULL," .
             "placed_by int unsigned DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (bounty_id)," .
             "KEY bounty_on (bounty_on)," .
             "KEY placed_by (placed_by)" .
             ")");
db_create_result();

echo "Creating table: movement_log ";
$db->Execute("CREATE TABLE $dbtables[movement_log](" .
             "event_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "ship_id int DEFAULT '0' NOT NULL," .
             "sector_id int DEFAULT '0'," .
             "time TIMESTAMP(14) ," .
             "PRIMARY KEY (event_id)," .
             "KEY ship_id(ship_id)," .
             "KEY sector_id (sector_id)" .
             ")");
db_create_result();

echo "Creating table: planet_log ";
$db->Execute("CREATE TABLE $dbtables[planet_log](" .
              "planetlog_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
              "planet_id int DEFAULT '0' NOT NULL," .
              "player_id int DEFAULT '0' NOT NULL," .
              "owner_id int DEFAULT '0' NOT NULL," .
              "ip_address tinytext NOT NULL," .
              "action int DEFAULT '0' NOT NULL," .
              "time TIMESTAMP(14) ," .
              "PRIMARY KEY (planetlog_id)," .
              "KEY planet_id (planet_id)" .
              ")");
db_create_result();

echo "Creating table: ip_log ";
$db->Execute("CREATE TABLE $dbtables[ip_log](" .
              "log_id int unsigned DEFAULT '0' NOT NULL auto_increment," .       
              "player_id int DEFAULT '0' NOT NULL," .
              "ip_address tinytext NOT NULL," .
              "time TIMESTAMP(14) ," .
              "PRIMARY KEY (log_id)," .
              "KEY ship_id (player_id)" . 
              ")");
db_create_result();

echo "Creating planet/ip address index";
$db->Execute("ALTER table $dbtables[ip_log] ADD INDEX planet_id (ip_address(15))");
db_create_result();

echo "Creating table: ship_types ";
$db->Execute("CREATE TABLE $dbtables[ship_types] (" .
             "type_id int unsigned DEFAULT '1' NOT NULL," .
             "name char(20)," .
             "image char(20)," .
             "description text," .
             "buyable enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "cost_credits bigint(20) unsigned DEFAULT '0' NOT NULL," . 
             "cost_ore bigint(20) unsigned DEFAULT '0' NOT NULL," . 
             "cost_goods bigint(20) unsigned DEFAULT '0' NOT NULL," . 
             "cost_energy bigint(20) unsigned DEFAULT '0' NOT NULL," . 
             "cost_organics bigint(20) unsigned DEFAULT '0' NOT NULL," . 
             "turnstobuild int unsigned DEFAULT '0' NOT NULL," .
             "minhull tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxhull tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "minengines tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxengines tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "minpower tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxpower tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "mincomputer tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxcomputer tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "minsensors tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxsensors tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "minbeams tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxbeams tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "mintorp_launchers tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxtorp_launchers tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "minshields tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxshields tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "minarmour tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxarmour tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "mincloak tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "maxcloak tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "PRIMARY KEY (type_id)" .
             ")");
db_create_result();

echo "Creating table: ships ";
$db->Execute("CREATE TABLE $dbtables[ships](" .
             "ship_id int unsigned DEFAULT '0' NOT NULL auto_increment," .
             "player_id int unsigned DEFAULT '0' NOT NULL," .
             "class int unsigned DEFAULT '1' NOT NULL," .
             "name char(20)," .
             "destroyed enum('Y','N') DEFAULT 'N' NOT NULL," .
             "hull tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "engines tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "power tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "computer tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "sensors tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "beams tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "torp_launchers tinyint(3) DEFAULT '0' NOT NULL," .
             "torps bigint(20) DEFAULT '0' NOT NULL," .
             "shields tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "armour tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "armour_pts bigint(20) DEFAULT '0' NOT NULL," .
             "cloak tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "sector_id int unsigned DEFAULT '0' NOT NULL," .
             "ore bigint(20) DEFAULT '0' NOT NULL," .
             "organics bigint(20) DEFAULT '0' NOT NULL," .
             "goods bigint(20) DEFAULT '0' NOT NULL," .
             "energy bigint(20) DEFAULT '0' NOT NULL," .
             "colonists bigint(20) DEFAULT '0' NOT NULL," .
             "fighters bigint(20) DEFAULT '0' NOT NULL," .
             "on_planet enum('Y','N') DEFAULT 'N' NOT NULL," .
             "dev_warpedit smallint(5) DEFAULT '0' NOT NULL," .
             "dev_genesis smallint(5) DEFAULT '0' NOT NULL," .
             "dev_beacon smallint(5) DEFAULT '0' NOT NULL," .
             "dev_emerwarp smallint(5) DEFAULT '0' NOT NULL," .
             "dev_escapepod enum('Y','N') DEFAULT 'N' NOT NULL," .
             "dev_fuelscoop enum('Y','N') DEFAULT 'N' NOT NULL," .
             "dev_minedeflector bigint(20) DEFAULT '0' NOT NULL," .
             "planet_id int unsigned DEFAULT '0' NOT NULL," .
             "cleared_defences tinytext," .
             "dev_lssd enum('Y','N') DEFAULT 'Y' NOT NULL," .
             "PRIMARY KEY (ship_id)," .
             "KEY sector_id (sector_id)" .
             ")");
db_create_result();

echo "Creating table: email_log...";
$db->Execute("CREATE TABLE $dbtables[email_log](" .
             "log_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
             "sp_name varchar(50) NOT NULL," .
             "sp_IP tinytext NOT NULL," .
             "dp_name varchar(50) NOT NULL," .
             "e_subject varchar(250)," .
             "e_status enum('Y','N') DEFAULT 'N' NOT NULL," .
             "e_type tinyint(3) unsigned DEFAULT '0' NOT NULL," .
             "e_stamp char(20)," .
             "e_response varchar(250)," .
             "PRIMARY KEY (log_id)" .
             ")");
db_create_result();

//Finished
echo "<b>Database schema creation completed successfully.</b><BR>";

}

?>
