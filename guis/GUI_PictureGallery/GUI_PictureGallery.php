<?php
namespace filemigo\guis\GUI_PictureGallery;

use mypoolapp\guis\GUI_PictureBox\GUI_PictureBox;
use pool\classes\Core\Input\Filter\DataType;
use pool\classes\Core\Input\Input;
use pool\classes\GUI\GUI_Module;

class GUI_PictureGallery extends GUI_Module {
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
        $this->Template->setVar('name', $this->getName());

        $baseDirectory = $this->Input->getVar($this->getName());
        if (!$baseDirectory) {
            $this->Template->newBlock('galleryErrorBlock');
            $this->Template->setVar('errorMessage', 'Directory parameter missing');
            $this->Template->setVar('baseDirectoryKey', $this->getName());
            $this->Template->setVar('baseDirectoryValue', $baseDirectory);
            return;
        }

        $absoluteBaseDirectory = addEndingSlash(DIR_DATA_ROOT) . addEndingSlash($baseDirectory);
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
                    if (str_starts_with($image, addEndingSlash(DIR_DATA_ROOT))) {
                        $imagePath = substr($image, strlen(addEndingSlash(DIR_DATA_ROOT)));

                        /** @var GUI_PictureBox $GUI_PictureBox */
                        $GUI_PictureBox = GUI_Module::createGUIModule(GUI_PictureBox::class, $this->Weblication, $this);
                        $GUI_PictureBox->setVar('baseDirectory', dirname($imagePath));
                        $GUI_PictureBox->setVar('fileName', basename($imagePath));
                        $GUI_PictureBox->setVar('frameHeight', $this->getVar('frameHeight'));
                        $GUI_PictureBox->setVar('index', $index);

                        $GUI_PictureBox->provisionContent();
                        $GUI_PictureBox->prepareContent();
                        $pictureBox = $GUI_PictureBox->finalizeContent();
                        // ---------------------------------------------------

                        $this->Template->newBlock('pictureBlock');
                        $this->Template->setVar('pictureBox', $pictureBox);

                        $files[] = $GUI_PictureBox->getFilePath();
                        $index++;
                    }
                }
            }
            $this->setClientVar('files', $files);
        }
    }
}