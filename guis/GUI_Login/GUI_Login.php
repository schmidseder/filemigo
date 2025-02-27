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
        'password' => [ DataType::ALPHANUMERIC, '']
    ];

    /**
     * Templates laden
     */
    public function loadFiles(): static
    {
        parent::loadFiles();

        $picoCss = $this->Weblication->findStyleSheet('pico.min.css');
        $this->getHeadData()->addStyleSheet($picoCss);

        $appCss = $this->Weblication->findStyleSheet('app.css');
        $this->getHeadData()->addStyleSheet($appCss);

//        $appJS = $this->Weblication->findJavaScript('app.js');
//        $this->addScriptFileAtTheEnd($appJS);

        return $this;
    }

    public function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        $this->Template->setVar('FMG_TITLE', $this->Weblication->getConfigValue('FMG_TITLE'));
        $this->Template->setVar('FMG_FOOTER', $this->Weblication->getConfigValue('FMG_FOOTER'));

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $user = $this->Input->getAsString('user');
                $password = $this->Input->getAsString('password');

                $allowedUsers = $this->Weblication->getConfigValue('FMG_USERS');
                if ($allowedUsers && isset($allowedUsers[$user])) {
                    $allowedPassword = $allowedUsers[$user];

                    if (password_verify($password, $allowedPassword)) {
                        $this->Session->setVar('loggedIn', true);
                        $this->Session->setVar('loggedInUser', $user);
                    }
                }
                $Url = new Url();
                $Url->clearQuery();
                $Url->reload();
                break;
            default:
                // Todo
                $here = 'i am here';
        };

    }

}