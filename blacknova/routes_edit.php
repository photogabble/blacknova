<?
// routes_edit.php
//
// All editing for individual route steps is done here.

include("config.php");
include("languages/$lang");

updatecookie();

connectdb();

// TODO: change title to something appropriate
$title="routes_edit.php";
$stylesheet='<link rel="stylesheet" href="routes.css" type="text/css">';
include("header.php");

if(checklogin()) { die(); }

bigtitle();

// header stuff before this line

// TODO: internationalize

$form = <<<EOD
<form method="post" action="routes_edit.php">
  <input type="hidden" name="command" value="[command]">
  <input type="hidden" name="step_id" value="[step_id]">
  <table border="0" cellspacing="0">
    <tr>
      <td valign="top" colspan="2" class="xoox"><b>Action</b></td>
      <td colspan="4" align="left" class="xxog"><b>Settings</b></td>
      <td><b></b></td>
      <td align="left" class="xxox"><b>Help</b></td>
    </tr>
    <tr>
      <td class="xoox" valign="baseline">
        <input type="radio" name="action" value="move" [move]>
      </td>
      <td class="xooo" valign="baseline">Move</td>
      <td colspan="2" class="xoog">Type?</td>
      <td class="xooo" rowspan="3">&nbsp;</td>
      <td class="xxoo" align="center">Sector</td>
      <td>&nbsp;</td>
      <td rowspan="3" class="xxox" valign="top">Preforms a move. Select either Realspace
        or Warp and enter the destination.</td>
    </tr>
    <tr>
      <td valign="top" class="ooox" colspan="2" rowspan="2">&nbsp;</td>
      <td align="center" class="ooog">
        <input type="radio" name="move" value="real" [move_real]>
      </td>
      <td>Realspace</td>
      <td rowspan="2" class="oxoo">
        <input type="text" name="sector" [move_sector]>
      </td>
      <td rowspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="radio" name="move" value="warp" [move_warp]>
      </td>
      <td>Warp</td>
    </tr>
    <tr>
      <td class="xoox" valign="baseline">
        <input type="radio" name="action" value="trade" [trade]>
      </td>
      <td class="xooo" valign="baseline">Trade</td>
      <td colspan="2" class="xoog">&nbsp;</td>
      <td valign="bottom" align="center" class="xooo">All?</td>
      <td class="xxoo" align="center" valign="bottom">Amount</td>
      <td rowspan="2">&nbsp;</td>
      <td valign="top" class="xxox" rowspan="2">Trading at regular ports is done
        automatically. Everything you have is sold and your holds will be filled
        with whatever the port is selling. The 'Energy' field is for those situations
        when you wish to trade energy but do not want to utilize your full energy
        capacity. (Energy trade settings can be overridden from the global trade
        settings menu.)</td>
    </tr>
    <tr>
      <td valign="top" class="ooox" colspan="2">&nbsp;</td>
      <td valign="top" class="ooog">&nbsp;</td>
      <td>Energy</td>
      <td align="center">
        <input type="checkbox" name="trade_energy_all" value="1" [trade_energy_all]>
      </td>
      <td class="oxoo">
        <input type="text" name="trade_energy" [trade_energy]>
      </td>
    </tr>
    <tr>
      <td class="xoox" valign="baseline">
        <input type="radio" name="action" value="special" [special]>
      </td>
      <td class="xooo" valign="baseline">Special</td>
      <td class="xxog" colspan="4">Purchase?</td>
      <td>&nbsp;</td>
      <td rowspan="4" class="xxox" valign="top">Buys from a special port. Select
        the items you'd like to buy. Your holds, as well as fighter and torpedo
        bays, will be filled to capacity.</td>
    </tr>
    <tr>
      <td valign="top" colspan="2" class="ooox" rowspan="3">&nbsp;</td>
      <td align="center" class="ooog">
        <input type="checkbox" name="special_colonists" value="1" [special_colonists]>
      </td>
      <td colspan="3" class="oxoo">Colonists </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="checkbox" name="special_fighters" value="1" [special_fighters]>
      </td>
      <td colspan="3" class="oxoo">Fighters </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="checkbox" name="special_torpedoes" value="1" [special_torpedoes]>
      </td>
      <td colspan="3" class="oxoo">Torpedoes </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="xoox" valign="baseline">
        <input type="radio" name="action" value="defense" [defense]>
      </td>
      <td class="xooo" valign="baseline">Defense&nbsp;</td>
      <td align="left" colspan="2" class="xoog" nowrap>Pick up?</td>
      <td class="xooo" align="center">All?</td>
      <td class="xxoo" align="center">Amount</td>
      <td>&nbsp;</td>
      <td rowspan="3" class="xxox" valign="top">Changes sector defenses. Leave the
        'Pick up?' box unchecked if you'd like to drop off something. Check the
        'All?' box or enter the amount you'd like to transfer. Leaving the 'All?'
        box unchecked and the 'Amount' field empty means that the item will not
        be transfered. </td>
    </tr>
    <tr>
      <td valign="top" colspan="2" class="ooox" rowspan="2">&nbsp;</td>
      <td align="center" class="ooog">
        <input type="checkbox" value="1" name="defense_fighters_pickup" [defense_fighters_pickup]>
      </td>
      <td>Fighters</td>
      <td align="center">
        <input type="checkbox" name="defense_fighters_all" value="1" [defense_fighters_all]>
      </td>
      <td class="oxoo">
        <input type="text" name="defense_fighters" [defense_fighters]>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="checkbox" name="defense_torpedoes_pickup" value="1" [defense_torpedoes_pickup]>
      </td>
      <td>Torpedoes</td>
      <td align="center">
        <input type="checkbox" name="defense_torpedoes_all" value="1" [defense_torpedoes_all]>
      </td>
      <td class="oxoo">
        <input type="text" name="defense_torpedoes" [defense_torpedoes]>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="xoox" valign="baseline">
        <input type="radio" name="action" value="planet" [planet] [planet_disable]>
      </td>
      <td class="xooo" valign="baseline">Planet</td>
      <td colspan="2" align="left" class="xoog" nowrap>Pick up?</td>
      <td class="xooo" align="center">All?</td>
      <td class="xxoo" align="center">Amount</td>
      <td>&nbsp;</td>
      <td rowspan="11" class="xxox" valign="top">
        <p>Manages planet items.</p>
        <p>If you want to transfer Energy, Credits, Fighters or Torpedoes, you
          should either select the 'All?' checkbox or fill in the amount you'd
          like to transfer. (Energy trade settings can be overridden from the
          global trade settings menu.)</p>
        <p>Raw materials and colonists are transfered in an 'all or nothing' fashion.
          Selecting 'None' means they will not be transferred at all. Selecting
          'Empty holds' means that everything in your holds will be left on the
          planet and you will not pick up any new raw materials or colonists.</p>
        <p>If you and your alliance do not own any planets, this step will not be
          available to you.</p>
      </td>
    </tr>
    <tr>
      <td valign="baseline" colspan="2" class="ooox" rowspan="10">&nbsp;</td>
      <td align="center" class="ooog">
        <input type="checkbox" name="planet_energy_pickup" value="1" [planet_energy_pickup] [planet_disable]>
      </td>
      <td>Energy</td>
      <td align="center">
        <input type="checkbox" name="planet_energy_all" value="1" [planet_energy_all] [planet_disable]>
      </td>
      <td class="oxoo">
        <input type="text" name="planet_energy" [planet_energy] [planet_disable]>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="checkbox" name="planet_credits_pickup" value="1" [planet_credits_pickup] [planet_disable]>
      </td>
      <td>Credits</td>
      <td align="center">
        <input type="checkbox" name="planet_credits_all" value="1" [planet_credits_all] [planet_disable]>
      </td>
      <td class="oxoo">
        <input type="text" name="planet_credits" [planet_credits] [planet_disable]>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="checkbox" name="planet_fighters_pickup" value="1" [planet_fighters_pickup] [planet_disable]>
      </td>
      <td>Fighters</td>
      <td align="center">
        <input type="checkbox" name="planet_fighters_all" value="1" [planet_fighters_all] [planet_disable]>
      </td>
      <td class="oxoo" align="right">
        <input type="text" name="planet_fighters" [planet_fighters] [planet_disable]>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="center" class="ooog">
        <input type="checkbox" name="planet_torpedoes_pickup" value="1" [planet_torpedoes_pickup] [planet_disable]>
      </td>
      <td>Torpedoes</td>
      <td align="center">
        <input type="checkbox" name="planet_torpedoes_all" value="1" [planet_torpedoes_all] [planet_disable]>
      </td>
      <td class="oxoo">
        <input type="text" name="planet_torpedoes" [planet_torpedoes] [planet_disable]>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ooog" align="center">
        <input type="radio" name="planet_material" value="ore" [planet_material_ore] [planet_disable]>
      </td>
      <td colspan="2">Ore</td>
      <td class="oxoo" rowspan="2" align="center" valign="bottom">Planet?</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ooog" align="center">
        <input type="radio" name="planet_material" value="organics" [planet_material_organics] [planet_disable]>
      </td>
      <td colspan="2">Organics</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ooog" align="center">
        <input type="radio" name="planet_material" value="goods" [planet_material_goods] [planet_disable]>
      </td>
      <td colspan="2">Goods</td>
      <td class="oxoo" rowspan="4" align="center">
        <select name="planet_select" size="5" [planet_select] [planet_disable]>
          [planet_select_items]
        </select>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ooog" align="center">
        <input type="radio" name="planet_material" value="colonists" [planet_material_colonists] [planet_disable]>
      </td>
      <td colspan="2">Colonists</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ooog" align="center">
        <input type="radio" name="planet_material" value="empty" [planet_material_empty] [planet_disable]>
      </td>
      <td nowrap colspan="2">Empty holds</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td class="ooog" align="center">
        <input type="radio" name="planet_material" value="none" [planet_material_none] [planet_disable]>
      </td>
      <td colspan="2">None</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="6" class="xxxx" align="center">
        <input type="reset" value="Clear">
        <input type="submit" value="Add to trade route">
      </td>
      <td>&nbsp;</td>
      <td valign="top" class="xxxx">When you're finished configuring this step, click
        'Add to trade route'. This step will not be validated until you come to
        this step while using this trade route.</td>
    </tr>
  </table>
