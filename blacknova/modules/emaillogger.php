<?
if (preg_match("/emaillogger.php/i", $_SERVER["PHP_SELF"])) { echo "You can not access this module directly!";die();}

##############################################################################
# This script is ©1996 2002 Paul Kirby AKA TheMightyDude                     #
# And is free to use under condition the copyright notice remains untouched. #
# Email: webmaster@initcorp.co.uk                                            #
# WebSite: http://E-Script.initcorp.co.uk                                    #
##############################################################################
# Email Logger Module                                                        #
##############################################################################

##############################################################################
# Define the module Information.                                             #
##############################################################################
//    global $ModuleTAG;
    $ModuleTAG= "EmailLogger";
    if(!defined($ModuleTAG.'_Name')) define($ModuleTAG.'_Name', 'Email Logger', TRUE);
	if(!defined($ModuleTAG.'_Version')) define($ModuleTAG.'_Version', '0.3.03 ß', TRUE);
	if(!defined($ModuleTAG.'_Author')) define($ModuleTAG.'_Author', 'TheMightyDude', TRUE);
	if(!defined($ModuleTAG.'_Email')) define($ModuleTAG.'_Email', 'mailto:admin@initcorp.co.uk', TRUE);
	if(!defined($ModuleTAG.'_Website')) define($ModuleTAG.'_Website', 'http://e-script.initcorp.co.uk/Modular/EmailLogger', TRUE);
	if(!defined($ModuleTAG.'_Info')) define($ModuleTAG.'_Info','Mixed Module', TRUE);

#############
# Log Types #
#####################################
# These are for logging DON'T ALTER #
#####################################

	if(!defined('MiscEmail')) define('MiscEmail', '0', TRUE); 				// For Normal Email, For Future Use.
	if(!defined('Registering')) define('Registering', '1', TRUE);			// For when users Register.
	if(!defined('Feedback')) define('Feedback', '2', TRUE); 				// For when users Send Feedback.
	if(!defined('ReqPassword')) define('ReqPassword', '3', TRUE); 			// For when users Request Password.
	if(!defined('DebugInfo')) define('DebugInfo', '4', TRUE); 				// For when Debugging (Not Used yet).
	if(!defined('GlobalEmail')) define('GlobalEmail', '5', TRUE); 			// For sending Global Email to all registered players

	if(!defined($ModuleTAG.'_debug')) define($ModuleTAG.'_debug', False); 	// To be only used to fault find Errors when logging emails.



function AddELog($d_user,$e_type,$e_status,$e_subject,$e_response)
{
	global $username,$ip,$dbtables,$db,$ModuleTAG;
	if(constant($ModuleTAG."_debug")===True) echo "d_user,e_type,e_status,e_subject,e_response: $d_user,$e_type,$e_status,$e_subject,$e_response<br>\n";
    $result = $db->Execute("select * from $dbtables[players] LEFT JOIN $dbtables[ships] USING(player_id) WHERE email='$username'");
    $playerinfo = $result->fields;
    $result2 = $db->Execute("select * from $dbtables[players] LEFT JOIN $dbtables[ships] USING(player_id) WHERE email='$d_user'");
    $targetinfo = $result2->fields;

	if ($e_type == MiscEmail){$sp_id = $playerinfo[ship_id];$sp_name = $playerinfo[name];$sp_IP = $playerinfo[ip_address];$dp_id = $targetinfo[ship_id];$dp_name = $targetinfo[name];}
	else if ($e_type == Registering){$sp_id = -1;$sp_name = "Not Logged In";$sp_IP = $ip;$dp_id = $targetinfo[ship_id];$dp_name = $targetinfo[email];}
	else if ($e_type == Feedback){$sp_id = $playerinfo[ship_id];$sp_name = $playerinfo[name];$sp_IP = $playerinfo[ip_address];$dp_id = $targetinfo[ship_id];$dp_name = $targetinfo[name];}
	else if ($e_type == ReqPassword){$sp_id = -1;$sp_name = "Not Logged In";$sp_IP = $ip;$dp_id = $targetinfo[ship_id];$dp_name = $targetinfo[email];}
	else if ($e_type == DebugInfo){$sp_id = -1;$sp_name = "GameAdmin";$sp_IP = $ip;$dp_id = $targetinfo[ship_id];$dp_name = $d_user;}
	else if ($e_type == GlobalEmail){$sp_id = -1;$sp_name = "GameAdmin";$sp_IP = $ip;$dp_id = $targetinfo[ship_id];$dp_name = $d_user;}
	if ($e_response =='1') $e_response = "Sent OK";
	if ($e_status=='Y'){$attempt = '';}else{$attempt = " attempt";}
	$e_stamp =date("H:i:s d-m-Y");
	$dp_name=htmlspecialchars($dp_name,ENT_QUOTES);$sp_name=htmlspecialchars($sp_name,ENT_QUOTES);$e_subject=htmlspecialchars($e_subject,ENT_QUOTES);
	$res = $db->Execute("INSERT INTO $dbtables[email_log] VALUES('','$sp_name','$sp_IP','$dp_name','$e_subject','$e_status','$e_type','$e_stamp','$e_response')");
	if ($res){echo "<font color=\"yellow\">This Email$attempt has been Logged.</font><BR>\n";}
	else {echo "<font color=\"red\">Error Writing table: %s\n", mysql_error ()."</font><BR>\n";}
}


?>