<script type="text/javascript" defer="defer" src="backends/javascript/sha256.js"></script>

<h1>{$title}</h1>

<form action="option2.php" method="post">
  <table border="0" cellspacing="0" cellpadding="2">
    <tr bgcolor="{$color_header}">
      <td colspan="2"><strong>{$l_opt_chpass}</strong></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>{$l_opt_curpass}</td>
      <td><input type="password" name="oldpass" size="32" value=""></td>
    </tr>
    <tr bgcolor="{$color_line2}">
      <td>{$l_opt_newpass}</td>
      <td><input type="password" name="newpass1" size="32" value=""></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>{$l_opt_newpagain}</td>
      <td><input type="password" name="newpass2" size="32" value=""></td>
    </tr>
    <tr bgcolor="{$color_header}">
      <td colspan="2"><strong>{$l_opt_userint}</strong></td>
    </tr>
    <tr bgcolor="{$color_header}">
      <td colspan="2"><strong>{$l_opt_lang}</strong></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>{$l_opt_select}</td>
      <td><select name="newlang">{$lang_drop_down}</select></td>
    </tr>
    <tr bgcolor="{$color_header}">
      <td colspan="2"><strong>Ship Name</strong></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>Change ship name:</td>
      <td><input type="text" name="ship_name" size="32" value="{$ship_name}"></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>Use Gravatar?</td>
      <td><input type="checkbox" name="use_gravatar" value="Y"{if $use_gravatar eq "Y"} checked{/if}></td>
    </tr>
{if $allow_shoutbox}
    <tr bgcolor="{$color_header}">
      <td colspan="2"><strong>Shoutbox</strong></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>Shoutbox in footer?</td>
      <td><input type="checkbox" name="sb_footer" value="Y"{if $sb_footer eq "Y"} checked{/if}></td>
    </tr>
    <tr bgcolor="{$color_line2}">
      <td>Shoutbox lines</td>
      <td><input type="text" name="sb_lines" size="5" value="{$sb_lines}"></td>
    </tr>
    <tr bgcolor="{$color_line1}">
      <td>Shoutbox lines backwards?</td>
      <td><input type="checkbox" name="sb_backwards" value="Y"{if $sb_backwards eq "Y"} checked{/if}></td>
    </tr>
{/if}
  </table>
<br>
<input type="hidden" name="crypted_password" value="">
<input type="submit" value="{$l_opt_save}" onclick="crypted_password.value=sha256_once(newpass2.value)">
</form>

<a href="main.php">{$l_global_mmenu}</a>
