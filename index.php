<?php
declare(strict_types=1);

use src\classes\dispatch\Dispatcher;
use src\classes\repository\QuoicouRepository;

require_once 'vendor/autoload.php';

session_start();

QuoicouRepository::setConfig("config/config.db.ini");

if (!isset($_SESSION['user'])) {
    $action = "login";
} else {
    $action = $_GET['action'] ?? 'default';
}



$dispatcher = new Dispatcher($action);
$dispatcher->run();
