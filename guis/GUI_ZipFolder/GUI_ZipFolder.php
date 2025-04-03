<?php


namespace filemigo\guis\GUI_ZipFolder;

use filemigo\guis\GUI_FileResponder\GUI_FileResponder;
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
    protected int $superglobals = Input::POST|Input::GET;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_ZipFolder.html',
    ];

    protected array $inputFilter = [
        'path' => [DataType::ALPHANUMERIC_SPACE_PUNCTUATION, ''],
        'zipDownload' => [DataType::BOOL, false],
    ];

    public function init(?int $superglobals = null): void
    {
        if ($this->Weblication->hasFrame()) {
            $Frame = $this->Weblication->getFrame();
            $urlJS =  $this->Weblication->findJavaScript('url.js', '', true);
            $Frame->getHeadData()->addJavaScript($urlJS);
        }
        parent::init($superglobals);
    }

    protected function registerAjaxCalls(): void
    {
        $this->registerAjaxMethod('download', $this->download(...));
    }

    public function cleanUpZipFiles(): void
    {
        // clean up old zip files
        $loggedInUser = $this->Session->getVar('loggedInUser');
        $zipDir = $this->Weblication->getConfigValue('FMG_ZIP_DIR');
        if ($zipDir && $this->isWriteableDirectory($zipDir)) {
            $pattern = addEndingSlash($zipDir) . '*' . $loggedInUser . '.zip';
            foreach (glob($pattern) as $path) {
                unlink($path);
            }
        }
    }

    protected function prepare(): void
    {
        $ZipResponser = $this->getZipResponder();
        $zipDownload = $this->Input->getAsBool('zipDownload');
        $ZipResponser->setOutputFile($zipDownload);

        $this->Template->setVar('name', $this->getName());
    }

    private function download(string $path): array
    {
        $success = true;
        $message = '';
        $zipName = '';
        $zipUrl = '';

        $zipDir = $this->Weblication->getConfigValue('FMG_ZIP_DIR');
        if (!$zipDir || !$this->isWriteableDirectory($zipDir)) {
            $success = false;
            $message = 'Ein ZIP-Verzeichnis muss mit Schreibrechten konfiguriert werden!';
            return [
                'success' => $success,
                'message' => $message,
                'zipName' => $zipName,
            ];
        }

        $index = $this->Session->getVar('index');
        if ($index[$path]) {

            // $zipName = bin2hex(random_bytes(16)) . '.zip';
            $loggedInUser = $this->Session->getVar('loggedInUser');
            $now = new \DateTime();
            $nowStr = $now->format('Y-m-d_H_i_s_v');
            $zipName = $nowStr . '-' . $loggedInUser . '.zip';
            $zipPath = addEndingSlash($zipDir) .  $zipName;

            $success = $this->zipFolder($path, $zipPath);

            $ZipResponser = $this->getZipResponder();
            $zipUrl = $ZipResponser->getSrc($zipName);
        }

        return [
            'success' => $success,
            'message' => $message,
            'zipName' => $zipName,
            'zipUrl'  => $zipUrl
        ];
    }

    /**
     * Packs a path in the filemigo directory structure into a ZIP archive
     *
     * @param string $path path of the filemigo folder (=source)
     * @param string $target path of the zip archive
     * @return bool
     */
    private function zipFolder(string $path, string $target) : bool
    {
        $root = $this->Weblication->getConfigValue('FMG_DATA_ROOT');
        $folder = $root . $path;

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS));
        $zip = new ZipArchive;

        $index = $this->Session->getVar('index');
        if ($zip->open($target, ZipArchive::CREATE)) {

            $zipRootDir = basename($target, ".zip");
            $zip->addEmptyDir($zipRootDir);
            foreach ($files as $file) {
                $filepath = $this->removePrefix($file->getPathname(), $root);
                if (isset($index[$filepath])) {
                    $zip->addFile(realpath($file->getPathname()), addEndingSlash($zipRootDir) . ltrim($filepath, '/'));
                }
            }
            $zip->close();
        }
        return file_exists($target);
    }

    /**
     * Checks, if the directory is writeable.
     *
     * @param string $directory
     * @return bool
     */
    public function isWriteableDirectory(string $directory): bool
    {
        if (!file_exists($directory)) {
            return false;
        }

        if (!is_dir($directory)) {
            return false;
        }

        if (!is_writable($directory)) {
            return false;
        }

        // second check
        $webApp = $this->Weblication->getName();
        $testFile = addEndingSlash($directory) . ltrim($webApp, '/');
        $success = @file_put_contents($testFile, "--$webApp--") !== false;

        if ($success) {
            unlink($testFile); // Datei löschen
        }
        return $success;
    }

    /**
     *
     * @param $fullString
     * @param $prefix
     * @return string
     */
    private function removePrefix($fullString, $prefix) : string
    {
        if (str_starts_with($fullString, $prefix)) {
            return substr($fullString, strlen($prefix)); // Entfernt den Prefix
        }
        return $fullString; // Falls der Prefix nicht gefunden wird, bleibt der String unverändert
    }

    public function getZipResponder() : GUI_FileResponder
    {
        $rootDir = $this->Weblication->getConfigValue('FMG_ZIP_DIR');
        $GUI_FileResponder = $this->Weblication->findComponent('zip-responder');
        $GUI_FileResponder->setRootDirectory($rootDir);
        return $GUI_FileResponder;
    }
}