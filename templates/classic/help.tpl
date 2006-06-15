<h1>{$title}</h1>

Greetings and welcome to {$l_project_name}!
<br><br>
This is a game of inter-galactic exploration. Players explore the universe, trade for commodities and 
increase their wealth and power. Battles can be fought over space sectors and planets.
<br><br>
<a href=#mainmenu>Main Menu commands</a><br>
<a href=#techlevels>Tech levels</a><br>
<a href=#devices>Devices</a><br>
<a href=#zones>Zones</a><br>
<a name=mainmenu></a><H2>Main Menu commands:</H2>
<strong>Ship report:</strong><br>
Display a detailed report on your ship's systems, cargo and weaponry. You can display this report by 
clicking on your ship's name at the top of the main page. This page will also allow you to deploy 
fighters and mines. 
<br><br>
<strong>Warp links:</strong><br>
Move from one sector to another through warp links, by clicking on the sector numbers. If a sector
has a [U] symbol next to it, you have not explored that sector yet.
<br><br>
<strong>Long-range scan:</strong><br>
Scan a neighboring sector with your long range scanners without actually moving there. This will also
cause your map (if your server has it) to be updated and remove the [U] symbol next to that sector.

{if ($allow_fullscan)}
     A full scan will give you an outlook on all the neighboring sectors in one wide sweep of your 
    sensors. All information gathered will be updated in your map (if your server has it), as well 
    as remove the [U] symbol.
{/if}

<br><br>
<strong>Ships:</strong><br>
Scan or attack a ship (if it shows up on your sensors) by clicking on the appropriate link on the right 
of the ship's name. The attacked ship may evade your offensive maneuver depending on its tech levels. You
may also choose to send a message to that player.
<br><br>
<strong>Trading ports:</strong><br>
Access the port trading menu by clicking on a port's type when you enter a sector where one is present, and
if the sector laws allow it.
<br><br>
<strong>Planets:</strong><br>
Access the planet menu by clicking on a planet's name when you enter a sector where one is present. If the
planet is a friendly, you will be brought to a planet menu, allowing you to transfer commodities/credits,
modify production rates, or upgrade your planet's defenses. However, if it is an enemy planet, you will be
given the choices to either attack, scan, send spies (if enabled), or initiate a Sub-Orbital Fighter Attack,
or SOFA (if enabled).
<br><br>

{if ($allow_navcomp)}
<strong>Navigation computer:</strong><br>
Use your computer to find a route to a specific sector. The navigation computer's power depends on
your computer tech level.
<br><br>
{/if}

<strong>RealSpace:</strong><br>
Use your ship's engines to get to a specific sector. Upgrade your engines' tech level to use RealSpace 
moves effectively. By clicking on the 'Presets' link you can memorize up to 3 sector numbers for quick 
movement or you can target any sector using the 'Other' link. 
<br><br>

<strong>Plasma Engines:</strong><br>
Use your plasma engines to get to a specific sector. It feeds off of your energy levels, and fuel scoops
do not work in this mode of travel. The higher your plasma engine level, the less energy you use.
<br><br>

<strong>Trade routes:</strong><br>
Use trade routes to quickly trade commodities between ports. Trade routes take advantage of RealSpace 
movements to go back and forth between two ports and trade the maximum amount of commodities at each 
end. Ensure the remote sector contains a trading port before using a trade route. The trade route 
presets are shared with the RealSpace ones. As with RealSpace moves, any sector can be targeted using 
the 'Other' link. You can also trade from planet-to-planet or upgrades-to-planet
<br><br>
<h3>Menu bar (left part of the main page):</h3>

<strong>Devices:</strong><br>
Use the different devices that your ship carries (Genesis Torpedoes, Warp Editors, etc.). For 
more details on each individual device, scroll down to the 'Devices' section.
<br><br>

<strong>Planets:</strong><br>
Display a list of all your planets, with current totals on commodities, weaponry and credits. You can
also display their defense levels.
<br><br>

<strong>Log:</strong><br>
Display the log of events that have happened to your ship.
<br><br>

<strong>Send Message:</strong><br>
Send an in-game message to another player.
<br><br>

<strong>Rankings:</strong><br>
Display the list of the top players, ranked by their current scores.
<br><br>

<strong>Options:</strong><br>
Change user-specific options, including passwords, language, and DHTML settings.
<br><br>

<strong>Feedback:</strong><br>
Send an e-mail to the game admin.
<br><br>

<strong>Self-Destruct:</strong><br>
Destroy your ship and remove yourself from the game.
<br><br>

<strong>Help:</strong><br>
Display the help page (what you're reading right now).
<br><br>

<strong>Logout:</strong><br>
Remove any game cookies from your system, ending your current session.
<br><br>

//Techlevels
<a name="techlevels"></a><h2>Tech levels:</h2>
You can upgrade your ship components at any special port. Each component upgrade improves your ship's 
attributes and capabilities.
<br><br>
<strong>Hull:</strong><br>
Determines the number of holds available on your ship (for transporting commodities and 
colonists).
<br><br>
<strong>Engines:</strong><br>
Determines the size of your engines. Larger engines can move through RealSpace at a faster pace.
<br><br>
<strong>Plasma Engines:</strong><br>
Determines the size of your plasma engines. The higher the level, the less energy required.
<strong>Power:</strong><br>
Determines the number of energy units your ship can carry.
<br><br>
<strong>Computer:</strong><br>
Determines the number of fighters your ship can control.
<br><br>
<strong>Sensors:</strong><br>
Determines the precision of your sensors when scanning a ship or planet. Scan success is dependent upon 
the target's cloak level compared to your sensors
<br><br>
<strong>armor:</strong><br>
Determines the number of armor points your ship can use.
<br><br>
<strong>Shields:</strong><br>
Determines the efficiency of your ship's shield system during combat.
<br><br>
<strong>Beams:</strong><br>
Determines the efficiency of your ship's beam weapons during combat.
<br><br>
<strong>Torpedo launchers:</strong><br>
Determines the number of torpedoes your ship can use.
<br><br>
<strong>Cloak:</strong><br>
Determines the efficiency of your ship's cloaking system. See 'Sensors' for more details.
<br><br>

//Devices
<a name="devices"></a><h2>Devices:</h2>
<strong>Warp Editors:</strong><br>
Create or destroy warp links to another sector.
<br><br>
<strong>Genesis Torpedoes:</strong><br>
Create a planet in the current sector (if the star system is big enough).
<br><br>
<strong>Mine Deflector:</strong><br>
Protect the player against mines dropped in space. Each deflector takes out 1 mine.
<br><br>
<strong>Emergency Warp Device:</strong><br>
Transport your ship to a random sector, if manually engaged. Otherwise, an Emergency Warp Device can 
protect your ship when attacked by transporting you out of the reach of the attacker. However, the 
efficiency of it decreases over higher tech levels (usually 15).
<br><br>
<strong>Escape Pod (maximum of 1):</strong><br>
Keep yourself alive when your ship is destroyed, enabling you to keep your credits and planets.
<br><br>
<strong>Fuel Scoop (maximum of 1):</strong><br>
Accumulate energy units when using RealSpace movement.
<br><br>

//Zones
<a name="zones"></a><h2>Zones:</h2>
The galaxy is divided into different areas with different rules being enforced in each zone. To display 
the restrictions attached to your current sector, just click on the zone name (top right corner of the 
main page). Your ship can be towed out of a zone to a random sector when your hull size exceeds the 
maximum allowed level for that specific zone. Attacking other players and using some devices can also 
be disallowed in some zones.
<br><br>

<a href="main.php">{$l_global_mmenu}</a>
