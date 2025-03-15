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
            'success' => $success
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