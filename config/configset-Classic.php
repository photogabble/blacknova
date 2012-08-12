;<?php echo "You can not access this file directly!"; die();?>

[MAIN]
game_name = "Blacknova Traders";
server_closed = true;
account_creation_closed = true;
invitation_only = 0;
release_version = "0.64";
always_reincarnate = 0;
silent = 1;

[MAIL SYSTEM SETTINGS]
admin_mail_name = "BNT Admin";
mailer_type = "smtp";
mail_host = "localhost";

[SCHEDULER_VARS]
; All following vars below sched_ticks are in minutes.
; These are TRUE minutes, no matter to what interval
; you're running the scheduler script! The scheduler
; will auto-adjust, possibly running many of the same
; events in a single call.

sched_type = 0;
sched_ticks = 6;
sched_turns = 2;
sched_ports = 2;
sched_planets = 2;
sched_igb = 2;
sched_ranking = 15;
sched_news = 15;
sched_degrade = 6;
sched_apocalypse = 15;
sched_spies = 2;
sched_prune = 1440;
sched_serverlist = 60;
doomsday_value = 90000000;
max_turns = 3000;

[STARS]
max_star_size = 5;
no_stars = true;

[OPTIONAL_FEATURES]
allow_fullscan = true;
allow_navcomp = true;
allow_genesis_destroy = false;
allow_ibank = true;
sofa_on = false;
ksm_allowed = true;
hide_admin_rank = 0;
team_planet_transfers = 0;
display_password = false;
allow_shoutbox = false;

[STARTING VALUES]
start_fighters = 10;
start_armor = 10;
start_credits = 10000;
start_energy = 100;
start_turns = 2500;
start_pod = "N";
start_scoop = "N";

[BLOWN UP WITH POD VALUES]
boom_fighters = 10;
boom_armor = 10;
boom_energy = 100;
boom_pod = "N";
boom_scoop = "N";

[IBANK]
ibank_interest = 0.0003;
ibank_paymentfee = 0.05;
ibank_loaninterest = 0.0010;
ibank_loanfactor = 0.10;
ibank_loanlimit = 0.25;

[PLANET_PRODUCTION]
default_prod_ore      = 20;
default_prod_organics = 20;
default_prod_goods    = 20;
default_prod_energy   = 20;
default_prod_fighters = 10;
default_prod_torp     = 10;

[PORT_PRICES]
ore_price = 11;
ore_delta = 5;
ore_rate = 75000;
ore_prate = 0.25;
ore_limit = 10000000000;

[ORGANICS_PRICES]
organics_price = 5;
organics_delta = 2;
organics_rate = 5000;
organics_prate = 0.5;
organics_limit = 10000000000;

[GOODS_PRICES]
goods_price = 15;
goods_delta = 7;
goods_rate = 75000;
goods_prate = 0.25;
goods_limit = 10000000000;

[ENERGY_PRICES]
energy_price = 3;
energy_delta = 1;
energy_rate = 75000;
energy_prate = 0.5;
energy_limit = 100000000000;

[DEVICE SETTINGS]
dev_genesis_price = 1000000;
dev_emerwarp_price = 1000000;
dev_warpedit_price = 100000;
dev_minedeflector_price = 10;
dev_escapepod_price = 100000;
dev_fuelscoop_price = 100000;
armor_price = 5;
fighter_price = 50;
torpedo_price = 25;
colonist_price = 5;
torp_dmg_rate = 10;
max_emerwarp = 10;

[PRODUCTION RATES]
fighter_prate = .01;
torpedo_prate = .025;
credits_prate = 1.0;
colonist_production_rate = .005;
colonist_reproduction_rate = 0.0005;
interest_rate = 1.0005;

[BASE BUILD COSTS]
base_ore = 10000;
base_goods = 10000;
base_organics = 10000;
base_credits = 10000000;

