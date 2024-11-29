<?php
/*
 * This file is part of the pool project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * config.inc.php created on 12.09.23, 22:55.
 *
 * The file provides the following constants:
 *
 * DIR_DOCUMENT_ROOT (string) = (absolute path) refers to the base directory of the PHP sources
 * DIR_RELATIVE_DOCUMENT_ROOT (string) = (relative path) is only used internally in the configs.
 *
 * DIR_POOL_ROOT (string) = (absoluter) Pfad zeigt direkt auf den POOL
 * DIR_RELATIVE_POOL_ROOT (string) = (relativer) Pfad zeigt direkt auf den POOL
 *
 * DIR_DAOS_ROOT (string) = (absoluter) Pfad zeigt direkt auf das DAOS Verzeichnis
 *
 * DIR_DATA_DIR (string) = (absoluter pfad) zeigt auf das data Verzeichnis. App contents.
 * DIR_RESOURCES_DIR (string) = absoluter Pfad, zeigt auf das resources Verzeichnis. App resources.
 *
 * IS_TESTSERVER (boolean) = gibt an, ob es sich einen Testrechner handelt.
 *
 * ############################################################################################################################
 */
define('POOL_START', microtime(true));

require_once 'Stage.php';

use filemigo\config\Stage;

/** Server configs **/
$serverName = ($_SERVER['SERVER_NAME'] ??= gethostname());
switch($serverName) {
    case 'dev.local':
        #Develop box
        $stage = Stage::develop;
        $relativeRoot = '..';
        $SQL_Host = 'localhost';
        $defaultSessionDuration = 14400;//4h
        break;
    default:

}

//Config using Server environment
$baseNamespacePath ??= $_SERVER['_BaseNamespacePath'] ?? $_SERVER['DOCUMENT_ROOT'] ??
    die('Missing Config Parameter _BaseNamespacePath in Server Environment');
//echo $baseNamespacePath;
$relativeRoot ??= $_SERVER['_RelativeRoot'] ??
    die('Missing Config Parameter _RelativeRoot in Server Environment');
$SQL_Host ??= $_SERVER['_SQL_Host'] ??
    die('Missing Config Parameter _SQL_Host in Server Environment');
$stage ??= Stage::fromString($_SERVER['_Stage'] ?? 'production');
$defaultSessionDuration ??= $_SERVER['_DefaultSessionDuration'] ?? 1800;

// check if we are in command line mode
if(!defined('IS_CLI')) {
    define('IS_CLI', PHP_SAPI === 'cli');
}

//export to constants
define('DIR_DOCUMENT_ROOT', $baseNamespacePath);
define('DIR_RELATIVE_DOCUMENT_ROOT', $relativeRoot);
define('MYSQL_HOST', $SQL_Host);
define('IS_DEVELOP', $stage === Stage::develop);
define('IS_STAGING', $stage === Stage::staging);
define('IS_PRODUCTION', $stage === Stage::production);

define('DEFAULT_SESSION_LIFETIME', $defaultSessionDuration);
const IS_TESTSERVER = (IS_DEVELOP || IS_STAGING);
// Pool
const DIR_DATA_ROOT = DIR_DOCUMENT_ROOT . '/data';
const DIR_RELATIVE_DATA_ROOT = DIR_RELATIVE_DOCUMENT_ROOT . '/data';
const DIR_RESOURCES_ROOT = DIR_DOCUMENT_ROOT . '/resources';

const DIR_POOL_ROOT = '/virtualweb/pool';