<?php

namespace filemigo\guis\GUI_Frame;

use filemigo\guis\GUI_PictureGallery\GUI_PictureGallery;
use pool\classes\GUI\Builtin\GUI_CustomFrame;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;

class GUI_Frame extends GUI_CustomFrame
{
    protected int $superglobals = Input::GET;

    protected array $templates = [
        'stdout' => 'tpl_frame.html',
    ];

    protected array $inputFilter = [
        'path'      => [ DataType::ALPHANUMERIC_SPACE_PUNCTUATION, DIRECTORY_SEPARATOR]
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

        $appCss = $this->Weblication->findStyleSheet('app.css');
        $this->getHeadData()->addStyleSheet($appCss);

        $appJS = $this->Weblication->findJavaScript('app.js');
        $this->addScriptFileAtTheEnd($appJS);

        $this->getHeadData()->addStyleSheet('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined');

        return $this;
    }

    public function prepare(): void
    {
        $this->Template->setVar('FMG_TITLE', $this->Weblication->getConfigValue('FMG_TITLE'));
        $this->Template->setVar('FMG_FOOTER', $this->Weblication->getConfigValue('FMG_FOOTER'));

        $rootDir = $this->Weblication->getConfigValue('FMG_DATA_ROOT');

        $index = $this->Session->getVar('index');
        $path = $this->Input->getAsString('path');

        $notFound = !isset($index[$path]);
        if ($notFound) {
            http_response_code(404);
            // Todo : make a pretty Not Found Page
            die ('404 File Not Found');
        }

        // / ** @var  $GUI_PictureGallery GUI_PictureGallery * /
        $GUI_PictureGallery = $this->Weblication->findComponent('pictures');
        $GUI_PictureGallery->setRootDirectory($rootDir);
        $GUI_PictureGallery->setVar('pictures', $path);
    }
}