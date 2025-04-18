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

namespace filemigo\guis\GUI_FileResponder;

use finfo;
use JetBrains\PhpStorm\NoReturn;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;
use pool\classes\Core\Url;

class GUI_FileResponder extends GUI_Module
{
    private string $rootDirectory;

    private string $path;

    private bool $outputFile = false;

    private bool $outputURL = false;

    /**
     * @var int
     */
    protected int $superglobals = Input::GET | Input::POST;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_FileResponder.html',
    ];

    protected array $inputFilter = [
        'pathKey' => [DataType::ALPHANUMERIC, 'path'],
        'path' => [DataType::ALPHANUMERIC_SPACE_PUNCTUATION, DIRECTORY_SEPARATOR],
        'use' => [DataType::ALPHANUMERIC_SPACE_PUNCTUATION, ''],
    ];

    protected function provision(): void {}

    protected function prepare(): void
    {
        $pathKey = $this->Input->getAsString('pathKey');
        $path = $this->Input->getAsString($pathKey);
        $use = $this->Input->getAsString('use');

        $this->Template->setVar('src', '');

        $moduleName = $this->getName();
        if ($use !== $moduleName) {
            $this->disable();
            return;
        }

        if ($this->outputFile) {
            $this->openFile($path);
            $this->disable();
        } elseif ($this->outputURL) {
            $src = $this->getSrc($path);
            $this->Template->setVar('src', $src);
        }
    }

    public function getSrc(string $path): string
    {
        $pathKey = $this->Input->getAsString('pathKey');

        $url = new Url();
        $url->clearQuery();
        $url->setParam($pathKey, $path);
        $url->setParam('use', $this->getName());
        return $url->getUrl();
    }

    #[NoReturn]
    private function openFile(string $path): void
    {
        $path = ltrim($path, DIRECTORY_SEPARATOR);
        $filepath = addEndingSlash($this->getRootDirectory()) . $path;
        $filepath = realpath($filepath);

        if (!file_exists($filepath)) {
            http_response_code(404);
            die('404 File Not Found (FileResponder)');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filepath, FILEINFO_MIME);

        // Falls der MIME-Typ nicht bestimmt werden kann, Standard setzen
        if (!$mimeType) {
            $mimeType = 'application/octet-stream'; // Allgemeiner Typ für binäre Dateien
        }

        // Dateiname aus dem Pfad extrahieren
        $filename = basename($filepath);

        // Setze die notwendigen HTTP-Header
        header('Content-Type: ' . $mimeType); // Setze den MIME-Typ
        header('Content-Disposition: attachment; filename="' . $filename . '"'); // Erzwinge den Download
        header('Content-Length: ' . filesize($filepath)); // Setze die Dateigröße

        // Dateiinhalt lesen und an den Browser senden
        readfile($filepath);
        exit;
    }

    public function setRootDirectory(string $rootDirectory): void
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }

    public function setOutputFile(bool $outputFile = true): void
    {
        $this->outputFile = $outputFile;
    }

    public function setOutputURl(bool $outputURL = true): void
    {
        $this->outputURL = $outputURL;
    }
}