<script type="text/javascript" defer="defer" src="backends/javascript/focus.js"></script>

<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>
<h1>{$title}</h1>
<form action="make_galaxy.php" name="make_galaxy" method="post">
<table>
    <tr>
        <td><strong><u>{$l_planet_setup}</u></strong></td>
        <td>{$l_suggested_value}</td>
        <td>{$l_safe_range}</td>
    </tr>
{if $ship_classes}
    <tr>
        <td>{$l_percent_ship}</td>
        <td><input type="text" name="shipyards" size="5" maxlength="5" value="1"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_percent_upgrade}</td>
        <td><input type="text" name="upgrades" size="5" maxlength="5" value="1"></td>
        <td>[1-95]</td>
    </tr>
{else}
    <tr>
        <td></td>
        <td><input type="hidden" name="shipyards" size="5" maxlength="5" value="0"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_percent_upgrade}</td>
        <td><input type="text" name="upgrades" size="5" maxlength="5" value="2"></td>
        <td>[1-95]</td>
    </tr>
{/if}
    <tr>
        <td>{$l_percent_device}</td>
        <td><input type="text" name="devices" size="5" maxlength="5" value="1"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_percent_ore}</td>
        <td><input type="text" name="ore" size="5" maxlength="5" value="15"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_percent_organics}</td>
        <td><input type="text" name="organics" size="5" maxlength="5" value="5"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_percent_goods}</td>
        <td><input type="text" name="goods" size="5" maxlength="5" value="15"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_percent_energy}</td>
        <td><input type="text" name="energy" size="5" maxlength="5" value="10"></td>
        <td>[1-95]</td>
    </tr>
    <tr>
        <td>{$l_initscommod}<br></td>
        <td><input type="text" name="initscommod" size="6" maxlength="6" value="100.00"> {$l_percent_of_max}&nbsp;&nbsp;</td>
        <td></td>
    </tr>
    <tr>
        <td>{$l_initbcommod}<br></td>
        <td><input type="text" name="initbcommod" size="6" maxlength="6" value="100.00"> {$l_percent_of_max}&nbsp;</td>
        <td></td>
    </tr>
    <tr>
        <td><strong><u>{$l_sector_link_setup}</u></strong></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>{$l_num_sectors} &nbsp;</td>
        <td><input type="text" name="sektors" size="5" maxlength="6" value="{$sector_max}"></td>
        <td>[5,000-25,000]</td>
    </tr>
    <tr>
        <td>{$l_num_fedsecs}</td>
        <td><input type="text" name="fedsecs" size="6" maxlength="6" value="{$fedsecs}"></td>
        <td>[5-500]</td>
    </tr>
    <tr>
        <td>{$l_avg_links_per} &nbsp;</td>
        <td><input type="text" name="linksper" size="2" maxlength="2" value="8"></td>
        <td>[8-15]</td>
    </tr>
    <tr>
        <td>{$l_two_way_secs} &nbsp;</td>
        <td><input type="text" name="twoways" size="3" maxlength="3" value="40"></td>
        <td>[1-49]</td>
    </tr>
    <tr>
        <td>{$l_unowned_secs} &nbsp;</td>
        <td><input type="text" name="planets" size="3" maxlength="3" value="10"></td>
        <td>[1-99]</td>
    </tr>
</table>

<br><br>
<input type="submit" value="{$l_continue}">
<input type="reset" value="{$l_reset}">
<input type="hidden" name="gamenum" value="{$gamenum}">
<input type="hidden" name="admin_charname" value="{$admin_charname}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="autorun" value="{$autorun}">
<input type="hidden" name="step" value="{$step}">
</form>
<br><br><br><br>

{if $autorun}
<script type="text/javascript" src="backends/javascript/autorun.js"></script>
{/if}

