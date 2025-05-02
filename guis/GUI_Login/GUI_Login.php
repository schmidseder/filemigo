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

namespace filemigo\guis\GUI_Login;

use pool\classes\GUI\GUI_Module;
use filemigo\guis\GUI_Logout\GUI_Logout;
use filemigo\guis\GUI_ZipFolder\GUI_ZipFolder;
use pool\classes\GUI\Builtin\GUI_CustomFrame;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\Core\Url;
use const filemigo\APPLICATION_NAME;

class GUI_Login extends GUI_CustomFrame
{
    protected int $superglobals = Input::POST;

    protected array $templates = [
        'stdout' => 'tpl_login.html',
    ];

    protected array $inputFilter = [
        'user' => [DataType::ALPHANUMERIC, ''],
        'password' => [DataType::ALPHANUMERIC, ''],
        'csrf_token' => [DataType::ALPHANUMERIC, ''],
    ];

    /**
     * Templates laden
     */
    public function loadFiles(): static
    {
        parent::loadFiles();

        $appCss = $this->Weblication->findStyleSheet('app.css');
        $this->getHeadData()->addStyleSheet($appCss);

//        $appJS = $this->Weblication->findJavaScript('app.js');
//        $this->addScriptFileAtTheEnd($appJS);

        return $this;
    }

    public function prepare(): void
    {
        // csrf token for more security
        $tokenExists = $this->Session->exists('csrf_token');
        if (!$tokenExists) {
            $this->Session->setVar('csrf_token', bin2hex(random_bytes(32)));
        }
        $this->Template->setVar('csrf_token', $this->Session->getVar('csrf_token'));
        $this->setClientVar('csrf_token', $this->Session->getVar('csrf_token'));

        $this->Template->setVar('name', $this->getName());

        $this->Template->setVar('FMG_HEADER', $this->Weblication->getConfigValue('FMG_HEADER'));
        $this->Template->setVar('FMG_FOOTER', $this->Weblication->getConfigValue('FMG_FOOTER'));

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $csrf_token = $this->Input->getVar('csrf_token');
                $user = $this->Input->getAsString('user');
                $password = $this->Input->getAsString('password');

                if ($csrf_token !== $this->Session->getVar('csrf_token')) {
                    http_response_code(403);
                    die ('forbidden');
                }

                $allowedUsers = $this->Weblication->getConfigValue('FMG_USERS');
                if ($allowedUsers && isset($allowedUsers[$user])) {
                    $allowedPassword = $allowedUsers[$user];

                    if (password_verify($password, $allowedPassword)) {
                        // start session again if it was close to avoid race conditions
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        // regenerate session id after login
                        if (session_status() === PHP_SESSION_ACTIVE) {
                            $this->Session->regenerate_id();
                        }
                        // set values in session
                        $this->Session->setVar('loggedIn', true);
                        $this->Session->setVar('loggedInUser', $user);

                        /** @var GUI_ZipFolder $GUI_ZipFolder */
                        // $GUI_ZipFolder = GUI_Module::createGUIModule(GUI_ZipFolder::class, $this->Weblication, $this);
                        // $GUI_ZipFolder->cleanUpZipFiles();
                    }
                }
                // reload page
                $Url = new Url();
                $Url->clearQuery();

                // Get user configuration
                $users = $this->Weblication->getConfigValue('FMG_USERS', false);

                // Check if users is a valid array
                if (is_array($users)) {
                    $userCount = count($users);

                    // Security check: multiple users and the special "filemigo" user is still configured
                    if ($userCount > 1 && isset($users[APPLICATION_NAME])) {
                        /** @var GUI_Logout $GUI_Logout */
                        $GUI_Logout = GUI_Module::createGUIModule(GUI_Logout::class, $this->Weblication, $this);
                        $GUI_Logout->logout();

                        die('Security alert: Please remove user "filemigo" from config/filemigo.inc.php.');
                    }

                    // If "filemigo" is the only configured user, redirect to add-user page
                    if ($user === APPLICATION_NAME && $userCount === 1) {
                        $Url->setParam('schema', 'user-add');
                    }
                }

                $Url->reload();
        }
    }
}