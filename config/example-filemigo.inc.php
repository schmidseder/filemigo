<?php
return [
    'FMG_TITLE'  => 'Filemigo',
    'FMG_HEADER' => 'Filemigo - Simple Web File Browser',
    'FMG_FOOTER' => 'schmidseder.net',
    /*
    'FMG_DATA_ROOT_BRANCHES' => [
        'directory_one',
        'directory_two',
        'directory_three',
        'file_four.pdf'
    ],
    */
    'FMG_USERS' => [

        'filemigo' => '$2y$10$Voq267TbDd47AdxZcx4Ifuuga4LYRKc58S/IwN6hUcLrs2TSuLa3a',
    ],
    'FMG_DATA_ROOT' => getenv('filemigo_data'),
    'FMG_ZIP_DIR'   => getenv('filemigo_zip')
];