<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: readmail.php

include_once ("./global_includes.php"); 

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'readmail');
load_languages($db, $raw_prefix, 'mailto');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_readm_title;
updatecookie($db);
include_once ("./header.php");

echo "<h1>" . $title. "</h1>\n";

if (!isset($_GET['command']))
{
    $command = '';
}
else
{
    $command = $_GET['command'];
}

if ($command == "delete")
{
    $db->Execute("DELETE FROM {$db->prefix}messages WHERE message_id=? AND recp_id=?", array($_GET['message_id'], $playerinfo['player_id']));
}
else if ($command == "delete_all")
{
    $db->Execute("DELETE FROM {$db->prefix}messages WHERE recp_id=?", array($playerinfo['player_id']));
}

$cur_D = date("Y-m-d");
$cur_T = date("H:i:s");

$res = $db->Execute("SELECT * FROM {$db->prefix}messages WHERE recp_id=? ORDER BY sent DESC", array($playerinfo['player_id']));
?>
<div align="center">
  <table border="0" cellspacing="0" width="70%" bgcolor="silver" cellpadding="0">
    <tr>
      <td width="100%">
        <div align="center"><div style="text-align:center;">
          <table border="0" cellspacing="1" width="100%">
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="gray">
                    <tr>
                      <td width="75%" align="left"><font color="white" style="font-size: 0.8em;"><strong><?php echo $l_readm_center ?></strong></font></td>
                      <td width="21%" align="center" nowrap="nowrap"><font color="white" style="font-size: 0.8em;"><?echo "$cur_D" ?>&nbsp;<?php echo $cur_T; ?></font></td>
                      <td width="4%" align="center"><a href="main.php"><img alt="Return to the main menu" src="templates/<?php echo $templateset; ?>/images/c95x.png" width="16" height="14"></a></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>

<?
 if ($res->EOF)
 {
//  echo "$l_readm_nomessage";
?>
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="white">
                    <tr>
                      <td width="100%" align="center" bgcolor="white"><font color="red"><?php echo $l_readm_nomessage ?></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
<?
 }
 else
 {
  $line_counter = true;
  while (!$res->EOF)
  {
   $msg = $res->fields;
   $result = $db->Execute("SELECT * FROM {$db->prefix}ships WHERE player_id=?", array($msg['sender_id']));
   $sendership = $result->fields;
   $result2 = $db->Execute("SELECT * FROM {$db->prefix}players WHERE player_id=?", array($msg['sender_id']));
   $sender = $result2->fields;
?>
            <tr>
              <td width="100%" align="center" bgcolor="black" height="4"></td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="0" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="20%"><font color="white" style="font-size: 0.8em;"><strong><?php echo $l_readm_sender; ?></strong></td>
                      <td width="55%"><font color="yellow" style="font-size: 0.8em;"><?php echo $sender['character_name']; ?></font></td>
                      <td width="21%" align="center"><font color="white" style="font-size: 0.8em;"><?php echo "$msg[sent]" ?></font></td>
                      <td width="4%" align="center"><a class="but" href="readmail.php?command=delete&message_id=<?php echo $msg['message_id']; ?>"><img src="templates/<?php echo $templateset; ?>/images/c95x.png" width="16" height="14"></a></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="0" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="20%"><font color="white" style="font-size: 0.8em;"><strong><?php echo $l_readm_captn ?></strong></font></td>
                      <td width="80%"><font color="yellow" style="font-size: 0.8em;"><?php echo $sendership['name'] ?></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="0" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="20%"><font color="white" style="font-size: 0.8em;"><strong>Subject</strong></font></td>
                      <td width="80%"><strong><font color="yellow" style="font-size: 0.8em;"><?php echo $msg['subject']; ?></font></strong></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" bgcolor="black">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="white">
                    <tr>
                      <td width="100%"><font color="black" style="font-size: 0.8em;"><?php echo nl2br($msg['message']); ?></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
            <tr>
              <td width="100%" align="center" bgcolor="black">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="gray" cellpadding="0">
                    <tr>
                      <td width="100%" align="center" valign="middle"><a class="but" href="readmail.php?command=delete&message_id=<?php echo $msg['message_id']; ?>"><?php echo $l_readm_del ?></a> |
        <a class="but" href="mailto.php?name=<?php echo $sender['character_name']; ?>&subject=<?php echo $msg['subject'] ?>"><?php echo $l_readm_repl ?></a>
                      </td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
<?
    $res->MoveNext();
  }
}
?>
            <tr>
              <td width="100%" align="center" bgcolor="black" height="4"></td>
            </tr>
            <tr>
              <td width="100%" align="center" bgcolor="#000000" height="4">
                <div align="center">
                  <table border="1" cellspacing="1" width="100%" bgcolor="#808080">
                    <tr>
                      <td width="50%"><p align="left"><font color="#FFFFFF" style="font-size: 0.8em;">Mail Reader</font></td>
                      <td width="50%"><p align="right"><font color="#FFFFFF" style="font-size: 0.8em;"><a class="but" href="readmail.php?command=delete_all">Delete All</a></font></td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>

          </table>
          </div>
        </div>
      </td>
    </tr>
  </table>
</div>
<br>
<?
 //}

global $l_global_mmenu;
echo "<a href=\"main.php\">" . $l_global_mmenu . "</a>";

include_once ("./footer.php");
?>

