<?

// ****************************************************************
// *** This script is ©2002 2003 Paul Kirby AKA TheMightyDude   ***
// *** And is free to use under condition the copyright notice  ***
// *** remains untouched.                                       ***
// *** Email: admin@initcorp.co.uk                              ***
// *** WebSite: http://E-Script.initcorp.co.uk                  ***
// ****************************************************************
// *** Setup Info Script                                        ***
// ****************************************************************

	error_reporting(0);

	include("config.php");
	include("languages/$lang");

	if (function_exists('session_start')) {
		session_start();
		if (is_null($_SESSION["count"])) {
			$_SESSION['count'] = 0;
			SetCookie ("TestCookie", "",0);
			SetCookie ("TestCookie", "Shuzbutt",time()+300,$gamepath, $gamedomain);
			$header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
			header($header_location . append_sid($HTTP_SERVER_VARS["PHP_SELF"], false));exit;
		} else {
			$_SESSION['count']=NULL;
			unset($_SESSION["count"]); }
	}
	testcookies();

	include("header.php");
	global $HTTP_SERVER_VARS;
	$createdate = date("l, F d, Y",strtotime ("Oct 01, 2003"));
	$updatedate = date("l, F d, Y",filemtime (basename ($HTTP_SERVER_VARS["PHP_SELF"])));
	$release_type = "OEM";
	$version = "0.6.4c (<font color=\"white\">$release_type</font>)";
	$author = "TheMightyDude";
	$email = "admin@initcorp.co.uk";
	$desc = "Written for Blacknova Traders V0.4x";

	if (function_exists('md5_file')) $hash = strtoupper(md5_file(basename ($HTTP_SERVER_VARS['PHP_SELF'])));

	$title="Setup Information Script";

	####################################
	# Switch for Environment Variables #
	####################################

	$show_Env_Var = False;

	####################################

	testdb();

?>

<STYLE TYPE="text/css">
<!--
	.email          { text-decoration: none; }
	.email:link     { text-decoration: none; }
	.email:visited  { text-decoration: none;}
	.email:hover    { text-decoration: none; color: yellow;}
	.button         { text-decoration: none; }
	.button:link    { text-decoration: none; color: black;}
	.button:visited { text-decoration: none; color: black;}
	.button:hover   { text-decoration: none; color: red;}
-->
</STYLE>