[SPIES]
spy_success_factor = 0;
spy_kill_factor = 1.0;
allow_spy_capture_planets = true;
max_spies_per_planet = 10;
spy_price = 400000;
sneak_toplanet_success  = 5;
sneak_toship_success    = 7;
planet_detect_success1  = 70;
planet_detect_success2  = 10;
spy_cleanup_ship_turns1 = 2;
spy_cleanup_ship_turns2 = 4;
spy_cleanup_ship_turns3 = 6;
spy_cleanup_planet_credits1 = 2000000;
spy_cleanup_planet_credits2 = 4000000;
spy_cleanup_planet_credits3 = 8000000;

[SERVER_LIST]
server_list_key = "TYPE_YOUR_LIST_KEY_HERE";
server_list_url = "http://www.blacknovatraders.com/server_list/";

[LSSD]
lssd_level_two = 30;
lssd_level_three = 60;

[COLOR SETTINGS]
color_header = "#500050";
color_line1 = "#300030";
color_line2 = "#400040";
general_text_color = "silver";
general_highlight_color = "white";
main_table_heading = "#fff";

[NEWBIE_NICE]
newbie_nice = 0;
newbie_hull = 8;
newbie_engines = 8;
newbie_power = 8;
newbie_computer = 8;
newbie_sensors = 8;
newbie_armor = 8;
newbie_shields = 8;
newbie_beams = 8;
newbie_torp_launchers = 8;
newbie_cloak = 8;

[SHIP SETTINGS]
upgrade_cost = 1000;
upgrade_factor = 2;
level_factor = 1.5;
inventory_factor = 1;
trade_in_value = "1.00";
rs_difficulty = "500";

[IBANK SETTINGS]
igb_min_turns = 2500;
igb_svalue = 0.15;
igb_trate = 1440;
igb_lrate = 1440;
igb_tconsolidate = 10;
igb_consolidate_allowed = 0;

[BOUNTY SETTINGS]
bounty_maxvalue = 0.15;
bounty_ratio = 0.75;
bounty_minturns = 500;
enable_big_bounty = false;

[SCAN SETTINGS]
fullscan_cost = 3;
lrscan_cost = 1;
scan_error_factor = 10;

[MISCELLANEOUS]
basedefense = 1;
colonist_limit = 100000000;
organics_consumption = 0.05;
starvation_death_rate = 0.01;
max_traderoutes_player = 40;
min_value_capture = 0.20;
defense_degrade_rate = 0.05;
energy_per_fighter = 0.10;
space_plague_kills = 0.20;
default_lang = "english";
default_template = "classic";
ewd_maxavgtechlevel = 10;
mine_hullsize = 20;
max_avg_combat_tech = 10;
sector_max = 5000;
link_max = 10;
galaxy_size = 500;
max_rank = 40;
rating_combat_factor = .8;
session_time_out = 300;
max_credits_without_base = 10000000;
level_magnitude = 100;
max_team_members = 5;
attack_repeats = 10;
preset_limit = 5;
score_link = 1;
plasma_engines = 0;
perf_logging = 1;
picsperrow = 7;
open_time = "2010-01-01 01:01:01";
end_time = "2010-01-01 01:01:01";
sneak_by_beams = 0;
ship_classes = 0;
ship_based_combat = 1;
planettypes0 = "tinyplanet";
planettypes1 = "smallplanet";
planettypes2 = "mediumplanet";
planettypes3 = "largeplanet";
planettypes4 = "hugeplanet";
startypes0 = "";
startypes1 = "redstar";
startypes2 = "orangestar";
startypes3 = "yellowstar";
startypes4 = "greenstar";
startypes5 = "bluestar";

[AI_CODE]
ai_max = 10;
ai_start_credits = 50000000;
ai_unemployment = 25000;
ai_aggression = 40;
ai_planets = 5;

[NAME_SETTINGS]
ai_name = "Xenobe";
goodguys_name = "Federation";

[VERY_NEW]
ibank_allowloans = true;
ibank_allowtransfer = true;
max_team_members = 5;
max_team_changes = 3;
