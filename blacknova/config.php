<?
include("config_local.php");
include("$ADOdbpath" . "/adodb.inc.php");

/*********************************************
Main scheduler variables (game flow control)

All following vars are in minutes.
These are TRUE minutes, no matter to what interval
you're running the scheduler script! The scheduler
will auto-adjust, possibly running many of the same
events in a single call.
**********************************************
	0 = real cronjob,
	1 = player triggerd and JS-Cronjob,
	2 = JS-Cronjob only
*/
$sched_type		= 0;

// Set this to how often (in minutes) you are running the scheduler script.
$sched_ticks		= 6;
$sched_turns 		= 2;	//New turns rate (also includes towing, furangee)
$sched_ports 		= 2;	//How often port production occurs
$sched_planets		= 2;	//How often planet production occurs
$sched_IGB		= 2;	//How often IGB interests are added
$sched_ranking		= 30;	//How often rankings will be generated
$sched_news		= 15;	//How often news are generated
$sched_degrade 		= 6;	//How often sector fighters degrade when unsupported by a planet
$sched_apocalypse	= 15;

/**********************************************
 Game Settings 
/**********************************************/
// Change this to make it unique to you game of Blacknova
$gamename		= "Blacknova Traders";
// This is the name of the codebase that you are running, in most cases
// you dont need to change it.
$game_name		= "BlackNova Traders v0.42";

// Administrator's password and email:
// Be sure to change these. Don't leave them as is.
$adminpass		= "changemefirst";
$adminname		= "Administrator";
$admin_mail		= "admin@your.mail.server";
// If you want to hide your admin character from the rankings system,
// set this to 1, else set it to 0.  That way, you can edit your admin
// character to play with stuff and not skew your player's rankings.
$hide_admin_rank	= 1;

// Address the forum link, link's to:
$link_forums		= "http://forums.blacknova.net";

$server_closed		= false; //true = block logins but not new account creation
$account_creation_closed= false; //true = block new account creation

// Universe Settings
$sector_max		= 2000;
$universe_size		= 500;
$link_max		= 10;

// If true, will display password on signup screen.
$display_password 	= false; 
$maxlen_password	= 32;
$bnt_ls			= true;
// enter an authentification key here (always use the same key for the same server/game)
$bnt_ls_key		= "myKey";
$bnt_ls_url		= "http://www.rednova.de/";

$max_turns		= 2500;
$max_planets_sector	= 5;
$max_rank 		= 100;
$max_traderoutes_player	= 40;
$max_emerwarp		= 10;
$max_team_members	= 99;
// Max Members per Team - If max the invite link is disabled, set really high to disable
$fed_max_hull		= 8;
$mine_hullsize		= 8; //Minimum size hull has to be to hit mines
$ewd_maxhullsize	= 15; //Max hull size before EWD degrades

$fullscan_cost		= 1;
$scan_error_factor	= 20;

/* specify which special features are allowed */
$allow_fullscan 	= true;  // full long range scan
$allow_navcomp 		= true;  // navigation computer
$allow_genesis_destroy 	= true;  // Genesis torps can destroy planets
$allow_ibank 		= true;  // Intergalactic Bank (IGB)
$sofa_on		= false; // true = SOFA Attack enabled
$ksm_allowed		= true;  // true = known space map enabled
$enhanced_logging	= true;  // Set enhanced logging (ip and planet activity) on or off
$always_reincarnate 	= true;  // Will always let a player restart

/* Localization (regional) settings */
$local_number_dec_point	= ".";
$local_number_thousands_sep = ",";
$servertimezone		= "CET [GMT+1]";
$language		= "english";
$default_lang		= 'english';
$avail_lang[0][file] 	= 'english';
$avail_lang[0][name] 	= 'English';
$avail_lang[1][file] 	= 'german';
$avail_lang[1][name] 	= 'Deutsch';
$avail_lang[2][file] 	= 'french';
$avail_lang[2][name] 	= 'Français';
$avail_lang[3][file] 	= 'romanian';
$avail_lang[3][name] 	= 'Romanian';
$avail_lang[4][file] 	= 'czech';
$avail_lang[4][name] 	= 'Cesky';

/**********************************************
 Planet Config 
/**********************************************/
// Intrest rate on planet
$interest_rate 		= 1.0005;
// Allow transfer credits to/from corp planets. 1 = enables
$corp_planet_transfers	= 0;
// Percentage of colonists killed by space plague
$space_plague_kills	= 0.20; 
// number of colonists a planet needs before being affected by the apocalypse
$doomsday_value		= 90000000;

/**********************************************
 Base Config - All your base are belong to us.
/**********************************************/
// Requirements needed to build a base on a planet
$base_ore		= 10000;
$base_goods		= 10000;
$base_organics		= 10000;
$base_credits		= 10000000;
// Additional factor added to tech levels by having a base on your planet. 
$base_modifier		= 1;
$basedefense 		= 1; 
// Max amount of credits allowed on a planet without a base
$max_credits_without_base = $base_credits;
$min_bases_to_own	= 3; // This means how many u need to own a sector.

/**********************************************
 Ship Settings
/**********************************************/
$start_fighters 	= 10;
$start_armour 		= 10;
$start_credits		= 1000;
$start_energy 		= 100;
$start_turns 		= 1200;

$upgrade_cost 		= 1000;
$upgrade_factor 	= 2;
$level_factor 		= 1.5;

// Do players get an escape pod and/or fuel scoop at the start by default or not?
// Use Y or N to switch it (use uppper case!)
$start_pod 		= "N";
$start_scoop 		= "N";
// Do they get a pod and/or scoop if they get blown up, escape, and get a new ship?
$boom_pod 		= "N";
$boom_scoop 		= "N";

