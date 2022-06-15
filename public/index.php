<?php
ini_set('display_errors', '1');
date_default_timezone_set('Iran');

require_once __DIR__ . '/../vendor/autoload.php';

new Core\App();

function dd($input)
{
    echo "<pre>";
    var_dump($input);
    echo "</pre>";
    die;
}
