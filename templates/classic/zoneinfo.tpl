
<h1>{$title}</h1>

{if $editable}
<div style="text-align:center;">
  {$l_zi_control}<br>
  <a href="zoneedit.php">{$l_clickme}</a> {$l_zi_tochange}
</div>
<br>
{/if}

<table border="1" cellspacing="1" cellpadding="0" width="75%" align="center">
  <tr bgcolor="{$color_line2}">
    <td align="center" colspan="2">
      <strong><font color="white">{$zoneinfo_zonename}</font></strong>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <table border="0" cellspacing="0" cellpadding="2" width="100%" align="center">
        <tr bgcolor="{$color_line1}">
          <td width="50%">
            <font color="white">&nbsp;{$l_zi_owner}</font>
          </td>
          <td width="50%"><font color="white">{$ownername}&nbsp;</font>
          </td>
        </tr>
        <tr bgcolor="{$color_line2}">
          <td>
            <font color="white">&nbsp;{$l_att_att}</font>
          </td>
          <td>
            <font color="white">{$attack}&nbsp;</font>
          </td>
        </tr>
        <tr bgcolor="{$color_line1}">
          <td>
            <font color="white">&nbsp;{$l_md_title}</font>
          </td>
          <td>
            <font color="white">{$defense}&nbsp;</font>
          </td>
        </tr>
        <tr bgcolor="{$color_line2}">
          <td>
            <font color="white">&nbsp;{$l_warpedit}</font>
          </td>
          <td>
            <font color="white">{$warpedit}&nbsp;</font>
          </td>
        </tr>
        <tr bgcolor="{$color_line1}">
          <td>
            <font color="white">&nbsp;{$l_planets}</font>
          </td>
          <td>
            <font color="white">{$planet}&nbsp;</font>
          </td>
        </tr>
        <tr bgcolor="{$color_line2}">
          <td>
            <font color="white">&nbsp;{$l_title_port}</font>
          </td>
          <td>
            <font color="white">{$trade}&nbsp;</font>
          </td>
        </tr>
        <tr bgcolor="{$color_line1}">
          <td>
            <font color="white">&nbsp;{$l_zi_maxhull}</font>
          </td>
          <td>
            <font color="white">{$maxlevel}&nbsp;</font>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<br><br>

<a href="main.php">{$l_global_mmenu}</a>
