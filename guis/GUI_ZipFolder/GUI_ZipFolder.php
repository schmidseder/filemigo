<?php


namespace filemigo\guis\GUI_ZipFolder;

use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use ZipArchive;

class GUI_ZipFolder extends GUI_Module
{
    /**
     * @var int
     */
    protected int $superglobals = Input::POST;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_ZipFolder.html',
    ];

    protected array $inputFilter = [
        'path' => [DataType::ALPHANUMERIC_SPACE_PUNCTUATION, '']
    ];

    protected function registerAjaxCalls(): void
    {
        $this->registerAjaxMethod('download', $this->download(...));
    }

    protected function prepare(): void
    {
        $this->Template->setVar('name', $this->getName());

        // echo pray($this->Session->getData());
    }

    private function download(string $path): array
    {
        $success = false;



        $index = $this->Session->getVar('index');

        if ($index[$path]) {

            $this->zipFolder($path, 'zip');
        }

        return [
            'success' => $success,
            'path' => $path
        ];
    }

    private function zipFolder(string $path, string $target) : bool
    {
        $root = $this->Weblication->getConfigValue('FMG_DATA_ROOT');
        $folder = $root . $path;

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS));
        $zip = new ZipArchive;
        $create = $target . '.zip';

        $index = $this->Session->getVar('index');

        if ($zip->open($create, ZipArchive::CREATE)) {
            foreach ($files as $file) {
                $filepath = $this->removePrefix($file->getPathname(), $root);
                if (isset($index[$filepath])) {
                    $zip->addFile(realpath($file), $file);
                }
            }
            $zip->close();
        }

        return file_exists($create);
    }

    private function removePrefix($fullString, $prefix) : string
    {
        if (str_starts_with($fullString, $prefix)) {
            return substr($fullString, strlen($prefix)); // Entfernt den Prefix
        }
        return $fullString; // Falls der Prefix nicht gefunden wird, bleibt der String unverändert
    }
}

/*
<?php
function zipFolder(string $folder,string $target){
    $files  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder,FilesystemIterator::SKIP_DOTS));
    $zip    = new ZipArchive;
    $create = $target . '.zip';

    if($zip->open($create,ZipArchive::CREATE)):

        foreach($files as  $file):
            $zip->addFile(realpath($file),$file);

            print($file . " - Datei Hinzugefügt ". PHP_EOL);
        endforeach;

        $zip->close();
    endif;

    return file_exists($create);
}

if(zipFolder('bilder','bilder-archive')):
    print("ZIP File Erstellt");
endif;
 */