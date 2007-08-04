<div style="text-align:center;">
<h1>{$title}</h1>
<form action="install.php" method="post" accept-charset="utf-8">
<br><br>

<table cellpadding="4">
  <tr>
    <td align="right">{$l_login_email}</td>
    <td align="left"><input type="text" name="email" size="20" maxlength="40" value="{$username}"></td>
  </tr>
  <tr>
    <td align="right">{$l_login_pw}</td>
    <td align="left"><input type="password" name="pass" size="20" maxlength="20" value="{$password}"></td>
  </tr>
  <tr>
    <td colspan=2><div style="text-align:center;">{$l_login_forgot_pw}</div></td>
  </tr>
</table>

{literal}
<script type="text/javascript" defer="defer">
// <!--
var swidth = 0;
if(self.screen)
{
  swidth = screen.width;
  document.write("<input type=\"hidden\" name=\"res\" value=\"" + swidth + "\"></input>");
}
if(swidth != 640 && swidth != 800 && swidth != 1024)
{
  document.write("<table><tr><td colspan=2>");
  document.write("{/literal}{$l_login_chooseres}{literal}");
  document.write("<br><div style="text-align:center;"><input type=\"radio\" name=\"res\" value=\"640\">640x480</input>");
  document.write("<input type=\"radio\" name=\"res\" checked value=\"800\">800x600</input>");
  document.write("<input type=\"radio\" name=\"res\" value=\"1024\">1024x768</input></div>");
  document.write("</td></tr></table>");
}
// -->
</script>
{/literal}

<br>
<input type="submit" value="{$l_login_title}">
<br><br>
{$l_login_newp}
<br><br>
{$l_login_prbs} <a href="mailto:{$admin_mail}">{$l_login_emailus}</a>
<br>
</form>

<a href="{$link_forums}" target="_blank">{$l_forums}</a> - <a href="ranking.php">{$l_rankings}</a> - <a href="settings.php">{$l_login_settings}</a>
</div>