<?
	function append_sid($url, $non_html_amp = false)
	{
		global $SID;

		if ( !empty($SID) && !eregi('sid=', $url)) {
			$url .= ( ( strpos($url, '?') != false ) ?  ( ( $non_html_amp ) ? '&' : '&amp;' ) : '?' ) . $SID;
		} return($url);
	}

	################################
	#       Test the Cookies       #
	################################
	#       Enabled For Now.       #
	################################

	function testcookies()
	{
		global $COOKIE_Result,$gamepath, $gamedomain,$DoneRefresh,$_COOKIE;

		if (isset($_COOKIE['TestCookie'])) $COOKIE_Result = "<B>Passed</B>";
		else $COOKIE_Result = "<B><font color=\"red\">Failed testing Cookies!</font><br>\nPlease check your $"."gamepath and $"."gamedomain settings in config_local.php</B>";
	}

	################################
	# Test the Database Connection #
	################################
	#       Enabled For Now.       #
	################################

	function MySQL_Status()
	{
		global $db;

		if (!mysql_ping($db)) $MYSQL_STATUS= "<font color=\"red\">Down</font>";
		else $MYSQL_STATUS= "Running";
		return $MYSQL_STATUS;
	}

	function testdb()
	{

	################################
	# This is where we test the    #
	# connection to the database.  #
	################################

		global $connectedtodb;
		global $dbhost, $dbport, $dbuname, $dbpass, $dbname, $default_lang;
		global $lang, $gameroot, $db_type, $db_persistent, $db, $ADODB_FETCH_MODE;
		global $MYSQL_STATUS, $ADOdbpath;
		global $DB_Connect,$MYSQL_C_VERSION,$MYSQL_S_VERSION,$MYSQL_PROTO_INFO;

		if ($connectedtodb) { return; } else { $connectedtodb = true; }

		$MYSQL_C_VERSION = ''; $MYSQL_S_VERSION = ''; $MYSQL_PROTO_INFO = ''; 
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

		if(!empty($dbport)) $dbhost.= ":$dbport";

		if(is_dir($ADOdbpath)==true){
			$db = ADONewConnection("$db_type");

			if($db_persistent == 1) $result = $db->PConnect("$dbhost", "$dbuname", "$dbpass", "$dbname");
			else $result = $db->Connect("$dbhost", "$dbuname", "$dbpass", "$dbname");
			$DB_Connect = trim((($result) ? "<B>Connected OK</B>" : "<B><font color=red>".$db->ErrorMsg()."</font><br>\nPlease check you have the correct db info set in config_local.php.</B>"));

			if($result) {
				global $mySEC,$dbtables,$sched_ticks;
				$MYSQL_C_VERSION=mysql_get_client_info();
				$MYSQL_S_VERSION=mysql_get_server_info();
				$res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] LIMIT 1");
				$result = $res->fields;
				$mySEC = ($sched_ticks * 60) - (TIME()-$result['last_run']);
			}$db->Close();
		}
		else $DB_Connect="<B><font color=red>ADOdb files NOT found! </font><br>\nPlease check your $"."ADOdbpath setting in config_local.php</B>";
	}

	DisplayFlush("<div align=\"center\">\n");
	DisplayFlush("<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n");
	DisplayFlush("  <tr>\n");
	DisplayFlush("    <td><font size=\"6\" color=\"#FFFFFF\">$title</font></td>\n");
	DisplayFlush("  </tr>\n");
	DisplayFlush("  <tr>\n");
	DisplayFlush("    <td align=\"center\"><font size=\"2\" color=\"#FFFFFF\"><B>$desc</B></font></td>\n");
	DisplayFlush("  </tr>\n");
	DisplayFlush("</table>\n");
	DisplayFlush("</div><br>\n");

	##############################
	#      Table Functions.      #
	##############################

	Function do_Table_Title($title="Title",$Cols=2)
	{
		DisplayFlush("<div align=\"center\">\n");
		DisplayFlush("  <center>\n");
		DisplayFlush("  <table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" width=\"700\" bgcolor=\"#000000\">\n");
		DisplayFlush("    <tr>\n");
		DisplayFlush("      <td width=\"100%\" colspan=\"$Cols\" align=\"center\" bgcolor=\"#9999CC\">\n");
		DisplayFlush("        <p align=\"center\"><b><font face=\"Verdana\" color=\"#000000\">$title</font></b></td>\n");
		DisplayFlush("    </tr>\n");
	}

	Function do_Table_Blank_Row()
	{
		global $Cols;

		$Col_Str="colspan=\"".($Cols)."\"";
		DisplayFlush("    <tr>\n");
		DisplayFlush("      <td bgcolor=\"#9999CC\" width=\"75%\" $Col_Str bgcolor=\"#C0C0C0\" height=\"1\"></td>\n");
		DisplayFlush("    </tr>\n");
	}

	Function do_Table_Single_Row($col1="Col1")
	{
		global $Cols;

		$Col_Str="colspan=\"".($Cols)."\"";
		DisplayFlush("    <tr>\n");
		DisplayFlush("      <td bgcolor=\"#C0C0C0\" width=\"100%\" $Col_Str bgcolor=\"#C0C0C0\"><font face=\"Verdana\" size=\"1\" color=\"#000000\">$col1</font></td>\n");
		DisplayFlush("    </tr>\n");
	}

	Function do_Table_Row($col1="Col1",$col2="Col2",$status=False)
	{
		global $Cols, $Wrap;

		$Col_Str=''; $WrapStr=" nowrap";

		If ($Wrap==True) $WrapStr = '';
		if($status==False)
		{
			if ($Cols==3) $Col_Str="colspan=\"".($Cols-1)."\"";
			DisplayFlush("    <tr>\n");
			DisplayFlush("      <td width=\"25%\" bgcolor=\"#CCCCFF\"$WrapStr valign=\"top\"><font face=\"Verdana\" size=\"1\" color=\"#000000\">$col1</font></td>\n");
			DisplayFlush("      <td width=\"75%\" $Col_Str bgcolor=\"#C0C0C0\"$WrapStr><font face=\"Verdana\" size=\"1\" color=\"#000000\">$col2</font></td>\n");
			DisplayFlush("    </tr>\n");
		}
		else
		{
			DisplayFlush("    <tr>\n");
			DisplayFlush("      <td width=\"25%\" bgcolor=\"#CCCCFF\"$WrapStr valign=\"top\"><font face=\"Verdana\" size=\"1\" color=\"#000000\">$col1</font></td>\n");
			DisplayFlush("      <td width=\"65%\" bgcolor=\"#C0C0C0\"$WrapStr><font face=\"Verdana\" size=\"1\" color=\"#000000\">$col2</font></td>\n");
			DisplayFlush("      <td width=\"10%\" bgcolor=\"#CCCCFF\" align=\"center\"$WrapStr valign=\"top\"><font face=\"Verdana\" size=\"1\" color=\"#000000\"><b>$status</b></font></td>\n");
			DisplayFlush("    </tr>\n");
		}
	}

	Function do_Table_Footer($endline="<br>")
	{
		global $Cols;

		$Col_Str="colspan=\"".($Cols)."\"";
		DisplayFlush("    </tr>\n");
		DisplayFlush("    <tr>\n");
		DisplayFlush("      <td bgcolor=\"#9999CC\" width=\"75%\" $Col_Str bgcolor=\"#C0C0C0\" height=\"4\"></td>\n");
		DisplayFlush("    </tr>\n");
		DisplayFlush("  </table>\n");
		DisplayFlush("  </center>\n");
		DisplayFlush("</div>\n");
		DisplayFlush("$endline\n");
	}

	Function DisplayFlush($Text) 
	{
		print "$Text"; flush();
	}

	###############################
	# This gets the connection IP #
	###############################

	function getConIP() {

		global $HTTP_SERVER_VARS;

		if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]))
		{ 
			return $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
		}
		else
		{
			return $HTTP_SERVER_VARS["REMOTE_ADDR"];
		}

