    <form action="igb_transfer3.php" method="post">
    <table width="600" height="350" border="0">
      <tr>
        <td align="center" background="templates/{$templateset}/images/igbscreen.png">
          <table width="520" height="300" border="0">

    <tr><td colspan="2" align="center" valign="top"><font color="#00FF00">
$l_igb_transfersuccessful<br>---------------------------------</font></td></tr>
         <tr valign="top"><td colspan="2" align="center"><font color="#00FF00">
{$formatted_trans_to} {$l_igb_ctransferredfrom} {$source_name} {$l_igb_to} {$dest_name}.</tr>
         <tr valign="top">
         <td><font color="#00FF00">{$l_igb_transferamount} :</td><td align="right">
<font color="#00FF00">{$formatted_trans_amt} C<br>
         <tr valign="top">
         <td><font color="#00FF00">{$l_igb_transferfee} :</td><td align="right">
<font color="#00FF00">{$formatted_trans_fee}C<br>
         <tr valign="top">
         <td><font color="#00FF00">{$l_igb_amounttransferred} :</td><td align="right">
<font color="#00FF00">{$formatted_amt_transd} C<br>
         <tr valign="top">
         <td><font color="#00FF00">{$l_igb_srcplanet} {$source_name} {$l_igb_in}
{$source_sector_id} :</td><td align="right"><font color="#00FF00">{$formatted_src_creds} C<br>
         <tr valign="top">
         <td><font color="#00FF00">{$l_igb_destplanet} {$dest_name} {$l_igb_in}
{$dest_sector_id} :</td><td align="right"><font color="#00FF00"> {$formatted_dest_creds} C<br>
         <tr valign="bottom">
         <td><font color="#00FF00"><a href="igb_login.php">{$l_igb_back}</a>
</td><td align="right"><font color="#00FF00">&nbsp;<br><a href="main.php">
{$l_igb_logout}</a></td>
         </tr>
    </table></td></tr></table></form>

