<?
include("config.php");
connectdb();
include("languages/$lang");

function create_player($shipname, $character, $username, $password)
{
	global $db, $dbtables, $start_armour, $start_credits, $start_energy, $start_fighters, $start_pod, $start_scoop, $ip, $default_lang;
	$stamp	= date("Y-m-d H:i:s");
 	
 	$query	= $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM $dbtables[ships]");
	$res	= $query->fields;
	$mturns	= $res[mturns];
	
	if($mturns > $max_turns)	{ $mturns = $max_turns; }

	$query	= $db->Execute("SELECT count(*) AS vv FROM $dbtables[ships] WHERE vote >= 0");
	$res	= $query->fields;
	if ($res[vv] > 0)		{ $vote_stat = -2; }
	else				{ $vote_stat = -1; }

	$query	= $db->Execute("SELECT count(*) AS vv FROM $dbtables[ships] WHERE vote < -2");
	$res	= $query->fields;
	if ($res[vv] > 0)		{ $vote_stat = -3; }

	$result2 = $db->Execute("INSERT INTO $dbtables[ships] SET `ship_name`='$shipname', `character_name`='$character', `password`='$password', `email`='$username', `armour_pts`='$start_armour', `credits`='$start_credits', `ship_energy`='$start_energy', `ship_fighters`='$start_fighters', `turns`='$mturns', `dev_escapepod`='$start_pod', `dev_fuelscoop`='$start_scoop', `last_login`='$stamp', `ip_address`='$ip', `lang`='$default_lang', `vote`=$vote_stat");
	if ($result2)
	{
		$ship_id = $db->Insert_ID();
		if (!$ship_id)
		{
			$result2 = $db->Execute("SELECT ship_id FROM $dbtables[ships] WHERE email='$username'");
			$ship_id = $result2->fields[ship_id];
		}
		$db->Execute("INSERT INTO $dbtables[zones] VALUES('','$character\'s Territory', $ship_id, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
		$db->Execute("INSERT INTO $dbtables[ibank_accounts] (ship_id,balance,loan) VALUES($ship_id,0,0)");
	}
	else
	{
		echo $db->ErrorMsg();
		include("footer.php");
		die();
	}
}

function makepassword()
{
	$makepass	= "";
	$syllables	= "er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
	$syllable_array	= explode(",", $syllables);
	srand((double)microtime()*1000000);
	for ($count=1; $count<=4; $count++)
	{
		if (rand()%10 == 1)
		{
			$makepass .= sprintf("%0.0f",(rand()%50)+1);
		} else {
			$makepass .= sprintf("%s",$syllable_array[rand()%62]);
		}
	}
	return $makepass;
}

if ($_POST['command'] == "new")
{
	$error_mesg		= "";
		
	$username		= $_POST['character']; //This needs to STAY before the db query
	$character		= htmlspecialchars($_POST['character']);
	$character		= ereg_replace("[^[:digit:][:space:][:alpha:][\']]"," ",$character);
	$shipname		= htmlspecialchars($_POST['shipname']);
	$shipname		= ereg_replace("[^[:digit:][:space:][:alpha:][\']]"," ",$shipname);
	$password_MD5			= $_POST['password_MD5'];
	$password_check_MD5 	= $_POST['password_check_MD5'];

	if(!get_magic_quotes_gpc())
	{
		$username	= addslashes($username);
		$character	= addslashes($character);
		$shipname	= addslashes($shipname);
	}

	if (empty($username)  ||
	    empty($character) ||
	    empty($shipname)  ||
	    ($display_password && (($password_MD5 == md5("")) || ($password_check_MD5 == md5(""))) ) ) 
		{ $error_mesg .= $l_new_blank."<BR>"; }

	$result = $db->Execute("select email, character_name, ship_name from $dbtables[ships] where email='$username' OR character_name='$character' OR ship_name='$shipname'");
	if ($result > 0)
	{
		while (!$result->EOF)
		{
			$row = $result->fields;
			if (strtolower($row[email])==strtolower($username))
				{ $error_mesg .= "$l_new_inuse  $l_new_4gotpw1 <a href=mail.php?mail=$username>$l_clickme</a> $l_new_4gotpw2<BR>";}
			if (strtolower($row[character_name])==strtolower($character))
				{ $error_mesg .= "$l_new_inusechar<BR>";}
			if (strtolower($row[ship_name])==strtolower($shipname))
				{ $error_mesg .= "$l_new_inuseship<BR>";}
			$result->MoveNext();
		}
	}
	
	if($display_password)
	{
		if ($password_MD5 != $password_check_MD5)
		{
			$error_mesg .= $l_opt2_newpassnomatch;
		}
		$makepass = $password_MD5;
	} else {
		$makepass = md5(makepassword());
	}
	// All checks passed... If there is nothing in the error_mesg go ahead and create the player, otherwise display the form.
}

//----------- ALL IO is below this line.....

$title=$l_new_title;
include("header.php");

bigtitle();

if ($account_creation_closed)
{
	echo $l_new_closed_message;
}
else if (($_POST['command'] != "new") || !empty($error_mesg))
{

	echo "<center>$error_mesg;</center>\n";

	if ($display_password)
	{
?>
<SCRIPT language="JavaScript" SRC="md5.js"></SCRIPT>
<SCRIPT language="JavaScript">
function md5onsubmit()
{
	document.forms(0).password_MD5.value = calcMD5(document.forms(0).password.value);
	document.forms(0).password_check_MD5.value = calcMD5(document.forms(0).password_check.value);
	
	document.forms(0).password.value = "";
	document.forms(0).password_check.value = "";
	return true;
}
</SCRIPT>
<?
		echo "<form action=\"$PHP_SELF\" method=\"post\" onSubmit=\"md5onsubmit()\">\n";
	}
	else
	{
		echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
	}
?>
<input type="hidden" name="command" value="new">
<table align="center" width="75%" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td><? echo $l_login_email;?></td>
		<td><input type="text" name="username" size="20" maxlength="40" value=""></td>
	</tr>
	<tr>
		<td><? echo $l_new_shipname; ?></td>
		<td><input type="text" name="shipname" size="20" maxlength="20" value=""></td>
	</tr>
	<tr>
		<td><? echo $l_new_pname;?></td>
		<td><input type="text" name="character" size="20" maxlength="20" value=""></td>
	</tr>
<?
	if ($display_password)
	{
?>
	<tr>
		<td>Password</td>
		<td><input type="password" name="password" size="20" maxlength="20" value="">
		<input type="hidden" name="password_MD5"></td>
	</tr>
	<tr>
		<td>Password Confirm</td>
		<td><input type="password" name="password_check" size="20" maxlength="20" value="">
		<input type="hidden" name="password_check_MD5"></td>
	</tr>
<?	
	}
?>
	<tr>
		<td align=right><input type="submit" value="<? echo $l_submit;?>"></td>
		<td align=left><input type="reset" value="<? echo $l_reset;?>"></td>
	</tr>
	<tr>
		<td align=center colspan=2><? echo $l_new_info;?></td>
	</tr>
</table>
</form>
<?
}
else
{
	create_player($shipname, $character, $username, substr($makepass,0,$maxlen_password));
	
	if($display_password)
	{
		echo $l_new_pwis . " " . $makepass . "<BR><BR>";
	} else {
		$l_new_message = str_replace("[pass]", $makepass, $l_new_message);
		mail("$username", "$l_new_topic", "$l_new_message\r\n\r\nhttp://$gamedomain","From: $admin_mail\r\nReply-To: $admin_mail\r\nX-Mailer: PHP/" . phpversion());	
		echo "$l_new_pwsent<BR><BR>";
	}
	echo "<A HREF=login.php>$l_clickme</A> $l_new_login";	
}

echo "<BR><BR>";
include("footer.php");
?>
