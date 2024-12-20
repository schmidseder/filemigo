<?php
namespace filemigo\guis\GUI_PictureGallery;

use filemigo\guis\GUI_FileResponder\GUI_FileResponder;
use filemigo\guis\GUI_PictureBox\GUI_PictureBox;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;
use pool\classes\Core\Url;

class GUI_PictureGallery extends GUI_Module {

    private string $rootDirectory = DIR_DATA_ROOT;
    /**
     * @var int
     */
    protected int $superglobals = Input::GET;

    /**
     * @var array|string[]
     */
    protected array $templates = [
        'stdout' => 'tpl_PictureGallery.html',
    ];

    protected array $jsFiles = [
        'gallery-lib.js'
    ];

    protected array $inputFilter = [
        'frameHeight'     => [ DataType::INT, 200]
    ];

    protected array $imageFileExtensions = ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg'];


    protected function prepare(): void
    {
        /** @var GUI_FileResponder $GUI_FileResponder */
        $GUI_FileResponder = $this->Weblication->findComponent('gallery-responder');
        $GUI_FileResponder->setRootDirectory($this->rootDirectory);
        $GUI_FileResponder->setOutputFile(true);


        $public = (bool) $this->getVar('public');
        $this->Template->setVar('name', $this->getName());

        $baseDirectory = $this->Input->getVar($this->getName());
        if (!$baseDirectory) {
            $this->Template->newBlock('galleryErrorBlock');
            $this->Template->setVar('errorMessage', 'Directory parameter missing');
            $this->Template->setVar('baseDirectoryKey', $this->getName());
            $this->Template->setVar('baseDirectoryValue', $baseDirectory);
            return;
        }

        $absoluteBaseDirectory = addEndingSlash($this->rootDirectory)
                               . addEndingSlash(ltrim($baseDirectory, DIRECTORY_SEPARATOR));
//        $internals = $this->getInternalParams();
//        $createDir = isset($internals['createDir']) && (boolean)$internals['createDir'];
        if (!file_exists($absoluteBaseDirectory)) {
            $this->Template->newBlock('galleryErrorBlock');
            $this->Template->setVar('errorMessage', "Directory not found");
            $this->Template->setVar('baseDirectoryKey', $this->getName());
            $this->Template->setVar('baseDirectoryValue', $baseDirectory);
            return;
        }

        $lowerCaseImageFileExtensions = array_map(function($extension) { return strtolower($extension); }, $this->imageFileExtensions);
        $upperCaseImageFileExtensions = array_map(function($extension)  { return strtoupper($extension); }, $this->imageFileExtensions);
        $extensions = array_merge($lowerCaseImageFileExtensions, $upperCaseImageFileExtensions);

        $imagePathPattern = $absoluteBaseDirectory . '*.{' . implode(',', $extensions) . '}';

        $images = glob($imagePathPattern, GLOB_BRACE);

        if (count($images) > 0) {
            $files = [];
            $index = 0;
            foreach ($images as $image) {
                if (preg_match('/(' . implode('|', $this->imageFileExtensions) . ')$/i', $image)) {
                    if (str_starts_with($image, addEndingSlash($this->rootDirectory))) {
                        $imagePath = substr($image, strlen(addEndingSlash($this->rootDirectory)));

                        /** @var GUI_PictureBox $GUI_PictureBox */
                        $GUI_PictureBox = GUI_Module::createGUIModule(GUI_PictureBox::class, $this->Weblication, $this);

                        $baseDirectory = dirname($imagePath);
                        $fileName = basename($imagePath);
                        $filePath = addEndingSlash(DIR_RELATIVE_DATA_ROOT) . addEndingSlash($baseDirectory) . $fileName;
                        $src = $filePath;

                        if (!$public) {
                            $filePath = addEndingSlash($this->rootDirectory) .$imagePath;
                            $src = $GUI_FileResponder->getSrc(DIRECTORY_SEPARATOR.$imagePath);
                        }

                        $GUI_PictureBox->setVar('src', $src);
                        $GUI_PictureBox->setVar('filePath', $filePath);

                        $GUI_PictureBox->setVar('frameHeight', $this->getVar('frameHeight'));
                        $GUI_PictureBox->setVar('index', $index);

                        // $GUI_PictureBox->setVar('image', $image);

                        $GUI_PictureBox->provisionContent();
                        $GUI_PictureBox->prepareContent();
                        $pictureBox = $GUI_PictureBox->finalizeContent();
                        // ---------------------------------------------------

                        $this->Template->newBlock('pictureBlock');
                        $this->Template->setVar('pictureBox', $pictureBox);

                        $files[] = $GUI_PictureBox->getSrc();
                        $index++;
                    }
                }
            }
            $this->setClientVar('files', $files);
        }
    }

    public function setRootDirectory(string $rootDirectory): void
    {
        $this->rootDirectory = $rootDirectory;
    }
}