<h1>{$title}</h1>

<strong><a href="standard_report.php">{$l_pr_planetstatus}</a></strong><br>{$l_pr_comm_disp}<br><br>

{if !$ship_based_combat}
<strong><a href="planet_defense_report.php">{$l_pr_pdefenses}</a></strong><br>{$l_pr_displaydef}<br><br>
{/if}

<strong><a href="planet_production_change.php">{$l_pr_changeprods}</a></strong> &nbsp;&nbsp; {$l_pr_baserequired}<br>{$l_pr_prod_disp1}
<br>{$l_pr_prod_disp2}<br>

{if $playerinfo_team>0}
<br><strong><a href="team_planets.php">{$l_pr_teamlink}</a></strong><br>{$l_pr_team_disp}<br>
<br><strong><a href="team_defenses.php">{$l_pr_showtmdef}</a></strong><br>{$l_pr_showtmlvls}<br>
{/if}

<br><br><a href="main.php">{$l_global_mmenu}</a>
