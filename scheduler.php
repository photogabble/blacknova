<?php
// Copyright (C) 2001 Ron Harwood and L. Patrick Smallwood
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
// File: scheduler.php
//
// Explanation of the scheduler
//
// Here are the scheduler DB fields, and what they are used for:
// - sched_id : Unique ID. Before calling the file responsible
//   for the event, the variable $sched_var_id will be set to
//   this value, so the called file can modify the triggering
//   scheduler entry if it needs to.
//
// - ticks_left : Used internally by the scheduler. It represents
//   the number of mins elapsed since the last call. ALWAYS set
//   this to 0 when scheduling a new event.
//
// - ticks_full : This is the timer in minutes between
//   different runs of your event. Set this to the frenquency
//   you wish the event to happen. For example, if you want your
//   event to be run every three minutes, set this to 3.
//
// - file : This is the file that will be called when an event
//   has been trigerred.
//
// - extra_info : This is a text variable that can be used to
//   store any extra information concerning the event triggered.
//   It will be made available to the called file through the
//   variable $sched_var_extrainfo.
//
// If you are including files in your trigger file, it is important
// to use include_once() instead of include(), as your file might
// be called multiple times in a single execution. If you need
// functions, you can put them in your own
// include file, with an include statement. THEY CANNOT BE
// INCLUDED IN YOUR MAIN FILE BODY. This would cause PHP to issue
// multiple function declaration error.
//

require_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "seed_mt_rand.php");

// Load language variables
load_languages($db, $raw_prefix, 'scheduler');

$title = $l_scheduler_title;

// Templates
$template = new bnt_smarty;

echo "<h1>" . $title. "</h1>\n";

$sf = (bool) ini_get('safe_mode');
if (!$sf)
{
    set_time_limit(0);
}

seed_mt_rand();

$stoptime = $BenchmarkTimer->stop();
$elapsed = $BenchmarkTimer->elapsed();
$elapsed = substr($elapsed,0,5);

$game_query = $db->Execute("SELECT gamenumber FROM {$raw_prefix}instances ORDER BY gamenumber ASC");
db_op_result($db,$game_query,__LINE__,__FILE__);
while (!$game_query->EOF)
{
    echo "Game # " . $game_query->fields['gamenumber'] . "<br>";
    $_POST['gamenum'] = $game_query->fields['gamenumber']; // Set the game number so that global cleanups can set the correct game number.
    include_once './global_cleanups.php'; // Get the config variables like server closed, etc so the scheduler file and the called files have access.

    if (!$server_closed)
    {
        $sched_res = $db->Execute("SELECT * FROM {$db->prefix}scheduler");
        if ($sched_res)
        {
            while (!$sched_res->EOF)
            {
                echo "<br><br> NEW EVENT";
                $event = $sched_res->fields;
                $event['last_run'] = $db->UnixTimeStamp($event['last_run']);

                echo $event['last_run'] . " last run<br>";
                echo time(). " current time<br>";
                echo $event['timer'] . "<br>";
                echo (time() - $event['last_run']);

                if ((time() - $event['last_run']) >= $event['timer'])
                {
                    echo "Running! It has now been " . ((time() - $event['last_run']) /60) . " minutes since last run of $event[sched_file].";
                    echo "\n<br><br>";
                    echo "This scheduler timed to trigger after " . $event['timer'] . " minutes<br>";
                    $multiplier = floor (strtotime($event['last_run'], time()) / 60);
                    $sched_var_id = $event['sched_id'];
                    $sched_var_extrainfo = $event['extra_info'];
                    $result = @include ("./$event[sched_file]");
                    if ($result != 1)
                    {
                        echo "Error loading scheduler file: " . $event['sched_file'];
                    }
                    $stamp = date("Y-m-d H:i:s");
                    $debug_query = $db->Execute("UPDATE {$db->prefix}scheduler SET last_run='$stamp' WHERE sched_id=$event[sched_id]");
                    db_op_result($db,$debug_query,__LINE__,__FILE__);
                }
                else
                {
                    echo "Not running. It has only been " . floor(strtotime($event['last_run'], time())) . " minutes since last scheduler run.";
                    echo "\n<br><br>";
                    echo "This scheduler timed to trigger after " . $event['timer'] . " minutes";
                    // DONT run.
                }

                $sched_res->MoveNext();
                echo "<br>Elapsed time so far: " . $elapsed . "<br><br>";
            }
        }

        if ($sched_type != 1)
        {
            $stamp = date("Y-m-d H:i:s");
            $debug_query = $db->Execute("UPDATE {$db->prefix}scheduler SET last_run='$stamp'");
            db_op_result($db,$debug_query,__LINE__,__FILE__);
        }
    }
    else
    {
        echo "Game is closed";
    }

    $game_query->MoveNext();
}

include_once './footer.php';
?>
