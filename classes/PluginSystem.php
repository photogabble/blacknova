<?php
// Blacknova Traders - A web-based massively multiplayer space combat and trading game
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
// File: classes/PluginSystem.php

namespace Bnt;

// Load up the list of supported Events.
require_once 'eventsystem/event_list.php';

class PluginSystem
{
    public static $version                         = '0.0.5 (0010) Alpha';
    public static $author                          = 'Blacknova Development';

    private static $callbackfunc            = 'onEvent';
    private static $db                      = null;
    private static $events                  = null;
    private static $pluginlist              = null;

    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    public static function initialize($db = null)
    {
        self::$events = array();
        self::$db = $db;
        self::$pluginlist = array();
    }

    public static function loadPlugins()
    {
        global $plugin_config;
        $d = dir('./plugins');

        while (false !== ($entry = $d->read()))
        {
            if (is_dir("{$d->path}/{$entry}") && $entry != '.' && $entry != '..')
            {
                $plugin_name = $entry;
                if (file_exists("{$d->path}/{$plugin_name}/plugin_config.php"))
                {
                    require_once("{$d->path}/{$plugin_name}/plugin_config.php");
                    if (isset($plugin_config[$pluginname]['enabled']) && $plugin_config[$pluginname]['enabled'] == true)
                    {
                        if (isset($plugin_config[$pluginname]['has_loader']) && $plugin_config[$pluginname]['has_loader'] == true)
                        {
                            if (file_exists("{$d->path}/{$plugin_name}/plugin_loader.php"))
                            {
                                global $$pluginname;
                                require_once("{$d->path}/{$plugin_name}/plugin_loader.php");

                                self::$pluginlist[$pluginname] = new $pluginname();
                                self::$pluginlist[$pluginname]->initialize(self::$db);
                            }
                        }
                        unset($plugin_config[$pluginname]);
                    }
                    else
                    {
                        unset($plugin_config[$pluginname]);
                    }
                }
            }
        }
        $d->close();
    }

    public static function getPluginInfo($plugin = null)
    {
        if (is_null($plugin))
        {
            return (array) self::$pluginlist;
        }
        else
        {
            if (array_key_exists($plugin, self::$pluginlist))
            {
                return (array) self::$pluginlist[$plugin];
            }
        }
    }

    public static function addEventHook($event = null, Plugin $callback = null)
    {
        if (!is_numeric($event) && !is_null($event))
        {
            $event = constant($event);
        }

        if (is_null($event) || !is_numeric($event))
        {
            AdminLog::writeLog(self::$db, LOG_RAW, 'Plugin Error on line ('. __LINE__ .'): invalid event ID.');

            return (boolean) false;
        }

        if (is_null($callback) || !is_object($callback))
        {
            AdminLog::writeLog(self::$db, LOG_RAW, 'Plugin Error on line ('. __LINE__ .'): invalid callback.');

            return (boolean) false;
        }

        // Check if the callback class::function exists and is callable.
        if (!method_exists($callback, self::$callbackfunc) || !is_callable(array($callback, self::$callbackfunc), false, $callable_name))
        {
            AdminLog::writeLog(self::$db, LOG_RAW, 'Plugin Error on line ('. __LINE__ .'): ' . get_class($callback) .'::'. self::$callbackfunc .' function doesn\'t exist or it isn\'t callable.');

            return (boolean) false;
        }

        if (!isset(self::$events[$event]))
        {
            self::$events[$event] = array();
        }
        array_push(self::$events[$event], $callback);

        return (boolean) true;
    }

    public static function removeEventHook($event = null, Plugin $callback = null)
    {
        if (!array_key_exists($event, self::$events) || !in_array($callback, self::$events[$event]))
        {
            AdminLog::writeLog(self::$db, LOG_RAW, 'Plugin Error on line ('. __LINE__ .'): Cannot find supplied Event.');

            return (boolean) false;
        }

        $index = array_search($callback, self::$events[$event]);
        array_splice(self::$events[$event], $index, 1);
    }

    public static function listEventHooks($event = null)
    {
        if (is_null($event))
        {
            return (array) self::$events;
        }
        else
        {
            if (!is_numeric($event))
            {
                $event = constant($event);
            }

            return (array) self::$events[$event];
        }
    }

    public static function raiseEvent($event = null, $args = array())
    {
        if (!is_numeric($event))
        {
            $event = constant($event);
        }

        if (is_null($event) || !is_int($event))
        {
            AdminLog::writeLog(self::$db, LOG_RAW, 'Plugin Error on line ('. __LINE__ .'): invalid event ID.');

            return (boolean) false;
        }

        foreach (self::$events[$event] as $hook)
        {
            if (method_exists($hook, self::$callbackfunc))
            {
                call_user_func_array(array($hook, self::$callbackfunc), $args);
            }
            else
            {
                AdminLog::writeLog(self::$db, LOG_RAW, 'Plugin Error on line ('. __LINE__ .'): Invalid Hook.');
            }
        }
    }
}
?>
