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
// File: navcomp.php

include_once './global_includes.php';

// Dynamic functions
dynamic_loader ($db, "checklogin.php");
dynamic_loader ($db, "get_info.php");
dynamic_loader ($db, "checkdead.php");
dynamic_loader ($db, "updatecookie.php");

// Load language variables
load_languages($db, $raw_prefix, 'navcomp');
load_languages($db, $raw_prefix, 'global_includes');
load_languages($db, $raw_prefix, 'common');

checklogin($db);
get_info($db);
checkdead($db);

$title = $l_nav_title;
updatecookie($db);
include_once './header.php';

if (!isset($_POST['state']))
{
    $_POST['state'] = '';
}

if ($allow_navcomp)
{
    $computer_tech  = $shipinfo['computer'];

    // Without these here.  You will receive warnings.
    $search_results = NULL;
    $links = NULL;
    $search_depth = NULL;
    if ($_POST['state'] == 1 && ($shipinfo['sector_id'] != $_POST['destination']))
    {
        $max_search_depth = round($computer_tech / 14);
        if ($max_search_depth > 7) // Anything over 7 puts a HUGE load on the db, and takes forever to complete.
        {
            $max_search_depth = 7;
        }
        elseif ($max_search_depth < 3) // You have to ensure the nav computer is at least functional at low computer levels
        {
            $max_search_depth = 3;
        }

        $prelim_query = "SELECT * FROM {$db->prefix}links WHERE link_dest = $_POST[destination]";
        $debug_query = $db->SelectLimit($prelim_query) or die ("Invalid Query");
        $prelim_count = $debug_query->RecordCount();
        if ($prelim_count) // If there are no links to the destination, it cant be reached via link.
        {
            for ($search_depth = 1; $search_depth <= $max_search_depth; $search_depth++)
            {
                $search_query = "SELECT a1.link_start , a1.link_dest \n";
                for ($i = 2; $i<=$search_depth; $i++)
                {
                    $search_query = $search_query . " ,a". $i . ".link_dest \n";
                }

                $search_query = $search_query . "FROM {$db->prefix}links AS a1 \n";
                for ($i = 2; $i<=$search_depth; $i++)
                {
                    $search_query = $search_query . " ,{$db->prefix}links AS a". $i . " \n";
                }

                $search_query = $search_query . "WHERE a1.link_start = $shipinfo[sector_id] \n";
                for ($i = 2; $i<=$search_depth; $i++)
                {
                    $k = $i-1;
                    $search_query = $search_query . " AND a" . $k . ".link_dest = a" . $i . ".link_start \n";
                }

                $search_query = $search_query . " AND a" . $search_depth . ".link_dest = $_POST[destination] \n";
                for ($i=2; $i<=$search_depth; $i++)
                {
                    $search_query = $search_query . " AND a" . $i . ".link_dest not in (a1.link_dest, a1.link_start ";
                    for ($j=2; $j<$i; $j++)
                    {
                        $search_query = $search_query . ",a".$j.".link_dest ";
                    }

                    $search_query = $search_query . ")\n";
                }

                $search_query = $search_query . "ORDER BY a1.link_start, a1.link_dest ";
                for ($i=2; $i<=$search_depth; $i++)
                {
                    $search_query = $search_query . ", a" . $i . ".link_dest";
                }

                // Okay, this is tricky. We need the db returns to be numeric, not associative, so that we
                // can get a count from it. A good page on it is here: php.weblogs.com/adodb_tutorial .
                // We also dont need to set it BACK to the game default, because each page sets it again (by calling config).
                // If someone can think of a way to recode this to not need this line, I would deeply appreciate it!

                $debug_query = $db->SelectLimit($search_query,1) or die ("Invalid Query");
                //$debug_query = $db->Execute ($search_query) or die ("Invalid Query");
                $found = $debug_query->RecordCount();
                if ($found > 0)
                {
                    break;
                }
            }

            if ($found > 0)
            {
                $links = $debug_query->fields;
                $search_results = '';
                for ($i=1; $i<$search_depth+1; $i++)
                {
                    if ($i==1)
                    {
                        $search_results = $search_results . " >> " . "<a href=move.php?move_method=warp&amp;destination=$links[$i]>$links[$i]</a>";
                    }
                    else
                    {
                        $search_results = $search_results . " >> " . $links[$i];
                    }
                }
            }
        }
    }

    if ((!isset($found)) || ($found == ''))
    {
        $found = '';
    }

    global $l_global_mmenu;
    $template->assign("title", $title);
    $template->assign("l_global_mmenu", $l_global_mmenu);
    $template->assign("l_nav_nocomp", $l_nav_nocomp);
    $template->assign("allow_navcomp", $allow_navcomp);
    $template->assign("l_submit", $l_submit);
    $template->assign("state", $_POST['state']);
    $template->assign("search_results", $search_results);
    $template->assign("start_sector", $links[0]);
    $template->assign("found", $found);
    $template->assign("search_depth", $search_depth);
    $template->assign("l_nav_pathfnd", $l_nav_pathfnd);
    $template->assign("l_nav_answ1", $l_nav_answ1);
    $template->assign("l_nav_answ2", $l_nav_answ2);
    $template->assign("l_nav_proper", $l_nav_proper);
    $template->assign("l_nav_query", $l_nav_query);
    $template->display("$templateset/navcomp.tpl");
}

include_once './footer.php';
?>
