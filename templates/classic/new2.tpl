{if ($account_creation_closed)}
{$l_new_closed_message}
{else}
<h1>{$title}</h1>

<form action="new3.php" method="post" accept-charset="utf-8">
  <div style="text-align:center;">
      <input type="hidden" name="email" size="20" maxlength="40" value="{$email}">
      <input type="hidden" name="shipname" size="20" maxlength="20" value="{$shipname}">
      <input type="hidden" name="character" size="20" maxlength="20" value="{$character}">
      <input type="hidden" name="password" size="20" maxlength="20" value="{$password}">
  {$l_tos}
  <br><br>
  {$additional_rules}
  <input type="submit" value="{$l_agree}">
  <input type="reset" value="{$l_reset}">
  </div>
</form>
{/if}

<a href="main.php">{$l_global_mmenu}</a>