#		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
#			$ip = getenv("HTTP_CLIENT_IP");
#		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
#			$ip = getenv("HTTP_X_FORWARDED_FOR");
#		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
#			$ip = getenv("REMOTE_ADDR");
#		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
#			$ip = $_SERVER['REMOTE_ADDR'];
#		else
#			$ip = "unknown";
#		return($ip);
	}

	##############################
	#  This gets the Game Path.  #
	##############################

	function get_gamepath($display=True)
	{
	    global $HTTP_SERVER_VARS,$PHP_SELF;
		global $gamepath, $status_gamepath;$status_gamepath="<font color=\"red\">Incorrect</font>";

		$status = "<font color=\"Red\">The settings you have set for $"."gamepath in config_local.php are different.  Please set these to what I said above.</font>";
		$result=dirname($HTTP_SERVER_VARS["PHP_SELF"]);

		if ($result ==="\\")$result="/";
		if ($result[0] !="."){
			if ($result[0] !="/"){$result="/$result";}
			if ($result[strlen($result)-1] !="/"){$result="$result/";}
		}else $result ="/";
		$result =str_replace("\\", "/", stripcslashes($result));

		if($result == $gamepath) 
		{
			$status_gamepath = "<font color=\"Blue\">Correct</font>";
			$status = "<font color=\"lime\">The settings you have set for $"."gamepath in config_local.php are the same.</font>";
		}

		if($display==True)
		{
			DisplayFlush("// This is the trailing part of the URL, that is not part of the domain.<br>\n");
			DisplayFlush("// If you enter www.blah.com/blacknova to access the game, you would leave the line as it is.<br>\n");
			DisplayFlush("// If you do not need to specify blacknova, just enter a single slash eg:<br>\n");
			DisplayFlush("// $"."gamepath = \"/\";<br>\n");
			DisplayFlush("<font color=yellow>$"."gamepath = \"$result\";</font><br>\n");
			DisplayFlush("# $status #<br>\n");
			DisplayFlush("<br>\n");
		}
		return $result;
	}

	##############################
	# This gets the Game Domain. #
	##############################

	function get_gamedomain($display=False)
	{
		global $HTTP_SERVER_VARS;
		global $gamedomain, $status_gamedomain;$status_gamedomain="<font color=\"red\">Incorrect</font>";

		$status = "<font color=\"Red\">The settings you have set for $"."gamedomain in config_local.php are different. Please set these to what I said above.</font>";
		$RemovePORT = True;
		$result = $HTTP_SERVER_VARS["HTTP_HOST"];
		$pos = strpos($result,"http://");if (is_integer($pos)) $result = substr($result,$pos+7);
		$pos = strpos($result,"www."); if (is_integer($pos)) $result = substr($result,$pos+4);

		if($RemovePORT) $pos = strpos($result,":"); if (is_integer($pos)) $result = substr($result,0,$pos);
		if ($result[0]!=".") $result=".$result";
		if($result == $gamedomain)
		{
			$status_gamedomain = "<font color=\"Blue\">Correct</font>";
			$status = "<font color=\"lime\">The settings you have set for $"."gamedomain in config_local.php are the same.</font>";
		}

		if($display==True)
		{
			DisplayFlush("// Domain & path of the game on your webserver (used to validate login cookie)<br>\n");
			DisplayFlush("// This is the domain name part of the URL people enter to access your game.<br>\n");
			DisplayFlush("// So if your game is at www.blah.com you would have:<br>\n");
			DisplayFlush("// $"."gamedomain = \"www.blah.com\";<br>\n");
			DisplayFlush("// Do not enter slashes for $gamedomain or anything that would come after a slash<br>\n");
			DisplayFlush("// if you get weird errors with cookies then make sure the game domain has TWO dots<br>\n");
			DisplayFlush("// i.e. if you reside your game on http://www.blacknova.net put .blacknova.net as $"."gamedomain.<br>\n");
			DisplayFlush("// If your game is on http://www.some.site.net put .some.site.net as your game domain.<br>\n");
			DisplayFlush("// Do not put port numbers in $"."gamedomain.<br>\n");
			DisplayFlush("<font color=yellow>$"."gamedomain = \"$result\";</font><br>\n");
			DisplayFlush("# $status #<br>\n");
			DisplayFlush("<br>\n");
		}
		return $result;
	}

	##############################
	#  This gets the Game Root.  #
	##############################

	function get_gameroot($display=False)
	{
		global $HTTP_SERVER_VARS;
		global $gameroot, $status_gameroot;$status_gameroot="<font color=\"red\">Incorrect</font>";

		$status = "<font color=\"Red\">The settings you have set for $"."gameroot in config_local.php are different. Please set these to what I said above.</font>";
		$result = $HTTP_SERVER_VARS["PATH_TRANSLATED"];
		if(!isset($result))
			$result = $HTTP_SERVER_VARS["SCRIPT_FILENAME"];

		$result =str_replace("\\", "/", stripcslashes(dirname($result)));

		if($result == $gameroot) {
			$status_gameroot = "<font color=\"Blue\">Correct</font>";
			$status = "<font color=\"lime\">The settings you have set for $"."gameroot in config_local.php are the same.</font>";
		}

		if($display==True) {
	        DisplayFlush("// Path on the filesystem where the blacknova files will reside:<br>\n");
			DisplayFlush("<font color=yellow>$"."gameroot = \"$result\";</font><br>\n");
			DisplayFlush("# $status #<br>\n");
			DisplayFlush("<br>\n");
		}
		return $result;
	}

	##############################
	#  This gets the ADOdb Path  #
	##############################

	function get_ADOdb_path()
	{
		global $HTTP_SERVER_VARS;
		global $ADOdbpath, $status_ADOdb;$status_ADOdb="<font color=\"red\">Incorrect</font>";

		if(is_dir($ADOdbpath)) $status_ADOdb = "<font color=\"Blue\">Correct</font>";

		return $ADOdbpath;
	}

	function get_server_software()
	{

		global $SERVER_SOFTWARE,$REMOTE_ADDR,$REMOTE_HOST,$REMOTE_PORT, $TERM,$HOSTTYPE,$LOCAL_ADDR;
		global $ADODB_vers, $PHP_VERSION, $gd_array, $PHP_Interface, $Wrap,$Cols;
		global $db,$HTTP_SERVER_VARS;
		global $COOKIE_Result;
		global $SERVER_PROTOCOL, $OS_TYPE, $PlatOS;
		global $DB_Connect,$MYSQL_C_VERSION,$MYSQL_S_VERSION,$MYSQL_PROTO_INFO;
		global $SERVER_ADDR,$SERVER_PORT,$GATEWAY_ADDR;
		global $ADODB_Database,$ADODB_EXTENSION, $MYSQL_STATUS;
		global $Zend_Version,$compiler,$APACHE_VERSION,$MOD_SSL_VERSION,$OPENSSL_VERSION,$php_sapi;

		global $sapi_module;

		DisplayFlush("<br>\n");
		DisplayFlush("// This is just to find out what Server Operating System your running bnt on.<br>\n");
		DisplayFlush("// And to find out what other software is running e.g. PHP,<br>\n");


		$Cols = 3; $Wrap = True;
		DisplayFlush("<br>\n");
		do_Table_Title("Server Software/Operating System",$Cols);

		if(!empty($php_sapi)) do_Table_Row("System",$php_sapi);

		if(!empty($OS_TYPE)) do_Table_Row("Operating System",$OS_TYPE);
		if(defined('PHP_OS')) do_Table_Row("OS Type",PHP_OS);
		if(!empty($PlatOS)) do_Table_Row("Platform System","$PlatOS");

		if(!empty($REMOTE_ADDR)) do_Table_Row("LOCAL ADDR","$LOCAL_ADDR:$REMOTE_PORT");
		if(!empty($REMOTE_ADDR)&&!empty($REMOTE_PORT)) do_Table_Row("PROXY ADDR","$GATEWAY_ADDR");
		if(!empty($SERVER_ADDR)&&!empty($SERVER_PORT)) do_Table_Row("DESTINATION ADDR","$SERVER_ADDR:$SERVER_PORT");

		if(!empty($DB_Connect)) do_Table_Row("DB CONNECTION","$DB_Connect");
		if(!empty($COOKIE_Result)) do_Table_Row("COOKIE TEST","$COOKIE_Result");
		if(!empty($ADODB_Database)) do_Table_Row("ADODB Database","$ADODB_Database");

		do_Table_Footer("");

		$Cols = 3; $Wrap = True;
		DisplayFlush("<br>\n");
		do_Table_Title("Software Versions",$Cols);

		if(!empty($Zend_Version)) {do_Table_Row("Zend Version","$Zend_Version");}
		if(!empty($APACHE_VERSION)) {do_Table_Row("Apache Version","$APACHE_VERSION");}
		if(!empty($PHP_VERSION)) {do_Table_Row("PHP Version","$PHP_VERSION");}
		if(!empty($PHP_Interface)) {do_Table_Row("PHP Interface ","$PHP_Interface");}

		if(!empty($MOD_SSL_VERSION)) {do_Table_Row("* mod_ssl Version","$MOD_SSL_VERSION");}
		if(!empty($OPENSSL_VERSION)){do_Table_Row("* OpenSSL Version","$OPENSSL_VERSION");}

		if(!empty($MYSQL_C_VERSION)){do_Table_Row("mySQL Client Version","$MYSQL_C_VERSION");}
		if(!empty($MYSQL_S_VERSION)){do_Table_Row("mySQL Server Version","$MYSQL_S_VERSION");}
		if(!empty($ADODB_vers)){do_Table_Row("Adodb Version","$ADODB_vers");}

		do_Table_Blank_Row();
		do_Table_Single_Row("* = Module (if any installed).");
		do_Table_Footer("");


		DisplayFlush("<p>// This GD Library section is just displayed for future use, and may not even be used within Blacknova Traders.</p>\n");

		$Cols = 3; $Wrap = True;
		do_Table_Title("GD Library Information",$Cols);
	
		if(!empty($gd_array)){
			do_Table_Row("GD Version",$gd_array['GD Version']);
			do_Table_Row("JPG Support",TRUEFALSE($gd_array['JPG Support'],True,"Enabled","Disabled"));
			do_Table_Row("PNG Support",TRUEFALSE($gd_array['PNG Support'],True,"Enabled","Disabled"));
			do_Table_Row("GIF Read Support",TRUEFALSE($gd_array['GIF Read Support'],True,"Enabled","Disabled"));
	
			do_Table_Blank_Row();
			do_Table_Single_Row("These are optional installed libraries (if any installed).");
		}else{
			do_Table_Single_Row("Sorry GD Library not installed.");
		}
		do_Table_Footer("");

	}

	$SERVER_SOFTWARE = ''; $REMOTE_ADDR = ''; $REMOTE_HOST = ''; $HOSTTYPE = '';
	$PHP_VERSION = ''; $OS_TYPE ='';

	if(isset($HTTP_SERVER_VARS["SERVER_SOFTWARE"])) $SERVER_SOFTWARE = $HTTP_SERVER_VARS["SERVER_SOFTWARE"];
	if(isset($HTTP_SERVER_VARS["REMOTE_ADDR"])) $GATEWAY_ADDR = $HTTP_SERVER_VARS["REMOTE_ADDR"];
	if(empty($LOCAL_ADDR)) $LOCAL_ADDR = getConIP();

	$pos=strpos(getConIP(),",");$REMOTE_ADDR=$LOCAL_ADDR;
	if(is_integer($pos)){$GATEWAY_ADDR=substr($LOCAL_ADDR,$pos+1);$LOCAL_ADDR=substr($LOCAL_ADDR,0,$pos);}

	$Zend_Version = zend_version();

	if(isset($HTTP_SERVER_VARS["SERVER_ADDR"])) $SERVER_ADDR = $HTTP_SERVER_VARS["SERVER_ADDR"];
	if(isset($HTTP_SERVER_VARS["SERVER_PORT"])) $SERVER_PORT = $HTTP_SERVER_VARS["SERVER_PORT"];
	if(isset($HTTP_SERVER_VARS["REMOTE_PORT"])) $REMOTE_PORT = $HTTP_SERVER_VARS["REMOTE_PORT"];