</form>
EOD;

// nested template identifiers: [planet_select_[planet_select_id]] is turned into [planet_select_1234]
// which is then either removed or turned into 'selected'
$planet_select_item = <<<EOD
          <option value="[planet_select_id]" [planet_select_[planet_select_id]]>[planet_select_name]</option>
EOD;

$bug_msg = "'This is a bug in the BNT source code. Please report it to the game's admin or the developers.'";

function validate(&$value) {
  if(isset($value)) {
    $value = (int)stripnum($value);
  }
}

// used when the variable is one the user shouldn't be messing with
function validate_or_die(&$value, $msg) {
  if(!isset($value) || $value !== (string)(int)stripnum($value)) {
    die($msg);
  }
}

// $command MUST be present and correct
if(!in_array($command,array('before','insert','update','delete'))) {
  // not normally possible, something funky's going on
  die("error: invalid access attempt, command failed check");
}

// $step_id MUST be present and correct
validate_or_die($step_id,"error: invalid access attempt, step_id check failed");

unset($route_id); // This gets set somewhere along the line, if not, it's looked up in the database

if($command == 'delete') {
  // a hack to get the delete command working in the right section
  $action = 'delete';
}

// $action determines what we do even if it isn't set
if(isset($action)) {
  // Parse the form
  unset($arguments);
  // note: checkboxes are special in that the variable associated with them is either set or it's not.
  // this means that we can use isset() and empty() to see if they're checked or not so we don't have
  // to validate the actual contents of the variable.
  switch($action) {
    case 'move':
      // validate the move command
      if(!in_array($move,array('real','warp'))) {
        // not normally possible, something funky's going on
        die("error: unable to parse 'move' command");
      }
      if(!isset($sector)) {
        // user didn't enter a sector
        echo "You must specify a destination if you wish to use a move action.";
        break;
      }
      validate($sector);
      if($sector>=$sector_max) {
        // value is out of range
        echo "You must specify a sector that is within the known universe.";
        break;
      }
      $arguments = (($move == 'real')?'R':'W').$sector;
      break;

    case 'trade':
      // validate the trade command
      validate($trade_energy);
      $arguments  = isset($trade_energy_all)?'Y':'N';
      $arguments .= isset($trade_energy)?$trade_energy:'0';
      break;

    case 'special':
      // make sure at least one of them is checked
      if(empty($special_colonists) && empty($special_fighters) && empty($special_torpedoes)) {
        die("There's no point in trading at a special port if you don't buy anything.");
      }
      $arguments  = isset($special_colonists)?'Y':'N';
      $arguments .= isset($special_fighters)?'Y':'N';
      $arguments .= isset($special_torpedoes)?'Y':'N';
      break;

    case 'defense':
      validate($defense_fighters);
      if(isset($defense_fighters) && $defense_fighters <= 0) {
        unset($defense_fighters);
      }
      if(isset($defense_fighters_pickup) && !(isset($defense_fighters_all) || isset($defense_fighters))) {
        unset($defense_fighters_pickup);
      }
      validate($defense_torpedoes);
      if(isset($defense_torpedoes) && $defense_torpedoes <= 0) {
        unset($defense_torpedoes);
      }
      if(isset($defense_torpedoes_pickup) && !isset($defense_torpedoes_all) && !isset($defense_torpedoes)) {
        unset($defense_torpedoes_pickup);
      }
      if(!(isset($defense_fighters_pickup) ||
           isset($defense_fighters_all) ||
           isset($defense_fighters) ||
           isset($defense_torpedoes_pickup) ||
           isset($defense_torpedoes_all) ||
           isset($defense_torpedoes))) {
        // user didn't set this step up corrently
        die("There's no point in adjusting defenses if you're not going to transfer anything.");
      }
      $arguments  = isset($defense_fighters_pickup)?'Y':'N';
      $arguments .= isset($defense_torpedoes_pickup)?'Y':'N';
      $arguments .= isset($defense_fighters_all)?'Y':'N';
      $arguments .= isset($defense_torpedoes_all)?'Y':'N';
      $arguments .= isset($defense_fighters)?$defense_fighters:'0';
      $arguments .= ',';
      $arguments .= isset($defense_torpedoes)?$defense_torpedoes:'0';
      break;

    case 'planet':
      if(!isset($planet_select)) {
        die("You must select a planet if you want to transfer planetary items.");
      }
      validate_or_die($planet_select,"error: invalid access attempt, planet_select failed check");
      $query = $db->Execute("SELECT COUNT(*) AS count FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['planets']} AS t2 ON t1.player_id=t2.owner OR t1.team=t2.corp WHERE t1.email='{$username}' AND t2.planet_id={$planet_select} LIMIT 1");
      if(!$query) {
        echo $db->ErrorMsg();
        trigger_error($bug_msg,E_USER_ERROR);
      }
      if($query->fields['count'] == 0) {
        // could be that the planet was captured or changed to personal while the user was
        // setting up this step
        die("You no longer have access to that planet.");
      }
      validate($planet_energy);
      if($planet_energy <= 0) {
        unset($planet_energy);
      }
      if(isset($planet_energy_pickup) && !isset($planet_energy_all) && !isset($planet_energy)) {
        unset($planet_energy_pickup);
      }
      validate($planet_credits);
      if($planet_credits <= 0) {
        unset($planet_credits);
      }
      if(isset($planet_credits_pickup) && !isset($planet_credits_all) && !isset($planet_credits)) {
        unset($planet_credits_pickup);
      }
      validate($planet_fighters);
      if($planet_fighters <= 0) {
        unset($planet_fighters);
      }
      if(isset($planet_fighters_pickup) && !isset($planet_fighters_all) && !isset($planet_fighters)) {
        unset($planet_fighters_pickup);
      }
      validate($planet_torpedoes);
      if($planet_torpedoes <= 0) {
        unset($planet_torpedoes);
      }
      if(isset($planet_torpedoes_pickup) && !isset($planet_torpedoes_all) && !isset($planet_torpedoes)) {
        unset($planet_torpedoes_pickup);
      }
      if(!in_array($planet_material,array('ore','organics','goods','colonists','empty','none'))) {
        // not normally possible, something funky's going on
        die("error: planet_material was not set properly");
      }
      if(!(isset($planet_energy_pickup) ||
           isset($planet_energy_all) ||
           isset($planet_energy) ||
           isset($planet_credits_pickup) ||
           isset($planet_credits_all) ||
           isset($planet_credits) ||
           isset($planet_fighters_pickup) ||
           isset($planet_fighters_all) ||
           isset($planet_fighters) ||
           isset($planet_torpedoes_pickup) ||
           isset($planet_torpedoes_all) ||
           isset($planet_torpedoes)) &&
         $planet_material == 'none') {
        // user didn't set this step up corrently
        die("There's no point in dealing with a planet if you're not going to transfer anything.");
      }
      // add this planet command to the database
      switch($planet_material) {
        case 'ore':
          $arguments = 'O';
          break;
        case 'organics':
          $arguments = 'R';
          break;
        case 'goods':
          $arguments = 'G';
          break;
        case 'colonists':
          $arguments = 'C';
          break;
        case 'empty':
          $arguments = 'E';
          break;
        case 'none':
          $arguments = 'N';
          break;
        default:
          trigger_error($bug_msg,E_USER_ERROR);
      }
      $arguments .= isset($planet_energy_pickup)?'Y':'N';
      $arguments .= isset($planet_credits_pickup)?'Y':'N';
      $arguments .= isset($planet_fighters_pickup)?'Y':'N';
      $arguments .= isset($planet_torpedoes_pickup)?'Y':'N';
      $arguments .= isset($planet_energy_all)?'Y':'N';
      $arguments .= isset($planet_credits_all)?'Y':'N';
      $arguments .= isset($planet_fighters_all)?'Y':'N';
      $arguments .= isset($planet_torpedoes_all)?'Y':'N';
      $arguments .= isset($planet_energy)?$planet_energy:'0';
      $arguments .= ',';
      $arguments .= isset($planet_credits)?$planet_credits:'0';
      $arguments .= ',';
      $arguments .= isset($planet_fighters)?$planet_fighters:'0';
      $arguments .= ',';
      $arguments .= isset($planet_torpedoes)?$planet_torpedoes:'0';
      $arguments .= ',';
      $arguments .= $planet_select;
      break;

    case 'delete':
      // a hack to prevent an error further on
      $arguments = "DELETE";
      break;

    default:
      // not normally possible, something funky's going on
      die("error: unable to process action");
  }
  if(empty($arguments)) {
    trigger_error($bug_msg,E_USER_ERROR);
  }
  if($command != 'before') { // if $command is 'before' then $step_id is really a route_id
    $query = $db->Execute("SELECT t3.* FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['routes_headers']} AS t2 ON t1.player_id=t2.player_id LEFT JOIN {$dbtables['routes_steps']} AS t3 ON t2.route_id=t3.route_id WHERE t1.email='{$username}' AND t3.step_id={$step_id} LIMIT 1");
    if(!$query) {
      echo $db->ErrorMsg();
      // it doesn't exist or they don't own it
      // TODO: need a better error message
      die("<br>error: unable to gather route data");
    }
  } else {
    $query = $db->Execute("SELECT t2.* FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['routes_headers']} AS t2 ON t1.player_id=t2.player_id WHERE t1.email='{$username}' AND t2.route_id={$step_id} LIMIT 1");
    if(!$query) {
      echo $db->ErrorMsg();
      // it doesn't exist or they don't own it
      // TODO: need a better error message
      die("<br>error: unable to get route header");
    }
  }
  $fields = $query->fields;
  $route_id = $fields['route_id'];
  switch($command) {
    case 'before':
      // if $command is 'before' then $step_id is really a route_id
      $query = $db->Execute("UPDATE {$dbtables['routes_steps']} SET step=step+1 WHERE route_id={$step_id}");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to update the route data");
      }
      $query = $db->Execute("INSERT {$dbtables['routes_steps']} (route_id,step,action,arguments) VALUES({$step_id},0,'{$action}','{$arguments}')");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to add to the route data");
      }
      // report results
      echo "<p>Route action insert successful.";
      break;

    case 'insert':
      $query = $db->Execute("UPDATE {$dbtables['routes_steps']} SET step=step+1 WHERE route_id={$fields['route_id']} AND step>={$fields['step']}");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to update route data");
      }
      $query = $db->Execute("INSERT INTO {$dbtables['routes_steps']} (route_id,step,action,arguments) VALUES({$fields['route_id']},{$fields['step']},'{$action}','{$arguments}')");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to add route data");
      }
      // report results
      echo "<p>Route action insert successful.";
      break;

    case 'update':
      $query = $db->Execute("UPDATE {$dbtables['routes_steps']} SET action='{$action}',arguments='{$arguments}' WHERE step_id={$step_id} LIMIT 1");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to change route data");
      }
      // report results
      echo "<p>Route action update successful.";
      break;

    case 'delete':
      // process delete command
      $query = $db->Execute("DELETE FROM {$dbtables['routes_steps']} WHERE step_id={$step_id} LIMIT 1");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to delete route data");
      }
      $query = $db->Execute("UPDATE {$dbtables['routes_steps']} SET step=step-1 WHERE route_id={$fields['route_id']} AND step>{$fields['step']}");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to adjust route data");
      }
      // report results
      echo "<p>Route action deletion successful.";
      break;

    default:
      trigger_error($bug_msg,E_USER_ERROR);
  }
} else { // $action was not set
  // gather planet list
  $planet_query = $db->Execute("SELECT t2.planet_id,t2.name,t2.sector_id FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['planets']} AS t2 ON t1.player_id=t2.owner OR (t1.player_id<>t2.owner AND t2.corp!=0 AND t1.team=t2.corp) WHERE t1.email='{$username}'");
  if(!$planet_query) {
    echo $db->ErrorMsg();
    die("<br>error: unable to access database while trying to gather planet list");
  }
  // fill in the list of planets
  $temp_list = '';
  while(!$planet_query->EOF) {
    if($planet_query->fields['sector_id']) {
      $temp = $planet_select_item;

      $temp_name = $planet_query->fields['name'];
      if($temp_name == "") {
        $temp_name = $l_tdr_unnamed;
      }

      $temp = str_replace(array('[planet_select_id]',
                                '[planet_select_name]'),
                          array($planet_query->fields['planet_id'],
                                "{$temp_name} in sector {$planet_query->fields['sector_id']}"),
                          $temp);
      $temp_list .= $temp;
    }

    $planet_query->MoveNext();
  }
  // if there's no planets, disable to planet section of the form
  if(strlen($temp_list) == 0) {
    $temp_list = $planet_select_item;
    $temp_list = str_replace(array('[planet_select_id]',
                                   '[planet_select_name]'),
                             array('',
                                   "No planets available"),
                             $temp_list);
    $form = str_replace('[planet_disable]','disabled',$form);
  }
  $form = str_replace('[planet_select_items]',$temp_list,$form);
  switch($command) {
    case 'before':
    case 'insert':
      // insert the command and the step_id and replace a few markers with 'checked'
      $form = str_replace(array('[move]',
                                '[move_real]',
                                '[planet_material_none]'),
                          array('checked',
                                'checked',
                                'checked'),
                          $form);
      break;

    case 'update':
      // initialize the form with the original values
      $query = $db->Execute("SELECT * FROM {$dbtables['routes_steps']} WHERE step_id={$step_id} LIMIT 1");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: unable to access database while trying to gather 'update' data");
      }
      switch($query->fields['action']) {
        case 'move':
          $sector = substr($query->fields['arguments'],1);
          $form = str_replace(array('[move]',
                                    '[move_sector]',
                                    '[planet_material_none]'),
                              array('checked',
                                    "value='{$sector}'",
                                    'checked'),
                              $form);
          switch(substr($query->fields['arguments'],0,1)) {
            case 'R': // a realspace move
              $form = str_replace(array('[move_real]',
                                        '[move_warp]'),
                                  array('checked',
                                        ''),
                                  $form);
              break;
            case 'W': // a warp
              $form = str_replace(array('[move_real]',
                                        '[move_warp]'),
                                  array('',
                                        'checked'),
                                  $form);
              break;
            default: // a bug
              trigger_error($bug_msg,E_USER_ERROR);
          }
          break;
        case 'trade':
          $trade_energy = substr($query->fields['arguments'],1);
          if($trade_energy == 0) {
            $trade_energy = "";
          }
          $form = str_replace(array('[trade]',
                                    '[trade_energy]',
                                    '[move_real]',
                                    '[planet_material_none]'),
                              array('checked',
                                    "value='{$trade_energy}'",
                                    'checked',
                                    'checked'),
                              $form);
          if(substr($query->fields['arguments'],0,1) == 'Y') {
              $form = str_replace('[trade_energy_all]', 'checked', $form);
          }
          break;
        case 'special':
          $form = str_replace(array('[special]',
                                    '[move_real]',
                                    '[planet_material_none]'),'checked', $form);
          if(substr($query->fields['arguments'],0,1) == 'Y') {
              $form = str_replace('[special_colonists]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],1,1) == 'Y') {
              $form = str_replace('[special_fighters]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],2,1) == 'Y') {
              $form = str_replace('[special_torpedoes]', 'checked', $form);
          }
          break;
        case 'defense':
          $form = str_replace(array('[defense]',
                                    '[move_real]',
                                    '[planet_material_none]'),'checked', $form);
          if(substr($query->fields['arguments'],0,1) == 'Y') {
              $form = str_replace('[defense_fighters_pickup]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],1,1) == 'Y') {
              $form = str_replace('[defense_torpedoes_pickup]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],2,1) == 'Y') {
              $form = str_replace('[defense_fighters_all]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],3,1) == 'Y') {
              $form = str_replace('[defense_torpedoes_all]', 'checked', $form);
          }
          list($defense_fighters, $defense_torpedoes) =
            explode(',',substr($query->fields['arguments'],4));
          if($defense_fighters == 0) {
            $defense_fighters = "";
          }
          if($defense_torpedoes == 0) {
            $defense_torpedoes = "";
          }
          $form = str_replace(array('[defense_fighters]',
                                    '[defense_torpedoes]'),
                              array("value='{$defense_fighters}'",
                                    "value='{$defense_torpedoes}'"),
                              $form);
          break;
        case 'planet':
          $form = str_replace(array('[planet]',
                                    '[move_real]'), 'checked', $form);
          switch(substr($query->fields['arguments'],0,1)) {
            case 'O': // ore
              $form = str_replace('[planet_material_ore]', 'checked', $form);
              break;
            case 'R': // organics
              $form = str_replace('[planet_material_organics]', 'checked', $form);
              break;
            case 'G': // goods
              $form = str_replace('[planet_material_goods]', 'checked', $form);
              break;
            case 'C': // colonists
              $form = str_replace('[planet_material_colonists]', 'checked', $form);
              break;
            case 'E': // empty holds
              $form = str_replace('[planet_material_empty]', 'checked', $form);
              break;
            case 'N': // none
              $form = str_replace('[planet_material_none]', 'checked', $form);
              break;
            default:  // a bug
              trigger_error($bug_msg,E_USER_ERROR);
          }
          if(substr($query->fields['arguments'],1,1) == 'Y') {
              $form = str_replace('[planet_energy_pickup]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],2,1) == 'Y') {
              $form = str_replace('[planet_credits_pickup]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],3,1) == 'Y') {
              $form = str_replace('[planet_fighters_pickup]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],4,1) == 'Y') {
              $form = str_replace('[planet_torpedoes_pickup]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],5,1) == 'Y') {
              $form = str_replace('[planet_energy_all]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],6,1) == 'Y') {
              $form = str_replace('[planet_credits_all]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],7,1) == 'Y') {
              $form = str_replace('[planet_fighters_all]', 'checked', $form);
          }
          if(substr($query->fields['arguments'],8,1) == 'Y') {
              $form = str_replace('[planet_torpedoes_all]', 'checked', $form);
          }
          list($planet_energy, $planet_credits, $planet_fighters, $planet_torpedoes, $planet_select) =
            explode(',',substr($query->fields['arguments'],9));
          if($planet_energy == 0) {
            $planet_energy = "";
          }
          if($planet_credits == 0) {
            $planet_credits = "";
          }
          if($planet_fighters == 0) {
            $planet_fighters = "";
          }
          if($planet_torpedoes == 0) {
            $planet_torpedoes = "";
          }
          // replace some markers with thier values and clear the rest of the [planet_material_*] series
          $form = str_replace(array('[planet_energy]',
                                    '[planet_credits]',
                                    '[planet_fighters]',
                                    '[planet_torpedoes]',
                                    "[planet_select_{$planet_select}]",
                                    '[planet_material_ore]',
                                    '[planet_material_organics]',
                                    '[planet_material_goods]',
                                    '[planet_material_colonists]',
                                    '[planet_material_empty]',
                                    '[planet_material_none]'),
                              array("value='{$planet_energy}'",
                                    "value='{$planet_credits}'",
                                    "value='{$planet_fighters}'",
                                    "value='{$planet_torpedoes}'",
                                    'selected',
                                    '','','','','',''),
                              $form);
          break;
        default:
          // nothing else should've made it into the database
          trigger_error($bug_msg,E_USER_ERROR);
      }
      // replace a few markers with 'checked'
      $form = str_replace(array('[move_real]',
                                '[planet_material_none]'),
                          array('checked',
                                'checked'),
                          $form);
      break;

    case 'delete':
      die("error: invalid access attempt, 'delete' has to be both an action and a command");
      break;

    default:
      // since $command was validated near the beginning, getting here represents a bug in the
      // validation scheme.
      trigger_error($bug_msg,E_USER_ERROR);
  }

  // replace command and step_id markers
  $form = str_replace(array('[command]',
                            '[step_id]'),
                      array($command,
                            $step_id),
                      $form);
  // remove the remaining markers
  $form = preg_replace("/\[..*?\]/", "", $form);

  // write out the completed form
  echo $form;
}

if(!isset($route_id)) { // look it up if we don't have it yet
  if($command == 'before') {
    // 'before' uses $step_id as a route_id
    $route_id = $step_id;
  } else {
    // look up $step_id in the routes_steps table
    $query = $db->Execute("SELECT route_id FROM {$dbtables['routes_steps']} WHERE route_id={$step_id} LIMIT 1");

    if(!$query) {
      echo $db->ErrorMsg();
      die("<br>error: unable to access database while trying to determine route_id");
    }
    $route_id = $query->fields['route_id'];
  }
}

// output some links so the user can get to another page
echo "<p>Click <a href=\"routes.php?route_id={$route_id}\">here</a> to go back to editing this route.";
echo "<p>Click <a href=\"routes.php\">here</a> to list available routes.";
echo "<p>Click <a href=\"main.php\">here</a> to return to the main menu.";

// footer stuff after this line

include("footer.php");

?>