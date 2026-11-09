<?php

// Front controller for Vercel Serverless Function
define('FCPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

if (getcwd() !== FCPATH) {
    chdir(FCPATH);
}

// Normalize URI and script name for CodeIgniter router
if (isset($_SERVER['REQUEST_URI'])) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF']    = '/index.php';
}

require dirname(__DIR__) . '/app/Config/Paths.php';

$paths = new Config\Paths();

require $paths->systemDirectory . '/Boot.php';

exit(CodeIgniter\Boot::bootWeb($paths));

