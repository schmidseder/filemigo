<?php
namespace filemigo\guis\GUI_UserAdd;

use filemigo\guis\GUI_ZipFolder\GUI_ZipFolder;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;

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
        'user' => [ DataType::ALPHANUMERIC, ''],
        'password' => [ DataType::ALPHANUMERIC, ''],
    ];

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':

                $user = $this->Input->getAsString('user');
                $password = $this->Input->getAsString('password');

                $this->Template->newBlock('resultBlock');
                $this->Template->setVar('user', $user);;
                $this->Template->setVar('password_hash', password_hash($password, PASSWORD_DEFAULT));



                // reload page
//                $Url = new Url();
//                $Url->reload();
        }


    }
}