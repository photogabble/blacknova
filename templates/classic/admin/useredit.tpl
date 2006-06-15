<script type="text/javascript" src="backends/javascript/sha256.js" defer="defer"></script>
<form action="admin.php" method="post">
<strong>User editor</strong><br>
{if $number_dropdown > 0}
    <select size="1" name="user">
{section name=selectuser loop=$player_list_id}
        <option value="{$player_list_id[selectuser]}">{$player_list_name[selectuser]}</option>
{/section}
    </select>
<input type="submit" value="Edit">
{elseif $post_operation ==''}
<table border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td>Player name</td>
        <td><input type="text" name="character_name" value="{$character_name}" size="32" maxlength="25"></td>
    </tr>
    <tr>
        <td>Password</td>
        <td><input type="text" name="password2" value="{$password}" size="32" maxlength="40"></td>
    </tr>
    <tr>
        <td>E-mail</td>
        <td><input type="text" name="email" value="{$email}"></td>
    </tr>
    <tr>
        <td>Player ID</td>
        <td>{$user}</td>
    </tr>
    <tr>
        <td>Ship ID </td>
        <td>{$currentship_id}</td>
    </tr>
    <tr>
        <td>Confirmation code</td>
        <td><input type="text" name="c_code" value="{$c_code}"></td>
    </tr>
    <tr>
        <td>Ship</td>
        <td><input type="text" name="ship_name" value="{$shipname}"></td>
    </tr>
    <tr>
        <td>Ship Class</td>
        <td><input type="text" name="ship_class" value="{$ship_class}"></td>
    </tr>
    <tr>
        <td>Destroyed?</td>
        <td><input type="checkbox" name="destroyed" value="on"{if $destroyed == "Y"} checked{/if}></td>
    </tr> 
    <tr>
        <td>Use Gravatar?</td>
        <td><input type="checkbox" name="use_gravatar" value="on"{if $use_gravatar == "Y"} checked{/if}></td>
    </tr> 
    <tr>
        <td>Override Gravatar?</td>
        <td><input type="checkbox" name="override_gravatar" value="on"{if $override_gravatar == "Y"} checked{/if}></td>
    </tr> 
    <tr>
        <td>Activated?</td>
        <td><input type="checkbox" name="active" value="on"{if $active == "Y"} checked{/if}></td>
    </tr> 
    <tr>
        <td>Clear defenses?</td>
        <td><input type="checkbox" name="cleared_defences" value="on"{if $destroyed == "Y"} checked{/if}></td>
    </tr> 
    <tr>
        <td>Account notes</td>
        <td><textarea name="account_notes" rows="3" cols="50">{$account_notes}</textarea></td>
    </tr> 
    <tr>
        <td nowrap="nowrap">Levels</td>
        <td nowrap="nowrap" colspan="3">
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td>Hull</td>
                    <td><input type="text" size="5" name="hull" value="{$hull}"></td>
                    <td>Engines</td>
                    <td><input type="text" size="5" name="engines" value="{$engines}"></td>
                    <td>Power</td>
                    <td><input type="text" size="5" name="power" value="{$power}"></td>
                    <td>Computer</td>
                    <td><input type="text" size="5" name="computer" value="{$computer}"></td>
                </tr>
                <tr>
                    <td>Sensors</td>
                    <td><input type="text" size="5" name="sensors" value="{$sensors}"></td>
                    <td>Armor</td>
                    <td><input type="text" size="5" name="armor" value="{$armor}"></td>
                    <td>Shields</td>
                    <td><input type="text" size="5" name="shields" value="{$shields}"></td>
                    <td>Beams</td>
                    <td><input type="text" size="5" name="beams" value="{$beams}"></td>
                </tr>
                <tr>
                    <td>Torpedo Launchers</td>
                    <td><input type="text" size="5" name="torp_launchers" value="{$torp_launchers}"></td>
                    <td>Cloak</td>
                    <td><input type="text" size="5" name="cloak" value="{$cloak}"></td>
{if $plasma_engines}
                    <td>Plasma Engines</td>
                    <td><input type="text" size="5" name="pengines" value="{$pengines}"></td>
{/if}
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">Holds</td>
        <td nowrap="nowrap" colspan="3">
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td>{$l_ore}</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="ship_ore" value="{$ore}"></td>
                    <td>{$l_organics}</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="ship_organics" value="{$organics}"></td>
                    <td>{$l_goods}</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="ship_goods" value="{$goods}"></td>
                    <td>{$l_energy}</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="ship_energy" value="{$energy}"></td>
                </tr>
                <tr>
                    <td>{$l_colonists}</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="ship_colonists" value="{$colonists}"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">Combat</td>
        <td nowrap="nowrap" colspan="3">
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td>Fighters</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="ship_fighters" value="{$fighters}"></td>
                    <td>Torpedoes</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="torps" value="{$torps}"></td>
                    <td>Armor Pts</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="armor_pts" value="{$armor_pts}"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">Devices</td>
        <td nowrap="nowrap" colspan="3">
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td>Warp Editors</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="dev_warpedit" value="{$dev_warpedit}"></td>
                    <td>Genesis Torpedoes</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="dev_genesis" value="{$dev_genesis}"></td>
                    <td>Mine Deflectors</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="dev_minedeflector" value="{$dev_minedeflector}"></td>
                </tr>
                <tr>
                    <td>Emergency Warp</td>
                    <td nowrap="nowrap"><input type="text" size="8" name="dev_emerwarp" value="{$dev_emerwarp}"></td>
                    <td>Escape Pod</td>
                    <td nowrap="nowrap"><input type="checkbox" name="dev_escapepod" value="on"{if $dev_escapepod == "Y"} checked{/if}></td>
                    <td>Fuel scoop</td>
                    <td nowrap="nowrap"><input type="checkbox" name="dev_fuelscoop" value="on"{if $dev_fuelscoop == "Y"} checked{/if}></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td nowrap="nowrap">Money and more</td>
        <td nowrap="nowrap">
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td nowrap="nowrap">Credits</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="credits" value="{$credits}"></td>
                </tr>
                <tr>
                    <td nowrap="nowrap">Turns</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="turns" value="{$turns}"></td>
                </tr>
                <tr>
                    <td nowrap="nowrap">Turns Used</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="turns_used" value="{$turns_used}"></td>
                </tr>
                <tr>
                    <td nowrap="nowrap">Current Sector</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="sector" value="{$sector_id}"></td>
                </tr>
            </table>
        </td>
        <td nowrap="nowrap">
            <table border="0" cellspacing="0" cellpadding="5">
                <tr>
                    <td nowrap="nowrap">Current balance</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="igb_balance" value="{$igb_balance}"></td>
                </tr>
                <tr>
                    <td nowrap="nowrap">Loan</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="igb_loan" value="{$igb_loan}"></td>
                </tr>
                <tr>
                    <td nowrap="nowrap">Loan Timestamp</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" name="igb_loantime" value="{$igb_loantime}"></td>
                </tr>
                <tr>       
                    <td nowrap="nowrap">Federation Bounty</td>
                    <td nowrap="nowrap" colspan="3"><input type="text" readonly="readonly" name="bounty" value="{$bounty}"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <!-- This still looks crappy. I'll think of something better later. :)
            {section name=v loop=$stuff}
            {$template.section.v.iteration} {$v} => {$stuff[v]}<br>
            {sectionelse}
            This players owns no planets.
            {/section}
            <p>
            This player owns {$template.section.v.loop} planets.-->
        </td>
    </tr>
    <tr>
        <td>
            <input type="hidden" name="user" value="{$user}">
            <input type="hidden" name="operation" value="save">
            <input type="submit" value="Save">
        </td>
    </tr>
</table>
<input type="hidden" name="account_id" value="{$account_id}">
{else}
<input type=submit value="Return to User editor">
{/if}
<input type="hidden" name="menu" value="useredit">
</form>
