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

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        $monitorInactivity = $this->Input->getAsBool('monitorInactivity');
        $this->setClientVar('monitorInactivity', $monitorInactivity);

        $this->setClientVar('session_name', session_name());
        $this->setClientVar('session_maxlifetime', ini_get('session.gc_maxlifetime'));
    }
}