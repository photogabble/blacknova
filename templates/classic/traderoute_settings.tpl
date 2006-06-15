<p><strong>{$l_tdr_globalset}</strong></p>
<p><strong>{$l_tdr_tdrsportsrc} :</strong></p>
<form action="traderoute.php?command=setsettings" method="post">
<table border="0"><tr>
<td> - {$l_tdr_colonists} :</td>
<td>

{if $playerinfo_trade_colonists == 'Y'}
<input type="checkbox" name="colonists" checked="checked">
{else}
<input type="checkbox" name="colonists">
{/if}

</tr><tr>
<td> - {$l_tdr_fighters} :</td>
<td>

{if $playerinfo_trade_fighters == 'Y'}
<input type="checkbox" name="fighters" checked="checked">
{else}
<input type="checkbox" name="fighters">
{/if}

</tr><tr>
<td> - {$l_tdr_torps} :</td>
<td>

{if $playerinfo_trade_torps == 'Y'}
<input type="checkbox" name="torps" checked="checked">
{else}
<input type="checkbox" name="torps">
{/if}

</tr>
</table>
<p>
<strong>{$l_tdr_tdrescooped} :</strong></p>
<table border="0"><tr>
<td>&nbsp;&nbsp;&nbsp;{$l_tdr_trade}</td>
<td>

{if $playerinfo_trade_energy == 'Y'}
<input type="radio" name="energy" value="Y" checked="checked">
{else}
<input type="radio" name="energy" value="Y">
{/if}

</td></tr><tr>
<td>&nbsp;&nbsp;&nbsp;{$l_tdr_keep}</td>
<td>

{if $playerinfo_trade_energy == 'N'}
<input type="radio" name="energy" value="N" checked="checked">
{else}
<input type="radio" name="energy" value="N">
{/if}

</td></tr><tr><td>&nbsp;</td></tr><tr><td>
<td><input type="submit" value="{$l_tdr_save}"></td>
</tr></table>
</form>

<a href="traderoute.php">{$l_tdr_returnmenu}</a>
