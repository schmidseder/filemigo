<?php
    namespace filemigo;
	

    const DIR_CONFIGS_ROOT = __DIR__.'/config';
	
    require_once DIR_CONFIGS_ROOT.'/config.inc.php';
    require_once '../pool/pool.lib.php';
	
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
    }
    catch (Exception $e) {
        throw $e;
    }