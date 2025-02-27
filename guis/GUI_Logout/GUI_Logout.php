<?php


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
        'doLogout' => [DataType::INT]
    ];

    protected function registerAjaxCalls(): void
    {
        $this->registerAjaxMethod('doLogout', $this->doLogout(...));
    }

    private function doLogout(): array
    {
        session_start();
        $_SESSION = [];
        $success = session_destroy();
        return [
            'success' => $success,
            'session_name' => session_name()
        ];
    }

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

    }
}