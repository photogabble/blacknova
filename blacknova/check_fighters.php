<?


    if (preg_match("/check_fighters.php/i", $PHP_SELF)) {
        echo "You can not access this file directly!";
        die();
    }

    include("languages/$lang");

    $result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
    //Put the sector information into the array "sectorinfo"
    $sectorinfo=$result2->fields;
    $result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='F' ORDER BY quantity DESC");
    //Put the defence information into the array "defenceinfo"
    $i = 0;
    $total_sector_fighters = 0;
    $owner = true;
    if($result3 > 0)
    {
       while(!$result3->EOF)
       {
          $row = $result3->fields;
          $defences[$i] = $row;
           $total_sector_fighters += $defences[$i]['quantity'];
          if($defences[$i][player_id] != $playerinfo[player_id])
          {
             $owner = false;
          }
          $i++;
          $result3->MoveNext();
       }
    }
    $num_defences = $i;
    if ($num_defences > 0 && $total_sector_fighters > 0 && !$owner)
    {
        // find out if the fighter owner and player are on the same team
        // All sector defences must be owned by members of the same team
        $fm_owner = $defences[0]['player_id'];
        $result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
        $fighters_owner = $result2->fields;
        if ($fighters_owner[team] != $playerinfo[team] || $playerinfo[team]==0)
        {
           switch($response) {

              case "fight":
                 $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $shipinfo[ship_id]");
                 bigtitle();
                 include("sector_fighters.php");

                 break;

              case "retreat":
                 if( $calledfrom == 'rsmove.php' )
                 {
                   $shipspeed = mypw($level_factor, $shipinfo['engines']);
                   $turns_back = 2 * round($distance / $shipspeed);
                   if($turns_back == 0 )
                   {
                     $turns_back = 2;
                   }

                 }
                 else
                   $turns_back = 2; //Warp

                 //TODO: what happens if we don't have enough turns for BOTH moves (forth+back)?? Destroy the ship? Order him to wait turns?

                 $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $shipinfo[ship_id]");
                 $stamp = date("Y-m-d H-i-s");
                 $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-$turns_back, turns_used=turns_used+$turns_back WHERE player_id=$playerinfo[player_id]");
                 $db->Execute("UPDATE $dbtables[ships] SET sector_id=$shipinfo[sector_id] WHERE ship_id=$shipinfo[ship_id]");
                 bigtitle();
                 echo "$l_chf_youretreatback<BR>";
                 TEXT_GOTOMAIN();
                 die();
                 break;

              case "pay":
                 $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $shipinfo[ship_id]");
                 $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                 if($playerinfo[credits] < $fighterstoll)
                 {
                   if( $calledfrom == 'rsmove.php' )
                   {
                     $shipspeed = mypw($level_factor, $shipinfo['engines']);
                     $turns_back = 2 * round($distance / $shipspeed);
                     if($turns_back == 0 )
                     {
                       $turns_back = 2;
                     }
                   }
                   else
                     $turns_back = 2; //Warp

                   echo "$l_chf_notenoughcreditstoll<BR>";
                   echo "$l_chf_movefailed<BR>";
                   // undo the move

                   //TODO: what happens if we don't have enough turns for BOTH moves (forth+back)?? Destroy the ship? Order him to wait turns?

                   $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $shipinfo[ship_id]");
                   $stamp = date("Y-m-d H-i-s");
                   $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-$turns_back, turns_used=turns_used+$turns_back WHERE player_id=$playerinfo[player_id]");
                   $db->Execute("UPDATE $dbtables[ships] SET sector_id=$shipinfo[sector_id] WHERE ship_id=$shipinfo[ship_id]");

                   $ok=0;
                 }
                 else
                 {
                    $tollstring = NUMBER($fighterstoll);
                    $l_chf_youpaidsometoll = str_replace("[chf_tollstring]", $tollstring, $l_chf_youpaidsometoll);
                    echo "$l_chf_youpaidsometoll<BR>";
                    $db->Execute("UPDATE $dbtables[players] SET credits=credits-$fighterstoll WHERE player_id=$playerinfo[player_id]");
                    distribute_toll($sector,$fighterstoll,$total_sector_fighters);
                    playerlog($playerinfo[player_id], LOG_TOLL_PAID, "$tollstring|$sector");
                    $ok=1;
                 }
                 break;

              case "sneak":
                 {
                    $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = ' ' WHERE ship_id = $shipinfo[ship_id]");
                    $res=$db->Execute("SELECT GREATEST(sensors) AS sensors FROM $dbtables[ships] WHERE player_id=$fm_owner");
                    $sensors = $res->fields[sensors];

                    $success = SCAN_SUCCESS($sensors, $shipinfo[cloak]);
                    if($success < 5)
                    {
                       $success = 5;
                    }
                    if($success > 95)
                    {
                       $success = 95;
                    }
                    $roll = rand(1, 100);
                    if($roll < $success)
                    {
                        // sector defences detect incoming ship
                        bigtitle();
                        echo "$l_chf_thefightersdetectyou<BR>";
                        include("sector_fighters.php");
                        break;
                    }
                    else
                    {
                       // sector defences don't detect incoming ship
                       $ok=1;
                    }
                 }
                 break;

              default:
                 $interface_string = $calledfrom . '?sector='.$sector.'&destination='.$destination.'&engage='.$engage;
                 $db->Execute("UPDATE $dbtables[ships] SET cleared_defences = '$interface_string' WHERE ship_id = $shipinfo[ship_id]");

                 $fighterstoll = $total_sector_fighters * $fighter_price * 0.6;
                 bigtitle();
                 echo "<FORM ACTION=$calledfrom METHOD=POST>";
                 $l_chf_therearetotalfightersindest = str_replace("[chf_total_sector_fighters]", $total_sector_fighters, $l_chf_therearetotalfightersindest);
                 echo "$l_chf_therearetotalfightersindest<br>";

                 if($defences[0]['fm_setting'] == "toll")
                 {
                    $l_chf_creditsdemanded = str_replace("[chf_number_fighterstoll]", NUMBER($fighterstoll), $l_chf_creditsdemanded);
                    echo "$l_chf_creditsdemanded<BR>";
                 }
                 echo "$l_chf_youcanretreat";
                 if($defences[0]['fm_setting'] == "toll")
                 {
                    echo "$l_chf_inputpay";
                 }

                 echo "$l_chf_inputfight";
                 echo "$l_chf_inputcloak<BR>";
                 echo "<INPUT TYPE=SUBMIT VALUE=$l_chf_go><BR><BR>";
                 echo "<input type=hidden name=sector value=$sector>";
                 echo "<input type=hidden name=engage value=1>";
                 echo "<input type=hidden name=destination value=$destination>";
                 echo "</FORM>";
                 die();
                 break;
            }


           // clean up any sectors that have used up all mines or fighters
           $db->Execute("delete from $dbtables[sector_defence] where quantity <= 0 ");
        }

    }

?>
