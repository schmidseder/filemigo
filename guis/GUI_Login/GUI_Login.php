<?php

namespace filemigo\guis\GUI_Login;

use pool\classes\GUI\Builtin\GUI_CustomFrame;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\Core\Url;

class GUI_Login extends GUI_CustomFrame
{
    protected int $superglobals = Input::POST;

    protected array $templates = [
        'stdout' => 'tpl_login.html',
    ];

    protected array $inputFilter = [
        'user' => [ DataType::ALPHANUMERIC, ''],
        'password' => [ DataType::ALPHANUMERIC, ''],
        'csrf_token' => [ DataType::ALPHANUMERIC, '']
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

        $this->Template->setVar('FMG_TITLE', $this->Weblication->getConfigValue('FMG_TITLE'));
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
                    }
                }
                // reload page
                $Url = new Url();
                $Url->clearQuery();
                $Url->reload();
        }
    }
}