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

const DIR_CONFIGS_ROOT = __DIR__ . '/config';

require_once DIR_CONFIGS_ROOT . '/config.inc.php';
require_once '../pool/pool.lib.php';

if (!file_exists(DIR_CONFIGS_ROOT . '/filemigo.inc.php')) {
    die ('Please rename `config/example-filemigo.inc.php` to `config/filemigo.inc.php` and adjust the configurations within it.');
}
$config = require DIR_CONFIGS_ROOT . '/filemigo.inc.php';

//    if (IS_TESTSERVER) {
//        ini_set('session.gc_maxlifetime', 10);
//        ini_set('session.cookie_lifetime', 10);
//    }

use Exception;

use filemigo\classes\FilemigoApp;
use filemigo\guis\GUI_Frame\GUI_Frame;
use filemigo\guis\GUI_Login\GUI_Login;

FilemigoApp::caching(IS_PRODUCTION);

$App = FilemigoApp::getInstance();
$App->startPHPSession();
$App->setConfig($config);

$loggedIn = $App->Session->getVar('loggedIn', false);
$launchModule = $loggedIn ? GUI_Frame::class : GUI_Login::class;

$settings = [
    'application.name' => 'filemigo',
    'application.title' => 'Filemigo - Simple Web File Browser',
    'application.launchModule' => $launchModule,
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