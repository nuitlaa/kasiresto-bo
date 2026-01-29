<?php

use CodeIgniter\Boot;
use Config\Paths;
use Config\Services;

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

require FCPATH . '../s/app/Config/Paths.php';
$paths = new Paths();
require $paths->systemDirectory . '/Boot.php';

Boot::bootWeb($paths);

$config = config('App');
var_dump($config);

if ($config instanceof \Config\App) {
    echo "Config App loaded successfully.\n";
} else {
    echo "Config App failed to load.\n";
}
