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

namespace filemigo\guis\GUI_Breadcrumb;

use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;
use pool\classes\Core\Url;

class GUI_Breadcrumb extends GUI_Module
{
    /**
     * @var int
     */
    protected int $superglobals = Input::GET;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_Breadcrumb.html',
    ];

    protected array $inputFilter = [
        'displayPath' => [DataType::ALPHANUMERIC_SPACE_PUNCTUATION, DIRECTORY_SEPARATOR],
    ];

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        $displayPath = $this->Input->getAsString('displayPath');
        $this->Template->setVar('displayPath', $displayPath);

        $pieces = $this->cutUpPath($displayPath);

        $url = new Url();
        $url->clearQuery();
        foreach ($pieces as $index => $piece) {
            $isFirst = ($index === 0);
            $isLast = ($index === count($pieces) - 1);

            $num = $index + 1;
            $part = array_slice($pieces, 0, $num);
            $pathValue = implode(DIRECTORY_SEPARATOR, $part);
            $pathValue = DIRECTORY_SEPARATOR . ltrim($pathValue, DIRECTORY_SEPARATOR);

            $url->setParam('path', $pathValue);

            $this->Template->newBlock('breadcrumb');
            if ($isFirst) {
                $blockName = 'first';
            } elseif ($isLast) {
                $blockName = 'last';
            } else {
                $blockName = 'separator';
            }
            $this->Template->newBlock($blockName);
            $this->Template->setVar('piece', $piece);
            $this->Template->setVar('url', $url->getUrl());
            $this->Template->leaveBlock();
        }
        $this->Template->leaveBlock();
    }

    private function cutUpPath(string $path): array
    {
        $pieces = [];
        if ($path === DIRECTORY_SEPARATOR) {
            $pieces[] = DIRECTORY_SEPARATOR;
        } else {
            $pieces = explode(DIRECTORY_SEPARATOR, $path);
            $pieces[0] = $pieces[0] ?: DIRECTORY_SEPARATOR;
        }
        return $pieces;
    }
}