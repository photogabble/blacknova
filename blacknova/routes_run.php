<?

// TODO: insert header stuff

// TODO: internationalize everything

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

function step_move(&$arguments) {
  global $user_data;
  global $ship_data;
  global $step_this;
  // TODO: preform a move
  $target = (int)substr($arguments,1);
  if($ship_data['sector_id'] == $target) {
    $status = "Warning: Current location and target sector are the same. Skipping this step.";
    $results = TRUE;
  } else {
    switch(substr($arguments,0,1)) {
      case 'R': // realspace
        // calculate the distance

        // test to see if this step has been done before by checking the existance of one of it's
        // variables, if not then calculate the static data
        if(!isset($step_this['turns'])) {
          $distance = calc_dist($ship_data['sector_id'],$target);
          if($distance<1) {
            // TODO: The query failed. What now?
          }

          $step_this['turns'] = max(round($distance / $shipdata['speed']),1);

          if($ship_data['dev_fuelscoop'] == 'Y') {
            $step_this['scoopable'] = $distance * 100;
          } else {
            $step_this['scoopable'] = 0;
          }

          $step_this['energy_max'] = NUM_ENERGY($ship_data['power']);
        }
        if($user_data['turns'] > $step_this['turns']) {
          // TODO: handle not enough turns
          $step_this['output'] = str_replace(array('[turns_needed]',
                                                   '[turns_have]'),
                                             array($step_this['turns'],
                                                   $user_data['turns']),
                                             $html_not_enough_turns);
          return FALSE;
        }
        $scooped = min(max($step_this['energy_max']-$ship_data['energy'],0),$step_this['scoopable']);
        $user_data['turns'] -= $step_this['turns'];
        $user_data['turns_used'] += $step_this['turns'];
        $ship_data['sector_id'] = $target;
        $ship_data['energy'] += $scooped;
        $query = $db->Execute("UPDATE {$dbtables['players']} SET turns={$user_data['turns']},turns_used={$user_data['turns_used']} WHERE player_id={$user_data['player_id']} LIMIT 1");
        if(!$query) {
          // TODO: handle error
        }
        $query = $db->Execute("UPDATE {$dbtables['ships']} SET sector_id={$ship_data['sector_id']},energy={$ship_data['energy']} WHERE ship_id={$ship_data['ship_id']} LIMIT 1");
        if(!$query) {
          // TODO: handle error
        }
        break;
      case 'W': // warp
        // TODO: handle warp move
        break;
      default: // bug
        trigger_error($bug_msg,E_USER_ERROR);
    }
    // TODO: handle sector defenses
  }
}

function step_trade() {
  // TODO: preform a trade at a regular port
}

function step_special() {
  // TODO: preform a buy at a special port
}

function step_defense() {
  // TODO: alter sector defenses
}

function step_planet() {
  // TODO: transfer stuff on a planet
}

validate_or_die($route_id,"error: invalid access attempt, route_id failed check");

// make sure the route exists and is owned by the user
$query = $db->Execute("SELECT t1.route_name,t1.sector_id AS starts_in,t3.sector_id AS location FROM {$dbtables['routes_headers']} AS t1 LEFT JOIN {$dbtables['players']} AS t2 ON t1.player_id=t2.player_id LEFT JOIN {$dbtables['ships']} AS t3 ON t2.currentship=t3.ship_id WHERE t2.email='{$username}' AND t1.route_id={$route_id} LIMIT 1");
if(!$query) {
  echo $db->ErrorMsg();
  die("<br>error: db query failed while getting route header");
}
if($query->EOF) {
  die("error: invalid access attempt, route_id {$route_id} is not available to this user");
}

// check to see if user is in the right start sector
if($query->fields['starts_in'] != $location) {
  die("error: invalid access attempt, current location is not the same as route start location");
}

// gather the steps
$query = $db->Execute("SELECT * FROM {$dbtables['routes_steps']} WHERE route_id={$route_id} ORDER BY step ASC");
if(!$query) {
  echo $db->ErrorMsg();
  die("<br>error: db query failed while getting route steps");
}
if($query->EOF) {
  die("error: invalid access attempt, route_id {$route_id} has no steps");
}

$user_data = $db->Execute("SELECT * FROM {$dbtables['players']} WHERE email='{$username}' LIMIT 1");
if(!$user_data) {
  echo $db->ErrorMsg();
  die("<br>error: db query failed while getting player data");
}
$user_data = userdata->fields;

$ship_data = $db->Execute("SELECT * FROM {$dbtables['ships']} WHERE ship_id={$user_data['currentship']} LIMIT 1");
if(!$ship_data) {
  echo $db->ErrorMsg();
  die("<br>error: db query failed while getting ship data");
}
$ship_data = ship_data->fields;
$ship_data['speed'] = mypw($level_factor, $ship_data['engines']);

$step_data = array();

// preprocess the route steps and send them off to the right functions
$step_results = TRUE;
while(!$query->EOF && $step_results) {
  if(!$step_data[$query->fields['step']]) {
    $step_data[$query->fields['step']] = array();
  }
  $step_this = &$stepdata[$query->fields['step']];
  switch($query->fields['action']) {
    case 'move':
      $step_results = step_move($query->fields['arguments']);
      break;
    case 'trade':
      $step_results = step_trade($query->fields['arguments']);
      break;
    case 'special':
      $step_results = step_special($query->fields['arguments']);
      break;
    case 'defense':
      $step_results = step_defense($query->fields['arguments']);
      break;
    case 'planet':
      $step_results = step_planet($query->fields['arguments']);
      break;
    default: // bug
      trigger_error($bug_msg,E_USER_ERROR);
  }
}

// TODO: insert footer stuff

?>
