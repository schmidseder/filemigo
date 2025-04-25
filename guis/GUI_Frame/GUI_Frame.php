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

        $appJS = $this->Weblication->findJavaScript('app.js');
        $this->addScriptFileAtTheEnd($appJS);

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