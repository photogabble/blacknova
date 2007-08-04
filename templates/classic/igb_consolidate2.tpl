<table width="600" height="350" border="0">
  <tr>
    <td align="center" background="templates/{$templateset}/images/igbscreen.png">
      <table width="520" height="300" border="0">


<tr><td colspan="2" align="center" valign="top"><font color="#00FF00">{$l_igb_planetconsolidate}<br>---------------------------------</td></tr>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_currentpl} {$dest_name} {$l_igb_in} {$dest_sector_id} :</td>
<td align="right"><font color="#00FF00"> {$dest_credits} C</td>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_transferamount} :</td>
<td align="right"><font color="#00FF00"> {$amount_total} C</td>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_transferfee} :</td>
<td align="right"><font color="#00FF00"> {$fee} C </td>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_plaffected} :</td>
<td align="right"><font color="#00FF00"> {$amount_count}</td>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_turncost} :</td>
<td align="right"><font color="#00FF00"> {$tcost}</td>
<tr valign="top">
<td><font color="#00FF00">{$l_igb_amounttransferred} :</td>
<td align="right"><font color="#00FF00"> {$transfer} C</td>
<tr valign="top"><td colspan="2" align="right">
<form action="igb_consolidate3.php" method="post" accept-charset="utf-8">
<input type="hidden" name="minimum" value="{$minimum}"><br>
<input type="hidden" name="maximum" value="{$maximum}"><br>
<input type="hidden" name="dplanet_id" value="{$dplanet_id}">
<input class="term" type="submit" value="{$l_igb_consolidate}"></td>
</form>
<tr valign="bottom">
<td><font color="#00FF00"><a href="igb_transfer.php">{$l_igb_back}</a></td><td align="right"><font color="#00FF00">&nbsp;<br><a href="main.php">{$l_igb_logout}</a></td>
</tr>

</table></td></tr></table>

