<?


include("config.php");
updatecookie();

include("languages/$lang");

$title="Admin Reports";
include("header.php");

connectdb();
bigtitle();

function CHECKED($yesno)
{
  return(($yesno == "Y") ? "CHECKED" : "");
}

function YESNO($onoff)
{
  return(($onoff == "ON") ? "Y" : "N");
}

$module = $menu;

if($swordfish != $adminpass)
{
  echo "<FORM ACTION=admin_reports.php METHOD=POST>";
  echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
  echo "</FORM>";
}
else
{
  // ******************************
  // ******** MAIN MENU ***********
  // ******************************
  if(empty($module))
  {
    echo "Welcome to the BlackNova Traders Admin Reports Page<BR><BR>";
    echo "Select a report from the list below:<BR>";
    echo "<FORM ACTION=admin_reports.php METHOD=POST>";
    echo "<SELECT NAME=menu>";
    echo "<OPTION VALUE=shipnegval SELECTED>Ships with Negative Cargo Values</OPTION>";
    echo "<OPTION VALUE=portnegval>Ports with Negative Cargo Values</OPTION>";
    echo "<OPTION VALUE=portposval>Complete Port Cargo Values</OPTION>";
    echo "</SELECT>";
    echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
    echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>";
    echo "</FORM>";
  }
  else
  {
    $button_main = true;
    // ***********************************************
    // ********* START OF Player Negative Value ******
    // ***********************************************
    if($module == "shipnegval")
    {
      echo "<H2>Ships with Negative Values</H2>";
      echo "<table>\n";
      echo "<tr><td>Player</td><td>Ship</td><td>credits</td>";
      echo "<td>ore</td><td>organic</td><td>goods</td><td>energy</td></tr>\n";
      $res = $db->Execute("SELECT * FROM $dbtables[ships]");
      while(!$res->EOF)
      {
        $row=$res->fields;
        if ($row[ore] < 0 || $row[organics] < 0 || $row[goods] < 0 || $row[energy] < 0) {
          $res2 = $db->Execute("SELECT credits, character_name FROM $dbtables[players] WHERE player_id=$row[player_id]");
          $row2=$res2->fields;
          echo "<tr><td>$row2[character_name]</td><td>$row[name]</td><td>$row2[credits]</td>";
          echo "<td>$row[ore]</td><td>$row[organics]</td><td>$row[goods]</td><td>$row[energy]</td></tr>\n";
          $res2->_close();
        }
        $res->MoveNext();
      }
      $res->_close();
      echo "</table>\n";
      $button_main = true;

    }
    // ***********************************************
    // ********* START OF Port Negative Value ********
    // ***********************************************
    elseif($module == "portposval")
    {
      echo "<H2>Complete Port Values</H2>";
      echo "<table>\n";
      echo "<tr><td>Sector</td><td>Port</td><td>ore</td>";
      echo "<td>organic</td><td>goods</td><td>energy</td></tr>\n";
      $res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE port_type != 'none' AND port_type != 'special'");
      while(!$res->EOF)
      {
        $row=$res->fields;
        echo "<tr><td>$row[sector_id]</td><td>$row[port_type]</td><td>$row[port_ore]</td>";
        echo "<td>$row[port_organics]</td><td>$row[port_goods]</td><td>$row[port_energy]</td></tr>\n";
        $res->MoveNext();
      }
      $res->_close();
      echo "</table>\n";
      $button_main = true;
    }
    // ***********************************************
    // ********* START OF Port Negative Value ********
    // ***********************************************
    elseif($module == "portnegval")
    {
      echo "<H2>Ports with Negative Values</H2>";
      echo "<table>\n";
      echo "<tr><td>Sector</td><td>Port</td><td>ore</td>";
      echo "<td>organic</td><td>goods</td><td>energy</td></tr>\n";
      $res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE port_type != 'none' AND port_type != 'special'");
      while(!$res->EOF)
      {
        $row=$res->fields;
        if ($row[port_ore] < 0 || $row[port_organics] < 0 || $row[port_goods] < 0 || $row[port_energy] < 0) {
          echo "<tr><td>$row[sector_id]</td><td>$row[port_type]</td><td>$row[port_ore]</td>";
          echo "<td>$row[port_organics]</td><td>$row[port_goods]</td><td>$row[port_energy]</td></tr>\n";
        }
        $res->MoveNext();
      }
      $res->_close();
      echo "</table>\n";
      $button_main = true;
    }
    else
    {
      echo "Unknown Report";
    }

    if($button_main)
    {
      echo "<BR><BR>";
      echo "<FORM ACTION=admin_reports.php METHOD=POST>";
      echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$swordfish>";
      echo "<INPUT TYPE=SUBMIT VALUE=\"Return to main menu\">";
      echo "</FORM>";
    }
  }
}
  
include("footer.php");

?> 
