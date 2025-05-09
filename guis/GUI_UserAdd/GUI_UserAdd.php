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