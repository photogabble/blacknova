<?php
// Blacknova Traders - A web-based massively multiplayer space
// combat and trading game
// Copyright (C) 2001-2014 Ron Harwood and the BNT development team
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU Affero General Public License as
//  published by the Free Software Foundation, either version 3 of the
//  License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU Affero General Public License for more details.
//
//  You should have received a copy of the GNU Affero General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// File: admin.php

require_once './common.php';
require_once './config/admin_config.php';

// Database driven language entries
$langvars = Bnt\Translate::load($pdo_db, $lang, array('admin', 'common',
                                'global_includes', 'global_funcs', 'combat',
                                'footer', 'news', 'report', 'main', 'zoneedit',
                                'planet'));
$title = $langvars['l_admin_title'];

function checked($yesno)
{
    return (($yesno == 'Y') ? 'checked' : '');
}

// We only want menu values that come from $_POST, and only want string values.
$menu = filter_input(INPUT_POST, 'menu', FILTER_SANITIZE_STRING);
$swordfish  = filter_input(INPUT_POST, 'swordfish', FILTER_SANITIZE_URL);
$filename = null;
$menu_location = null;
$button_main = false;

// Clear variables array before use, and set array with all variables in page
$variables = null;

$variables['is_admin'] = false;
$variables['module'] = null;

if ($swordfish == ADMIN_PW)
{
    $i = 0;
    $variables['is_admin'] = true;
    $option_title = array();
    $admin_dir = new DirectoryIterator('admin/');
    // Get a list of the files in the admin directory
    foreach ($admin_dir as $file_info)
    {
        // If it is a PHP file, add it to the list of accepted admin files
        if ($file_info->isFile() && $file_info->getExtension() == 'php')
        {
            $i++; // Increment counter so we know how many files there are
            // Actual file name
            $filename[$i]['file'] = $file_info->getFilename();

            // Set option title to lang string of the form l_admin + file name
            $option_title = 'l_admin_' . mb_substr($filename[$i]['file'], 0, -4);

            if (isset($langvars[$option_title]))
            {
                // The language translated title for option
                $filename[$i]['option_title'] = $langvars[$option_title];
            }
            else
            {
                // The placeholder text for a not translated module
                $filename[$i]['option_title'] = $langvars['l_admin_new_module'] . $filename[$i]['file'];
            }

            if (!empty ($menu))
            {
                if ($menu == $filename[$i]['file'])
                {
                    $button_main = true;
                    $module_name = mb_substr($filename[$i]['file'], 0, -4);
                    include_once './admin/'. $filename[$i]['file'];
                }
            }
        }
    }
}

$variables['body_class'] = 'admin';
$variables['lang'] = $lang;
$variables['swordfish'] = $swordfish;
$variables['linkback'] = array('fulltext' => $langvars['l_global_mmenu'], 'link' => 'main.php');
$variables['menu'] = $menu;
$variables['filename'] = $filename;
$variables['menu_location'] = $menu_location;
$variables['button_main'] = $button_main;

// Set a container for variables & langvars & send them to the template system
//$variables['container'] = "variable";
//$langvars['container'] = "langvar";

// Pull in footer variables from footer_t.php
require_once './footer_t.php';
$langvars = Bnt\Translate::load($pdo_db, $lang, array('admin', 'common',
                                'global_includes', 'global_funcs', 'combat',
                                'footer', 'news', 'report', 'main', 'zoneedit',
                                'planet'));
$template->addVariables('langvars', $langvars);
$template->addVariables('variables', $variables);
$template->display('admin.tpl');
?>
