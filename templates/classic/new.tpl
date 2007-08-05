<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

{if ($account_creation_closed)}
{$l_new_closed_message}
{else}
<h1>{$title}</h1>

<form action="new3.php" method="post">
  <div style="text-align:center;">
  <table border="0" cellspacing="0" cellpadding="4">
    <tr>
      <td>{$l_login_email}</td>
      <td><input type="text" name="email" size="20" maxlength="40" value=""></td>
    </tr>
    <tr>
      <td>{$l_new_shipname}</td>
      <td><input type="text" name="shipname" size="20" maxlength="20" value=""></td>
    </tr>
    <tr>
      <td>{$l_new_pname}</td>
      <td><input type="text" name="character" size="20" maxlength="20" value=""></td>
    </tr>
    <tr>
      <td>{$l_login_pw}</td>
      <td><input type="password" name="password" size="20" maxlength="20" value=""></td>
    </tr>
    <tr>
      <td>{$l_login_pw2}</td>
      <td><input type="password" name="repassword" size="20" maxlength="20" value=""></td>
    </tr>
    <tr>
      <td>{$l_gamenum}</td>
      <td>
        <select name=gamenum id="Game" style="color:#ff3; font-weight:bold; background:black; border-style:none;">
        {$game_drop_down}
        </select>
      </td>
    </tr>
  </table>
  <br>
  <input type="submit" value="{$l_submit}">
  <input type="reset" value="{$l_reset}">
  <br><br>{$l_new_info}<br>
  </div>
</form>
{/if}
