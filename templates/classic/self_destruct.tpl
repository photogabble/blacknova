<h1>{$title}</h1>

{if $sure == ''}
    <font color=red><strong>{$l_die_rusure}</strong></font><br><br>
    <a href="main.php">{$l_die_nonono}</a> {$l_die_what}<br><br>
    <a href="self_destruct.php?sure=1">{$l_yes}!</a> {$l_die_goodbye}<br><br>
{elseif $sure == 1}
    <font color="red"><strong>{$l_die_check}</strong></font><br><br>
    <a href="main.php">{$l_die_nonono}</a> {$l_die_what}<br><br>
    <a href="self_destruct.php?sure=2">{$l_yes}!</a> {$l_die_goodbye}<br><br>
{elseif $sure == 2}
    {$l_die_count}<br>
    {$l_die_vapor}<br><br>
    <a href="index.php">{$l_global_mlogin}</a>
{else}
    {$l_die_exploit}<br><br>
{/if}

{if $sure != 2}
    <a href="main.php">{$l_global_mmenu}</a>
{/if}
