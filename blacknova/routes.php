<?
// routes.php
//
// Route and step listing are done here. This is also the main entry
// point for the routes interface.

include("config.php");
include("languages/$lang");

updatecookie();

connectdb();

// TODO: change title to something appropriate
$title="routes.php";
$stylesheet='<link rel="stylesheet" href="routes.css" type="text/css">';
include("header.php");

if(checklogin()) { die(); }

bigtitle();

// header stuff before this line

// TODO: internationalize

$html_routes_header = <<<EOD
<table border="1" cellspacing="0">
  <tr>
    <td align="center" colspan="3">&nbsp;</td>
    <td align="center" nowrap>Starts in</td>
    <td nowrap>Route name</td>
  </tr>
EOD;

$html_routes_item = <<<EOD
  <tr>
    <form method="post" action="route_run.php">
      <td align="center">
        <input type="hidden" name="route_id" value="[route_id]">
        [submit]
      </td>
    </form>
    <form method="post" action="routes.php">
      <td align="center">
        <input type="hidden" name="route_id" value="[route_id]">
        <input type="submit" value="Edit">
      </td>
    </form>
    <form method="post" action="routes.php">
      <td align="center">
        <input type="hidden" name="route_id" value="[route_id]">
        <input type="hidden" name="command" value="delete">
        <input type="submit" value="Delete">
      </td>
    </form>
    <td align="center">[sector]</td>
    <td nowrap>[name]</td>
  </tr>
EOD;

$html_routes_submit = <<<EOD
        <input type="submit" value="Run">
EOD;

$html_routes_none = <<<EOD
  <tr align="center">
    <td colspan="5" nowrap><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No routes have been
    created.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><br></td>
  </tr>
EOD;

$html_routes_new = <<<EOD
  <tr align="right">
    <form method="post" action="routes.php">
      <td colspan="5">
        <input type="hidden" name="command" value="new">
        <input type="submit" value="Create a new route">
      </td>
    </form>
  </tr>
EOD;

$html_routes_footer = <<<EOD
</table>
EOD;

$html_steps_header = <<<EOD
<table border="1" cellspacing="0">
  <tr>
    <td colspan="3">&nbsp;</td>
    <td>Action</td>
  </tr>
  <tr>
    <form method="post" action="routes_edit.php">
      <td>
        <input type="hidden" name="command" value="before">
        <input type="hidden" name="step_id" value="[route_id]">
        <input type="submit" value="Insert new step">
      </td>
    </form>
    <form method="post" action="routes.php">
      <td colspan="2">
        <input type="hidden" name="command" value="change">
        <input type="hidden" name="route_id" value="[route_id]">
        <input type="submit" value="Change start sector">
      </td>
    </form>
    <td>[action]</td>
  </tr>
EOD;

$html_steps_item = <<<EOD
  <tr>
    <form method="post" action="routes_edit.php">
      <td>
        <input type="hidden" name="command" value="insert">
        <input type="hidden" name="step_id" value="[step_id]">
        <input type="submit" value="Insert new step">
      </td>
    </form>
    <form method="post" action="routes_edit.php">
      <td align="center">
        <input type="hidden" name="command" value="update">
        <input type="hidden" name="step_id" value="[step_id]">
        <input type="submit" value="Edit">
      </td>
    </form>
    <form method="post" action="routes_edit.php">
      <td align="center">
        <input type="hidden" name="command" value="delete">
        <input type="hidden" name="step_id" value="[step_id]">
        <input type="submit" value="Delete">
      </td>
    </form>
    <td>[action]</td>
  </tr>
EOD;

$html_steps_none = <<<EOD
  <tr>
    <td align="center" colspan="4"><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;There are no actions in this
    route.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><br></td>
  <tr>
EOD;

$html_steps_footer = <<<EOD
</table>
EOD;

$html_create_new_route = <<<EOD
<form method="post" action="routes.php">
  <input type="hidden" name="command" value="new">
  <table border="1" cellspacing="0">
    <tr>
      <td nowrap>Sector to start in&nbsp;</td>
      <td>
        <input type="text" name="sector_id" value="[sector]">
      </td>
    </tr>
    <tr>
      <td>Route name</td>
      <td>
        <input type="text" name="route_name">
      </td>
    </tr>
    <tr>
      <td colspan=2 align="right">
        <input type="submit" value="Create this route">
      </td>
    </tr>
  </table>
</form>
EOD;

