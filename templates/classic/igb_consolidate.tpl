<table width="600" height="350" border="0">
  <tr>
    <td align="center" background="templates/{$templateset}/images/igbscreen.png">
      <table width="520" height="300" border="0">

<tr><td colspan="2" align="center" valign="top"><font color="#00FF00">{$l_igb_planetconsolidate}<br>---------------------------------</td></tr>
<form action="igb_consolidate2.php" method="post">
<tr valign="top">
<td colspan="2"><font color="#00FF00">{$l_igb_consolrates} :</td>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_minimum} :<br>
<br>{$l_igb_maximum} :</td>
<td align="right"><font color="#00FF00">
<input class="term" type="text" size="15" maxlength="50" name="minimum" value="0"><br><br>
<input class="term" type="text" size="15" maxlength="50" name="maximum" value="0"><br><br>
<input class="term" type="submit" value="{$l_igb_compute}"></td>
<input type="hidden" name="dplanet_id" value="{$dplanet_id}">
</form>
<tr><td colspan="2" align="center"><font color="#00FF00">
{$l_igb_transferrate3}
<tr valign="bottom">
<td><font color="#00FF00"><a href="igb_transfer.php">{$l_igb_back}</a></td><td align="right"><font color="#00FF00">&nbsp;<br><a href="main.php">{$l_igb_logout}</a></td>
</tr>

</table></td></tr></table>
