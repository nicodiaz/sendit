<?php
/**
 * Send It! Server
 * 
 * This process run forever and process the queue every specified seconds
 *
 * @link      https://github.com/nicodiaz/sendit/ for the canonical source repository
 * @copyright Copyright (c) 2013 Nicolás Díaz País. (http://www.nicodp.com.ar)
 * @license   http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author nicodiaz
 * @package Sendit
 * @version 1.0.5
 */
(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

require_once (__DIR__ . '/../src/Sendit/Sendit.php'); // Retrieve the $config variables
require_once (__DIR__ . '/../config/config.php'); // Retrieve the $config variables

use Sendit\Sendit;

/**
 * Modify this value to get more or less sleep time between queue prosecution
 * @var long
 */
const SECONDS_BETWEEN_PROCESSES = 5;

/**
 * Default Timezone for logging purposes
 * @var String
 */
const DEFAULT_TIMEZONE = 'America/Buenos_Aires'; 
const DEFAULT_TIME_FORMAT = 'd/m/Y H:i:s'; 


date_default_timezone_set(DEFAULT_TIMEZONE);

$sendit = new Sendit($GLOBALS['config']);


while(true)
{
	try 
	{
		$sendit->processQueue();
	} 
	catch (\Exception $e) 
	{
		$datetime = new \DateTime();
		$errorMsg = $datetime->format(DEFAULT_TIME_FORMAT) . " " . "Sendit!: Process error - " . $e->getMessage();
		error_log($errorMsg);
	}
	
	sleep(SECONDS_BETWEEN_PROCESSES);
}
