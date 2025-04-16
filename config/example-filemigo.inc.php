<?php
return [
    'FMG_TITLE' => 'Filemigo - Simple Web File Browser',
    'FMG_FOOTER' => 'schmidseder.net',
    'FMG_DATA_ROOT' => getenv('filemigo_data'),
    /*
    'FMG_DATA_ROOT_BRANCHES' => [
        'directory_one',
        'directory_two',
        'directory_three',
        'file_four.pdf'
    ],
    */
    'FMG_USERS' => [
        'admin' => '$2y$10$0k2zlQZ435c7DqONv/duieOxFV2BMlV/vmwnXJ0mxV5sK855FWu9m' // juggler
    ],
    'FMG_ZIP_DIR' => getenv('filemigo_zip')
];