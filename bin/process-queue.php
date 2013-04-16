<?php

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

require_once (__DIR__ . '/../src/Sendit/Sendit.php'); // Retrieve the $config variables
require_once (__DIR__ . '/../config/config.php'); // Retrieve the $config variables

use Sendit\Sendit;


foreach ($GLOBALS['config'] as $key => $value) 
{
	$GLOBALS[$key] = $value;
}

$sendit = new Sendit();

$sendit->processQueue();