// *** PHP Interface *** //
	$sapi_type = php_sapi_name();
	if (preg_match ("/cgi/", $sapi_type)) $PHP_Interface = "CGI PHP";
	else if (preg_match ("/apache/", $sapi_type)) $PHP_Interface = "mod_PHP";
	else $PHP_Interface = "Unknown ($sapi_type)";
// ********************* //

	if (function_exists('gd_info')) $gd_array = gd_info();

	if(isset($PHP_VERSION)) $PHP_VERSION = PHP_VERSION;
	if (is_integer(strpos($SERVER_SOFTWARE, "Apache"))) $PlatOS = "Apache"; else $PlatOS = "IIS";

	##############################
	# Get Apache Version + Mods. #
	##############################
 
	$ar = split("[/ ]",$HTTP_SERVER_VARS['SERVER_SOFTWARE']);
	for ($i=0;$i<(count($ar));$i++){
		switch(strtoupper($ar[$i])){
			case 'APACHE': $i++;if(empty($APACHE_VERSION)) $APACHE_VERSION = $ar[$i];break;
			case 'PHP': $i++;if(empty($PHP_VERSION)) $PHP_VERSION = $ar[$i];break;
			case 'MOD_SSL':$i++;if(empty($MOD_SSL_VERSION)) $MOD_SSL_VERSION = $ar[$i];break;
			case 'OPENSSL':$i++;if(empty($OPENSSL_VERSION)) $OPENSSL_VERSION = $ar[$i];break;
		}
	}

	if(function_exists('php_uname')) $php_sapi =php_uname();
	if(function_exists('apache_get_version')) $apache_version =apache_get_version();

	$Spos = strpos($SERVER_SOFTWARE, "(")+1; 
	$Epos = strpos($SERVER_SOFTWARE, ")",(int)$Spos);

	if (is_integer($Spos) && is_integer($Epos)) {
		$Lpos = $Epos-$Spos; $Platform = substr($SERVER_SOFTWARE, $Spos, $Lpos); DisplayFlush("<hr>\n");
		if($Platform=="Win32") {
			$OS_TYPE = "Windows"; if (function_exists('exec')) $OS_TYPE = exec("ver");}
		Else If($Platform=="Red Hat Linux"||$Platform=="Unix"||$Platform=="UNIX") 
			$OS_TYPE = "Linux / Unux";
		Else If($Platform=="Gentoo/Linux")
			$OS_TYPE = "Gentoo Linux";
		Else 
#			$OS_TYPE = $Platform;
			$OS_TYPE = "Unknown OS [<B>Tell Author to add Platform = $Platform</B>]"; 
	}

	DisplayFlush("<font color=#FFFF00><i>Well since a lot of people are having problems setting up Blacknova Traders on a Linux based server.</i></font><br>\n");
	DisplayFlush("<font color=#FFFF00><i>Here is the settings that you may require to set.</i></font><br>\n");
	DisplayFlush("<br><font color=#FFFFFF>ADMINS:</font> <font color=#FFFF00>If you get any errors or incorrect info returned then set $"."show_Env_Var = True;</font><br>\n");
	DisplayFlush("<font color=yellow>Then refresh the page and then save it as htm or html and then Email it to me.</font><br>\n");
	DisplayFlush("<hr>\n");

	get_server_software();
	get_ADOdb_path();

	#########################################
	#         Config_Local Settings.        #
	#########################################

	DisplayFlush("<p>// This is what you need to put in your config_local.php file.<br>\n");
	DisplayFlush("// If you are having problems using this script then email me <a class=\"email\" href=\"mailto:$email\">$author</a>.<br>\n");
	DisplayFlush("// Also if you think the info displayed is Incorrect then Email me <a class=\"email\" href=\"mailto:$email\">$author</a> with the following information:</p>\n");
	DisplayFlush("<ul>\n");
	DisplayFlush("  <li><font color=#FFFF00>A htm or html saved page from within you browser of Setup Info with <font color=#00FF00>$"."show_Env_Var = True;</font> This is settable within setup_info.php.</font></li>\n");
	DisplayFlush("  <li><font color=#FFFF00>What Operating System you are using.</font></li>\n");
	DisplayFlush("  <li><font color=#FFFF00>What Version of Apache, PHP and mySQL that you are using.</font></li>\n");
	DisplayFlush("  <li><font color=#FFFF00>And if using Windows OS are you using IIS.</font></li>\n");
	DisplayFlush("</ul>\n");
	DisplayFlush("<p>// With this information it will help me to help you much faster and also get my Script to display more reliable information.</p>\n");

	$Cols = 2;
	do_Table_Title("Config_Local Settings",$Cols);
	do_Table_Row("gameroot","<B>".get_gameroot(false)."</B>");
	do_Table_Row("gamepath","<B>".get_gamepath(false)."</B>");
	do_Table_Row("gamedomain","<B>".get_gamedomain(false)."</B>");
	do_Table_Footer();

	#########################################
	#  This gets the Environment Variables  #
	#########################################

	if($show_Env_Var) {
		DisplayFlush("<p>// This is used to help the admin of the server set up BNT, Or its used by me if you are having problems setting up BNT.</p>\n");
		$Cols = 2;$Wrap = True;
		do_Table_Title("Environment Variables",$Cols);
		ksort($HTTP_SERVER_VARS); reset($HTTP_SERVER_VARS);
		foreach($HTTP_SERVER_VARS as $k => $v) 
		{$v =implode("; ",explode(";", $v)); do_Table_Row("$k","$v");}
		do_Table_Footer();
	}

	#########################################
	#   Current Config_Local Information.   #
	#########################################

	DisplayFlush("<p>// This is what you already have set in config_local.php.<br>\n");
	DisplayFlush("// This will also tell you if what you have set in config_local.php is the same as what Setup Info has Auto Detected.</p>\n");
	$Cols = 3;
	do_Table_Title("Current Config_Local Information",$Cols);
	do_Table_Row("Code Base","$game_name");

	if(isset($gamename)) do_Table_Row("Game Name","$gamename");

	do_Table_Row("Database Type","$db_type");
	do_Table_Row("Connection Type",($db_persistent ? "Persistent Connection" : "Non Persistent Connection"));
	$dbport_Tmp = ($dbport=="") ? "3306" : $dbport;
	do_Table_Row("Database Server Address","$dbhost:$dbport_Tmp");
	do_Table_Row("Database Name","$dbname");
	do_Table_Row("Table Prefix","$db_prefix");
	do_Table_Row("Admin Name",((strlen($adminname)>0) ? $adminname : "NOT SET or NOT Available in this Version"));
	do_Table_Row("Admin Email","$admin_mail");
	do_Table_Blank_Row();
	do_Table_Row("$"."gameroot","$gameroot",$status_gameroot);
	do_Table_Row("$"."gamepath","$gamepath",$status_gamepath);
	do_Table_Row("$"."gamedomain","$gamedomain",$status_gamedomain);
	do_Table_Blank_Row();
	do_Table_Row("$"."ADOdbpath","$ADOdbpath",$status_ADOdb);
	do_Table_Footer("<br>");

	#########################################
	#      Current PHP.INI Information.     #
	#########################################

	DisplayFlush("<p>// This is the information thats in PHP.INI that may needed to get BNT set-up.</p>\n");
	$Cols = 3;

	do_Table_Title("PHP.INI Information",$Cols);
	do_Table_Row("<b>* Register Globals</b>", "<b>".(get_cfg_var('register_globals') ? "On" : "Off")."</b>",((reg_global_fix===True) ? "<b>Patched</b>":""));
	if(get_cfg_var('register_globals') !=True && (!defined('reg_global_fix'))) do_Table_Single_Row("<font color=\"red\"><b>*** Warning BNT at this time requires Register Globals to be enabled. Or to be patched.</b></font>");

	do_Table_Row("Register argc argv",(get_cfg_var('register_argc_argv') ? "On" : "Off") );
	do_Table_Row("Post Max Size",get_cfg_var('post_max_size'));

	do_Table_Row("<b>* Safe Mode</b>","<b>".(get_cfg_var('safe_mode') ? "On" : "Off")."</b>" );
	if(get_cfg_var('safe_mode')==True) do_Table_Single_Row("<font color=\"red\"><b>*** Warning BNT at this time requires Safe Mode to be disabled.</b></font>");


	do_Table_Row("Display Errors",(get_cfg_var('display_errors') ? "On" : "Off") );
	do_Table_Row("zlib Output Compression",(get_cfg_var('zlib.output_compression') ? "On" : "Off") );
	do_Table_Row("Implicit Flush",(get_cfg_var('implicit_flush') ? "On" : "Off") );
	do_Table_Row("Output Buffering",(get_cfg_var('output_buffering') ? "On" : "Off") );
	do_Table_Row("Short Open Tag",(get_cfg_var('short_open_tag') ? "On" : "Off") );
	do_Table_Blank_Row();
	$disable_functions = get_cfg_var('disable_functions');

	if (empty($disable_functions)) $disable_functions = "No disabled functions";

	$disable_functions = implode(", ",explode(",", $disable_functions));
	do_Table_Row("Disabled Functions",$disable_functions);
	do_Table_Blank_Row();
	do_Table_Row("Magic Quotes",(get_cfg_var('magic_quotes_gpc') ? "On" : "Off") );
	do_Table_Row("Magic Quotes Runtime",(get_cfg_var('magic_quotes_runtime') ? "On" : "Off") );
	do_Table_Row("Magic Quotes Sybase",(get_cfg_var('magic_quotes_sybase') ? "On" : "Off") );
	do_Table_Blank_Row();
	do_Table_Row("Sql Safe Mode",(get_cfg_var('sql.safe_mode') ? "On" : "Off") );
	do_Table_Blank_Row();
	do_Table_Row("odbc Allow Persistent",(get_cfg_var('odbc.allow_persistent') ? "On" : "Off") );
	do_Table_Row("odbc Check Persistent",(get_cfg_var('odbc.check_persistent') ? "On" : "Off") );
	do_Table_Row("odbc Max Persistent",(get_cfg_var('odbc.max_persistent') ? "No Limit" : get_cfg_var('odbc.max_persistent')) );
	do_Table_Row("odbc Max Links",(get_cfg_var('odbc.max_links') ? "No Limit" : get_cfg_var('odbc.max_links')) );

	switch(get_cfg_var('odbc.defaultlrl')){
		case 0: $defaultlrl="Passthru"; break;
		default: $defaultlrl=get_cfg_var('odbc.defaultlrl'); break;}
	do_Table_Row("odbc LONG fields",$defaultlrl);

	switch(get_cfg_var('odbc.defaultbinmode')){
		case 0: $defaultbinmode="Passthru Data"; break;
		case 1: $defaultbinmode="Return data as is"; break;
		case 2: $defaultbinmode="Convert data to char"; break;
		default: $defaultbinmode="Ubknown Mode"; break;}

	do_Table_Row("odbc Binary Data",$defaultbinmode);
	do_Table_Blank_Row();
	do_Table_Row("mySQL Allow Persistent",(get_cfg_var('mysql.allow_persistent') ? "On" : "Off") );
	do_Table_Row("mySQL Max Persistent",(get_cfg_var('mysql.max_persistent') ? "No Limit" : get_cfg_var('mysql.max_persistent')) );
	do_Table_Row("mySQL Max Links",(get_cfg_var('mysql.max_links') ? "No Limit" : get_cfg_var('mysql.max_links')) );

	if ($default_port = get_cfg_var('mysql.default_port')=='') $default_port = "Default (3306)";
	do_Table_Row("mySQL Default Port",$default_port);

	if ($default_socket = get_cfg_var('mysql.default_socket')=='') $default_socket = "Using built-in Socket";
	do_Table_Row("mySQL Default Socket",$default_socket);

	if ($default_host = get_cfg_var('mysql.default_host')=='') $default_host = "Using Default Host";
	do_Table_Row("mySQL Default Host",$default_host);

	if ($default_user = get_cfg_var('mysql.default_user')=='') $default_user = "Using Default User";
	do_Table_Row("mySQL Default User",$default_user);

	switch(get_cfg_var('mysql.connect_timeout')){
		case 0: $connect_timeout="Default \"0\""; break;
		default: $connect_timeout=(get_cfg_var('mysql.connect_timeout') ? "No Limit" : get_cfg_var('mysql.connect_timeout') ); break;}

	do_Table_Row("mySQL Connect Timeout",$connect_timeout);
	do_Table_Blank_Row();
	do_Table_Row("HTML Errors",(ini_get('html_errors') ? "On" : "Off") );
	do_Table_Row("Report Memleaks",(ini_get('report_memleaks') ? "On" : "Off") );
	do_Table_Row("Log Errors",(ini_get('log_errors') ? "On" : "Off") );
	do_Table_Single_Row("* = Important variable.");
	do_Table_Footer("<br>");

	#########################################
	#         My Script Information.        #
	#########################################

	DisplayFlush("<hr size=\"1\">\n");
	DisplayFlush("<div align=\"center\">\n");
	DisplayFlush("  <center>\n");
	DisplayFlush("  <table cellSpacing=\"0\" width=\"100%\" border=\"0\">\n");
	DisplayFlush("    <tbody>\n");
	DisplayFlush("      <tr>\n");
	DisplayFlush("        <td vAlign=\"top\" noWrap align=\"left\" width=\"50%\"><font face=\"Verdana\" size=\"1\" color=\"white\">Version <font color=\"lime\">$version</font></font></td>\n");
	DisplayFlush("        <td vAlign=\"top\" noWrap align=\"right\" width=\"50%\"><font face=\"Verdana\" size=\"1\" color=\"white\">Created on <font color=\"lime\">$createdate</font></font></td>\n");
	DisplayFlush("      </tr>\n");
	DisplayFlush("      <tr>\n");
	DisplayFlush("        <td vAlign=\"top\" noWrap align=\"left\" width=\"50%\"><font face=\"Verdana\" size=\"1\" color=\"white\">");

	if (function_exists('md5_file')) DisplayFlush(" Hash: [<font color=\"yellow\">$hash</font>]");
	else DisplayFlush(" Hash: [<font color=\"yellow\">Disabled</font>]");

	DisplayFlush("</font></td>\n");
	DisplayFlush("        <td vAlign=\"top\" noWrap align=\"right\" width=\"50%\"><font face=\"Verdana\" size=\"1\" color=\"white\">Updated on <font color=\"lime\">$updatedate</font></font></td>\n");
	DisplayFlush("      </tr>\n");
	DisplayFlush("    </tbody>\n");
	DisplayFlush("  </table>\n");
	DisplayFlush("  </center>\n");
	DisplayFlush("</div>\n");
	DisplayFlush("<hr size=\"1\"><br>\n");

	if(empty($username)) TEXT_GOTOLOGIN(); else TEXT_GOTOMAIN();

	include("footer.php");

?>
