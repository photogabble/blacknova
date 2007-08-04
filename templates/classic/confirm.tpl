<script type="text/javascript" defer="defer" src="backends/javascript/sha256.js"></script>
<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>

<form method="post" action="confirm.php" accept-charset="utf-8">
{if $submit!="submit"}
<table>
  <tr>
    <td>Email address &nbsp;</td>
    <td><input type="text" name="email" value="{$email}"></td>
  </tr>
  <tr>
    <td>Password  &nbsp;</td>
    <td><input type="password" name="password" value=""></td>
  </tr>
  <tr>
    <td>Confirmation code      &nbsp;</td>
    <td><input type="text" name="code" value="{$c_code}"></td>
  </tr>
</table>
<p>
<input type="hidden" name="crypted_password" value="">
<input type="submit" value="submit" name="submit" onclick="crypted_password.value=hex_sha256(password.value)">
{/if}
<br><br>
