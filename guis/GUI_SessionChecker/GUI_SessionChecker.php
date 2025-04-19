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