<?
include("config.php");
include("languages/$lang");

connectdb();

//test to see if server is closed to logins
$playerfound = false;

$screen_res = $HTTP_POST_VARS[res];
if(empty($screen_res))
  $screen_res = 800;

$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$email'");
if($res)
{
  $playerfound = $res->RecordCount();
}
$playerinfo = $res->fields;

if($playerfound)
{
  $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
  $shipinfo = $res->fields;
}

$lang=$playerinfo[lang];
if(empty($lang))
  $lang=$default_lang;
SetCookie("lang",$lang,time()+(3600*24)*365,$gamepath,$gamedomain);
include("languages/$lang" . ".inc");

/* took out the old interface, its not used anymore i guess
if($playerinfo[interface]=="N")
{
  $mainfilename="main.php";
  $interface="main.php";
}
else
{
  $mainfilename="maintext.php";
  $interface="maintext.php";
}
-- End of old interface code */ 

setcookie("interface", $mainfilename);
setcookie("screenres", $screen_res);

/* first placement of cookie - don't use updatecookie. */
$userpass = $email."+".$pass;
SetCookie("userpass",$userpass,time()+(3600*24)*365,$gamepath,$gamedomain);

$banned = 0;
$res = $db->Execute("SELECT * FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask OR '$playerinfo[ip_address]' LIKE ban_mask");
if($res->RecordCount() != 0)
{
  SetCookie("userpass","",0,$gamepath,$gamedomain);
  SetCookie("userpass","",0); // Delete from default path as well.
  setcookie("username","",0); // Legacy support, delete the old login cookies.
  setcookie("password","",0); // Legacy support, delete the old login cookies.
  setcookie("id","",0);
  setcookie("res","",0);
  $banned = 1;
}

if($server_closed)
{
  $title=$l_login_sclosed;
  include("header.php");
  die($l_login_closed_message);
}

$title=$l_login_title2;
include("header.php");

bigtitle();

if($banned == 1)
{
   echo "<center><p><font size=3 color=red>$l_login_banned<p></center>";
   include("footer.php");
   die();
}

if($playerfound)
{
  if($playerinfo[password] == $pass)
  {
    // password is correct
    if($shipinfo[destroyed] == "N")
    {
      // player's ship has not been destroyed
      playerlog($playerinfo[player_id], LOG_LOGIN, $ip);
      $stamp = date("Y-m-d H-i-s");
      $update = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',ip_address='$ip' WHERE player_id=$playerinfo[player_id]");
	  TEXT_GOTOMAIN();
      echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=$interface?id=" . $playerinfo[player_id] . "\">";
    }
    else
    {
      // player's ship has been destroyed
      if($shipinfo[dev_escapepod] == "Y")
      {
        $db->Execute("UPDATE $dbtables[ships] SET class=1, hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector_id=0,ore=0,organics=0,energy=1000,colonists=0,goods=0,fighters=100,on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,destroyed='N',dev_lssd='N' where player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
        echo $l_login_died;
      }
		else
		{
    	echo "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<BR><BR>";

        // Check if $newbie_nice is set, if so, verify ship limits
			if ($newbie_nice == "YES")
			{
				$newbie_info = $db->Execute("SELECT hull,engines,power,computer,sensors,armour,shields,beams,torp_launchers,cloak FROM $dbtables[ships] WHERE player_id='$playerinfo[player_id]' AND $ship_id=$playerinfo[currentship] AND hull<='$newbie_hull' AND engines<='$newbie_engines' AND power<='$newbie_power' AND computer<='$newbie_computer' AND sensors<='$newbie_sensors' AND armour<='$newbie_armour' AND shields<='$newbie_shields' AND beams<='$newbie_beams' AND torp_launchers<='$newbie_torp_launchers' AND cloak<='$newbie_cloak'");
				$num_rows = $newbie_info->RecordCount();

				if ($num_rows)
				{
					echo "<BR><BR>$l_login_newbie<BR><BR>";
					$db->Execute("UPDATE $dbtables[ships] SET hull=0,engines=0,power=0,computer=0,sensors=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=100,cloak=0,shields=0,sector_id=0,ore=0,organics=0,energy=1000,colonists=0,goods=0,fighters=100,on_planet='N',dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,destroyed='N',dev_lssd='N' where player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
          $db->Execute("UPDATE $dbtables[players] SET credits=credits+1000 WHERE player_id=$playerinfo[player_id]");

					echo $l_login_newlife;
				}
                else
				{
				echo "<BR><BR>$l_login_looser";
				}

			} // End if $newbie_nice
			else
			{
				echo "<BR><BR>$l_login_looser";
			}
		}
    }
  }
  else
  {
    // password is incorrect
    echo "$l_login_4gotpw1 <A HREF=mail.php?mail=$email>$l_clickme</A> $l_login_4gotpw2 <a href=login.php>$l_clickme</a> $l_login_4gotpw3 $ip...";
    playerlog($playerinfo[player_id], LOG_BADLOGIN, $ip);
  }
}
else
{
  echo "<B>$l_login_noone</B><BR>";
}

include("footer.php");

?>
