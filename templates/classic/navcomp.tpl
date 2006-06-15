<h1>{$title}</h1>

{if !$allow_navcomp}
$l_nav_nocomp<br><br>
{else}
    {if $state == 0}
        <form action="navcomp.php" method=post>
        {$l_nav_query} <input name=destination>
        <input type=submit value={$l_submit}><br>
        <input name=state value=1 type=hidden>
        </form>
    {else}
        {if $found > 0}
            <h3>{$l_nav_pathfnd}</h3>
            {$start_sector} {$search_results_echo}<br>
            {$l_nav_answ1} {$search_depth} {$l_nav_answ2}<br><br>
        {else}
        {$l_nav_proper}<br><br>
        {/if}
    {/if}
{/if}

<a href="main.php">{$l_global_mmenu}</a>

