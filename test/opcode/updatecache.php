<?php


ini_set('display_errors', 0);
//error_reporting(E_ALL ^ E_NOTICE);


ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

if (!isset($_SERVER['DOCUMENT_ROOT']) || !strlen($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = rtrim(realpath(pathinfo(__FILE__, PATHINFO_DIRNAME).'/../../'), '/');
}

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/stdf.php';
//require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
//require_once($_SERVER['DOCUMENT_ROOT'] . "/classes/profiler.php");
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/op_codes.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/op_codes_price.php';

//------------------------------------------------------------------------------


$results = array();
//if(count($argv) > 1) parse_str(implode('&', array_slice($argv, 1)), $_GET);


//------------------------------------------------------------------------------


$results['op_codes_price::updateCache'] = (int) op_codes_price::updateCache();
op_codes::clearCache();
$results['op_codes::getAllOpCodes'] = (int) op_codes::getAllOpCodes();

//------------------------------------------------------------------------------

array_walk($results, function (&$value, $key) {
    $value = sprintf('%s = %s'.PHP_EOL, $key, $value);
});

print_r(implode('', $results));

exit;