$html_confirm_delete = <<<EOD
<table border="0" align="center">
  <tr align="center">
    <td colspan="2">Are you sure you want to <b>permanently</b> delete this route?</td>
  </tr>
  <tr align="center">
    <form method="post" action="routes.php">
      <input type="hidden" name="route_id" value="[route_id]">
      <input type="hidden" name="command" value="delete">
      <input type="hidden" name="confirm" value="1">
      <td>
        <input type="submit" value="Yes, I'm sure. Delete this route.">
      </td>
    </form>
    <form method="post" action="routes.php">
      <td>
        <input type="submit" value="No, I want to keep this route.">
      </td>
    </form>
  </tr>
</table>
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

if(isset($command)) {
  switch($command) {
    case 'delete':
      if(isset($confirm)) {
        // validate $route_id
        validate_or_die($route_id,"error: invalid access attempt, route_id check failed during delete command");
        $query = $db->Execute("SELECT t1.route_id FROM {$dbtables['routes_headers']} AS t1 LEFT JOIN {$dbtables['players']} AS t2 ON t1.player_id=t2.player_id WHERE t2.email='{$username}' AND t1.route_id={$route_id} LIMIT 1");
        if(!$query) {
          echo $db->ErrorMsg();
          die("<br>error: database error while checking route ownership for deletion");
        }
        if($query->EOF || empty($query->fields['route_id'])) {
          die("error: invalid access attempt, route not owned by player or doesn't exist");
        }
        $player_id = $query->fields['player_id'];
        $query = $db->Execute("DELETE FROM {$dbtables['routes_headers']} WHERE route_id={$route_id} LIMIT 1");
        if(!$query) {
          echo $db->ErrorMsg();
          die("<br>error: database error while trying to delete route header");
        }
        $query = $db->Execute("DELETE FROM {$dbtables['routes_steps']} WHERE route_id={$route_id}");
        if(!$query) {
          echo $db->ErrorMsg();
          die("<br>error: database error while trying to delete route steps");
        }
        echo "Route deleted successfully.";
      } else {
        // output route deletion confirmation form
        $html_confirm_delete = str_replace('[route_id]',$route_id,$html_confirm_delete);
        echo $html_confirm_delete;
      }
      break;

    case 'change':
      // TODO: handle command to change a routes start sector
      echo "Not currently implemented.";
      break;

    case 'new':
      // check to see if they have room for a new route
      $query = $db->Execute("SELECT COUNT(t2.route_id) AS count FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['routes_headers']} AS t2 ON t1.player_id=t2.player_id WHERE t1.email='{$username}' GROUP BY t2.player_id");
      if(!$query) {
        echo $db->ErrorMsg();
        die("<br>error: db query error");
      }
      if($query->fields['count']>=$max_traderoutes_player) {
        die("error: invalid access attempt, to many routes");
      }
      // if we have $sector_id then we are processing a post, otherwise show the form
      if(isset($sector_id)) {
        // validate input
        validate($sector_id);
        if($sector_id >= $sector_max || $sector_id < 0) {
          echo "You must select a sector that is within the known universe.";
          break;
        }
        // figure out the player_id
        $query = $db->Execute("SELECT player_id FROM {$dbtables['players']} WHERE email='{$username}' LIMIT 1");
        if(!$query) {
          echo $db->ErrorMsg();
          trigger_error($bug_msg,E_USER_ERROR);
        }
        // create the route header
        $query = $db->Execute("INSERT {$dbtables['routes_headers']} (route_name,sector_id,player_id) VALUES('{$route_name}',{$sector_id},{$query->fields['player_id']})");
        if(!$query) {
          echo $db->ErrorMsg();
          trigger_error($bug_msg,E_USER_ERROR);
        }
        // tell the user the results
        echo "New route created.";
      } else {
        // show 'new route' creation form
        $query = $db->Execute("SELECT t2.sector_id FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['ships']} AS t2 ON t1.player_id=t2.player_id WHERE t1.email='{$username}' LIMIT 1");
        if(!$query) {
          echo $db->ErrorMsg();
          trigger_error($bug_msg,E_USER_ERROR);
        }
        $temp = $html_create_new_route;
        $temp = str_replace('[sector]',$query->fields['sector_id'],$temp);
        echo $temp;
      }
      break;
    default:
      die("error: invalid access attempt, command check failed");
  }
  // show some links so the user can get off this page
  echo "<p>Click <a href=\"routes.php\">here</a> to list available routes.";
} elseif(isset($route_id)) {
  // validate $route_id
  validate_or_die($route_id,"error: invalid access attempt, route_id check failed");

  $query_header = $db->Execute("SELECT t2.* FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['routes_headers']} AS t2 ON t1.player_id=t2.player_id WHERE t1.email='{$username}' AND t2.route_id={$route_id} LIMIT 1");
  if(!$query_header) {
    // not normally possible, something funky's going on
    die("error: invalid access attempt, route_id not available");
  }

  $temp = $html_steps_header;
  $temp = str_replace(array('[action]',
                            '[route_id]'),
                      array("Start in sector {$query_header->fields['sector_id']}",
                            $route_id),
                      $temp);
  echo $temp;

  $query = $db->Execute("SELECT t3.* FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['routes_headers']} AS t2 ON t1.player_id=t2.player_id LEFT JOIN {$dbtables['routes_steps']} AS t3 ON t2.route_id=t3.route_id WHERE t1.email='{$username}' AND t2.route_id={$route_id}");
  if(!$query) {
    echo $db->ErrorMsg();
    trigger_error($bug_msg,E_USER_ERROR);
  }
  if(!isset($query->fields['action'])) {
    // Handle a route with no steps
    echo $html_steps_none;
  } else {
    unset($action);
    while(!$query->EOF) {
      switch($query->fields['action']) {
        case 'move':
          switch(substr($query->fields['arguments'],0,1)) {
            case 'R': // a realspace move
              $action = "Realspace";
              break;
            case 'W': // a warp
              $action = "Warp";
              break;
            default: // a bug
              trigger_error($bug_msg,E_USER_ERROR);
          }
          $action .= " to sector ".substr($query->fields['arguments'],1).".";
          break;
        case 'trade':
          $action = "Trade at port.";
          if (substr($query->fields['arguments'],0,1) == 'Y') {
            $action .= " (Trade energy in addition to other materials.)";
          } elseif (substr($query->fields['arguments'],1) > 0) {
            $action .= " (Limit energy trades to ".substr($query->fields['arguments'],1)." units.)";
          } else {
            $action .= " (Do not trade energy.)";
          }
          break;
        case 'special':
          $action = array();
          if(substr($query->fields['arguments'],0,1) == 'Y') {
            $action[] = "colonists";
          }
          if(substr($query->fields['arguments'],1,1) == 'Y') {
            $action[] = "fighters";
          }
          if(substr($query->fields['arguments'],2,1) == 'Y') {
            $action[] = "torpedoes";
          }
          if(count($action) > 1) {
            $action = implode(', ', $action);
            $action = substr_replace($action,' and',strrpos($action,','),1);
          } else {
            $action = $action[0];
          }
          $action = "Buy {$action} at special port.";
          break;
        case 'defense':
          $flags = ereg_replace("[^Y]","N",substr($query->fields['arguments'],0,4));
          list($defense_fighters, $defense_torpedoes) =
            explode(',',substr($query->fields['arguments'],4));
          switch($flags) {
            case 'YYYY':
              $action = "Pick up all fighters and torpedoes.";
              break;
            case 'YYYN':
              if($defense_torpedoes > 0) {
                $action = "Pick up all fighters and {$defense_torpedoes} torpedoes.";
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'YYNY':
              if($defense_fighters > 0) {
                $action = "Pick up {$defense_fighters} fighters and all torpedoes.";
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'YYNN':
              if($defense_fighters > 0 && $defense_torpedoes > 0) {
                $action = "Pick up {$defense_fighters} fighters and {$defense_torpedoes} torpedoes.";
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'YNYY':
              $action = "Pick up all fighters and drop off all torpedoes.";
              break;
            case 'YNYN':
              if($defense_torpedoes > 0) {
                $action = "Pick up all fighters and drop off {$defense_torpedoes} torpedoes.";
              } else {
                $action = "Pick up all fighters.";
              }
              break;
            case 'YNNY':
              if($defense_fighters > 0) {
                $action = "Pick up {$defense_fighters} fighters and drop off all torpedoes.";
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'YNNN':
              if($defense_fighters > 0) {
                if($defense_torpedoes > 0) {
                  $action = "Pick up {$defense_fighters} fighters and drop off {$defense_torpedoes} torpedoes.";
                } else {
                  $action = "Pick up {$defense_fighters} fighters.";
                }
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'NYYY':
              $action = "Drop off all fighters and pick up all torpedoes.";
              break;
            case 'NYYN':
              if($defense_torpedoes > 0) {
                $action = "Drop off all fighters and pick up {$defense_torpedoes} torpedoes.";
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'NYNY':
              if($defense_fighters > 0) {
                $action = "Drop off {$defense_fighters} fighters and pick up all torpedoes.";
              } else {
                $action = "Pick up all torpedoes.";
              }
              break;
            case 'NYNN':
              if($defense_torpedoes > 0) {
                if($defense_fighters > 0) {
                  $action = "Drop off {$defense_fighters} fighters and pick up {$defense_torpedoes} torpedoes.";
                } else {
                  $action = "Pick up {$defense_torpedoes} torpedoes.";
                }
              } else {
                // This situation should've been caught when the step was set up.
                trigger_error($bug_msg,E_USER_ERROR);
              }
              break;
            case 'NNYY':
              $action = "Drop off all fighters and torpedoes.";
              break;
            case 'NNYN':
              if($defense_torpedoes > 0) {
                $action = "Drop off all fighters and {$defense_torpedoes} torpedoes.";
              } else {
                $action = "Drop off all fighters.";
              }
              break;
            case 'NNNY':
              if($defense_fighters > 0) {
                $action = "Drop off {$defense_fighters} fighters and all torpedoes.";
              } else {
                $action = "Drop off all torpedoes.";
              }
              break;
            case 'NNNN':
              if($defense_fighters > 0) {
                if($defense_torpedoes > 0) {
                  $action = "Drop off {$defense_fighters} fighters and {$defense_torpedoes} torpedoes.";
                } else {
                  $action = "Drop off {$defense_fighters} fighters.";
                }
              } else {
                if($defense_torpedoes > 0) {
                  $action = "Drop off {$defense_torpedoes} torpedoes.";
                } else {
                  // This situation should've been caught when the step was set up.
                  trigger_error($bug_msg,E_USER_ERROR);
                }
              }
              break;
            default:
              // there's a bug in the switch setup
              trigger_error($bug_msg,E_USER_ERROR);
          }
          break;
        case 'planet':
          $drop_off = array();
          $drop_off_all = array();
          $pick_up = array();
          $pick_up_all = array();
          $planet_material = substr($query->fields['arguments'],0,1);
          if($planet_material == 'E') {
            $drop_off_all[] = 'ore';
            $drop_off_all[] = 'organics';
            $drop_off_all[] = 'goods';
            $drop_off_all[] = 'colonists';
          } elseif($planet_material != 'N') {
            if($planet_material == 'O') {
              $pick_up_all[] = 'ore';
            } else {
              $drop_off_all[] = 'ore';
            }
            if($planet_material == 'R') {
              $pick_up_all[] = 'organics';
            } else {
              $drop_off_all[] = 'organics';
            }
            if($planet_material == 'G') {
              $pick_up_all[] = 'goods';
            } else {
              $drop_off_all[] = 'goods';
            }
            if($planet_material == 'C') {
              $pick_up_all[] = 'colonists';
            } else {
              $drop_off_all[] = 'colonists';
            }
          }
          list($planet_energy, $planet_credits, $planet_fighters, $planet_torpedoes, $planet_id) =
            explode(',',substr($query->fields['arguments'],9));
          if(substr($query->fields['arguments'],1,1) == 'Y') {
            if(substr($query->fields['arguments'],5,1) == 'Y') {
              $pick_up_all[] = "energy";
            } elseif($planet_energy > 0) {
              $pick_up[] = "{$planet_energy} energy";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          } else {
            if(substr($query->fields['arguments'],5,1) == 'Y') {
              $drop_off_all[] = "energy";
            } elseif($planet_energy > 0) {
              $drop_off[] = "{$planet_energy} energy";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          }
          if(substr($query->fields['arguments'],2,1) == 'Y') {
            if(substr($query->fields['arguments'],6,1) == 'Y') {
              $pick_up_all[] = "credits";
            } elseif($planet_credits > 0) {
              $pick_up[] = "{$planet_credits} credits";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          } else {
            if(substr($query->fields['arguments'],6,1) == 'Y') {
              $drop_off_all[] = "credits";
            } elseif($planet_credits > 0) {
              $drop_off[] = "{$planet_credits} credits";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          }
          if(substr($query->fields['arguments'],3,1) == 'Y') {
            if(substr($query->fields['arguments'],7,1) == 'Y') {
              $pick_up_all[] = "fighters";
            } elseif($planet_fighters > 0) {
              $pick_up[] = "{$planet_fighters} fighters";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          } else {
            if(substr($query->fields['arguments'],7,1) == 'Y') {
              $drop_off_all[] = "fighters";
            } elseif($planet_fighters > 0) {
              $drop_off[] = "{$planet_fighters} fighters";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          }
          if(substr($query->fields['arguments'],4,1) == 'Y') {
            if(substr($query->fields['arguments'],8,1) == 'Y') {
              $pick_up_all[] = "torpedoes";
            } elseif($planet_torpedoes > 0) {
              $pick_up[] = "{$planet_torpedoes} torpedoes";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          } else {
            if(substr($query->fields['arguments'],8,1) == 'Y') {
              $drop_off_all[] = "torpedoes";
            } elseif($planet_torpedoes > 0) {
              $drop_off[] = "{$planet_torpedoes} torpedoes";
            } else {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            }
          }
          $drop_off = implode(", ", $drop_off);
          $drop_off = str_replace(",", " and", $drop_off);
          $drop_off_all = implode(", ", $drop_off_all);
          $drop_off_all = str_replace(",", " and", $drop_off_all);
          $pick_up = implode(", ", $pick_up);
          $pick_up = str_replace(",", " and", $pick_up);
          $pick_up_all = implode(", ", $pick_up_all);
          $pick_up_all = str_replace(",", " and", $pick_up_all);
          if(empty($pick_up)) {
            if(!empty($pick_up_all)) {
              $pick_up = $pick_up_all;
            }
          } else {
            if(!empty($pick_up_all)) {
              $pick_up .= " as well as all {$pick_up_all}";
            }
          }
          if(empty($drop_off)) {
            if(!empty($drop_off_all)) {
              $drop_off = $drop_off_all;
            }
          } else {
            if(!empty($drop_off_all)) {
              $drop_off .= " as well as all {$drop_off_all}";
            }
          }
          if(empty($drop_off)) {
            if(empty($pick_up)) {
              // This situation should've been caught when the step was set up.
              trigger_error($bug_msg,E_USER_ERROR);
            } else {
              $action = "pick up {$pick_up}";
            }
          } else {
            if(empty($pick_up)) {
              $action = "drop off {$drop_off}";
            } else {
              $action = "drop off {$drop_off} and pick up {$pick_up}";
            }
          }
          $query = $db->Execute("SELECT name,sector_id FROM {$dbtables['planets']} WHERE planet_id={$planet_id} LIMIT 1");
          $action = "On planet, \"{$query->fields['name']},\" in sector {$query->fields['sector_id']}, {$action}.";
          break;

        default:
          // nothing else should've made it into the database
          trigger_error($bug_msg,E_USER_ERROR);
      }
      $temp = $html_steps_item;
      $temp = str_replace(array('[step_id]',
                                '[action]'),
                          array($query->fields['step_id'],
                                $action),
                          $temp);
      echo $temp;
      unset($action);

      $query->MoveNext();
    }
  }
  echo $html_steps_footer;

  // show some links so the user can get off this page
  echo "<p>Click <a href=\"routes.php\">here</a> to list available routes.";
} else { // $route_id is not set
  // try to show a list of routes
  echo $html_routes_header;
  $query = $db->Execute("SELECT t2.*,t3.sector_id AS location,COUNT(t4.route_id) as step_count FROM {$dbtables['players']} AS t1 LEFT JOIN {$dbtables['routes_headers']} AS t2 ON t1.player_id=t2.player_id LEFT JOIN {$dbtables['ships']} AS t3 ON t2.player_id=t3.player_id LEFT JOIN {$dbtables['routes_steps']} as t4 ON t2.route_id=t4.route_id WHERE t1.email='{$username}' GROUP BY t2.route_id");
  // should always return at least one record
  if(!$query) {
    echo $db->ErrorMsg();
    trigger_error($bug_msg,E_USER_ERROR);
  }
  // if the routes_headers fields are NULL, then there aren't any routes
  if($query->fields['route_id']) { // if route_id is not null...
    // list the routes
    $i=1;
    while(!$query->EOF) {
      $temp = $html_routes_item;
      $temp = str_replace(array('[route_id]',
                                '[submit]',
                                '[sector]',
                                '[name]'),
                          array($query->fields['route_id'],
                                ($query->fields['location']==$query->fields['sector_id']
                                  && $query->fields['step_count'] > 0)
                                    ? $html_routes_submit
                                    : "&nbsp;",
                                $query->fields['sector_id'],
                                $query->fields['route_name']),
                          $temp);
      echo $temp;
      $query->MoveNext();
      $i++;
    }
    if($i<$max_traderoutes_player) {
      echo $html_routes_new;
    }
  } else { // route_id is null...
    // tell the user there's no routes to list
    echo $html_routes_none;
    echo $html_routes_new;
  }
  echo $html_routes_footer;
}

// show some links so the user can get off this page
echo "<p>Click <a href=\"main.php\">here</a> to return to the main menu.";

// footer stuff after this line

include("footer.php");

?>
