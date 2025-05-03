<?php
/**
 * Copyright (C) 2025 schmidseder.net
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace filemigo;

use Exception;

use filemigo\classes\FilemigoApp;
use filemigo\guis\GUI_Frame\GUI_Frame;
use filemigo\guis\GUI_Login\GUI_Login;

//    if (IS_TESTSERVER) {
//        ini_set('session.gc_maxlifetime', 10);
//        ini_set('session.cookie_lifetime', 10);
//    }

const APPLICATION_NAME = 'filemigo';
const APPLICATION_DIR = __DIR__;

if (!getenv('_RelativeRoot') ) {
    putenv('_RelativeRoot=..');
}

if (!getenv('_SQL_Host') ) {
    putenv('_SQL_Host=localhost');
}

const DIR_CONFIGS_ROOT = __DIR__ . '/config';

require_once DIR_CONFIGS_ROOT . '/config.inc.php';
require_once '../pool/pool.lib.php';

if (!file_exists(DIR_CONFIGS_ROOT . '/filemigo.inc.php')) {
    die ('Please rename `config/example-filemigo.inc.php` to `config/filemigo.inc.php` and adjust the configurations within it.');
}

# set default values for environment variables
if (!getenv('filemigo_data') ) {
    $data = realpath(DIR_DOCUMENT_ROOT . '/../data');
    if ($data) {
        putenv('filemigo_data=' . $data);
    }
}
if (!getenv('filemigo_zip') ) {
    $tmp = realpath(DIR_DOCUMENT_ROOT . '/../tmp');
    if ($tmp) {
        putenv('filemigo_zip=' . $tmp);
    }
}

FilemigoApp::caching(IS_PRODUCTION);

$App = FilemigoApp::getInstance();
$App->startPHPSession();

$loggedIn = $App->Session->getVar('loggedIn', false);

define("filemigo\ENVVAR_FILEMIGO_DATA", getenv('filemigo_data'));
define("filemigo\ENVVAR_FILEMIGO_ZIP", getenv('filemigo_zip'));
const FILEMIGO_ALL_DATA = null;
const FILEMIGO_NO_DATA = [];

# global config
$config = require DIR_CONFIGS_ROOT . '/filemigo.inc.php';

# set launch module
$launchModule = GUI_Login::class;
if ($loggedIn) {
    # set launch module for loggedIn users
    $launchModule = GUI_Frame::class;

    # user config
    $usersConfigDir = 'users';
    if (is_dir(addEndingSlash(DIR_CONFIGS_ROOT) . $usersConfigDir)) {
        $loggedInUser = $App->Session->getVar('loggedInUser');
        $userConfigFile = addEndingSlash(DIR_CONFIGS_ROOT) .  addEndingSlash($usersConfigDir) . "$loggedInUser.inc.php";
        if (is_file($userConfigFile)) {
            $userConfig =  require $userConfigFile;

            if (is_array($userConfig)) {
                $config = array_merge($config, $userConfig);
            }
        }
    }
}

# set config
$App->setConfig($config);

$settings = [
    'application.name'          => APPLICATION_NAME,
    'application.title'         => $App->getConfigValue('FMG_TITLE','Unkwown'),
    'application.launchModule'  => $launchModule,
    // 'memcached.servers' => 'memcached:11211'
];

$memcachedServers = getenv('filemigo_memcached');
if ($memcachedServers) {
    $settings['memcached.servers'] = $memcachedServers;
}

try {
    $App->setup($settings);
    $App->render();
} catch (Exception $e) {
    throw $e;
}