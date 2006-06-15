<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>

<h1>{$title}</h1>
<strong>{$l_config_scheduler}</strong>
<br><br>
{$l_updates_run}<br>
{$l_turns_run}{$l_turns_run_result}
{$l_sched_igb_run}{$l_sched_igb_result}
{$l_sched_planets_run}{$l_sched_planets_result}
{$l_sched_spies_run}{$l_sched_spies_result}
{$l_sched_ports_run}{$l_sched_ports_result}
{$l_sched_ranking_run}{$l_sched_ranking_result}
{$l_sched_degrade_run}{$l_sched_degrade_result}
{$l_sched_apoc_run}{$l_sched_apoc_result}
{$l_sched_prune_run}{$l_sched_prune_result}
<br>
<strong>{$l_config_shiptypes}</strong>
<br><br>
{section name=ships loop=$l_shiptype_array}
{$l_shiptype_array[ships]}{$shiptype_results_array[ships]}
{/section}
{$l_allow_newaccounts}{$l_allow_newaccounts_result}
{$l_allow_logins}{$l_allow_logins_result}
{$l_add_gamemaster}{$l_add_gamemaster_result}
{$l_add_gamemaster_invite}{$l_add_gamemaster_invite_result}
<br><br>
<div style="text-align:center;"><strong>{$l_admin_login}
<br><br>
{$l_email} {$admin_mail}
<br>
Password: {$adminpass}<br></strong></div>
<br><br>
<div style="text-align:center;"><strong>{$l_universe_success}<br>
Click <a href="index.php">here</a> to return to the login screen.</strong></div><br><br>
