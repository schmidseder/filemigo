<?php
namespace filemigo;

return [
    'FMG_USERS' => [
        /* ************************************************************************************** */
        /* Please add the line containing the user and the password hash here.                    */
        /* IMPORTANT: For security reasons, make sure to remove the line for the user "filemigo". */
        'filemigo' => '$2y$10$Voq267TbDd47AdxZcx4Ifuuga4LYRKc58S/IwN6hUcLrs2TSuLa3a',

        /* ************************************************************************************** */
    ],
    'FMG_APP_NAME'    => 'filemigo',
    'FMG_TITLE'       => 'Filemigo',
    'FMG_HEADER'      => 'Filemigo - Web File Browser',
    'FMG_FOOTER'      => 'schmidseder.net',
    'FMG_DATA_ROOT'   => ENVVAR_FILEMIGO_DATA,
    'FMG_DATA_ROOT_BRANCHES' => FILEMIGO_ALL_DATA,
//    'FMG_DATA_ROOT_BRANCHES' => [
//        'directory_one',
//        'directory_two',
//        'directory_three',
//        'file_four.pdf'
//    ],
    'FMG_ZIP_DIR'     => ENVVAR_FILEMIGO_ZIP,
    'FMG_LIST_IMAGES' => true,
];