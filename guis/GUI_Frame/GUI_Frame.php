<?php

namespace filemigo\guis\GUI_Frame;

use pool\classes\GUI\Builtin\GUI_CustomFrame;

class GUI_Frame extends GUI_CustomFrame
{

    protected array $templates = [
        'stdout' => 'tpl_frame.html',
    ];

    /**
     * Templates laden
     */
    public function loadFiles() : static
    {
        parent::loadFiles();

        //$appCSS = $this->Weblication->findStyleSheet('app.css');
        //$this->getHeadData()->addStyleSheet($appCSS);

        $picoCss = $this->Weblication->findStyleSheet('pico.min.css');
        $this->getHeadData()->addStyleSheet($picoCss);

        $appJS = $this->Weblication->findJavaScript('app.js');
        $this->addScriptFileAtTheEnd($appJS);

        return $this;
    }

    public function prepare(): void
    {
        $this->Template->setVars($this->Weblication->getConfig());
    }
}