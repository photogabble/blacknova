<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: new_ai.php

$pos = (strpos($_SERVER['PHP_SELF'], "/new_ai.php"));
if ($pos !== false)
{
    include_once ("./global_includes.php");
    dynamic_loader ($db, "load_languages.php");

    // Load language variables
    load_languages($db, $raw_prefix, 'common');

    $title = $l_error_occured;
    include_once ("./header.php");
    echo $l_cannot_access;
    include_once ("./footer.php");
    die();
}

// Dynamic functions
dynamic_loader ($db, "num_level.php");
dynamic_loader ($db, "num_energy.php");
dynamic_loader ($db, "seed_mt_rand.php");

echo "<B>Create A New " . $ai_name . "</B>";
echo "<br>";
echo "<form action=\"admin.php\" method=\"post\" accept-charset=\"utf-8\">";

if (empty($operation))
{
    // Create AI Name
    $Sylable1 = array("Ak","Al","Ar","B","Br","D","F","Fr","G","Gr","K","Kr","N","Ol","Om","P","Qu","R","S","Z");
    $Sylable2 = array("a","ar","aka","aza","e","el","i","in","int","ili","ish","ido","ir","o","oi","or","os","ov","u","un");
    $Sylable3 = array("ag","al","ak","ba","dar","g","ga","k","ka","kar","kil","l","n","nt","ol","r","s","ta","til","x");
    $sy1roll = mt_rand(0,19);
    $sy2roll = mt_rand(0,19);
    $sy3roll = mt_rand(0,19);
    $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
    $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
    $resultnm = $db->Execute ("SELECT character_name from {$db_prefix}players where character_name='$character'");
    $namecheck = $resultnm->fields;
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $nametry = 1;
    // If Name Exists Try Again - Up To Nine Times

    while (($namecheck[0]) and ($nametry <= 9)) 
    {
        $sy1roll = mt_rand(0,19);
        $sy2roll = mt_rand(0,19);
        $sy3roll = mt_rand(0,19);
        $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
        $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
        $resultnm = $db->Execute ("select character_name from {$db_prefix}players where character_name='$character'");
        $namecheck = $resultnm->fields;
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $nametry++;
    }

    // Create Ship Name
    $shipname = $ai_name . "-" . $character; 
    // Select Random Sector
    $sector = mt_rand(1,$sector_max); 
    // Display Confirmation form
    echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
    echo "<TR><TD>" . $ai_name . " Name</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=character VALUE=$character></TD>";
    echo "<TD>Level <INPUT TYPE=TEXT SIZE=5 NAME=ai_level VALUE=3></TD>";
    echo "<TD>Ship Name <INPUT TYPE=TEXT SIZE=20 NAME=shipname VALUE=$shipname></TD>";
    echo "<TR><TD>Active?<INPUT TYPE=CHECKBOX NAME=active VALUE=ON CHECKED ></TD>";
    echo "<TD>Orders ";
    echo "<SELECT SIZE=1 NAME=orders>";
    echo "<OPTION SELECTED=0 VALUE=0>Sentinel</OPTION>";
    echo "<OPTION VALUE=1>Roam</OPTION>";
    echo "<OPTION VALUE=2>Roam and Trade</OPTION>";
    echo "<OPTION VALUE=3>Roam and Hunt</OPTION>";
    echo "</SELECT></TD>";
    echo "<TD>Sector <INPUT TYPE=TEXT SIZE=5 NAME=sector VALUE=$sector></TD>";
    echo "<TD>Aggression ";
    echo "<SELECT SIZE=1 NAME=aggression>";
    echo "<OPTION SELECTED=0 VALUE=0>Peaceful</OPTION>";
    echo "<OPTION VALUE=1>Attack Sometimes</OPTION>";
    echo "<OPTION VALUE=2>Attack Always</OPTION>";
    echo "</SELECT></TD></TR>";
    echo "</TABLE>";
    echo "<HR>";
    echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=create_ai>";
    echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ai>";
    echo "<INPUT TYPE=SUBMIT VALUE=Create>";
}
elseif ($operation == "create_ai")
{
    // update database
    $_active = empty($active) ? "N" : "Y";
    $errflag = 0;
    if ( $character=='' || $shipname=='' )
    {
        echo "Ship name, and character name may not be blank.<br>"; 
        $errflag=1;
    }
    // Change Spaces to Underscores in shipname
    $shipname = str_replace(" ","_",$shipname);
    // Create emailname from character
    $emailname = str_replace(" ","_",$character) . "@aiplayer";
    $ADODB_FETCH_MODE = ADODB_FETCH_NUM;
    $result = $db->Execute ("select email, character_name from {$db_prefix}players where email='$emailname' OR character_name='$character'");

    if ($result>0)
    {
        while (!$result->EOF)
        {
            $row= $result->fields;
            if ($row[0]==$emailname) 
            {
                echo "ERROR: E-mail address $emailname, is already in use.  ";
                $errflag=1;
            }
            if ($row[1]==$character) 
            {
                echo "ERROR: Character name $character, is already in use.<br>";
                $errflag=1;
            }

            $result->MoveNext();
        }
    }

    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    if ($errflag==0)
    {
        $makepass="";
        $syllables="er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
        $syllable_array=explode(",", $syllables);
        seed_mt_rand();
        for ($count=1;$count<=4;$count++) 
        {
            if (mt_rand()%10 == 1) 
            {
                $makepass .= sprintf("%0.0f",(mt_rand()%50)+1);
            }
            else 
            {
                $makepass .= sprintf("%s",$syllable_array[mt_rand()%62]);
            }
        }

        if ($ai_level=='') 
        {
            $ai_level=0;
        }

        $maxarmor = num_level($ai_level, $level_factor, $level_magnitude);
        $maxfighters = num_level($ai_level, $level_factor, $level_magnitude);
        $maxtorps = num_level($ai_level, $level_factor, $level_magnitude);
        $maxenergy = num_energy($ai_level, $level_factor, $level_magnitude);
        $stamp=date("Y-m-d H:i:s");
        $c_code = '666666';

        // ADD AI RECORD TO ships TABLE ... MODIFY IF ships SCHEMA CHANGES
        $player_id = newplayer($emailname, $character, $makepass, $c_code, $shipname);
        $res = $db->Execute("SELECT ship_id FROM {$db_prefix}players LEFT JOIN {$db_prefix}ships ON {$db_prefix}players.player_id = {$db_prefix}ships.player_id WHERE {$db_prefix}players.player_id=$player_id");
        $ship_id = $res->fields['ship_id'];

        $result2 = $db->Execute("UPDATE {$db_prefix}ships SET hull=?, engines=?, pengines=?, power=?, computer=?, sensors=?, beams=?, torp_launchers=?, shields=?, armor=?, cloak=?, torps=?, armor_pts=?, sector_id=?, energy=?, fighters=? WHERE ship_id=?", array($ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $ai_level, $maxtorps, $maxarmor, $sector, $maxenergy, $maxfighters, $ship_id));
        if (!$result2) 
        {
            echo $db->ErrorMsg() . "<br>";
        } 
        else 
        {
            echo $ai_name . " has been created.<br><br>";
            echo "Ship Records have been updated.<br><br>";
        }

        $debug_query = $db->Execute("INSERT INTO {$db_prefix}ai (ai_id, active, aggression, orders) VALUES ".
                                    "(?,?,?,?)", array($emailname, $_active, $aggression, $orders));
        db_op_result($debug_query,__LINE__,__FILE__);
    }
}
else
{
    echo "Invalid operation";
}

echo "<INPUT TYPE=HIDDEN NAME=module VALUE=createnew>";
echo "</form>";

?>
