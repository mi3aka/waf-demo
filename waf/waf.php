<?php
#error_reporting(E_ALL);
#ini_set('display_errors','1');

require_once 'autoload.php';

use waf\Firewall;
use waf\Config;

$config = new Config();
$waf = new Firewall($config);
$waf->run();
