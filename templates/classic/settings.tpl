<h2>{$title}</h2>
<div align="center">

{if $dbprefix != $raw_prefix}

<table border=0 cellspacing=0 cellpadding=2>
  <tbody>
{section name=index start=0 loop=$gamestatus}
    <tr bgcolor="{$color_line1}">
      <td width="450">
        <font color="#FFFFFF">{$gamestatus[index].item}</font>
      </td>
      <td align="right" width="200">
        <font color="#00FF00">{$gamestatus[index].value}</font>
      </td>
    </tr>
{/section}
  </tbody>
</table>

<h2>{$l_s_gameoptions}</h2>
<table border=0 cellspacing=0 cellpadding=2>
  <tbody>
{section name=index start=0 loop=$gameoptions}
    <tr bgcolor="{$color_line1}">
      <td width="450">
        <font color="#FFFFFF">{$gameoptions[index].item}</font>
      </td>
      <td align="right" width="200">
        <font color="#00FF00">{$gameoptions[index].value}</font>
      </td>
    </tr>
{/section}
  </tbody>
</table>

<h2>{$l_s_gamesettings}</h2>
<table border=0 cellspacing=0 cellpadding=2>
  <tbody>
{section name=index start=0 loop=$gamesettings}
    <tr bgcolor="{$color_line1}">
      <td width="450">
        <font color="#FFFFFF">{$gamesettings[index].item}</font>
      </td>
      <td align="right" width="200">
        <font color="#00FF00">{$gamesettings[index].value}</font>
      </td>
    </tr>
{/section}
  </tbody>
</table>

<h2>{$l_s_gameschedsettings}</h2>
<table border=0 cellspacing=0 cellpadding=2>
  <tbody>
{section name=index start=0 loop=$schedsettings}
    <tr bgcolor="{$color_line1}">
      <td width="450">
        <font color="#FFFFFF">{$schedsettings[index].item}</font>
      </td>
      <td align="right" width="200">
        <font color="#00FF00">{$schedsettings[index].value}</font>
      </td>
    </tr>
{/section}
  </tbody>
</table>

{if $spy_success_factor > 0}
<h2>{$l_s_spysettings}</h2>
<table border=0 cellspacing=0 cellpadding=2>
  <tbody>
{section name=index start=0 loop=$spysettings}
    <tr bgcolor="{$color_line1}">
      <td width="450">
        <font color="#FFFFFF">{$spysettings[index].item}</font>
      </td>
      <td align="right" width="200">
        <font color="#00FF00">{$spysettings[index].value}</font>
      </td>
    </tr>
{/section}
  </tbody>
</table>
{/if}

{if $allow_ibank}
<h2>{$l_s_ibanksettings}</h2>
<table border=0 cellspacing=0 cellpadding=2>
  <tbody>
{section name=index start=0 loop=$ibanksettings}
    <tr bgcolor="{$color_line1}">
      <td width="450">
        <font color="#FFFFFF">{$ibanksettings[index].item}</font>
      </td>
      <td align="right" width="200">
        <font color="#00FF00">{$ibanksettings[index].value}</font>
      </td>
    </tr>
{/section}
  </tbody>
</table>
{/if}

</div>
<br><br>

{if ($session_email)}
<a href="index.php">{$l_global_mlogin}</a>
{else}
<a href="main.php">{$l_global_mmenu}</a>
{/if}

{else}
<form action="settings.php" method="post" accept-charset="utf-8">
<select name=gamenum>
{html_options options=$game_instances}
</select>
<input type="submit" value="{$l_submit}">
</form>
<br><br>
{/if}
</div>
