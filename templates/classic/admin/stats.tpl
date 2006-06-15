<table border="0" cellpadding="1" cellspacing="0">
{section name=count start=0 loop=$browsers}
<tr>
<td>
{if $browsers[count].name == "question"}
<img src="templates/{$templateset}/images/browsers/browser_{$browsers[count].name}.png" height="14" width="14" alt="Unknown">
{else}
<img src="templates/{$templateset}/images/browsers/browser_{$browsers[count].name}.png" height="14" width="14" alt="{$browsers[count].name}">
{/if}
</td>
<td>
{if $browsers[count].name == "question"}
Unknown&nbsp;
{else}
{$browsers[count].name}&nbsp;
{/if}
</td>
<td>
<div align="right">
{$browsers[count].count}&nbsp;
</div>
</td>
<td>
<div align="right">
{$browsers[count].percent}%
</div>
</td>
</tr>
{/section}
</table>
<br><br>
