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
        'path' => [DataType::ALPHANUMERIC_SPACE_PUNCTUATION, DIRECTORY_SEPARATOR],
    ];

    /**
     * Templates laden
     */
    public function loadFiles(): static
    {
        parent::loadFiles();

        $appCSS = $this->Weblication->findStyleSheet('app.css');
        $this->getHeadData()->addStyleSheet($appCSS);

//        $appJS = $this->Weblication->findJavaScript('app.js');
//        $this->addScriptFileAtTheEnd($appJS);

        // $this->getHeadData()->addStyleSheet('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined');

        return $this;
    }

    public function prepare(): void
    {
        $this->Template->setVar('FMG_HEADER', $this->Weblication->getConfigValue('FMG_HEADER'));
        $this->Template->setVar('FMG_FOOTER', $this->Weblication->getConfigValue('FMG_FOOTER'));

        $rootDir = $this->Weblication->getConfigValue('FMG_DATA_ROOT');
        $path = $this->Input->getAsString('path');
        /** @var GUI_PictureGallery $GUI_PictureGallery */
        $GUI_PictureGallery = $this->Weblication->findComponent('pictures');
        if ($GUI_PictureGallery) {
            $GUI_PictureGallery->setImageFileExtensions(IMAGE_FILE_EXTENSIONS);
            $GUI_PictureGallery->setRootDirectory($rootDir);
            $GUI_PictureGallery->setVar('pictures', $path);
        }
    }
}