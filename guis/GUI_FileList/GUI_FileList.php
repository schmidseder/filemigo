<?php

namespace filemigo\guis\GUI_FileList;

use finfo;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;
use pool\classes\Core\Url;

class GUI_FileList extends GUI_Module
{

    protected array $icons = [
        'PDF'           => [ 'type_name' => 'picture_as_pdf', 'color' => 'red' ],
        'EXCEL'         => [ 'type_name' => 'grid_on', 'color' => 'green' ],
        'WORD'          => [ 'type_name' => 'description', 'color' => 'blue' ],
        'TEXT'          => [ 'type_name' => 'text_snippet', 'color' => 'gray' ],
        'DIRECTORY'     => [ 'type_name' => 'folder', 'color' => 'gold' ],
        'HELP'          => [ 'type_name' => 'help_outline', 'color' => 'blue' ],
        'EMPTY'         => [ 'type_name' => 'hourglass_empty', 'color' => 'lightgray' ],
        'README'        => [ 'type_name' => 'info', 'color' => 'cornflowerblue' ],
        'ARTICLE'       => [ 'type_name' => 'article', 'color' => 'darkgray' ],
        'IMAGE'         => [ 'type_name' => 'image', 'color' => 'darkgoldenrod' ],
        'ANIMATION'     => [ 'type_name' => 'animation', 'color' => 'purple' ]
    ];
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
        'stdout' => 'tpl_FileList.html',
    ];

    protected array $inputFilter = [
        'path'      => [ DataType::ALPHANUMERIC_SPACE_PUNCTUATION, DIRECTORY_SEPARATOR],
//        'frameHeight'   => [ DataType::INT ],
//        'index'         => [ DataType::INT ]
    ];

    protected function provision(): void
    {
        $root = $this->Weblication->getConfigValue('FMG_DATA_ROOT');

        if (!$this->Session->exists('structure')) {
            // Struktur einlesen
            $readDirectoryStructure = static function(string $directory) use (&$readDirectoryStructure): array
            {
                $result = []; // Das Array, das die Struktur speichert

                // Sicherstellen, dass das Verzeichnis existiert
                if (!is_dir($directory)) {
                    return $result; // Gib ein leeres Array zurück, wenn das Verzeichnis nicht existiert
                }

                // Alle Dateien und Verzeichnisse einlesen
                $items = scandir($directory);

                foreach ($items as $item) {
                    // Ignoriere `.` und `..` (Spezialverzeichnisse)
                    if ($item === '.' || $item === '..') {
                        continue;
                    }

                    $path = $directory . DIRECTORY_SEPARATOR . $item;

                    // Wenn es ein Verzeichnis ist, rufe die Funktion rekursiv auf
                    if (is_dir($path)) {
                        $result[$item] = $readDirectoryStructure($path); // Verzeichnisse als Arrays speichern
                    } else {
                        $result[] = $item; // Dateien direkt in das Array einfügen
                    }
                }

                return $result;
            };

            $index = [DIRECTORY_SEPARATOR=> true];

            $loopStructure = static function (array $array, string $dir, string $path='') use (&$loopStructure, &$index)
            {
                foreach ($array as $key => $value) {

                    if (is_array($value)) {
                        if (is_dir( $dir . DIRECTORY_SEPARATOR . $key)) {
                            $name = $key;
                            // is directory
                            $index[$path . DIRECTORY_SEPARATOR . $name] = true;

                            // all dirs
                            $loopStructure($value, $dir . DIRECTORY_SEPARATOR . $key, $path . DIRECTORY_SEPARATOR . $name);
                        }
                    } else if(is_string($value)) {
                            $name = $value;
                            // is file
                            $index[$path . DIRECTORY_SEPARATOR . $name] = false;
                    }
                }
            };

            $structure = $readDirectoryStructure($root);
            $this->Session->setVar('structure', $structure);
            // echo pray($structure);

            $loopStructure($structure, $root);
            $this->Session->setVar('index', $index);
            //echo pray($index);
        }
        $this->Session->destroy();
    }

    protected function prepare(): void
    {
        $index = $this->Session->getVar('index');
        $path = $this->Input->getVar('path');

        if (!isset($index[$path])) {
            die ('404 File Not Found');
        }

        $this->Template->setVar('path', $path);
        $this->Template->setVar('name', $this->getName());

        $list = $this->getDirectoryContent($path);

        $directories = array_keys($list);
        if (count($directories) > 0) {
            $directories = array_filter($directories, static function ($directory) use($list) {
                return is_array($list[$directory]);
            });
            if (count($directories) > 1) {
                sort($directories);
            }
        }

        $files = array_values($list);
        if (count($files) > 0) {
            $files = array_filter($files, static function ($file) {
                return is_string($file);
            });
            if (count($files) > 1) {
                sort($files);
            }
        }

        $content = [ ...$directories, ...$files];

        $format = $this->Weblication->getDefaultFormat('php.date.time');
        foreach ($content as $entry) {
            $filepath = $this->Weblication->getConfigValue('FMG_DATA_ROOT'). addEndingSlash($path) . $entry;

            $type = $this->type($filepath);

            $pathinfo = pathinfo($filepath);
            if ($type === 'TEXT'
                && strtolower($pathinfo['extension']) === 'md'
                && strtolower($pathinfo['filename']) === 'readme') {
                $type = 'README';
            }

            $infos = [
                'isDir' => is_dir($filepath),
                'isFile' => is_file($filepath),
                'size' => filesize($filepath),
                'modified' => date($format, filemtime($filepath)),
                'created' => date($format, filectime($filepath)),
                //'mime_content_type' => mime_content_type($filepath),
                //'finfo_mime' => $finfo_mime,
                'type' => $type,
            ];

            $infos = [...$infos, ...$this->icons[$type]];

            $this->Template->newBlock('fileblock');

            $url = new Url();
            $url->setParam('path', addEndingSlash($path) . basename($filepath));
            $url = $url->getUrl();


            $this->Template->setVar('url', $url);
            $this->Template->setVar('filename', $entry);
            $this->Template->setVar('target', $infos['isFile'] ? '_blank' : '_self');

            $this->Template->setVars($infos);
        }
        //echo pray($this->Session->getData());
        // $this->Session->destroy();
    }

    private function getDirectoryContent(string $path): array
    {
        $index = $this->Session->getVar('index');
        $structure = $this->Session->getVar('structure');

        if (isset($index[$path]) && $index[$path] === true) { // is directory
            $trimmedPath = trim($path, DIRECTORY_SEPARATOR);
            if (!$trimmedPath) {
                return $structure;
            }
            $pieces = explode(DIRECTORY_SEPARATOR, $trimmedPath);

             return array_reduce($pieces, static function ($carry, $key) {
                return $carry[$key] ?? [];
            }, $structure);
        }
        return [];
    }

    private function sanitizeDirectoryPath(string $path): string
    {
        // Erlaube: Buchstaben (inkl. Umlaute), Zahlen, Leerzeichen, Bindestriche, Unterstriche
        return preg_replace('/[^a-zA-Z0-9äöüÄÖÜß _\-]/u', '', $path);
    }

    private function type(string $filepath) : string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $finfo_mime = $finfo->file($filepath, FILEINFO_MIME);
        $mime = strtok($finfo_mime, ';');

        $return = 'UNKNOWN';
        switch ($mime) {
            case 'directory':
                $return = 'DIRECTORY';
                break;
            case 'application/pdf':
                $return = 'PDF';
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $return = 'WORD';
                break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $return = 'EXCEL';
                break;
            case 'image/jpeg':
                $return = 'JPEG';
                break;
            case 'image/png':
                $return = 'PNG';
                break;
            case 'image/gif':
                $return = 'GIF';
                break;
            case 'text/plain':
                $return = 'TEXT';
                break;
            case 'application/x-empty':
                $return = 'EMPTY';
                break;
        }
        return $return;
    }

    /*
    protected array $cssFiles = [
    ];



    protected function registerAjaxCalls(): void
    {

    }
    */
}