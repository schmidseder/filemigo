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


namespace filemigo\guis\GUI_UserAdd;

use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;
use pool\classes\Core\Url;

class GUI_UserAdd extends GUI_Module
{
    /**
     * @var int
     */
    protected int $superglobals = Input::POST;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_UserAdd.html',
    ];

    protected array $inputFilter = [
        'generated-user' => [DataType::ALPHANUMERIC, ''],
        'generated-password' => [DataType::ALPHANUMERIC, ''],
    ];

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $user = $this->Input->getAsString('generated-user');
                $password = $this->Input->getAsString('generated-password');

                $this->Session->setVar('generated_user', [$user, password_hash($password, PASSWORD_DEFAULT)]);

                // reload page
                $Url = new Url();
                $Url->reload();
        }

        if ($this->Session->exists('generated_user')) {
            $this->Template->newBlock('resultBlock');
            $this->Template->setVar('user', $this->Session->getVar('generated_user')[0]);
            $this->Template->setVar('password_hash', $this->Session->getVar('generated_user')[1]);

            $this->Session->delVar('generated_user');
        }
    }
}