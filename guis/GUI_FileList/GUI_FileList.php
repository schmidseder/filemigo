<?php

namespace filemigo\guis\GUI_FileList;

use filemigo\guis\GUI_Breadcrumb\GUI_Breadcrumb;
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
        'JPEG'          => [ 'type_name' => 'image', 'color' => 'darkgoldenrod' ],
        'GIF'           => [ 'type_name' => 'image', 'color' => 'darkgoldenrod' ],
        'PNG'           => [ 'type_name' => 'image', 'color' => 'darkgoldenrod' ],
        'SVG'           => [ 'type_name' => 'image', 'color' => 'darkgoldenrod' ],
        'ANIMATION'     => [ 'type_name' => 'animation', 'color' => 'purple' ],
        'UNKNOWN'       => [ 'type_name' => 'unknown_document', 'color' => 'lightgray' ],

    ];

    private null|array $branches = null;

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
//        $this->Session->destroy();
//        exit;
    }

    protected function prepare(): void
    {
        $index = $this->Session->getVar('index');
        $path = $this->Input->getVar('path');

        $notFound = !isset($index[$path]);
        if ($notFound) {
            http_response_code(404);
            // Todo : make a pretty Not Found Page
            die ('404 File Not Found (FileList)');
        }

        /** @var GUI_Breadcrumb $GUI_Breadcrumb */
        $GUI_Breadcrumb = $this->Weblication->findComponent('breadcrumb');
        $GUI_Breadcrumb->setVar('displayPath', $path);

        $isFile = $index[$path] === false;

        $this->branches = $this->Weblication->getConfigValue('FMG_DATA_ROOT_BRANCHES');
        if ($this->branches !== null) {
            $this->branches = array_unique($this->branches);
            $this->branches = array_flip($this->branches);
        }

        $rootDir = $this->Weblication->getConfigValue('FMG_DATA_ROOT');
        $GUI_FileResponder = $this->Weblication->findComponent('responder');
        $GUI_FileResponder->setRootDirectory($rootDir);
        $GUI_FileResponder->setOutputFile($isFile);


        $isSubDirectory = str_starts_with($path, DIRECTORY_SEPARATOR) && strlen($path) > 1;
        if ($isSubDirectory) {
            $this->Template->newBlock('isSubDirectory');

            $pieces = explode(DIRECTORY_SEPARATOR, $path);
            array_pop($pieces);
            $parentDirectory = implode(DIRECTORY_SEPARATOR, $pieces);
            if (!$parentDirectory) {
                $parentDirectory = DIRECTORY_SEPARATOR;
            }

            $parentUrl = new Url();
            $parentUrl->setParam('path', $parentDirectory);
            $this->Template->setVar('parentUrl', $parentUrl->getUrl());
            $this->Template->leaveBlock();
        }

        $isRootDirectory = !$isSubDirectory;

        $this->Template->setVar('path', $path);
        $this->setClientVar('path', $path);
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

        $fileList = [];
        foreach ($content as $entry) {
            if ($isRootDirectory && !$this->displayAllowed($entry)) {
                continue;
            }

            $filepath = $rootDir . addEndingSlash($path) . $entry;

            if (!file_exists($filepath)) {
                continue;
            }

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
                'last_modified' => date($format, filemtime($filepath)),
                'last_access' => date($format, fileatime($filepath)),
                //'mime_content_type' => mime_content_type($filepath),
                //'finfo_mime' => $finfo_mime,
                'type' => $type,
            ];

            $infos = [...$infos, ...$this->icons[$type]];

            if ($infos['isFile']) {
                $url = $GUI_FileResponder->getSrc(addEndingSlash($path) . basename($filepath));
            } elseif ($infos['isDir']) {
                $DirUrl = new Url();
                $DirUrl->setParam('path', addEndingSlash($path) . basename($filepath));
                $url = $DirUrl->getUrl();
            }

            $infos['url'] = $url;
            $infos['filename'] = $entry;

            $fileList[] = $infos;
        }

        foreach ($fileList as $fileRecord) {
            $this->Template->newBlock('tableFile');
            $this->Template->setVars($fileRecord);
        }

        foreach ($fileList as $fileRecord) {
            $this->Template->newBlock('cardFile');
            $this->Template->setVars($fileRecord);
        }
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
            case 'image/svg+xml':
                $return = 'SVG';
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

    /**
     * Checks, if the entry is allowed to be displayed according to the branches definitions.
     *
     * @param string $entry
     * @return bool
     */
    private function displayAllowed(string $entry) : bool
    {
        // no definition - all entries will be displayed
        if ($this->branches === null) {
            return true;
        }

        // explicit defined - entry will be displayed
        if (isset($this->branches[$entry])) {
            return true;
        }

        // no explicit definition - entry will not be displayed
        return false;
    }
}