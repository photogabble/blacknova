<br>
{if $flag == 0}

    {$l_new_charis} {$character}<br><br>
    {if $display_password}
        {$l_new_pwis} {$password}<br><br>
    {/if}

    <br>
    <a href="confirm.php" class=nav>{$l_clickme}</a> {$l_new_login}
{/if}

{if $flag != 0}
    <a href="new.php">{$l_new_err}</a><br>
{/if}
