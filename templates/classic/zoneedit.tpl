<h1>{$title}</h1>

<form name="bntform" action="zoneedit.php?command=change" method="post" onsubmit="document.bntform.submit_button.disabled=true;">
     <table border="0"><tr>
     <td align="right"><font style="font-size: 0.8em;"><strong>
{$l_ze_name} : &nbsp;</strong></font></td>
     <td><input type="text" name="name" size="30" maxlength="30" value="{$zoneinfo_zone_name}"></td>
</tr><tr>

<td align="right"><font style="font-size: 0.8em;"><strong>{$l_ze_attacks} :
&nbsp;</strong></font></td>
     <td><input type="radio" name="attacks" value="Y" {$yattack}>&nbsp;{$l_yes}&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="attacks" value="N" {$nattack}>&nbsp;
{$l_no}</td>
     </tr><tr>
     <td align="right"><font style="font-size: 0.8em;"><strong>{$l_ze_allow}
{$l_warpedit} : &nbsp;</strong></font></td>
     <td><input type="radio" name="warpedits" value="Y" {$ywarpedit}>&nbsp;{$l_yes}
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="warpedits" value="N"
{$nwarpedit}>&nbsp;{$l_no}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="warpedits"
value="L" {$lwarpedit}>&nbsp;{$l_zi_limit}</td>
     </tr><tr>
     <td align="right"><font style="font-size: 0.8em;"><strong>{$l_ze_allow}
{$l_sector_def} : &nbsp;</strong></font></td>
     <td><input type="radio" name="defenses" value="Y" {$ydefense}>&nbsp;{$l_yes}&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="defenses" value="N" {$ndefense}>&nbsp;
{$l_no}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="defenses" value="L"
{$ldefense}>&nbsp;{$l_zi_limit}</td>
     </tr><tr>
     <td align="right"><font style="font-size: 0.8em;"><strong>{$l_ze_genesis} :
&nbsp;</strong></font></td>
     <td><input type="radio" name="planets" value="Y" {$yplanet}>&nbsp;{$l_yes}&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;<input type="radio" name="planets" value="N" {$nplanet}>&nbsp;{$l_no}&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="planets" value="L" {$lplanet}>&nbsp;
{$l_zi_limit}</td>

</tr><tr>
<td align="right"><font style="font-size: 0.8em;"><strong>{$l_ze_allow}
{$l_title_port} : &nbsp;</strong></font></td>
<td><input type="radio" name="trades" value="Y" {$ytrade}>&nbsp;{$l_yes}&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="trades" value="N" {$ntrade}>&nbsp;
{$l_no}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="trades" value="L" {$ltrade}>
&nbsp;{$l_zi_limit}</td>
</tr><tr>
<td colspan="2" align="center"><br><input name="submit_button" type="submit" value="{$l_submit}"></td></tr>
</table>
</form>

<a href="zoneinfo.php">{$l_clickme}</a> {$l_ze_return}.<p>
<a href="main.php">{$l_global_mmenu}</a>

