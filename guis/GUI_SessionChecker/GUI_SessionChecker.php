<?php
/**
 * Filemigo
 * Copyright (c) 2025 Christian Schmidseder
 *
 * This file is part of Filemigo.
 *
 * Licensed under the MIT License. See the LICENSE file
 * in the project root for full license information.
 */

namespace filemigo\guis\GUI_SessionChecker;

use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;

class GUI_SessionChecker extends GUI_Module
{
    /**
     * @var int
     */
    protected int $superglobals = Input::GET | Input::POST;

//    protected array $templates = [
//        'stdout' => 'tpl_SessionChecker.html',
//    ];

    protected function registerAjaxCalls(): void
    {
        $this->registerAjaxMethod('checkSession', $this->checkSession(...));
    }

    private function checkSession(): array
    {
        $loggedIn = $this->Session->exists('loggedIn');
        return ['noSession' => !$loggedIn];
    }
}