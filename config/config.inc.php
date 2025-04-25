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

/*
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
 */

define('POOL_START', microtime(true));

// Autoloading & Imports
require_once 'Stage.php';

use filemigo\config\Stage;

/**
 * -------------------------------
 * Server Environment Detection
 * -------------------------------
 */
$serverName = $_SERVER['SERVER_NAME'] ??= gethostname();

switch ($serverName) {
    case 'localhost':
    case 'dev.local':
        // Development environment
        $stage = Stage::develop;
        $relativeRoot = '..';
        $SQL_Host = 'localhost';
        $defaultSessionDuration = 14400; // 4 hours
        break;

        // Production environment
    case 'your-domain.com':
        $stage = Stage::production;
        $relativeRoot = '..';
        $SQL_Host = 'localhost';
        $defaultSessionDuration = 1800;
        break;

    default:
        // Fallbacks via environment below
        break;
}

/**
 * -------------------------------
 * Configuration from Environment
 * -------------------------------
 */
// Setzt den Basis-Namespace-Pfad, entweder aus der Server-Variable _BaseNamespacePath,
// oder aus dem DOCUMENT_ROOT (z.B. bei Apache),
// andernfalls wird das Skript mit einer Fehlermeldung beendet.
$baseNamespacePath ??= $_SERVER['_BaseNamespacePath']
    ?? $_SERVER['DOCUMENT_ROOT']
    ?? die('Missing Config Parameter _BaseNamespacePath in Server Environment');

// Setzt den relativen Pfad zum Projekt (z.B. für URLs), oder beendet bei fehlender Angabe.
$relativeRoot ??= $_SERVER['_RelativeRoot']
    ?? die('Missing Config Parameter _RelativeRoot in Server Environment');

// Setzt den Hostnamen der MySQL-Datenbank, oder beendet bei fehlender Angabe.
$SQL_Host ??= $_SERVER['_SQL_Host']
    ?? die('Missing Config Parameter _SQL_Host in Server Environment');

// Setzt die aktuelle Stage (Entwicklungs-, Staging- oder Produktionsumgebung).
// Fallback ist 'production', wenn nichts angegeben ist.
$stage ??= Stage::fromString($_SERVER['_Stage'] ?? 'production');

// Setzt die Session-Laufzeit in Sekunden. Fallback: 1800 Sekunden (30 Minuten)
$defaultSessionDuration ??= $_SERVER['_DefaultSessionDuration'] ?? 1800;

// Setzt das Root-Verzeichnis des "Pools", entweder aus Server-Variable,
// oder als Unterverzeichnis von $baseNamespacePath
$PoolRoot ??= $_SERVER['_PoolRoot'] ?? $baseNamespacePath . '/pool';


/**
 * -------------------------------
 * CLI Mode Detection
 * -------------------------------
 */
if (!defined('IS_CLI')) {
    define('IS_CLI', PHP_SAPI === 'cli');
}

/**
 * -------------------------------
 * Constant Exports
 * -------------------------------
 */
define('DIR_DOCUMENT_ROOT', $baseNamespacePath);
define('DIR_RELATIVE_DOCUMENT_ROOT', $relativeRoot);
define('MYSQL_HOST', $SQL_Host);

define('IS_DEVELOP', $stage === Stage::develop);
define('IS_STAGING', $stage === Stage::staging);
define('IS_PRODUCTION', $stage === Stage::production);

define('DEFAULT_SESSION_LIFETIME', $defaultSessionDuration);
define('DIR_POOL_ROOT', $PoolRoot);

/**
 * -------------------------------
 * Constant Exports (for the application)
 * -------------------------------
 */
const IMAGE_FILE_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg', 'apng', 'avif', 'bmp', 'ico'];

// Combined environment flag
const IS_TESTSERVER = (IS_DEVELOP || IS_STAGING);

/**
 * -------------------------------
 * POOL Directory Constants
 * -------------------------------
 */
const DIR_DATA_ROOT = DIR_DOCUMENT_ROOT . '/data';
const DIR_RELATIVE_DATA_ROOT = DIR_RELATIVE_DOCUMENT_ROOT . '/data';
const DIR_RESOURCES_ROOT = DIR_DOCUMENT_ROOT . '/resources';