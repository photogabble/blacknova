<h2>{$ai_name}Instructions</h2>
<p>&nbsp;&nbsp;&nbsp; Welcome to the {$ai_name} Control module.  This is the module that will control the {$ai_name} players in the game.
It is very simple right now, but will be expanded in future versions. 
The ultimate goal of the {$ai_name} players is to create some interactivity for those games without a large user base. 
I need not say that the {$ai_name} will also make good cannon fodder for those games with a large user base. 

<h3>{$ai_name} Creation</h3>
<p>&nbsp;&nbsp;&nbsp; In order to create a {$ai_name} you must choose the <strong>"Create A {$ai_name} Character"</strong> option from the menu. 
This will bring up the {$ai_name} character creation screen.  There are only a few fields for you to edit. 
However, with these fields you will determine not only how your {$ai_name} will be created, but how he will act in the game. 
We will now go over these fields and what they will do. 

<p>&nbsp;&nbsp;&nbsp; When creating a new {$ai_name} character the <strong>{$ai_name} Name</strong> and the <strong>Shipname</strong> are automatically generated. 
You can change these default values by editing these fields before submitting the character for creation. 
Take care not to duplicate a current player or ship name, for that will result in creation failure. 
<br>&nbsp;&nbsp;&nbsp; The starting <strong>Sector</strong> number will also be randomly generated. 
You can change this to any sector.  However, you should take care to use a valid sector number. Otherwise the creation will fail.
<br>&nbsp;&nbsp;&nbsp; The <strong>Level</strong> field will default to '3'.  This field refers to the starting tech level of all ship stats. 
So a default {$ai_name} will have it's Hull, Beams, Power, Engine, etc... all set to 3 unless this value is changed. 
All appropriate ship stores will be set to the maximum allowed by the given tech level. 
So, starting levels of energy, fighters, armor, torps, etc... are all affected by this setting. 
<br>&nbsp;&nbsp;&nbsp; The <strong>Active</strong> checkbox will default to checked. 
This box refers to if the {$ai_name} system will see this {$ai_name} and execute it's orders. 
If this box is not checked then the {$ai_name} system will ignore this record and the next two fields are ignored. 
<br>&nbsp;&nbsp;&nbsp; The <strong>Orders</strong> selection box will default to 'SENTINEL'. 
There are three other options available: ROAM, ROAM AND TRADE, and ROAM AND HUNT. 
These Orders and what they mean will be detailed below. 
<br>&nbsp;&nbsp;&nbsp; The <strong>Aggression</strong> selection box will default to 'PEACEFul'. 
There are two other options available: ATTACK SOMETIMES, and ATTACK ALWAYS. 
These Aggression settings and what they mean will be detailed below. 
<br>&nbsp;&nbsp;&nbsp; Pressing the <strong>Create</strong> button will create the {$ai_name} and return to the creation screen to create another. 

<h3>{$ai_name} Orders</h3>
<p> Here are the {$ai_name} Order options and what the {$ai_name} system will do for each: 
<ul>SENTINEL<br> 
This {$ai_name} will stay in place.  His only interactions will be with those who are in his sector at the time he takes his turn. 
The aggression level will determine what those player interactions are.</ul> 
<ul>ROAM<br> 
This {$ai_name} will warp from sector to sector looking for players to interact with. 
The aggression level will determine what those player interactions are.</ul> 
<ul>ROAM AND TRADE<br> 
This {$ai_name} will warp from sector to sector looking for players to interact with and ports to trade with. 
The {$ai_name} will trade at a port if possible before looking for player interactions. 
The aggression level will determine what those player interactions are.</ul> 
<ul>ROAM AND HUNT<br> 
This {$ai_name} has a taste for blood and likes the sport of a good hunt. 
Ocassionally (around 1/4th the time) this {$ai_name} has the urge to go hunting.  He will randomly choose one of the top ten players to hunt. 
If that player is in a sector that allows attack, then the {$ai_name} warps there and attacks. 
When he is not out hunting this {$ai_name} acts just like one with ROAM orders.</ul>  

<h3>{$ai_name} Aggression</h3>
<p> Here are the {$ai_name} Aggression levels and what the {$ai_name} system will do for each: 
<ul>PEACEFul<br> 
This {$ai_name} will not attack players.  He will continue to roam or trade as ordered but will not launch any attacks. 
If this {$ai_name} is a hunter then he will still attack players on the hunt but never otherwise.</ul> 
<ul>ATTACK SOMETIMES<br> 
This {$ai_name} will compare it's current number of fighters to a players fighters before deciding to attack. 
If the {$ai_name}'s fighters are greater then the player's, then the {$ai_name} will attack the player.</ul> 
<ul>ATTACK ALWAYS<br> 
This {$ai_name} is just mean.  He will attack anyone he comes across regardless of the odds.</ul> 
