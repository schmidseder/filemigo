<?php
    namespace filemigo;
    const DIR_CONFIGS_ROOT = __DIR__.'/config';
    require_once DIR_CONFIGS_ROOT.'/config.inc.php';
    require_once '../pool/pool.lib.php';

    $config = require DIR_CONFIGS_ROOT . '/filemigo.inc.php';

    use Exception;

    use filemigo\classes\FilemigoApp;
    use filemigo\guis\GUI_Frame\GUI_Frame;

    FilemigoApp::caching(IS_PRODUCTION);

    $App = FilemigoApp::getInstance();
    $App->startPHPSession();
    $App->setConfig($config);

    try {
        $App->setup([
            'application.name' => 'filemigo',
            'application.title' => 'Filemigo - Simple Web File Manager',
            'application.launchModule' => GUI_Frame::class,
        ]);

        $App->render();
    }
    catch (Exception $e) {
        throw $e;
    }