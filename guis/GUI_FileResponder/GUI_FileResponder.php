<?php

namespace filemigo\guis\GUI_FileResponder;

use finfo;
use JetBrains\PhpStorm\NoReturn;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;
use pool\classes\Core\Url;

class GUI_FileResponder extends GUI_Module
{

    /**
     * Attributes for the script tag of the assoziated javascript file/class for this module.
     *
     * @var array|string[]
     */

    /**
     * @var int
     */
    protected int $superglobals = Input::GET|Input::POST;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_FileResponder.html',
    ];

    protected array $inputFilter = [
        'path'      => [ DataType::ALPHANUMERIC_SPACE_PUNCTUATION, DIRECTORY_SEPARATOR],
    ];

    protected function provision(): void
    {

    }

    protected function prepare(): void
    {
        $this->Template->setVar('src', 'hallo');
//        $path = $this->Input->getVar('path');
//        $this->openFile($path);
//        $this->disable();
    }


    #[NoReturn]
    private function openFile(string $path) : void
    {
        $filepath = $this->Weblication->getConfigValue('FMG_DATA_ROOT'). $path;
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
}