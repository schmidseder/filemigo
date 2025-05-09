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

namespace filemigo\guis\GUI_Logout;

use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;

class GUI_Logout extends GUI_Module
{
    /**
     * @var int
     */
    protected int $superglobals = Input::GET;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_Logout.html',
    ];

    protected array $inputFilter = [
        'doLogout' => [DataType::INT],
    ];

    protected function registerAjaxCalls(): void
    {
        $this->registerAjaxMethod('doLogout', $this->doLogout(...));
    }

    private function doLogout(): array
    {
        $success = false;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            $success = session_destroy();

            session_start();
            session_regenerate_id(true);
        }
        return [
            'success' => $success,
        ];
    }

    public function logout(): void
    {
        $this->doLogout();
    }

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        $monitorInactivity = $this->Input->getAsBool('monitorInactivity');
        $this->setClientVar('monitorInactivity', $monitorInactivity);

        $this->setClientVar('session_name', session_name());
        $this->setClientVar('session_maxlifetime', ini_get('session.gc_maxlifetime'));
    }
}