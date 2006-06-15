<h1>{$title}</h1>

{if $newpass1 == "" && $newpass2 == ""}
{$l_opt2_passunchanged}<br><br>
{elseif ($password != $oldpass) || ($oldpass != $accountinfo_password)}
{$l_opt2_srcpassfalse}<br><br>
{elseif $newpass1 != $newpass2}
{$l_opt2_newpassnomatch}<br><br>
{elseif ($oldpass == $accountinfo_password) && $debug_query}
{$l_opt2_passchanged}<br><br>
{else}
{$l_opt2_passchangeerr}<br><br>
{/if}

{if $l_opt2_chlang != ''}
{$l_opt2_chlang}<br><br>
{/if}

<a href="main.php">{$l_global_mmenu}</a>