/**********************************************
 newbie niceness variables
/**********************************************/
$newbie_nice 		= "YES";
$newbie_extra_nice 	= "YES";
$newbie_hull 		= "8";
$newbie_engines 	= "8";
$newbie_power 		= "8";
$newbie_computer 	= "8";
$newbie_sensors 	= "8";
$newbie_armour 		= "8";
$newbie_shields 	= "8";
$newbie_beams 		= "8";
$newbie_torp_launchers 	= "8";
$newbie_cloak 		= "8";

/**********************************************
 iBank Config - Intergalactic Banking - All your bases really belong to us
/**********************************************/
// Interest rate for account funds NOTE: this is calculated every system update!
$ibank_interest		= 0.0003;
$ibank_paymentfee	= 0.05;		// Paymentfee
$ibank_loaninterest	= 0.0010; 	// Loan interest
$ibank_loanfactor	= 0.10; 	// One-time loan fee
$ibank_loanlimit	= 0.25; 	// Maximum loan allowed, percent of net worth

$IGB_allowloans		= true;
$IGB_allowtransfer	= true;

//Turns a player has to play before ship transfers are allowed 0=disable
$IGB_min_turns 		= $start_turns;
//Max amount of sender's value allowed for ship transfers 0=disable
$IGB_svalue 		= 0.15;
//Time (in minutes) before two similar transfers are allowed for ship transfers.0=disable
$IGB_trate 		= 1440;
//Time (in minutes) players have to repay a loan
$IGB_lrate 		= 1440;
//Cost in turns for consolidate : 1/$IGB_consolidate
$IGB_tconsolidate 	= 10;

// Information displayed on the 'Manage Own Account' section
$ibank_ownaccount_info = "Interest rate is " . $ibank_interest * 100 . "%<BR>Loan rate is " .
$ibank_loaninterest * 100 . "%<P>If you have loans Make sure you have enough credits deposited each turn " .
  "to pay the interest and mortage, otherwise it will be deducted from your ships acccount at <FONT COLOR=RED>" .
  "twice the current Loan rate (" . $ibank_loaninterest * 100 * 2 .")%</FONT>.";

/**********************************************
 default planet production percentages
/**********************************************/
$start_fighters 	= 10;
$start_armour 		= 10;
$start_credits		= 1000;
$start_energy 		= 100;
$start_turns		= 1200;

$default_prod_ore	= 20.0;
$default_prod_organics	= 20.0;
$default_prod_goods	= 20.0;
$default_prod_energy	= 20.0;
$default_prod_fighters	= 10.0;
$default_prod_torp	= 10.0;

/**********************************************
 port pricing variables
/**********************************************/
$ore_price		= 11;
$ore_delta		= 5;
$ore_rate		= 75000;
$ore_prate		= 0.25;
$ore_limit		= 100000000;

$organics_price		= 5;
$organics_delta		= 2;
$organics_rate		= 5000;
$organics_prate		= 0.5;
$organics_limit		= 100000000;

$goods_price		= 15;
$goods_delta		= 7;
$goods_rate		= 75000;
$goods_prate		= 0.25;
$goods_limit		= 100000000;

$energy_price		= 3;
$energy_delta		= 1;
$energy_rate		= 75000;
$energy_prate		= 0.5;
$energy_limit		= 1000000000;

$dev_beacon_price	= 100;
$dev_emerwarp_price	= 1000000;
$dev_escapepod_price 	= 100000;
$dev_fuelscoop_price 	= 100000;
$dev_genesis_price	= 1000000;
$dev_lssd_price		= 10000000;
$dev_minedeflector_price= 10;
$dev_warpedit_price	= 100000;

$fighter_price 		= 50;
$fighter_prate 		= .01;
$torpedo_price 		= 25;
$torpedo_prate 		= .025;
$torp_dmg_rate 		= 10;
$armour_price		= 5;
$credits_prate 		= 3.0;

$colonist_price 	= 5;
$colonist_production_rate = .005;
$colonist_reproduction_rate = 0.0005;
$colonist_limit		= 100000000;
$organics_consumption	= 0.05;
$starvation_death_rate	= 0.01;

$inventory_factor	= 1;

$rating_combat_factor	= .8;    //ammount of rating gained from combat
$min_value_capture 	= 0; //Percantage of planet's value a ship must be worth to be able to capture it. 0=disable
$defence_degrade_rate	= 0.05;
$energy_per_fighter	= 0.10;

$bounty_maxvalue	= 0.15; //Max amount a player can place as bounty - good idea to make it the same as $IGB_svalue. 0=disable
$bounty_ratio		= 0.75; // ratio of players networth before attacking results in a bounty. 0=disable
$bounty_minturns	= 500; // Minimum number of turns a target must have had before attacking them may not get you a bounty. 0=disable

/* GUI colors (temporary until we have something nicer) */
$color_header 		= "#500050";
$color_line1		= "#300030";
$color_line2		= "#400040";

/*
You add all your module switches here

Example:
$Enable_PlanetWatcher = false;  //Which Disables the Planet Watcher Module.
$Enable_PlanetWatcher = true;   //Which Enables the Planet Watcher Module.
*/

$Enable_EmailLoggerModule = false;
$Enable_GlobalMailerModule = false;

$ip = getenv("REMOTE_ADDR");
include("global_funcs.php");
/*
This includes the module main file.
And this include must stay at the end.
Well after global_funcs.php include anyway :)
*/
include("modules.php");
?>