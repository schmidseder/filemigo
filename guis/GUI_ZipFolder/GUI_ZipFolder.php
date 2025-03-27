<?php


namespace filemigo\guis\GUI_ZipFolder;

use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;

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

        return [
            'success' => $success,
            'path' => $path
        ];
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